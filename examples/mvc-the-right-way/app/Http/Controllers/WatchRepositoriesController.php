<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models;

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

        $preferredLanguage = $preferredLanguages->last()['language'];

        Models\GithubProfile::updateOrCreate([
            'username' => $username,
        ], [
            'preferred_language' => $preferredLanguage
        ]);

        $suggestedRepos = collect([]);
        if (!empty($preferredLanguage)) {
            $rawSuggestedRepos = Http::get("https://api.github.com/search/repositories?q={$preferredLanguage}&sort=stars&order=desc")->json();

            $suggestedRepos = collect($rawSuggestedRepos['items'])->slice(0, 3)->map(function ($repo) {
                return [
                    'full_name' => $repo['full_name'],
                    'description' => $repo['description'],
                    'html_url' => $repo['html_url'],
                    'license_name' => $repo['license']['name'],
                    'watchers_count' => $repo['watchers_count'],
                    'forks_count' => $repo['forks_count'],
                    'open_issues_count' => $repo['open_issues_count'],
                ];
            });
        }

        return response()->json([
            'message' => __('Registro realizado com sucesso'),
            'preferred_language' => $preferredLanguage,
            'suggested_repositories_found' => $suggestedRepos->count() > 0,
            'suggested_repositories' => $suggestedRepos
        ]);
    }
}
