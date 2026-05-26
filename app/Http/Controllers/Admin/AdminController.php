<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $users = User::withCount('expenses')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.dashboard', [
            'users' => $users,
            'totalUsers' => User::count(),
        ]);
    }

    public function showUser(Request $request, User $user)
    {
        $expenses = $user->expenses()
            ->with('category')
            ->latest('spent_at')
            ->paginate(20);

        $total = $user->expenses()->sum('amount');

        return view('admin.user_expenses', [
            'targetUser' => $user,
            'expenses' => $expenses,
            'total' => $total,
        ]);
    }
}
