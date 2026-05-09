<x-filament-panels::page.simple>
    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    {{-- ── Dev Quick Login Buttons ── --}}
    <div style="margin-top:20px">
        <hr style="border:none;border-top:1px solid #e5e7eb;margin-bottom:16px">
        <p style="font-size:11px;color:#9ca3af;text-align:center;margin-bottom:10px;font-weight:500;text-transform:uppercase;letter-spacing:.5px">
            🔑 Quick Login (Dev)
        </p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
            @php
            $devUsers = [
                ['label'=>'Super Admin',        'email'=>'spmb.ypf@superadmin.dev', 'color'=>'#7c3aed'],
                ['label'=>'Admin Yayasan',       'email'=>'spmb.ypf@admin.dev',       'color'=>'#0f4c3a'],
                ['label'=>'Admin SMP',           'email'=>'spmb.smp@admin.dev',        'color'=>'#0369a1'],
                ['label'=>'Admin Fatser',        'email'=>'spmb.fatser@admin.dev',     'color'=>'#b45309'],
                ['label'=>'Admin Fatcil 1',      'email'=>'spmb.fatcil1@admin.dev',    'color'=>'#be123c'],
                ['label'=>'Admin Fatcil 2',      'email'=>'spmb.fatcil2@admin.dev',    'color'=>'#0e7490'],
            ];
            @endphp
            @foreach($devUsers as $u)
            <button type="button"
                onclick="fillLogin('{{ $u['email'] }}')"
                style="
                    padding:8px 10px;border-radius:8px;font-size:12px;font-weight:600;
                    border:1.5px solid {{ $u['color'] }};color:{{ $u['color'] }};
                    background:transparent;cursor:pointer;transition:all .15s;text-align:left;
                    line-height:1.3;
                "
                onmouseover="this.style.background='{{ $u['color'] }}';this.style.color='#fff'"
                onmouseout="this.style.background='transparent';this.style.color='{{ $u['color'] }}'">
                {{ $u['label'] }}<br>
                <span style="font-weight:400;opacity:.75;font-size:10px">{{ $u['email'] }}</span>
            </button>
            @endforeach
        </div>
        <p style="font-size:10px;color:#d1d5db;text-align:center;margin-top:8px">
            Password default: <strong style="color:#9ca3af">123</strong>
        </p>
    </div>
</x-filament-panels::page.simple>

<script>
function fillLogin(email) {
    // Cari input email dan password via Livewire/Alpine binding
    // Filament pakai wire:model='data.email' dan wire:model='data.password'
    var emailInputs = document.querySelectorAll('input[type="email"], input[wire\\:model*="email"]');
    var passInputs  = document.querySelectorAll('input[type="password"], input[wire\\:model*="password"]');

    emailInputs.forEach(function(el) {
        el.value = email;
        el.dispatchEvent(new Event('input', {bubbles:true}));
        el.dispatchEvent(new Event('change', {bubbles:true}));
    });
    passInputs.forEach(function(el) {
        el.value = '123';
        el.dispatchEvent(new Event('input', {bubbles:true}));
        el.dispatchEvent(new Event('change', {bubbles:true}));
    });

    // Livewire v3 - update component state
    if (window.Livewire) {
        var component = window.Livewire.getByName('filament.pages.auth.login') 
            || window.Livewire.first();
        if (component) {
            component.set('data.email', email);
            component.set('data.password', '123');
        }
    }
}
</script>
