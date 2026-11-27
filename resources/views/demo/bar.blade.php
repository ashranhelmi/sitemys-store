@php
    // Sitemys demo strip, same look as the property demo (ren-demo-runtime demo-bar).
    $demoRole = auth()->user()?->is_admin ? 'admin' : 'shopper';
    $demoRoles = ['shopper' => 'Shopper', 'admin' => 'Admin'];
@endphp
<style>
.demo-bar{position:fixed;inset:0 0 auto;z-index:1000;height:48px;padding:0 16px;background:#fbfaf7;color:#15171a;border-bottom:1px solid #e3e1dc;font:600 13px/1 "Plus Jakarta Sans",system-ui,-apple-system,"Segoe UI",sans-serif;-webkit-font-smoothing:antialiased;display:flex;align-items:center;gap:14px;white-space:nowrap}
.demo-bar *{box-sizing:border-box}.demo-bar a{color:inherit;text-decoration:none}.demo-bar form{margin:0}
.demo-bar .sm-mark{font:700 16px/1 Sora,"Plus Jakarta Sans",system-ui,sans-serif;letter-spacing:-.02em;padding-right:14px;border-right:1px solid #e3e1dc}.demo-bar .sm-mark i{color:#E0312B;font-style:normal}
.demo-bar .demo-name{color:#6b717a;font-weight:500}
.demo-bar .sm-seg{display:flex;padding:3px;border:1px solid #e3e1dc;border-radius:999px;background:#fff}
.demo-bar .sm-seg button{font:inherit;color:#4a5059;background:none;border:0;padding:7px 14px;border-radius:999px;cursor:pointer}.demo-bar .sm-seg button:hover{color:#15171a}.demo-bar .sm-seg button[aria-current]{background:#15171a;color:#fff}
.demo-bar .sm-more{margin-left:auto;position:relative}.demo-bar summary{list-style:none;cursor:pointer;width:34px;height:34px;display:flex;align-items:center;justify-content:center;border:1px solid #e3e1dc;border-radius:999px;background:#fff;font-size:15px}.demo-bar summary::-webkit-details-marker{display:none}.demo-bar summary:hover{background:#f3f2ee}
.demo-bar .demo-menu{position:absolute;top:calc(100% + 8px);right:0;min-width:230px;background:#fff;border:1px solid #e3e1dc;border-radius:12px;box-shadow:0 10px 28px rgba(21,23,26,.12);padding:6px;display:grid;gap:2px;font-weight:500}
.demo-bar .demo-menu a,.demo-bar .demo-menu button{display:flex;width:100%;font:inherit;color:#15171a;background:none;border:0;border-radius:8px;padding:10px 12px;text-align:left;cursor:pointer}.demo-bar .demo-menu a:hover,.demo-bar .demo-menu button:hover{background:#f3f2ee}
.demo-bar .demo-menu hr{border:0;border-top:1px solid #e3e1dc;margin:4px 6px}.demo-bar .demo-menu small{display:block;color:#6b717a;font-size:11px;line-height:1.4;padding:4px 12px 6px;white-space:normal}
.demo-bar a:focus-visible,.demo-bar button:focus-visible,.demo-bar summary:focus-visible{outline:2px solid #5cc8ff;outline-offset:2px}
body{padding-top:48px!important}
body.storefront>nav.fixed.top-0{top:48px!important}
body.storefront>div.fixed.inset-0{top:48px!important}
[data-slot=sidebar-container]{top:48px!important;height:calc(100svh - 48px)!important}
@media(max-width:900px){.demo-bar .demo-name{display:none}}
@media(max-width:400px){.demo-bar{padding:0 10px;gap:10px}.demo-bar .sm-seg button{padding:7px 11px}}
.demo-toast{position:fixed;z-index:1002;left:50%;top:58px;transform:translateX(-50%);max-width:calc(100vw - 32px);background:#15171a;color:#fff;border-radius:8px;padding:10px 14px;font:500 13px/1.4 "Plus Jakarta Sans",system-ui,sans-serif;box-shadow:0 8px 24px rgba(0,0,0,.2)}
</style>
<aside class="demo-bar" aria-label="Demo controls">
    <a class="sm-mark" href="{{ config('demo.examples_url') }}" aria-label="sitemys. studio, all examples">sitemys<i>.</i></a>
    <span class="demo-name">{{ config('store.name') }} · demo</span>
    <nav class="sm-seg" aria-label="View the demo as">
        @foreach($demoRoles as $value => $label)
            <form method="post" action="{{ route('demo.enter') }}">@csrf<input type="hidden" name="role" value="{{ $value }}"><button type="submit"@if($value === $demoRole) aria-current="true"@endif>{{ $label }}</button></form>
        @endforeach
    </nav>
    <details class="sm-more">
        <summary aria-label="More">⋯</summary>
        <div class="demo-menu">
            <a href="{{ config('demo.examples_url') }}">All examples</a>
            <a href="{{ config('demo.source_url') }}" target="_blank" rel="noopener">Source code (opens GitHub)</a>
            <hr>
            <form method="post" action="{{ route('demo.reset') }}" onsubmit="return confirm('Reset the demo? Your orders and edits in this demo will be cleared.')">@csrf<button type="submit">Reset demo</button></form>
            <small>Fictional products and orders. No real payments. Your changes stay in your own private copy for 24 hours.</small>
        </div>
    </details>
</aside>
<script>
(function(){
  function toast(msg){var t=document.querySelector('.demo-toast');if(!t){t=document.createElement('div');t.className='demo-toast';t.setAttribute('role','status');document.body.appendChild(t);}t.textContent=msg;clearTimeout(t._h);t._h=setTimeout(function(){t.remove();},4000);}
  document.addEventListener('click',function(e){
    var b=e.target.closest('button,a');
    if(b&&!b.closest('.demo-bar')&&/^\s*(sign|log)\s*out\s*$/i.test(b.textContent)){e.preventDefault();e.stopPropagation();toast('In the demo, switch between Shopper and Admin at the top.');return;}
    document.querySelectorAll('.demo-bar details[open]').forEach(function(d){if(!d.contains(e.target))d.removeAttribute('open');});
  },true);
})();
</script>
