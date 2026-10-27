@props(['status' => ''])

@php
$classes = match($status) {
    'menunggu' => 'badge-menunggu',
    'disetujui' => 'badge-disetujui',
    'ditolak' => 'badge-ditolak',
    'selesai' => 'badge-selesai',
    'tersedia' => 'bg-green-100 text-green-800 border border-green-200',
    'dipinjam' => 'bg-blue-100 text-blue-800 border border-blue-200',
    'perbaikan' => 'bg-red-100 text-red-800 border border-red-200',
    'baik' => 'bg-green-100 text-green-800 border border-green-200',
    'rusak' => 'bg-orange-100 text-orange-800 border border-orange-200',
    'hilang' => 'bg-gray-100 text-gray-800 border border-gray-200',
    default => 'bg-gray-100 text-gray-800 border border-gray-200',
};

$icon = match($status) {
    'menunggu' => 'clock',
    'disetujui' => 'check-circle',
    'ditolak' => 'x-circle',
    'selesai' => 'check-square',
    'tersedia' => 'check',
    'dipinjam' => 'arrow-right-left',
    'perbaikan' => 'tool',
    'baik' => 'thumbs-up',
    'rusak' => 'thumbs-down',
    'hilang' => 'help-circle',
    default => 'info',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium capitalize $classes"]) }}>
    <i data-lucide="{{ $icon }}" class="w-3.5 h-3.5"></i>
    {{ str_replace('_', ' ', $status) }}
</span>
