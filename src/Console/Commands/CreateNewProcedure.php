<?php

namespace AtLab\ProcMig\Console\Commands;

use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;
use File;

class CreateNewProcedure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:procedure {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создание новой процедуры';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        $dbPath = database_path("procedures");
        $filename = "{$now->format('Y_m_d_his')}_{$this->argument('name')}";
        $this->warn("Создается новая процедура: {$filename}..");
        File::ensureDirectoryExists($dbPath);
        try {
            File::put("{$dbPath}/{$filename}.sql", '');
        } catch (Exception $e) {
            $this->error($e);
        }

        $this->info("{$filename} процедура создана!");
    }
}
