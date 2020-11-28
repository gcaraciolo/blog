<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models;
use Illuminate\Support\Facades\Http;
use Tests\LoadFixture;

class WatchRepositoriesControllerTest extends TestCase
{
    use RefreshDatabase;
    use LoadFixture;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testRegisterToWatchRepositoriesOfIntereset()
    {
        $userRepos = $this->loadFixture('user-repos.json');
        $repos = $this->loadFixture('repos.json');

        Http::fake([
            'github.com/*' => Http::sequence()
                ->push($userRepos, 200)
                ->push($repos, 200)
        ]);

        $response = $this->postJson('/api/watch-repositories', [
            'profile' => 'https://github.com/someprofilename'
        ]);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'message' => 'Registro realizado com sucesso',
                'preferred_language' => 'JavaScript',
                'suggested_repositories_found' => true,
                'suggested_repositories' => [
                    [
                        'full_name' => "freeCodeCamp/freeCodeCamp",
                        'description' => "freeCodeCamp.org's open source codebase and curriculum. Learn to code at home.",
                        'html_url' => "https://github.com/freeCodeCamp/freeCodeCamp",
                        'license_name' => 'BSD 3-Clause "New" or "Revised" License',
                        'watchers_count' => 317064,
                        'forks_count' => 25088,
                        'open_issues_count' => 270,
                    ],
                    [
                        'full_name' => "vuejs/vue",
                        'description' => "🖖 Vue.js is a progressive, incrementally-adoptable JavaScript framework for building UI on the web.",
                        'html_url' => "https://github.com/vuejs/vue",
                        'license_name' => 'MIT License',
                        'watchers_count' => 175883,
                        'forks_count' => 27335,
                        'open_issues_count' => 545,
                    ],
                    [
                        'full_name' => "facebook/react",
                        'description' => "A declarative, efficient, and flexible JavaScript library for building user interfaces.",
                        'html_url' => "https://github.com/facebook/react",
                        'license_name' => 'MIT License',
                        'watchers_count' => 159743,
                        'forks_count' => 31726,
                        'open_issues_count' => 657,
                    ],

                ]
            ]);

        $this->assertDatabaseCount('github_profiles', 1);
        $this->assertDatabaseHas('github_profiles', [
            'username' => 'someprofilename',
            'preferred_language' => 'JavaScript'
        ]);
    }

    public function testRequiredUsernameParameter()
    {
        $response = $this->postJson('/api/watch-repositories', [
            'prof' => 'https://github.com/someprofilename'
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'profile' => ['The profile field is required.']
                ]
            ]);
    }

    public function testNoPreferredLanguageFound()
    {
        $userRepos = $this->loadFixture('user-repos-no-lang.json');

        Http::fake([
            'github.com/*' => Http::sequence()
                ->push($userRepos, 200)
        ]);

        $response = $this->postJson('/api/watch-repositories', [
            'profile' => 'https://github.com/someprofilename'
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Registro realizado com sucesso',
                'preferred_language' => NULL,
                'suggested_repositories_found' => false
            ]);

        $this->assertDatabaseCount('github_profiles', 1);
        $this->assertDatabaseHas('github_profiles', [
            'username' => 'someprofilename',
            'preferred_language' => NULL
        ]);
    }
}
