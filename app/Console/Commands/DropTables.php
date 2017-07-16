<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DropTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'table:drop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop any table from current database.';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $confirm = readline('Your database will be cleaned. Are you sure to continue? (Y/n)');
        if (strtolower($confirm) === 'y') {
            $tables = DB::select("select * from pg_tables where tableowner = current_user");
            foreach ($tables as $table) {
                DB::delete("drop table if exists {$table->tablename} cascade");
            }
            $this->info('Successful!');
        } else {
            $this->info('Cancelled!');
        }
    }
}
