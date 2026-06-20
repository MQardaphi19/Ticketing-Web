<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::withCount(['tickets', 'assignedTickets'])->get();

        return view('users.index', compact('users'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'nip' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(['admin', 'kepala-diskominfo', 'pegawai-dinas'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        $user->assignRole($validated['role']);

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil ditambahkan'
        ]);
    }
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'nip' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(['admin', 'kepala-diskominfo', 'pegawai-dinas'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($validated['role'] !== $user->role) {
            $user->syncRoles($validated['role']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil diperbarui'
        ]);
    }

    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password pengguna berhasil direset'
        ]);
    }

    public function destroy(int $id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil dihapus'
        ]);
    }
}
