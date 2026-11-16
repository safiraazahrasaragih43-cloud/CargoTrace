@php
    $currentLocale = app()->getLocale();
@endphp
<div class="inline-flex items-center p-0.5 rounded-full bg-cream-100/90 border border-white/80 shadow-2xs text-[11px] font-bold text-espresso-700">
    <a href="{{ route('lang.switch', 'id') }}" 
       class="flex items-center gap-1 px-2.5 py-1 rounded-full transition-all {{ $currentLocale === 'id' ? 'bg-sage-600 text-white shadow-xs' : 'text-espresso-600 hover:text-espresso-900 hover:bg-white/70' }}"
       title="Ganti ke Bahasa Indonesia">
        <span>🇮🇩</span>
        <span class="tracking-wider">ID</span>
    </a>
    <a href="{{ route('lang.switch', 'en') }}" 
       class="flex items-center gap-1 px-2.5 py-1 rounded-full transition-all {{ $currentLocale === 'en' ? 'bg-sage-600 text-white shadow-xs' : 'text-espresso-600 hover:text-espresso-900 hover:bg-white/70' }}"
       title="Switch to English">
        <span>🇬🇧</span>
        <span class="tracking-wider">EN</span>
    </a>
</div>
