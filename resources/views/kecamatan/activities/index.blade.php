<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Activities Kecamatan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 space-y-6">
                    <div class="flex justify-end">
                        <a href="{{ url('/kecamatan/activities/create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white">
                            Tambah Kegiatan
                        </a>
                    </div>

                    @forelse ($activities as $activity)
                        <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="font-semibold">{{ $activity->title }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">{{ $activity->activity_date }} | {{ $activity->status }}</div>
                            <div class="mt-3 flex gap-3 text-sm">
                                <a href="{{ url('/kecamatan/activities/' . $activity->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Lihat</a>
                                <a href="{{ url('/kecamatan/activities/' . $activity->id . '/edit') }}" class="text-yellow-600 dark:text-yellow-400 hover:underline">Edit</a>
                                <form action="{{ url('/kecamatan/activities/' . $activity->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-600 dark:text-gray-300">Belum ada kegiatan kecamatan.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
