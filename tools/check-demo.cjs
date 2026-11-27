// End-to-end check of the public demo: node tools/check-demo.cjs  (STORE_DEMO_URL to point at another host)
// Desktop 1440 visitor buys and pays, then runs the order in the admin; phone 390 visitor fails a payment and retries.
// Also: visitor isolation, reset, template untouched, no page errors / HTTP errors, no sideways scroll.
const { chromium } = require(process.env.PLAYWRIGHT || '../../audit-engine/node_modules/playwright');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const crypto = require('node:crypto');
const os = require('node:os');

const base = (process.env.STORE_DEMO_URL || 'http://127.0.0.1:8766/examples/store').replace(/\/$/, '');
const shots = process.env.SHOTS || os.homedir() + '/Downloads/Active-projects/sitemys-store/demo-qa';
const template = 'database/demo-template.sqlite';
const hash = () => fs.existsSync(template) ? crypto.createHash('sha256').update(fs.readFileSync(template)).digest('hex') : 'remote';
const problems = [];
let passed = 0;
const ok = (msg) => { passed++; console.log('PASS', msg); };

function watch(page, tag) {
  page.on('pageerror', (e) => problems.push(`${tag} page error: ${e.message}`));
  page.on('console', (m) => { if (m.type() === 'error') problems.push(`${tag} console: ${m.text()}`); });
  page.on('response', (r) => { if (r.status() >= 400) problems.push(`${tag} HTTP ${r.status()} ${r.url()}`); });
}
async function fits(page, label) {
  const wide = await page.evaluate(() => document.documentElement.scrollWidth > window.innerWidth + 1);
  assert.equal(wide, false, `${label}: sideways scroll`);
}
async function noServerError(page, label) {
  const text = await page.locator('body').innerText();
  assert.equal(/Server Error|Something went wrong/i.test(text), false, `${label}: server error`);
}
async function buy(page, size) {
  await page.goto(base + '/shop/dot-heavyweight-tee-ink', { waitUntil: 'networkidle' });
  await page.getByRole('button', { name: size, exact: true }).click();
  await Promise.all([page.waitForURL((u) => !u.pathname.endsWith('/shop/dot-heavyweight-tee-ink') || true), page.getByRole('button', { name: /add to cart/i }).click()]);
  await page.waitForLoadState('networkidle');
  await page.goto(base + '/checkout', { waitUntil: 'networkidle' });
  assert.equal(await page.locator('input[name=name]').inputValue(), 'Aina Rahman', 'demo sample details prefilled');
  await page.getByRole('button', { name: /pay now/i }).click();
  await page.waitForURL('**/demo/pay/**');
  await page.waitForLoadState('networkidle');
}

