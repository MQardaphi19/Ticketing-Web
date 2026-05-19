<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::withCount(['tickets', 'assignedTickets'])->get();

        return view('users.index', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'nip' => 'required|string|max:255|unique:users',
            'phone' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'role' => 'required|in:pemohon,teknisi,admin,super-admin',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$id,
            'nip' => 'required|string|max:255|unique:users,nip,'.$id,
            'phone' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'role' => 'required|in:pemohon,teknisi,admin,super-admin',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus');
    }
}