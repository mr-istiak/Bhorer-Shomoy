<?php

namespace App\Console\Commands;

use HtmlBladeRuntime\Runtime;
use Illuminate\Console\Command;

/**
 *  Builds The Parent bhorersomoy.com
*/
class HtmlBladeBuild extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'htmlblade:build';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This build the parent bhorersomoy.com public html';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Runtime::build();
    }
}