(async () => {
  const before = hash();
  // Outside interference: bots without our cookie must not create copies or reach the gateway addresses.
  const copies = () => fs.existsSync('storage/app/demo-sessions') ? fs.readdirSync('storage/app/demo-sessions').length : -1;
  const local = !process.env.STORE_DEMO_URL;
  const c0 = copies();
  for (const path of ['/', '/shop/dot-heavyweight-tee-ink', '/cart', '/login', '/demo/as/admin']) {
    const r = await fetch(base + path, { redirect: 'manual' });
    assert(r.status < 400, `cookie-less GET ${path} → ${r.status}`);
  }
  if (local) assert.equal(copies(), c0, 'cookie-less browsing created a copy');
  assert.equal((await fetch(base + '/cart/add', { method: 'POST', body: 'product_id=1&size=M&quantity=1', headers: { 'content-type': 'application/x-www-form-urlencoded' } })).status, 419, 'cookie-less POST refused');
  assert.equal((await fetch(base + '/webhook/billplz', { method: 'POST', body: 'id=x', headers: { 'content-type': 'application/x-www-form-urlencoded' } })).status, 404, 'webhook closed');
  assert.equal((await fetch(base + '/payment/callback?billplz[id]=x', { redirect: 'manual' })).status, 404, 'callback closed');
  if (local) assert.equal(copies(), c0, 'refused requests created a copy');
  ok('outside requests: browsing makes no copy, cookie-less actions refused (419), Billplz addresses closed (404)');

  const browser = await chromium.launch({ headless: true });
  const desk = await browser.newContext({ viewport: { width: 1440, height: 900 } });
  const phone = await browser.newContext({ viewport: { width: 390, height: 844 }, isMobile: true, hasTouch: true });
  const a = await desk.newPage(); watch(a, '1440');
  const b = await phone.newPage(); watch(b, '390');

  for (const [p, w] of [[a, 1440], [b, 390]]) {
    await p.goto(base + '/', { waitUntil: 'networkidle' });
    assert(await p.locator('.demo-bar').isVisible(), 'demo bar visible');
    await fits(p, `home ${w}`);
    await p.screenshot({ path: `${shots}/home-${w}.png` });
  }
  ok('home + demo bar at 1440 and 390, no sideways scroll');

  // Desktop: buy → demo pay → thank-you → admin
  await buy(a, 'M');
  await a.screenshot({ path: `${shots}/pay-1440.png`, fullPage: true });
  await a.getByRole('button', { name: /^Pay RM/ }).click();
  await a.waitForURL('**/thank-you/**');
  await a.getByText('Order Confirmed').waitFor();
  const orderNo = (await a.locator('p.font-mono').first().innerText()).trim();
  assert.match(orderNo, /^[A-Z]+-?\w+/, 'order number shown');
  await a.screenshot({ path: `${shots}/thank-you-1440.png`, fullPage: true });
  ok(`checkout with sample details → demo pay → thank-you (${orderNo})`);

  await a.getByRole('link', { name: 'Open in admin' }).click();
  await a.waitForURL('**/dashboard/admin/orders/**');
  await a.waitForLoadState('networkidle');
  await a.getByText(orderNo).first().waitFor();
  assert((await a.locator('body').innerText()).toLowerCase().includes('paid'), 'order paid in admin');
  assert.equal(await a.locator('[data-sidebar=menu-button][data-active=true]').innerText().then((t) => t.trim()), 'Orders', 'sidebar marks Orders');
  await a.screenshot({ path: `${shots}/admin-order-1440.png`, fullPage: true });
  ok('"Open in admin" lands on the same order, paid, Orders active in sidebar');

  await a.getByRole('combobox').first().click();
  await a.getByRole('option', { name: 'processing' }).click();
  // wait for the save request itself; reloading straight after the click raced it on the live server (27 Nov)
  await Promise.all([a.waitForResponse((r) => r.request().method() === 'PUT' && /admin\/orders\//.test(r.url())), a.getByRole('button', { name: 'Save Changes' }).click()]);
  await a.waitForLoadState('networkidle');
  await a.reload({ waitUntil: 'networkidle' });
  assert.equal((await a.locator('main, body').first().innerText()).includes('processing'), true, 'status saved');
  ok('admin moves the order to processing and it stays after reload');

  const stock = await a.goto(base + '/dashboard/admin/products', { waitUntil: 'networkidle' }).then(() => a.locator('tr', { hasText: 'Dot Heavyweight Tee' }).filter({ hasText: 'Ink' }).first().innerText());
  assert.match(stock, /\b39\b/, 'stock 40 → 39 after the paid order');
  ok('stock went down by one (40 → 39)');

  for (const page of ['/dashboard', '/dashboard/admin/orders', '/dashboard/admin/products', '/dashboard/admin/customers', '/dashboard/admin/payments', '/dashboard/profile']) {
    await a.goto(base + page, { waitUntil: 'networkidle' });
    await noServerError(a, page);
    await fits(a, page + ' 1440');
  }
  await a.goto(base + '/dashboard', { waitUntil: 'networkidle' });
  await a.screenshot({ path: `${shots}/admin-dashboard-1440.png` });
  ok('all 6 admin screens open at 1440 without errors');

  // Phone: isolation, failed payment, retry, admin on phone
  await b.goto(base + '/', { waitUntil: 'networkidle' });
  await b.getByRole('button', { name: 'Admin', exact: true }).click();
  await b.waitForURL('**/dashboard');
  await b.goto(base + '/dashboard/admin/orders', { waitUntil: 'networkidle' });
  assert.equal(await b.getByText(orderNo).count(), 0, 'other visitor cannot see the order');
  await fits(b, 'admin orders 390');
  await b.screenshot({ path: `${shots}/admin-orders-390.png`, fullPage: true });
  ok('a second visitor does not see the first visitor\'s order (private copies)');

  await b.getByRole('button', { name: 'Shopper', exact: true }).click();
  await b.waitForLoadState('networkidle');
  await buy(b, 'L');
  await fits(b, 'pay 390');
  await b.screenshot({ path: `${shots}/pay-390.png`, fullPage: true });
  await b.getByRole('button', { name: 'Payment fails' }).click();
  await b.waitForURL('**/payment/retry/**');
  await b.getByText('Payment Incomplete').waitFor();
  await b.getByRole('button', { name: /Complete Payment/ }).click();
  await b.waitForURL('**/demo/pay/**');
  await b.getByRole('button', { name: /^Pay RM/ }).click();
  await b.waitForURL('**/thank-you/**');
  await fits(b, 'thank-you 390');
  await b.screenshot({ path: `${shots}/thank-you-390.png`, fullPage: true });
  ok('phone: payment fails → retry page → pay → thank-you');

  // Reset clears only this visitor
  await a.goto(base + '/dashboard/admin/orders', { waitUntil: 'networkidle' });
  a.once('dialog', (d) => d.accept());
  await a.locator('.demo-bar summary').click();
  await a.getByRole('button', { name: 'Reset demo' }).click();
  await a.waitForLoadState('networkidle');
  await a.getByRole('button', { name: 'Admin', exact: true }).click();
  await a.waitForURL('**/dashboard');
  await a.goto(base + '/dashboard/admin/orders', { waitUntil: 'networkidle' });
  assert.equal(await a.getByText(orderNo).count(), 0, 'reset removed the order');
  ok('Reset demo brings this visitor back to the seed data');

  assert.equal(hash(), before, 'template untouched');
  ok('seed template unchanged');
  await browser.close();

  if (problems.length) { console.log('FAIL problems:\n' + problems.join('\n')); process.exit(1); }
  console.log(`\n${passed} PASS, 0 FAIL`);
})().catch((e) => { console.error('FAIL', e.message); if (problems.length) console.error(problems.join('\n')); process.exit(1); });
