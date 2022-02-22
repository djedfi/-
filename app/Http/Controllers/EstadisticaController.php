<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use \App\Models\Car;
use \App\Models\Customer;
use \App\Models\Loan;
use \App\Models\PaymentLoan;
use \App\Models\SchedulePayment;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use \App\Http\Traits\GFunctionsTrait;


class EstadisticaController extends Controller
{
    use GFunctionsTrait;
    public function getCarEstadistica()
    {
        try
        {
            $cars_activos   = Car::where('estado',1)->get()->count();
            $cars_sold      = Car::where('estado',2)->get()->count();
            return \response()->json(['res'=>true,'total_activos'=>$cars_activos,'total_sold'=>$cars_sold],200);
        }
        catch(\Exception $e)
        {
            return \response()->json(['res'=>false],200);
        }
    }

    public function getCustomerEstadistica()
    {
        try
        {
            $customers_female   = Customer::where('gender',1)->get()->count();
            $customers_male     = Customer::where('gender',2)->get()->count();
            $customers_other    = Customer::where('gender',3)->get()->count();
            return \response()->json(['res'=>true,'customer_female'=>$customers_female,'customer_male'=>$customers_male,'customer_other'=>$customers_other],200);
        }
        catch(\Exception $e)
        {
            return \response()->json(['res'=>false],200);
        }
    }

    public function getLoanEstadistica()
    {
        try
        {
            $today              =   CarbonImmutable::now();
            $first_day_week     =   $today->startOfWeek()->format('Y-m-d H:i:s');
            $last_day_week      =   $today->endOfWeek()->format('Y-m-d H:i:s');

            $first_day_month     =   $today->startOfMonth()->format('Y-m-d H:i:s');
            $last_day_month      =   $today->endOfMonth()->format('Y-m-d H:i:s');

            $first_day_year     =   $today->startOfYear()->format('Y-m-d H:i:s');
            $last_day_year      =   $today->endOfYear()->format('Y-m-d H:i:s');

            $total_loans        =   Loan::all()->count();
            $total_loans_week   =   Loan::whereBetween('loan_date',[$first_day_week,$last_day_week])->get()->count();
            $total_loans_month  =   Loan::whereBetween('loan_date',[$first_day_month,$last_day_month])->get()->count();
            $total_loans_year  =   Loan::whereBetween('loan_date',[$first_day_year,$last_day_year])->get()->count();


            return \response()->json(['res'=>true,'total_loan'=>$total_loans,'total_loan_week'=>$total_loans_week,'total_loan_month'=>$total_loans_month,'total_loan_year'=>$total_loans_year],200);
        }
        catch(\Exception $e)
        {
            return \response()->json(['res'=>false],200);
        }
    }

    public function getPaymentEstadistica()
    {
        try
        {
            $today              =   CarbonImmutable::now();
            $first_day_week     =   $today->startOfWeek()->format('Y-m-d H:i:s');
            $last_day_week      =   $today->endOfWeek()->format('Y-m-d H:i:s');

            $first_day_month     =   $today->startOfMonth()->format('Y-m-d H:i:s');
            $last_day_month      =   $today->endOfMonth()->format('Y-m-d H:i:s');

            $first_day_year     =   $today->startOfYear()->format('Y-m-d H:i:s');
            $last_day_year      =   $today->endOfYear()->format('Y-m-d H:i:s');

            $total_monto_payments_year      =   PaymentLoan::where('concepto',3)->where('estado',1)->whereBetween('date_doit',[$first_day_year,$last_day_year])->sum('monto');
            $total_payment_today             =   SchedulePayment::whereBetween('date_programable',[$today->format('Y-m-d').' 00:00:00',$today->format('Y-m-d').' 23:59:59'])->get()->count();
            $total_payment_week            =   SchedulePayment::whereBetween('date_programable',[$first_day_week,$last_day_week])->get()->count();
            $total_payment_month             =   SchedulePayment::whereBetween('date_programable',[$first_day_month,$last_day_month])->get()->count();

            return \response()->json(['res'=>true,'total_monto'=>$total_monto_payments_year,'total_payment_today'=>$total_payment_today,'total_payment_week'=>$total_payment_week,'total_payment_month'=>$total_payment_month],200);
        }
        catch(\Exception $e)
        {
            return \response()->json(['res'=>false],200);
        }
    }

