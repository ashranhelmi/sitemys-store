import { useState, useMemo, useEffect } from 'react';
import {
    AreaChart, Area, BarChart, Bar,
    XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer,
} from 'recharts';
import AppLayout from "@/components/layout/AppLayout";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Package, CreditCard, ShoppingBag, Users, Clock, AlertTriangle } from "lucide-react";
import { Link } from "@inertiajs/react";

// Reads resolved CSS variable values so SVG chart elements respect dark mode
function useChartTheme() {
    const read = () => {
        const s = getComputedStyle(document.documentElement);
        return {
            tick: s.getPropertyValue('--muted-foreground').trim(),
            grid: s.getPropertyValue('--border').trim(),
        };
    };
    const [theme, setTheme] = useState(read);
    useEffect(() => {
        const obs = new MutationObserver(() => setTheme(read()));
        obs.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
        return () => obs.disconnect();
    }, []);
    return theme;
}

const MONTHS = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
const COLOR_A = 'var(--chart-1)'; // ink (paper in dark mode)
const COLOR_B = 'var(--chart-2)'; // sitemys red dot
const fmtRM = v => `RM ${Number(v).toFixed(2)}`;

const statusColors = {
    pending:    'text-amber-400',
    paid:       'text-blue-400',
    processing: 'text-blue-400',
    packed:     'text-blue-400',
    shipped:    'text-purple-400',
    delivered:  'text-green-400',
    completed:  'text-green-400',
    cancelled:  'text-red-400',
    refunded:   'text-red-400',
    failed:     'text-red-400',
    expired:    'text-zinc-400',
};

const ChartTooltip = ({ active, payload, label }) => {
    if (!active || !payload?.length) return null;
    return (
        <div className="rounded-lg border bg-card shadow-md p-3 text-xs space-y-1">
            <p className="font-semibold text-card-foreground mb-1">{label}</p>
            {payload.map((p, i) => (
                <p key={i} style={{ color: p.color }}>{p.name}: {fmtRM(p.value)}</p>
            ))}
        </div>
    );
};

function RevenueTrendChart({ data }) {
    const [period, setPeriod] = useState('1Y');
    const theme = useChartTheme();

    const chartData = useMemo(() => {
        if (period === '1Y') return data.slice(-12);

        if (period === '5Y') {
            // Aggregate last 60 months into quarters so the view is always visually distinct
            const last60 = data.slice(-60);
            const quarters = [];
            for (let i = 0; i < last60.length; i += 3) {
                const chunk = last60.slice(i, i + 3);
                const [yr, mo] = chunk[0].period.split('-');
                const q = Math.ceil(parseInt(mo) / 3);
                quarters.push({
                    period: chunk[0].period,
                    label: `Q${q} ${yr}`,
                    revenue: parseFloat(chunk.reduce((s, d) => s + d.revenue, 0).toFixed(2)),
                    orders: chunk.reduce((s, d) => s + d.orders, 0),
                });
            }
            return quarters;
        }

        return data; // All — full monthly history
    }, [period, data]);

    return (
        <Card>
            <CardHeader className="flex flex-row items-center justify-between pb-4">
                <CardTitle>Revenue Trend</CardTitle>
                <div className="flex rounded-md border overflow-hidden text-xs">
                    {['1Y', '5Y', 'All'].map(p => (
                        <button
                            key={p}
                            onClick={() => setPeriod(p)}
                            className={`px-3 py-1.5 transition ${
                                period === p
                                    ? 'bg-primary text-primary-foreground'
                                    : 'text-muted-foreground hover:text-foreground hover:bg-muted'
                            }`}
                        >
                            {p}
                        </button>
                    ))}
                </div>
            </CardHeader>
            <CardContent>
                {chartData.length === 0 ? (
                    <div className="h-[280px] flex items-center justify-center text-sm text-muted-foreground">
                        No revenue data yet.
                    </div>
                ) : (
                    <ResponsiveContainer width="100%" height={280}>
                        <AreaChart data={chartData} margin={{ top: 4, right: 4, left: 0, bottom: 0 }}>
                            <defs>
                                <linearGradient id="revGrad" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="5%" stopColor={COLOR_A} stopOpacity={0.25} />
                                    <stop offset="95%" stopColor={COLOR_A} stopOpacity={0} />
                                </linearGradient>
                            </defs>
                            <CartesianGrid strokeDasharray="3 3" stroke={theme.grid} />
                            <XAxis
                                dataKey="label"
                                tick={{ fontSize: 11, fill: theme.tick }}
                                axisLine={false} tickLine={false}
                            />
                            <YAxis
                                tickFormatter={v => `RM ${v}`}
                                tick={{ fontSize: 11, fill: theme.tick }}
                                axisLine={false} tickLine={false}
                                width={72}
                            />
                            <Tooltip content={<ChartTooltip />} />
                            <Area
                                type="linear" dataKey="revenue" name="Revenue"
                                stroke={COLOR_A} fill="url(#revGrad)"
                                strokeWidth={2} dot={false} activeDot={{ r: 4 }}
                            />
                        </AreaChart>
                    </ResponsiveContainer>
                )}
            </CardContent>
        </Card>
    );
}

