@props([
    'height' => 'h-4',
    'width' => 'w-full'
])

<div class="relative overflow-hidden rounded-md bg-slate-800 {{ $height }} {{ $width }}">
    <div class="absolute inset-0 -translate-x-full animate-shimmer
                bg-gradient-to-r
                from-slate-800 via-slate-700 to-slate-800">
    </div>
</div>

<style>
@keyframes shimmer {
    100% {
        transform: translateX(100%);
    }
}
.animate-shimmer {
    animation: shimmer 1.6s infinite;
}
</style>
