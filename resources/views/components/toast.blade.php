{{-- Notifikasi pojok kanan atas. Menangkap event 'toast' dari Livewire dan flash session dari redirect. --}}
<div x-data="{ show: false, msg: '', type: 'success', timer: null,
               open(m, t = 'success') { this.msg = m; this.type = t; this.show = true; clearTimeout(this.timer); this.timer = setTimeout(() => this.show = false, 3500); } }"
     x-init="@if (session('success')) open(@js(session('success'))); @endif @if (session('error')) open(@js(session('error')), 'error'); @endif"
     @toast.window="open($event.detail.message, $event.detail.type ?? 'success')"
     x-show="show" x-transition x-cloak
     class="fixed right-5 top-5 z-[100] max-w-sm">
    <div class="flex items-center gap-3 rounded-2xl px-5 py-3 text-sm text-white shadow-lg"
         :class="type === 'error' ? 'bg-red-500' : 'bg-gray-900'">
        <span class="h-2 w-2 rounded-full" :class="type === 'error' ? 'bg-red-200' : 'bg-brand-300'"></span>
        <span x-text="msg"></span>
    </div>
</div>