function ComparisonChart({ monthly, daily }) {
    const [mode, setMode] = useState('month');
    const [rangeA, setRangeA] = useState({ start: '', end: '' });
    const [rangeB, setRangeB] = useState({ start: '', end: '' });
    const [applied, setApplied] = useState(null);
    const theme = useChartTheme();

    const now = new Date();
    const thisYear = now.getFullYear();
    const thisMonthNum = now.getMonth() + 1;

    const { chartData, keyA, keyB } = useMemo(() => {
        if (mode === 'month') {
            const prevMonthNum = thisMonthNum === 1 ? 12 : thisMonthNum - 1;
            const prevMonthYear = thisMonthNum === 1 ? thisYear - 1 : thisYear;
            const thisStr = `${thisYear}-${String(thisMonthNum).padStart(2, '0')}`;
            const prevStr = `${prevMonthYear}-${String(prevMonthNum).padStart(2, '0')}`;
            const labelA = new Date(thisYear, thisMonthNum - 1, 1)
                .toLocaleString('default', { month: 'short', year: 'numeric' });
            const labelB = new Date(prevMonthYear, prevMonthNum - 1, 1)
                .toLocaleString('default', { month: 'short', year: 'numeric' });

            const mapA = {}, mapB = {};
            daily.forEach(d => {
                const day = parseInt(d.period.split('-')[2]);
                if (d.period.startsWith(thisStr)) mapA[day] = d.revenue;
                if (d.period.startsWith(prevStr)) mapB[day] = d.revenue;
            });

            const daysA = new Date(thisYear, thisMonthNum, 0).getDate();
            const daysB = new Date(prevMonthYear, prevMonthNum, 0).getDate();

            return {
                chartData: Array.from({ length: Math.max(daysA, daysB) }, (_, i) => ({
                    label: String(i + 1),
                    [labelA]: mapA[i + 1] ?? 0,
                    [labelB]: mapB[i + 1] ?? 0,
                })),
                keyA: labelA, keyB: labelB,
            };
        }

        if (mode === 'year') {
            const labelA = String(thisYear);
            const labelB = String(thisYear - 1);
            const mapA = {}, mapB = {};
            monthly.forEach(d => {
                const [y, m] = d.period.split('-');
                if (y === labelA) mapA[parseInt(m) - 1] = d.revenue;
                if (y === labelB) mapB[parseInt(m) - 1] = d.revenue;
            });
            return {
                chartData: MONTHS.map((m, i) => ({
                    label: m,
                    [labelA]: mapA[i] ?? 0,
                    [labelB]: mapB[i] ?? 0,
                })),
                keyA: labelA, keyB: labelB,
            };
        }

        // custom
        if (!applied) return { chartData: [], keyA: 'Range A', keyB: 'Range B' };
        const aFiltered = daily.filter(d => d.period >= applied.a.start && d.period <= applied.a.end);
        const bFiltered = daily.filter(d => d.period >= applied.b.start && d.period <= applied.b.end);
        const maxLen = Math.max(aFiltered.length, bFiltered.length, 1);
        return {
            chartData: Array.from({ length: maxLen }, (_, i) => ({
                label: `Day ${i + 1}`,
                [applied.a.label]: aFiltered[i]?.revenue ?? 0,
                [applied.b.label]: bFiltered[i]?.revenue ?? 0,
            })),
            keyA: applied.a.label, keyB: applied.b.label,
        };
    }, [mode, daily, monthly, thisYear, thisMonthNum, applied]);

    const applyCustom = () => {
        if (!rangeA.start || !rangeA.end || !rangeB.start || !rangeB.end) return;
        setApplied({
            a: { ...rangeA, label: `${rangeA.start} → ${rangeA.end}` },
            b: { ...rangeB, label: `${rangeB.start} → ${rangeB.end}` },
        });
    };

    const TABS = [
        { key: 'month',  label: 'This Month vs Last' },
        { key: 'year',   label: 'This Year vs Last' },
        { key: 'custom', label: 'Custom Range' },
    ];

    return (
        <Card>
            <CardHeader className="pb-4">
                <div className="flex flex-wrap items-center justify-between gap-3">
                    <CardTitle>Revenue Comparison</CardTitle>
                    <div className="flex flex-wrap gap-1.5">
                        {TABS.map(t => (
                            <button
                                key={t.key}
                                onClick={() => setMode(t.key)}
                                className={`px-3 py-1 text-xs rounded-full border transition ${
                                    mode === t.key
                                        ? 'bg-primary text-primary-foreground border-primary'
                                        : 'text-muted-foreground hover:text-foreground border-border'
                                }`}
                            >
                                {t.label}
                            </button>
                        ))}
                    </div>
                </div>
            </CardHeader>
            <CardContent>
                {mode === 'custom' && (
                    <div className="flex flex-wrap gap-6 mb-5 items-end">
                        <div className="space-y-1.5">
                            <p className="text-xs font-medium" style={{ color: COLOR_A }}>Range A</p>
                            <div className="flex gap-2">
                                <input type="date" value={rangeA.start}
                                    onChange={e => setRangeA(r => ({ ...r, start: e.target.value }))}
                                    className="h-8 rounded-md border border-input bg-background px-2 text-xs text-foreground" />
                                <input type="date" value={rangeA.end}
                                    onChange={e => setRangeA(r => ({ ...r, end: e.target.value }))}
                                    className="h-8 rounded-md border border-input bg-background px-2 text-xs text-foreground" />
                            </div>
                        </div>
                        <div className="space-y-1.5">
                            <p className="text-xs font-medium" style={{ color: COLOR_B }}>Range B</p>
                            <div className="flex gap-2">
                                <input type="date" value={rangeB.start}
                                    onChange={e => setRangeB(r => ({ ...r, start: e.target.value }))}
                                    className="h-8 rounded-md border border-input bg-background px-2 text-xs text-foreground" />
                                <input type="date" value={rangeB.end}
                                    onChange={e => setRangeB(r => ({ ...r, end: e.target.value }))}
                                    className="h-8 rounded-md border border-input bg-background px-2 text-xs text-foreground" />
                            </div>
                        </div>
                        <button
                            onClick={applyCustom}
                            className="h-8 px-4 text-xs rounded-md bg-primary text-primary-foreground hover:bg-primary/90 transition"
                        >
                            Compare
                        </button>
                    </div>
                )}

                {chartData.length === 0 ? (
                    <div className="h-[280px] flex items-center justify-center text-sm text-muted-foreground">
                        {mode === 'custom'
                            ? 'Select two date ranges and click Compare.'
                            : 'No data for this period.'}
                    </div>
                ) : (
                    <ResponsiveContainer width="100%" height={280}>
                        <BarChart data={chartData} margin={{ top: 4, right: 4, left: 0, bottom: 0 }}>
                            <CartesianGrid strokeDasharray="3 3" stroke={theme.grid} />
                            <XAxis
                                dataKey="label"
                                tick={{ fontSize: 11, fill: theme.tick }}
                                axisLine={false} tickLine={false}
                            />
                            <YAxis
                                tickFormatter={v => `RM ${v}`}
                                tick={{ fontSize: 11, fill: theme.tick }}
                                axisLine={false} tickLine={false}
                                width={72}
                            />
                            <Tooltip content={<ChartTooltip />} />
                            <Legend wrapperStyle={{ fontSize: 11, color: theme.tick }} />
                            <Bar dataKey={keyA} name={keyA} fill={COLOR_A} radius={[3, 3, 0, 0]} />
                            <Bar dataKey={keyB} name={keyB} fill={COLOR_B} radius={[3, 3, 0, 0]} />
                        </BarChart>
                    </ResponsiveContainer>
                )}
            </CardContent>
        </Card>
    );
}

