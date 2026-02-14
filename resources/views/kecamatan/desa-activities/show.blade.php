<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Kegiatan Desa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 space-y-4">
                    <a href="{{ route('kecamatan.desa-activities.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Kembali ke Daftar</a>
                    <h3 class="text-lg font-semibold">{{ $activity->title }}</h3>
                    <p class="text-sm">Desa: {{ $activity->area?->name ?? '-' }}</p>
                    <p class="text-sm">{{ $activity->description ?: '-' }}</p>
                    <p class="text-sm">Tanggal: {{ $activity->activity_date }}</p>
                    <p class="text-sm">Status: {{ $activity->status }}</p>
                    <p class="text-sm">Dibuat oleh: {{ $activity->creator?->name ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
