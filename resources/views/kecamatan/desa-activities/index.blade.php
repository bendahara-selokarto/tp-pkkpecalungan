<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Activities Desa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 space-y-6">
                    <a href="{{ url('/kecamatan/activities') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Lihat Kegiatan Kecamatan</a>

                    @forelse ($activities as $activity)
                        <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="font-semibold">{{ $activity->title }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">
                                Desa: {{ $activity->area?->name ?? '-' }} |
                                Tanggal: {{ $activity->activity_date }} |
                                Status: {{ $activity->status }} |
                                Dibuat oleh: {{ $activity->creator?->name ?? '-' }}
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('kecamatan.desa-activities.show', $activity->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">Lihat Detail</a>
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-600 dark:text-gray-300">Belum ada kegiatan desa pada kecamatan ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
