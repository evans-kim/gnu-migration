<?php

namespace EvansKim\GnuMigration;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeGnuBoard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:g4board {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a scaffold model for Gnu Board4';

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
     * @return mixed
     */
    public function handle()
    {
        $boardName = $this->argument('name' );
        $Dummy = File::get(__DIR__."/GnuModel/Post.stub");
        $class = Str::studly(Str::singular($boardName));
        $Dummy = preg_replace("/DummyClass/", $class, $Dummy);
        $name = strtolower(Str::singular($boardName));
        $Dummy = preg_replace("/DummyName/", $name, $Dummy);

        try{
            File::put(app_path("/Boards/" . $class . ".php"), $Dummy);
        }catch (\ErrorException $exception){
            File::makeDirectory(app_path("/Boards"));
            $this->info(app_path("/Boards") . " 디렉토리가 생성되었습니다.");
            File::put(app_path("/Boards/" . $class . ".php"), $Dummy);
        }

        $this->info($class . " 모델이 생성되었습니다.");
    }
}
