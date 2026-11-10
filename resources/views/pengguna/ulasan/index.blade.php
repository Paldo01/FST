@extends('layouts.app')

@section('title', 'Feedback Sistem')

@section('content')
<div class="space-y-6 pb-10">
    <header>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Feedback Sistem</h1>
        <p class="mt-1 text-sm text-gray-500">Bagikan penilaian dan saran Anda untuk meningkatkan sistem peminjaman FST.</p>
    </header>

    <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <h2 class="text-lg font-bold text-gray-900">Nilai Pengalaman Anda</h2>
        <p class="mt-1 text-sm text-gray-500">Rating ini ditujukan untuk sistem, bukan untuk kegiatan atau fasilitas tertentu.</p>

        <form method="POST" action="{{ route('ulasan.simpan') }}" class="mt-5 space-y-4" x-data="{ rating: {{ (int) old('rating', 0) }} }">
            @csrf

            <fieldset>
                <legend class="mb-2 text-sm font-medium text-gray-800">Rating sistem</legend>
                <div class="flex items-center gap-1" role="radiogroup" aria-label="Rating sistem">
                    @for($star = 1; $star <= 5; $star++)
                        <button
                            type="button"
                            role="radio"
                            :aria-checked="rating === {{ $star }}"
                            aria-label="{{ $star }} dari 5 bintang"
                            @click="rating = {{ $star }}"
                            class="rounded-sm text-gray-500 hover:text-amber-400 focus-visible:text-amber-500"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                class="h-7 w-7"
                                :class="rating >= {{ $star }} ? 'fill-amber-400 text-amber-400' : 'fill-transparent text-gray-700'"
                                fill="currentColor"
                                stroke="currentColor"
                                stroke-width="1.7"
                                aria-hidden="true"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="m12 3 2.75 5.57 6.15.9-4.45 4.33 1.05 6.12L12 17.03l-5.5 2.89 1.05-6.12L3.1 9.47l6.15-.9L12 3Z"></path>
                            </svg>
                        </button>
                    @endfor
                </div>
                <input type="hidden" name="rating" :value="rating">
                @error('rating')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </fieldset>

            <div>
                <label for="komentar" class="mb-1 block text-sm font-medium text-gray-800">Komentar atau saran</label>
                <textarea
                    id="komentar"
                    name="komentar"
                    rows="4"
                    maxlength="1000"
                    class="block w-full resize-y rounded-lg border border-gray-300 px-3 py-3 text-sm text-gray-900 placeholder:text-gray-500 focus:border-marun-500 focus:ring-2 focus:ring-marun-100"
                    placeholder="Ceritakan pengalaman atau saran Anda untuk sistem ini"
                >{{ old('komentar') }}</textarea>
                @error('komentar')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    :disabled="rating === 0"
                    class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <i data-lucide="send" class="h-4 w-4"></i>
                    Kirim Feedback
                </button>
            </div>
        </form>
    </section>

    <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="mb-4">
            <h2 class="text-lg font-bold text-gray-900">Riwayat Feedback Sistem</h2>
            <p class="mt-1 text-sm text-gray-500">Daftar penilaian sistem yang pernah Anda kirimkan.</p>
        </div>

        @if($riwayatUlasan->isEmpty())
            <p class="py-6 text-center text-sm text-gray-500">Anda belum pernah mengirim feedback untuk sistem.</p>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($riwayatUlasan as $ulasan)
                    <article class="flex flex-col gap-2 py-4 first:pt-0 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Feedback Sistem</h3>
                            <p class="mt-1 text-xs text-gray-500">{{ $ulasan->created_at->translatedFormat('d F Y, H.i') }}</p>
                            @if($ulasan->komentar)
                                <p class="mt-2 text-sm text-gray-600">{{ $ulasan->komentar }}</p>
                            @endif
                        </div>
                        <div class="flex shrink-0 items-center gap-1" aria-label="Rating {{ $ulasan->rating }} dari 5">
                            @for($star = 1; $star <= 5; $star++)
                                <svg viewBox="0 0 24 24" class="h-4 w-4 {{ $star <= $ulasan->rating ? 'fill-amber-400 text-amber-400' : 'fill-transparent text-gray-300' }}" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m12 3 2.75 5.57 6.15.9-4.45 4.33 1.05 6.12L12 17.03l-5.5 2.89 1.05-6.12L3.1 9.47l6.15-.9L12 3Z"></path>
                                </svg>
                            @endfor
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-2">{{ $riwayatUlasan->links() }}</div>
        @endif
    </section>
</div>
@endsection
