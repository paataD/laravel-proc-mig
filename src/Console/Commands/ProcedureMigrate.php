<?php

namespace AtLab\ProcMig\Console\Commands;

use SplFileInfo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use File;
use Illuminate\Database\QueryException;

class ProcedureMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'procedure:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Миграция сохраненных процедур';

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
        $dbPath = database_path("procedures");
        if(!is_dir($dbPath)){
            $this->info("Директория {$dbPath} не существует!");
            exit(0);
        }

        $procedures = scandir($dbPath);
        foreach ($procedures as $procedure) {
            if (in_array($procedure, ['.', '..'])) {
                continue;
            }

            $file = new SplFileInfo($dbPath.'/'. $procedure);

            if ($file->getExtension() === 'sql') {
                $checksum = md5_file(database_path("procedures/$procedure"));
                try {
                $existing_procedure = DB::table('procedure_migrations')->where('filename', $file->getFilename())->exists();
                } catch(QueryException $e){
                    $this->line("Не удалось добавить процедуру {$file->getFilename()} !");
                    exit(1);
                }
                if (!$existing_procedure) {
                    $this->line("Найдена процедура: '{$file->getFilename()}'! Добавляем в базу данных");

                    try {
                        DB::unprepared(File::get(database_path("procedures/$procedure")));
                        DB::table('procedure_migrations')->insert([
                            'filename' => $file->getFilename(),
                            'checksum' => $checksum,
                            'created_at' => Carbon::now(),
                        ]);
                        $this->line("Процедура {$file->getFilename()} успешно добавлена!");
                    } catch (QueryException $e) {
                        $this->error($e->getMessage());
                        exit(1);
                    }
                }
            }
        }
    }
}