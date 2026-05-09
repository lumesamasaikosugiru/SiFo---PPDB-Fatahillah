<div style="margin-top:16px">
    <hr style="border:none;border-top:1px solid #e5e7eb;margin-bottom:14px">
    <p style="font-size:11px;color:#9ca3af;text-align:center;margin-bottom:10px;font-weight:600;text-transform:uppercase;letter-spacing:.6px">
        🔑 Quick Login — Dev Only
    </p>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:7px">
        @php
        $devUsers = [
            ['label'=>'Super Admin',    'email'=>'spmb.ypf@superadmin.dev', 'color'=>'#7c3aed'],
            ['label'=>'Admin Yayasan',  'email'=>'spmb.ypf@admin.dev',       'color'=>'#0f4c3a'],
            ['label'=>'Admin SMP',      'email'=>'spmb.smp@admin.dev',        'color'=>'#0369a1'],
            ['label'=>'Admin Fatser',   'email'=>'spmb.fatser@admin.dev',     'color'=>'#b45309'],
            ['label'=>'Admin Fatcil 1', 'email'=>'spmb.fatcil1@admin.dev',    'color'=>'#be123c'],
            ['label'=>'Admin Fatcil 2', 'email'=>'spmb.fatcil2@admin.dev',    'color'=>'#0e7490'],
        ];
        @endphp
        @foreach($devUsers as $u)
        <button type="button"
            onclick="quickFillLogin('{{ $u['email'] }}')"
            style="padding:8px 10px;border-radius:8px;font-size:11.5px;font-weight:600;
                   border:1.5px solid {{ $u['color'] }};color:{{ $u['color'] }};
                   background:transparent;cursor:pointer;transition:all .15s;
                   text-align:left;line-height:1.35;width:100%;"
            onmouseover="this.style.background='{{ $u['color'] }}';this.style.color='#fff'"
            onmouseout="this.style.background='transparent';this.style.color='{{ $u['color'] }}'">
            {{ $u['label'] }}<br>
            <span style="font-weight:400;font-size:10px;opacity:.75">{{ $u['email'] }}</span>
        </button>
        @endforeach
    </div>
    <p style="font-size:10px;color:#d1d5db;text-align:center;margin-top:8px">
        Password semua akun: <strong style="color:#9ca3af;font-family:monospace">123</strong>
    </p>
</div>

<script>
function quickFillLogin(email) {
    var emailEl = document.querySelector('input[type="email"]');
    if (emailEl) {
        emailEl.value = email;
        emailEl.dispatchEvent(new Event('input', {bubbles:true}));
        emailEl.dispatchEvent(new Event('change', {bubbles:true}));
    }
    var passEl = document.querySelector('input[type="password"]');
    if (passEl) {
        passEl.value = '123';
        passEl.dispatchEvent(new Event('input', {bubbles:true}));
        passEl.dispatchEvent(new Event('change', {bubbles:true}));
    }
    try {
        if (window.Livewire) {
            var all = window.Livewire.all ? window.Livewire.all() : [];
            all.forEach(function(c) {
                try { c.set('data.email', email); c.set('data.password', '123'); } catch(e) {}
            });
        }
    } catch(e) {}
}
</script>
