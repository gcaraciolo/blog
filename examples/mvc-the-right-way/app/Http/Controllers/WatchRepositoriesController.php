<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class WatchRepositoriesController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'profile' => 'required|max:255'
        ]);

        $username = Str::after($request->get('profile', ''), 'https://github.com/');


        $userRepositories = Http::get("https://api.github.com/users/{$username}/repos")->json();

        $preferredLanguages = collect($userRepositories)->map(function ($repo) {
            return $repo['language'];
        })->countBy()->map(function ($value, $key) {
            return [
                'language' => $key,
                'total' => $value
            ];
        })->filter(function ($counter) {
            return !empty($counter['language']);
        })->sortBy('total');

        Models\GithubProfile::firstOrCreate([
            'username' => $username,
        ], [
            'preferred_language' => $preferredLanguages->last()['language']
        ]);

        return response()->json([
            'message' => __('Registro realizado com sucesso')
        ]);
    }
}
