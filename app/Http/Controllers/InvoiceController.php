<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class InvoiceController extends Controller
{
    //
    public function getLoan($payment_id)
    {
        $name_company       =   'AA Motors Auto';
        $address_company_p  =   '725 W Manchester Ave';
        $address_company_s  =   'Los Angeles, CA 90044';
        $cellphone_company  =   '(323) 750-8891';

        $payment  =   DB::table('loans as l')
                        ->join('payments_loan as pl','pl.loan_id', '=', 'l.id')
                        ->join('customers as cus','cus.id', '=', 'l.customer_id')
                        ->join('cars as c','l.car_id', '=', 'c.id')
                        ->join('trims as tm', 'tm.id', '=', 'c.trim_id')
                        ->join('modelos as md', 'md.id', '=', 'tm.modelo_id')
                        ->join('makes as mk', 'mk.id', '=', 'md.make_id')
                        ->select('l.id as loan_id','pl.id as payment_id',DB::raw("CONCAT(cus.first_name,' ',cus.last_name) as name_customer"),'cus.cellphone','cus.email',DB::raw("CONCAT(mk.name,' ',md.name,' ',tm.name) as info_car"),'c.vin','c.year',DB::raw("DATE_FORMAT(pl.date_doit,'%m/%d/%Y') as date_payment"),'pl.description','pl.monto','pl.balance','pl.forma_pago',DB::raw("DATE_FORMAT(pl.date_doit,'%Y') as year_payment"),DB::raw("case when pl.forma_pago = 1 then 'Debit Card' when pl.forma_pago = 2 then 'Credit Card' when pl.forma_pago = 3 then 'Cheque' when pl.forma_pago = 4 then 'Cash' when pl.forma_pago = 5 then 'Zelle' when pl.forma_pago = 6 then 'Deposit Account' end as lbl_forma_pago"))
                        ->where('pl.id', '=', $payment_id)
                        ->where('pl.estado', '=', 1)
                        ->where('pl.concepto', '=', 3)
                        ->get();

        return view('receipt',compact('payment','name_company','address_company_p','address_company_s','cellphone_company'));
    }

    public function getLoanPDF($payment_id)
    {
        $name_company       =   'AA Motors Auto';
        $address_company_p  =   '725 W Manchester Ave';
        $address_company_s  =   'Los Angeles, CA 90044';
        $cellphone_company  =   '(323) 750-8891';

        $payment  =   DB::table('loans as l')
                        ->join('payments_loan as pl','pl.loan_id', '=', 'l.id')
                        ->join('customers as cus','cus.id', '=', 'l.customer_id')
                        ->join('cars as c','l.car_id', '=', 'c.id')
                        ->join('trims as tm', 'tm.id', '=', 'c.trim_id')
                        ->join('modelos as md', 'md.id', '=', 'tm.modelo_id')
                        ->join('makes as mk', 'mk.id', '=', 'md.make_id')
                        ->select('l.id as loan_id','pl.id as payment_id',DB::raw("CONCAT(cus.first_name,' ',cus.last_name) as name_customer"),'cus.cellphone','cus.email',DB::raw("CONCAT(mk.name,' ',md.name,' ',tm.name) as info_car"),'c.vin','c.year',DB::raw("DATE_FORMAT(pl.date_doit,'%m/%d/%Y') as date_payment"),'pl.description','pl.monto','pl.balance','pl.forma_pago',DB::raw("DATE_FORMAT(pl.date_doit,'%Y') as year_payment"),DB::raw("case when pl.forma_pago = 1 then 'Debit Card' when pl.forma_pago = 2 then 'Credit Card' when pl.forma_pago = 3 then 'Cheque' when pl.forma_pago = 4 then 'Cash' when pl.forma_pago = 5 then 'Zelle' when pl.forma_pago = 6 then 'Deposit Account' end as lbl_forma_pago"))
                        ->where('pl.id', '=', $payment_id)
                        ->where('pl.estado', '=', 1)
                        ->where('pl.concepto', '=', 3)
                        ->get();
        $pdf = PDF::loadView('receipt',compact('payment','name_company','address_company_p','address_company_s','cellphone_company'));
        return $pdf->download('receipt.pdf');
    }

    public function getSummaryLoan($loan_id)
    {

        $name_company       =   'AA Motors Auto';
        $address_company_p  =   '725 W Manchester Ave';
        $address_company_s  =   'Los Angeles, CA 90044';
        $cellphone_company  =   '(323) 750-8891';

        $loan  =   DB::table('loans as l')
                        ->join('customers as cus','cus.id', '=', 'l.customer_id')
                        ->join('cars as c','l.car_id', '=', 'c.id')
                        ->join('trims as tm', 'tm.id', '=', 'c.trim_id')
                        ->join('modelos as md', 'md.id', '=', 'tm.modelo_id')
                        ->join('makes as mk', 'mk.id', '=', 'md.make_id')
                        ->select('l.id as loan_id',DB::raw("CONCAT(cus.first_name,' ',cus.last_name) as name_customer"),'cus.cellphone','cus.email',DB::raw("CONCAT(mk.name,' ',md.name,' ',tm.name) as info_car"),'c.vin','c.year',DB::raw("DATE_FORMAT(l.loan_date,'%m/%d/%Y') as loan_date"),DB::raw("DATE_FORMAT(l.start_payment,'%m/%d/%Y') as start_payment"),'l.total_financed','l.balance','c.precio')
                        ->where('l.id', '=', $loan_id)
                        ->get();

        $payments   =DB::table('payments_loan')
                    ->select('id','description','monto','balance',DB::raw("DATE_FORMAT(date_doit,'%m/%d/%Y') as date_payment"))
                    ->where('loan_id','=',$loan_id)
                    ->where('estado','=',1)
                    ->orderBy('created_at', 'asc')
                    ->get();


        return view('summary',compact('loan','payments','name_company','address_company_p','address_company_s','cellphone_company'));
    }

    public function getSummaryLoanPDF($loan_id)
    {

        $name_company       =   'AA Motors Auto';
        $address_company_p  =   '725 W Manchester Ave';
        $address_company_s  =   'Los Angeles, CA 90044';
        $cellphone_company  =   '(323) 750-8891';

        $loan  =   DB::table('loans as l')
                        ->join('customers as cus','cus.id', '=', 'l.customer_id')
                        ->join('cars as c','l.car_id', '=', 'c.id')
                        ->join('trims as tm', 'tm.id', '=', 'c.trim_id')
                        ->join('modelos as md', 'md.id', '=', 'tm.modelo_id')
                        ->join('makes as mk', 'mk.id', '=', 'md.make_id')
                        ->select('l.id as loan_id',DB::raw("CONCAT(cus.first_name,' ',cus.last_name) as name_customer"),'cus.cellphone','cus.email',DB::raw("CONCAT(mk.name,' ',md.name,' ',tm.name) as info_car"),'c.vin','c.year',DB::raw("DATE_FORMAT(l.loan_date,'%m/%d/%Y') as loan_date"),DB::raw("DATE_FORMAT(l.start_payment,'%m/%d/%Y') as start_payment"),'l.total_financed','l.balance','c.precio')
                        ->where('l.id', '=', $loan_id)
                        ->get();

        $payments   =DB::table('payments_loan')
                    ->select('id','description','monto','balance',DB::raw("DATE_FORMAT(date_doit,'%m/%d/%Y') as date_payment"))
                    ->where('loan_id','=',$loan_id)
                    ->where('estado','=',1)
                    ->orderBy('created_at', 'asc')
                    ->get();

        //return PDF::loadFile(public_path().'/myfile.html')->save('/path-to/my_stored_file.pdf')->stream('download.pdf');
        $pdf = PDF::loadView('summary',compact('loan','payments','name_company','address_company_p','address_company_s','cellphone_company'));
        return $pdf->download('summary.pdf');
    }
}
