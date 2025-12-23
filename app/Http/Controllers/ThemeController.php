<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThemeController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'theme' => 'required|string|in:sneat,soft-ui'
        ]);

        $user = Auth::user();
        if ($user) {
            $user->theme = $request->theme;
            $user->save();
            
            return back()->with('success', 'Workspace theme switched to ' . ucfirst($request->theme) . '!');
        }

        return back()->with('error', 'Unable to update theme.');
    }
}
