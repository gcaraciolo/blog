<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models;

class EventsSeeder extends Seeder
{
    public function run()
    {
        Models\Events::factory()
            ->times(10)
            ->scheduled()
            ->create();

        Models\Events::factory()
            ->times(3)
            ->sent()
            ->create();

        Models\Events::factory()
            ->times(2)
            ->failed()
            ->create();
    }
}
