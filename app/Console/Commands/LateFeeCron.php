<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use \App\Http\Traits\GFunctionsTrait;

class LateFeeCron extends Command
{
    use GFunctionsTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'latefee:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Este cron servira para obtener todos los prestamos que luego del periodo de gracia no pagaron la cuota de su prestamo';

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
        Log::info("Cron is working fine!");
    }
}
