<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models;

class WatchRepositoriesControllerTest extends TestCase
{
    use RefreshDatabase;

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


        $this->assertEquals(
            1,
            Models\GithubProfile::where('username', 'someprofilename')->count()
        );
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
