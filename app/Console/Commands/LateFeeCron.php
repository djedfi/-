<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use \App\Models\PaymentLoan;
use \App\Models\Loan;
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
        $today      =   Carbon::now()->format('Y-m-d');

        $loan  =   DB::table('loans as l')
        ->join('cars as cr','l.car_id', '=', 'cr.id')
        ->join('customers as cust', 'l.customer_id', '=', 'cust.id')
        ->join('trims as tr', 'tr.id', '=', 'cr.trim_id')
        ->join('modelos as md', 'md.id', '=', 'tr.modelo_id')
        ->join('makes as mk', 'mk.id', '=', 'make_id')
        ->join('schedule_payments as sc', 'sc.loan_id', '=', 'l.id')
        ->select('l.id as loan_id','sc.id','sc.date_programable','sc.date_end','l.late_fee','l.balance')
        ->where('sc.date_end', '=', $today)->where('l.balance','>=',0)
        ->get();

        $data           =   json_decode($loan, true);
        $array_id_loan  =   array();
        if($loan->count())
        {
            foreach($data as $key => $qs)
            {
                if(!$this->checkpayment($qs['loan_id'],$qs['date_programable'],$qs['date_end']))
                {
                    //Log::info("Insertar late fee");

                    $late_fee       =   PaymentLoan::create( [
                        'loan_id'               => $qs['loan_id'],
                        'user_id'               => 2,
                        'description'           => 'Automatic Late Fee',
                        'concepto'              => 2,
                        'monto'                 => $qs['late_fee'],
                        'date_doit'             => $today,
                        'balance'               => floatval($qs['balance']) + floatval($qs['late_fee'])
                    ]);

                    if($late_fee->id > 0)
                    {
                        Loan::where('id',$qs['loan_id'])->update(array('balance'=>(floatval($qs['balance']) + floatval($qs['late_fee']))));
                        Log::info("Se inserto un Automatic Late Fee para el prestamo con codigo: ".$qs['loan_id'].'; por que no pago desde el periodo: '.$qs['date_programable'].' hasta '.$qs['date_end']);
                    }
                    else
                    {
                        Log::error("ERROR: NO se ingreso un Automatic Late Fee para el prestamo con codigo: ".$qs['loan_id'].'; por que no pago desde el periodo: '.$qs['date_programable'].' hasta '.$qs['date_end']);
                    }
                }
            }
        }
        else
        {
            Log::info("NO EXISTEN PAGOS VENCIDOS PARA AGREGAR LATE FEE. ESTO EN LA FECHA DEL LOG.");
        }

    }
}
