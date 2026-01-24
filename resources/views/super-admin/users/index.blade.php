<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            User Management
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-bold">Daftar User</h3>

                        <a href="{{ route('super-admin.users.create') }}"
                        class="btn btn-primary">
                            + Tambah User
                        </a>
                    </div>
                    <div class="p-6 bg-white rounded shadow">

                        <table class="table table-bordered w-full">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th width="160">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        {{ $user->roles->pluck('name')->join(', ') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('super-admin.users.edit', $user) }}"
                                        class="btn btn-sm btn-warning">
                                            Edit
                                        </a>
                                        <form action="{{ route('super-admin.users.destroy', $user) }}" method="POST" class="inline-block"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                Hapus
                                            </button>
                                        </form>
                                    
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>      
</x-app-layout>
