<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models;

class WatchRepositoriesController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'profile' => 'required|max:255'
        ]);

        $username = Str::after($request->get('profile', ''), 'https://github.com/');

        Models\GithubProfile::firstOrCreate([
            'username' => $username
        ]);

        return response()->json([
            'message' => __('Registro realizado com sucesso')
        ]);
    }
}
