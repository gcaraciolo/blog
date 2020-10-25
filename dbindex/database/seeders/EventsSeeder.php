<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models;

class EventsSeeder extends Seeder
{
    public function run()
    {
        Models\Events::factory()
            ->times(2000)
            ->scheduled()
            ->create();

        Models\Events::factory()
            ->times(10000)
            ->sent()
            ->create();

        Models\Events::factory()
            ->times(1000)
            ->failed()
            ->create();
    }
}
