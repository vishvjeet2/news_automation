@extends('Admin.layouts.app')

@section('content')
<div class="max-w-5xl mx-auto mt-10">

    <!-- Page Title -->
    <h1 class="text-2xl font-semibold mb-6">Manage Users</h1>

    <!-- Add User Card -->
    <div class="bg-white shadow-md rounded-xl p-6 mb-8">
        
        <h2 class="text-lg font-semibold mb-4">Add User</h2>

        @error('email')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror

        <form action="{{ route('admin.addUser') }}" method="POST">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <label class="block text-gray-700 mb-1">Name</label>
                <input type="text" name="name"
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black"
                    placeholder="Enter name">
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label class="block text-gray-700 mb-1">Email</label>
                <input type="email" name="email"
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black"
                    placeholder="Enter email">
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="block text-gray-700 mb-1">Password</label>
                <input type="password" name="password"
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black"
                    placeholder="Enter password">
            </div>

            <!-- Role -->
            <div class="mb-4">
                <label class="block text-gray-700 mb-1">Role</label>
                <select name="role" class="w-full border rounded-lg px-3 py-2">
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="bg-black text-white px-5 py-2 rounded-lg hover:bg-gray-800 transition">
                Save User
            </button>

        </form>
    </div>

    {{-- <!-- Users Table -->
    <div class="bg-white shadow-md rounded-xl p-6">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="py-2">Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="border-b">
                    <td class="py-2">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ ucfirst($user->role) }}</td>
                    <td class="text-center">
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div> --}}

</div>
@endsection