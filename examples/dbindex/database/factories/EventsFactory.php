<?php

namespace Database\Factories;

use App\Models\Events;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventsFactory extends Factory
{
    protected $model = Events::class;

    public function definition()
    {
        return [
            'status' => 'notifying',
            'launch_time' => now()
        ];
    }

    // scheduled, notifying, sent, error
    public function scheduled()
    {
        return $this->state(function ($attrs) {
            return [
                'status' => 'scheduled',
                'launch_time' => now()
            ];
        });
    }

    public function sent()
    {
        return $this->state(function ($attrs) {
            return [
                'status' => 'sent',
                'launch_time' => now()->subMinutes()
            ];
        });
    }

    public function failed()
    {
        return $this->state(function ($attrs) {
            return [
                'status' => 'error',
                'launch_time' => now()->subMinutes()
            ];
        });
    }
}
