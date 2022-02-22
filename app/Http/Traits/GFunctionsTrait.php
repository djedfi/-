<?php
namespace App\Http\Traits;
use \App\Models\PaymentLoan;

trait GFunctionsTrait
{
    public function checkpayment($loan_id,$date_ini,$date_fin,$concepto =3)
    {
        $check_payment  = false;
        $payment = PaymentLoan::where('loan_id',$loan_id)->where('concepto',$concepto)->where('estado',1)->whereBetween('date_doit',[$date_ini.' 00:00:00',$date_fin.' 23:59:59'])->count();

        if($payment>0)
        {
            $check_payment  =true;
        }

        return $check_payment;
    }
}
