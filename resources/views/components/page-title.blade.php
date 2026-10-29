@props(['title', 'subtitle' => null])
<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="font-display text-2xl text-gray-900">{{ $title }}</h1>
        @if ($subtitle)<p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>@endif
    </div>
    <div class="flex items-center gap-2">{{ $slot }}</div>
</div>