    public function getPaymentToday()
    {
        $array_color    = array('bg-primary','bg-secondary','bg-success','bg-warning','bg-danger','bg-info','bg-teal','bg-azure','bg-orange','bg-cyan','bg-yellow','bg-gray','bg-purple','bg-lime','bg-gray-dark','bg-green','bg-pink','bg-indigo','bg-red');
        $total_colores  = count($array_color) -1;
        $loan  =   DB::table('loans as l')
        ->join('cars as cr','l.car_id', '=', 'cr.id')
        ->join('customers as cust', 'l.customer_id', '=', 'cust.id')
        ->join('trims as tr', 'tr.id', '=', 'cr.trim_id')
        ->join('modelos as md', 'md.id', '=', 'tr.modelo_id')
        ->join('makes as mk', 'mk.id', '=', 'make_id')
        ->join('schedule_payments as sc', 'sc.loan_id', '=', 'l.id')
        ->select('l.id as loan_id',DB::raw("CONCAT(cust.first_name,' ',cust.last_name) as full_name"),DB::raw("CONCAT(mk.name,' ',md.name,' ',tr.name) as modelo_car"),'cr.vin','l.minimun_payment','l.pago_automatico','sc.date_programable','sc.date_end')
        ->where('sc.date_programable', '=', Carbon::now()->format('Y-m-d'))
        ->get();

        if($loan->count())
        {
            $data= json_decode($loan, true);
            $array_id_loan =array();
            foreach($data as $key => $qs)
            {
                if(!$this->checkpayment($qs['loan_id'],$qs['date_programable'],$qs['date_end']))
                {
                    $qs['color']            =   $array_color[rand(0,$total_colores)];
                    $qs['minimun_payment']  =   number_format($qs['minimun_payment'],2,".",",");
                    if($qs['pago_automatico'] == 1)
                    {
                        $qs['pago_automatico'] = '<a href="#" class="text-warning fs-5" data-bs-toggle="tooltip" data-bs-placement="top" title="Automatic payment" aria-label="Automatic payment"><span class="fe fe-alert-octagon"></span></a>';
                    }
                    else
                    {
                        $qs['pago_automatico'] = '';
                    }
                    array_push($array_id_loan,$qs);
                }
            }

            if(count($array_id_loan) > 0)
            {
                return \response()->json(['res'=>true,'data'=>$array_id_loan],200);
            }
            else
            {
                return \response()->json(['res'=>true,'data'=>[]],200);
            }
        }
        else
        {
            return \response()->json(['res'=>true,'data'=>[]],200);
        }
    }


    public function getPaymentDue()
    {
        $array_color    = array('bg-primary','bg-secondary','bg-success','bg-warning','bg-danger','bg-info','bg-teal','bg-azure','bg-orange','bg-cyan','bg-yellow','bg-gray','bg-purple','bg-lime','bg-gray-dark','bg-green','bg-pink','bg-indigo','bg-red');
        $total_colores  = count($array_color) -1;

        $loan  =   DB::table('loans as l')
        ->join('cars as cr','l.car_id', '=', 'cr.id')
        ->join('customers as cust', 'l.customer_id', '=', 'cust.id')
        ->join('trims as tr', 'tr.id', '=', 'cr.trim_id')
        ->join('modelos as md', 'md.id', '=', 'tr.modelo_id')
        ->join('makes as mk', 'mk.id', '=', 'make_id')
        ->join('schedule_payments as sc', 'sc.loan_id', '=', 'l.id')
        ->select('l.id as loan_id',DB::raw("CONCAT(cust.first_name,' ',cust.last_name) as full_name"),DB::raw("CONCAT(mk.name,' ',md.name,' ',tr.name) as modelo_car"),'cr.vin','l.minimun_payment','l.pago_automatico','sc.date_programable','sc.date_end')
        ->where('sc.date_end', '=', Carbon::now()->format('Y-m-d'))
        ->get();
        if($loan->count())
        {
            $data= json_decode($loan, true);
            $array_id_loan =array();
            foreach($data as $key => $qs)
            {
                if(!$this->checkpayment($qs['loan_id'],$qs['date_programable'],$qs['date_end']))
                {
                    $qs['color']            =   $array_color[rand(0,$total_colores)];
                    $qs['minimun_payment']  =   number_format($qs['minimun_payment'],2,".",",");
                    if($qs['pago_automatico'] == 1)
                    {
                        $qs['pago_automatico'] = '<a href="#" class="text-warning fs-5" data-bs-toggle="tooltip" data-bs-placement="top" title="Automatic payment" aria-label="Automatic payment"><span class="fe fe-alert-octagon"></span></a>';
                    }
                    else
                    {
                        $qs['pago_automatico'] = '';
                    }
                    array_push($array_id_loan,$qs);
                }
            }
            if(count($array_id_loan) > 0)
            {
                return \response()->json(['res'=>true,'data'=>$array_id_loan],200);
            }
            else
            {
                return \response()->json(['res'=>true,'data'=>[]],200);
            }

        }
        else
        {
            return \response()->json(['res'=>true,'data'=>[]],200);
        }
    }


    public function getFPagoEstadistica()
    {
        $today              =   CarbonImmutable::now();
        $first_day_year     =   $today->startOfYear()->format('Y-m-d H:i:s');
        $last_day_year      =   $today->endOfYear()->format('Y-m-d H:i:s');
        $forma_pago         =   [1 => 'Debit Card',2 => 'Credit Card',3 => 'Bank Check',4 => 'Cash'];
        $array_color        =   ['#6c5ffc','#05c3fb','#09ad95','#1170e4','#f82649'];
        $total_colores      =   count($array_color) -1;


        $report = DB::table('payments_loan')
                    ->where('estado',1)
                    ->where('concepto',1)
                    ->whereBetween('date_doit',[$first_day_year,$last_day_year])
                    ->selectRaw('forma_pago,count(*) as total')
                    ->groupBy('forma_pago')
                    ->get();
        if($report->count())
        {
            $data           =   json_decode($report, true);
            $array_fpago    =   array();
            $conteo         =0;
            foreach($data as $key => $qs)
            {

                $qs['descripcion']      =   $forma_pago[$qs['forma_pago']];
                $qs['color']            =   $array_color[$conteo];
                $conteo++;
                array_push($array_fpago,$qs);
            }
            if(count($array_fpago) > 0)
            {
                return \response()->json(['res'=>true,'data'=>$array_fpago],200);
            }
            else
            {
                return \response()->json(['res'=>true,'data'=>[]],200);
            }
        }
        else
        {
            return \response()->json(['res'=>true,'data'=>[]],200);
        }
    }
}
