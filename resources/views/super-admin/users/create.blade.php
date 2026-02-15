{{-- resources/views/super-admin/users/create.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Tambah User
        </h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto">
        <div class="bg-white p-6 shadow rounded">

            <form method="POST" action="{{ route('super-admin.users.store') }}">
                @csrf

                <div class="mb-4">
                    <x-input-label value="Nama" />
                    <x-text-input name="name" class="w-full" required />
                </div>

                <div class="mb-4">
                    <x-input-label value="Email" />
                    <x-text-input type="email" name="email" class="w-full" required />
                </div>

                <div class="mb-4">
                    <x-input-label value="Password" />
                    <x-text-input type="password" name="password" class="w-full" required />
                </div>

                <div class="mb-6">
                    <x-input-label value="Role" />
                    <select name="role" class="w-full border-gray-300 rounded">
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" @selected(old('role') === $role->name)>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <x-input-label value="Scope" />
                    <select name="scope" class="w-full border-gray-300 rounded" required>
                        <option value="kecamatan" @selected(old('scope') === 'kecamatan')>Kecamatan</option>
                        <option value="desa" @selected(old('scope', 'desa') === 'desa')>Desa</option>
                    </select>
                </div>

                <div class="mb-6">
                    <x-input-label value="Wilayah" />
                    <select name="area_id" class="w-full border-gray-300 rounded" required>
                        <option value="">Pilih wilayah</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" @selected((int) old('area_id') === (int) $area->id)>
                                {{ ucfirst($area->level) }} - {{ $area->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2">
                    {{-- <a href="{{ route('super-admin.users.index') }}"
                       class="px-4 py-2 border rounded">
                        Batal
                    </a> --}}
                    <button class="px-4 py-2 bg-indigo-600 rounded" type="submit">
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>
