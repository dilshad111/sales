<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'Admin') {
                return redirect()->route('dashboard')->with('error', 'Only administrators can manage users.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $menus = config('menu_permissions.menus');
        $menuGroups = config('menu_permissions.form_groups');

        return view('users.create', compact('menus', 'menuGroups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:Admin,User,Agent,Principal',
            'menu_permissions' => 'nullable|array',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['menu_permissions'] = $data['menu_permissions'] ?? [];

        User::create($data);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $menus = config('menu_permissions.menus');
        $menuGroups = config('menu_permissions.form_groups');

        return view('users.show', compact('user', 'menus', 'menuGroups'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $menus = config('menu_permissions.menus');
        $menuGroups = config('menu_permissions.form_groups');

        return view('users.edit', compact('user', 'menus', 'menuGroups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|in:Admin,User,Agent,Principal',
            'menu_permissions' => 'nullable|array',
        ]);

        if ($data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['menu_permissions'] = $data['menu_permissions'] ?? [];

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting the current authenticated user
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function rights(Request $request)
    {
        $users = User::orderBy('name')->get();
        $selectedUser = null;
        if ($request->user_id) {
            $selectedUser = User::find($request->user_id);
        }

        $menus = config('menu_permissions.menus');
        $menuGroups = config('menu_permissions.form_groups');

        return view('users.rights', compact('users', 'selectedUser', 'menus', 'menuGroups'));
    }

    public function updateRights(Request $request, User $user)
    {
        $user->update([
            'menu_permissions' => $request->menu_permissions ?? []
        ]);

        return redirect()->route('users.rights', ['user_id' => $user->id])->with('success', 'User rights updated successfully.');
    }
}
