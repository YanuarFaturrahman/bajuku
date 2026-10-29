@props(['label', 'value', 'hint' => null])
<div class="card !p-5">
    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">{{ $label }}</p>
    <p class="mt-2 font-display text-2xl text-gray-900">{{ $value }}</p>
    @if ($hint)<p class="mt-1 text-xs text-brand-600">{{ $hint }}</p>@endif
</div>