<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $like = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->where(function ($q) use ($search, $like) {
                $q->where('name', $like, '%' . $search . '%')
                  ->orWhere('email', $like, '%' . $search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        $sort = $request->input('sort', 'terbaru');
        match ($sort) {
            'nama_asc'  => $query->orderBy('name', 'asc'),
            'nama_desc' => $query->orderBy('name', 'desc'),
            'terlama'   => $query->orderBy('id', 'asc'),
            default     => $query->orderBy('id', 'desc'),
        };

        $users = $query->paginate(20)->withQueryString();
        $roles = Role::cases();

        return view('user.index', compact('users', 'roles', 'sort'));
    }

    public function create()
    {
        $roles = Role::cases();
        return view('user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|in:' . Role::validationRule(),
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()
            ->route('inventory.user.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        $roles = Role::cases();
        return view('user.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:' . Role::validationRule(),
        ]);

        // Optional password change
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:6|confirmed',
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return redirect()
            ->route('inventory.user.index')
            ->with('success', 'User berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun Anda sendiri');
        }

        $user->delete();

        return redirect()
            ->route('inventory.user.index')
            ->with('success', 'User berhasil dihapus');
    }
}
