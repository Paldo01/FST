<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden']) }}>
    @isset($header)
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            {{ $header }}
        </div>
    @endisset
    
    <div class="p-6">
        {{ $slot }}
    </div>
    
    @isset($footer)
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 text-sm">
            {{ $footer }}
        </div>
    @endisset
</div>
