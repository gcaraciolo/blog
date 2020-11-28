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

        $userRepos = $this->loadFixture('user-repos.json');

        Http::fake([
            'github.com/*' => Http::sequence()
                ->push($userRepos, 200),
        ]);
    }

    public function testRegisterToWatchRepositoriesOfIntereset()
    {
        $response = $this->postJson('/api/watch-repositories', [
            'profile' => 'https://github.com/someprofilename'
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Registro realizado com sucesso'
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
}
