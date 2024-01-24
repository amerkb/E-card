<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class delete_all_data_without_primary_links extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete_all_data_without_primary_links';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete_all_data_except_primary_links';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::table('profile_primary_links')->delete();
        DB::table('links')->delete();
        DB::table('sections')->delete();
        DB::table('personal_access_tokens')->delete();
        DB::table('migrations')->delete();
        DB::table('profiles')->delete();
        DB::table('users')->where('is_admin', 0)->delete();
        DB::table('themes')->delete();

    }
}