export default function Dashboard({ stats, recent_orders, recent_payments, top_products, low_stock, revenue_monthly, revenue_daily }) {
    const statCards = [
        { title: "Total Orders",    value: stats.total_orders,         icon: Package },
        { title: "Revenue",         value: `RM ${stats.total_revenue}`, icon: CreditCard },
        { title: "Products",        value: stats.total_products,        icon: ShoppingBag },
        { title: "Customers",       value: stats.total_customers,       icon: Users },
        { title: "Pending Payment", value: stats.pending_orders,        icon: Clock, alert: stats.pending_orders > 0 },
    ];

    return (
        <AppLayout title="Dashboard" breadcrumbs={[{ label: "Dashboard", url: route('dashboard') }]}>
            {/* Stat cards */}
            <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                {statCards.map((card) => (
                    <Card key={card.title} className={card.alert ? 'border-amber-500/50' : ''}>
                        <CardHeader className="flex flex-row items-center justify-between pb-2">
                            <CardTitle className="text-sm font-medium text-muted-foreground">{card.title}</CardTitle>
                            <card.icon className={`size-4 ${card.alert ? 'text-amber-400' : 'text-muted-foreground'}`} />
                        </CardHeader>
                        <CardContent>
                            <div className={`text-2xl font-bold ${card.alert ? 'text-amber-400' : ''}`}>{card.value}</div>
                        </CardContent>
                    </Card>
                ))}
            </div>

            {/* Low stock alert */}
            {low_stock.length > 0 && (
                <Card className="border-red-500/40">
                    <CardHeader className="flex flex-row items-center gap-2">
                        <AlertTriangle className="size-4 text-red-400" />
                        <CardTitle className="text-red-400">Low Stock Warning</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="flex flex-wrap gap-2">
                            {low_stock.map(p => (
                                <div key={p.id} className="flex items-center gap-2 text-sm bg-red-500/10 border border-red-500/30 rounded-lg px-3 py-1.5">
                                    <span className="font-medium">{p.name}</span>
                                    <Badge variant={p.stock === 0 ? 'destructive' : 'warning'} className="text-xs">
                                        {p.stock === 0 ? 'Out of stock' : `${p.stock} left`}
                                    </Badge>
                                </div>
                            ))}
                        </div>
                    </CardContent>
                </Card>
            )}

            {/* Revenue charts */}
            <RevenueTrendChart data={revenue_monthly} />
            <ComparisonChart monthly={revenue_monthly} daily={revenue_daily} />

            <div className="grid gap-4 md:grid-cols-2">
                {/* Recent Orders */}
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between">
                        <CardTitle>Recent Orders</CardTitle>
                        <Link href={route('admin.orders.index')} className="text-xs text-muted-foreground hover:text-foreground transition">View all →</Link>
                    </CardHeader>
                    <CardContent>
                        {recent_orders.length === 0 ? (
                            <p className="text-sm text-muted-foreground">No orders yet.</p>
                        ) : (
                            <div className="space-y-3">
                                {recent_orders.map((order) => (
                                    <Link key={order.id} href={route('admin.orders.show', order.id)} className="flex items-center justify-between text-sm hover:opacity-70 transition">
                                        <div>
                                            <p className="font-medium font-mono">{order.order_number}</p>
                                            <p className="text-muted-foreground text-xs">{order.customer_name} · {order.created_at}</p>
                                        </div>
                                        <div className="text-right">
                                            <p className="font-semibold">RM {order.total}</p>
                                            <p className={`text-xs capitalize ${statusColors[order.status] ?? 'text-zinc-400'}`}>{order.status}</p>
                                        </div>
                                    </Link>
                                ))}
                            </div>
                        )}
                    </CardContent>
                </Card>

                {/* Recent Payments */}
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between">
                        <CardTitle>Recent Payments</CardTitle>
                        <Link href={route('admin.payments.index')} className="text-xs text-muted-foreground hover:text-foreground transition">View all →</Link>
                    </CardHeader>
                    <CardContent>
                        {recent_payments.length === 0 ? (
                            <p className="text-sm text-muted-foreground">No payments yet.</p>
                        ) : (
                            <div className="space-y-3">
                                {recent_payments.map((payment) => (
                                    <div key={payment.id} className="flex items-center justify-between text-sm">
                                        <div>
                                            <p className="font-medium font-mono">{payment.order_number}</p>
                                            <p className="text-muted-foreground text-xs">{payment.paid_at}</p>
                                        </div>
                                        <p className="font-semibold text-green-400">RM {payment.amount}</p>
                                    </div>
                                ))}
                            </div>
                        )}
                    </CardContent>
                </Card>

                {/* Top Selling Products */}
                <Card>
                    <CardHeader><CardTitle>Top Selling Products</CardTitle></CardHeader>
                    <CardContent>
                        {top_products.length === 0 ? (
                            <p className="text-sm text-muted-foreground">No sales yet.</p>
                        ) : (
                            <div className="space-y-3">
                                {top_products.map((p, i) => (
                                    <div key={i} className="flex items-center justify-between text-sm">
                                        <div className="flex items-center gap-3">
                                            <span className="size-5 rounded-full bg-muted text-muted-foreground text-xs flex items-center justify-center font-bold">{i + 1}</span>
                                            <p className="font-medium">{p.product_name}</p>
                                        </div>
                                        <div className="text-right">
                                            <p className="font-semibold">{p.total_sold} sold</p>
                                            <p className="text-muted-foreground text-xs">RM {p.total_revenue}</p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </CardContent>
                </Card>

                {/* Inventory status */}
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between">
                        <CardTitle>Inventory Status</CardTitle>
                        <Link href={route('admin.products.index')} className="text-xs text-muted-foreground hover:text-foreground transition">Manage →</Link>
                    </CardHeader>
                    <CardContent>
                        {low_stock.length === 0 ? (
                            <p className="text-sm text-muted-foreground">All products well-stocked.</p>
                        ) : (
                            <div className="space-y-3">
                                {low_stock.map(p => (
                                    <div key={p.id} className="flex items-center justify-between text-sm">
                                        <p className="font-medium">{p.name}</p>
                                        <Badge variant={p.stock === 0 ? 'destructive' : 'warning'}>
                                            {p.stock === 0 ? 'Out of stock' : `${p.stock} left`}
                                        </Badge>
                                    </div>
                                ))}
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
