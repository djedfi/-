<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use \App\Models\SchedulePayment;
use \App\Http\Traits\GFunctionsTrait;


class ShedulePaymentController extends Controller
{
    use GFunctionsTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getReporteSchedule($id)
    {
        try
        {
            $loan  =   DB::table('loans as l')
            ->join('schedule_payments as sc', 'sc.loan_id', '=', 'l.id')
            ->select('sc.date_programable','sc.date_end')
            ->where('l.id', '=', $id)
            ->get();

            if($loan->count())
            {
                $data= json_decode($loan, true);

                $array_id_loan =array();
                $contador = 0;
                foreach($data as $key => $qs)
                {
                    $contador++;
                    $qs['id'] = $contador;
                    $date_programable      = Carbon::create($qs['date_programable'])->subDay(5);
                    if($this->checkpayment($id,$date_programable,$qs['date_end']))
                    {
                        $qs['pago'] = 1;
                    }
                    else
                    {
                        $qs['pago'] = 0;
                    }

                    $knownDate      = Carbon::create($qs['date_programable']);
                    $current_month  = Carbon::now()->month;

                    if($knownDate->month == $current_month)
                    {
                        $qs['current_month'] = 1;
                    }
                    else
                    {
                        $qs['current_month'] = 0;
                    }

                    array_push($array_id_loan,$qs);
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
        catch(\Exception $e)
        {
            return \response()->json(['res'=>false,'data'=>[],'message'=>$e],200);
        }
    }
}
