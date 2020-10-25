<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddToLaunchNowEventsIndex extends Migration
{
    public $withinTransaction = false; // Laravel 5.5+

    public function up()
    {
        $query = <<<SQL
            CREATE INDEX CONCURRENTLY "to_launch_now_events_idx"
            ON "public"."events"("status","launch_time" DESC)
            WHERE status = 'scheduled';
        SQL;

        DB::statement($query);
    }

    public function down()
    {
        $query = <<<SQL
            DROP INDEX CONCURRENTLY "to_launch_now_events_idx";
        SQL;

        DB::statement($query);
    }
}
