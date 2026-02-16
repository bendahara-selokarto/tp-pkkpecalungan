<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Inventaris Kecamatan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 space-y-6">
                    <a href="{{ url('/kecamatan/inventaris') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Kembali</a>

                    @if ($errors->any())
                        <div class="rounded-md bg-red-50 dark:bg-red-900/20 p-4">
                            <ul class="list-disc pl-5 text-sm text-red-700 dark:text-red-300">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ url('/kecamatan/inventaris') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium mb-1">Nama Barang</label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Deskripsi</label>
                            <textarea name="description" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('description') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Jumlah</label>
                            <input type="number" min="1" name="quantity" value="{{ old('quantity', 1) }}" required class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Satuan</label>
                            <input type="text" name="unit" value="{{ old('unit', 'unit') }}" required class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Kondisi</label>
                            <select name="condition" required class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                <option value="baik" {{ old('condition') === 'baik' ? 'selected' : '' }}>baik</option>
                                <option value="rusak_ringan" {{ old('condition') === 'rusak_ringan' ? 'selected' : '' }}>rusak ringan</option>
                                <option value="rusak_berat" {{ old('condition') === 'rusak_berat' ? 'selected' : '' }}>rusak berat</option>
                            </select>
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white">
                            Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
