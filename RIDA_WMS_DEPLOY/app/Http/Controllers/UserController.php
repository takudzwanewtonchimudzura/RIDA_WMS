<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Check if current user is admin.
     */
    private function requireAdmin()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Access denied. Admin only.');
        }
    }

    /**
     * Display all users.
     */
    public function index()
    {
        $this->requireAdmin();

        $users = User::orderBy('created_at', 'desc')->get();

        $stats = [
            'total'    => User::count(),
            'admins'   => User::where('role', 'admin')->count(),
            'active'   => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];

        return view('users.index', compact('users', 'stats'));
    }

    /**
     * Show form to create a new user.
     */
    public function create()
    {
        $this->requireAdmin();
        return view('users.create');
    }

    /**
     * Store a new user.
     */
    public function store(Request $request)
    {
        $this->requireAdmin();

        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => 'required|string|min:8|confirmed',
            'role'       => 'required|in:admin,operator,viewer',
            'phone'      => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'is_active'  => 'nullable|boolean',
        ]);

        User::create([
            'name'             => $request->name,
            'email'            => $request->email,
            'password'         => Hash::make($request->password),
            'role'             => $request->role,
            'phone'            => $request->phone,
            'department'       => $request->department,
            'is_active'        => $request->boolean('is_active'),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show user details.
     */
    public function show(User $user)
    {
        $this->requireAdmin();
        return view('users.show', compact('user'));
    }

    /**
     * Show form to edit a user.
     */
    public function edit(User $user)
    {
        $this->requireAdmin();
        return view('users.edit', compact('user'));
    }

    /**
     * Update a user.
     */
    public function update(Request $request, User $user)
    {
        $this->requireAdmin();

        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role'       => 'required|in:admin,operator,viewer',
            'phone'      => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'is_active'  => 'nullable|boolean',
        ]);

        $data = $request->only(['name', 'email', 'role', 'phone', 'department']);
        $data['is_active'] = $request->boolean('is_active');

        if ($user->isAdmin() && $user->is_active && ($data['role'] !== 'admin' || !$data['is_active'])
            && User::where('role', 'admin')->where('is_active', true)->count() <= 1) {
            return redirect()->back()->withInput()->with('error', 'You cannot remove the last active administrator.');
        }

        // Update password only if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed',
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user)
    {
        $this->requireAdmin();

        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        if ($user->isAdmin() && $user->is_active
            && User::where('role', 'admin')->where('is_active', true)->count() <= 1) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete the last active administrator.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle user active status.
     */
    public function toggleActive(User $user)
    {
        $this->requireAdmin();

        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->route('users.index')
            ->with('success', "User {$status} successfully.");
    }

    /**
     * Roles & Permissions overview page.
     */
    public function roles()
    {
        $this->requireAdmin();

        $roles = [
            'admin' => [
                'label'       => 'Administrator',
                'color'       => 'danger',
                'description' => 'Full access to all system features including user management.',
                'permissions' => [
                    'Manage all users',
                    'Create, edit, delete boreholes',
                    'Create, edit, delete dams',
                    'Approve/unapprove records',
                    'View all reports',
                    'Export data',
                    'System settings',
                ],
                'count' => User::where('role', 'admin')->count(),
            ],
            'operator' => [
                'label'       => 'Field Officer',
                'color'       => 'primary',
                'description' => 'Can create and edit water points and submit reports.',
                'permissions' => [
                    'Create boreholes',
                    'Edit own boreholes',
                    'Create dams',
                    'Edit own dams',
                    'View reports',
                    'Export own data',
                ],
                'count' => User::where('role', 'operator')->count(),
            ],
            'viewer' => [
                'label'       => 'Viewer',
                'color'       => 'success',
                'description' => 'Read-only access to view records and reports.',
                'permissions' => [
                    'View boreholes',
                    'View dams',
                    'View reports',
                    'Export data',
                ],
                'count' => User::where('role', 'viewer')->count(),
            ],
        ];

        return view('users.roles', compact('roles'));
    }

    /**
     * User activity log page.
     */
    public function activity()
    {
        $this->requireAdmin();

        $users = User::orderBy('last_login_at', 'desc')->get();

        $stats = [
            'total'          => User::count(),
            'active_today'   => User::whereDate('last_login_at', today())->count(),
            'active_week'    => User::where('last_login_at', '>=', now()->subWeek())->count(),
            'never_logged_in' => User::whereNull('last_login_at')->count(),
        ];

        return view('users.activity', compact('users', 'stats'));
    }
}