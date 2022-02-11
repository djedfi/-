<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use \App\Models\Loan;
use \App\Models\PaymentLoan;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use App\Http\Controllers\MailerController;

class PaymentLoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        try
        {
            if(count(PaymentLoan::all()) > 0)
            {
                return \response()->json(['res'=>true,'data'=>PaymentLoan::all()],200);
            }
            else
            {
                return \response()->json(['res'=>false,'message'=>config('constants.msg_empty')],200);
            }
        }
        catch(\Exception $e)
        {
            return \response()->json(['res'=>false,'message'=>config('constants.msg_error_srv')],200);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $inputs                                     =   $request->all();
        $bandera_balance = 0;
        //pago de mensualidad
        if(isset($inputs['hid_loan_id_payment']))
        {
            $rules = [
                'hid_loan_id_payment'         =>        'required|exists:App\Models\Loan,id',
                'hid_user_id_payment'         =>        'required|exists:App\Models\User,id',
                'hid_balance_loan_payment'    =>        'required|regex:/^\d+(\.\d{1,2})?$/',
                'hid_concepto_payment'        =>        'required|integer',
                'txt_amount_due_payment'      =>        'required|regex:/^\d+(\.\d{1,2})?$/',
                'txt_description_payment'     =>        'required|string',
                'date_payment_get_payment'    =>        'required|date_format:Y-m-d',
                'rdo_payment_form_get_payment'=>        'required|integer|between:1,4',
                'hid_email_customer_payment'  =>        'nullable|email'
            ];
            $inputs['hid_balance_loan_payment']         =   str_replace(array('US$ ',','),array('',''),$inputs['hid_balance_loan_payment']);
            $inputs['txt_amount_due_payment']           =   str_replace(array('US$ ',','),array('',''),$inputs['txt_amount_due_payment']);
            list($m_temp,$d_temp,$Y_temp)               =   explode('/',$inputs['date_payment_get_payment']);
            $inputs['date_payment_get_payment']         =   $Y_temp.'-'.$m_temp.'-'.$d_temp;
            $operacion                                  =   1;
            $input_email                                =   'hid_email_customer_payment';
            $input_loan_id                              =   'hid_loan_id_payment';
        }
        //pago del balance
        else if(isset($inputs['hid_loan_id_balance']))
        {
            $rules = [
                'hid_loan_id_balance'         =>        'required|exists:App\Models\Loan,id',
                'hid_user_id_balance'         =>        'required|exists:App\Models\User,id',
                'txt_balance_balance'         =>        'required|regex:/^\d+(\.\d{1,2})?$/',
                'hid_concepto_balance'        =>        'required|integer',
                'txt_discount_balance'        =>        'required|regex:/^\d+(\.\d{1,2})?$/',
                'txt_amount_due_balance'      =>        'required|regex:/^\d+(\.\d{1,2})?$/',
                'txt_description_balance'     =>        'required|string',
                'date_payment_balance'        =>        'required|date_format:Y-m-d',
                'rdo_payment_form_balance'    =>        'required|integer|between:1,4',
                'hid_email_customer_balance'  =>        'nullable|email'
            ];

            $inputs['txt_balance_balance']         =   str_replace(array('US$ ',','),array('',''),$inputs['txt_balance_balance']);
            $inputs['txt_discount_balance']        =   str_replace(array('US$ ',','),array('',''),$inputs['txt_discount_balance']);
            $inputs['txt_amount_due_balance']      =   str_replace(array('US$ ',','),array('',''),$inputs['txt_amount_due_balance']);
            list($m_temp,$d_temp,$Y_temp)          =   explode('/',$inputs['date_payment_balance']);
            $inputs['date_payment_balance']        =   $Y_temp.'-'.$m_temp.'-'.$d_temp;
            $operacion                             =   2;
            $input_email                           =   'hid_email_customer_balance';
            $input_loan_id                         =   'hid_loan_id_balance';
        }
        else if(isset($inputs['hid_loan_id_late_fee']))
        {
            $rules = [
                'hid_loan_id_late_fee'          =>        'required|exists:App\Models\Loan,id',
                'hid_user_id_late_fee'          =>        'required|exists:App\Models\User,id',
                'hid_balance_late_fee'          =>        'required|regex:/^\d+(\.\d{1,2})?$/',
                'txt_amount_late_fee'           =>        'required|regex:/^\d+(\.\d{1,2})?$/',
                'hid_concepto_late_fee'         =>        'required|integer',
                'txt_description_late_fee'      =>        'required|string',
                'date_late_fee'                 =>        'required|date_format:Y-m-d'
            ];

            $inputs['hid_balance_late_fee']         =   str_replace(array('US$ ',','),array('',''),$inputs['hid_balance_late_fee']);
            $inputs['txt_amount_late_fee']          =   str_replace(array('US$ ',','),array('',''),$inputs['txt_amount_late_fee']);
            list($m_temp,$d_temp,$Y_temp)           =   explode('/',$inputs['date_late_fee']);
            $inputs['date_late_fee']                =   $Y_temp.'-'.$m_temp.'-'.$d_temp;
            $operacion                              =   3;
            $input_loan_id                          =   'hid_loan_id_late_fee';
            $input_email                           =    'hid_email_customer_late_fee';
        }


        DB::beginTransaction();


        try
        {
            $obj_validacion     = Validator::make($inputs,$rules);


            if(!$obj_validacion->fails())
            {
                if($operacion == 1)
                {
                    $new_balance            =   floatval($inputs['hid_balance_loan_payment']) - floatval($inputs['txt_amount_due_payment']);
                    $payment_loan           =   PaymentLoan::create
                    ([
                        'loan_id'               => $inputs['hid_loan_id_payment'],
                        'user_id'               => $inputs['hid_user_id_payment'],
                        'description'           => $inputs['txt_description_payment'],
                        'concepto'              => 1,
                        'monto'                 => $inputs['txt_amount_due_payment'],
                        'date_doit'             => $inputs['date_payment_get_payment'],
                        'forma_pago'            => $inputs['rdo_payment_form_get_payment'],
                        'balance'               => $new_balance
                    ]);
                    $payment_loan           = $payment_loan->id;
                    if($new_balance == 0)
                    {
                        $bandera_balance = 1;
                    }
                }
                else if($operacion == 2)
                {
                    $new_balance            =   floatval($inputs['txt_balance_balance']) - floatval($inputs['txt_discount_balance']) - floatval($inputs['txt_amount_due_balance']);

                    PaymentLoan::create
                    (
                        [
                            'loan_id'       =>  $inputs['hid_loan_id_balance'],
                            'user_id'       =>  $inputs['hid_user_id_balance'],
                            'description'   =>  'Discount of the interest for paying the balance',
                            'concepto'      =>  1,
                            'monto'         =>  $inputs['txt_discount_balance'],
                            'date_doit'     =>  $inputs['date_payment_balance'],
                            'forma_pago'    =>  $inputs['rdo_payment_form_balance'],
                            'balance'       =>  floatval($inputs['txt_balance_balance']) - floatval($inputs['txt_discount_balance']),
                        ]
                    );


                    $payment_loan           =   PaymentLoan::create([
                        'loan_id'       =>  $inputs['hid_loan_id_balance'],
                        'user_id'       =>  $inputs['hid_user_id_balance'],
                        'description'   =>  $inputs['txt_description_balance'],
                        'concepto'      =>  1,
                        'monto'         =>  $inputs['txt_amount_due_balance'],
                        'date_doit'     =>  $inputs['date_payment_balance'],
                        'forma_pago'    =>  $inputs['rdo_payment_form_balance'],
                        'balance'       =>  $new_balance,
                    ]);

                    if($new_balance == 0)
                    {
                        $bandera_balance = 1;
                    }
                }
                else if($operacion == 3)
                {
                    $new_balance            =   floatval($inputs['hid_balance_late_fee']) + floatval($inputs['txt_amount_late_fee']);
                    $payment_loan           =   PaymentLoan::create
                    ([
                        'loan_id'               => $inputs['hid_loan_id_late_fee'],
                        'user_id'               => $inputs['hid_user_id_late_fee'],
                        'description'           => $inputs['txt_description_late_fee'],
                        'concepto'              => 2,
                        'monto'                 => $inputs['txt_amount_late_fee'],
                        'date_doit'             => $inputs['date_late_fee'],
                        'balance'               => $new_balance
                    ]);
                    $payment_loan           = $payment_loan->id;
                    if($new_balance == 0)
                    {
                        $bandera_balance = 1;
                    }
                }

                if($payment_loan > 0)
                {
                    Loan::where('id',$inputs[$input_loan_id])->update(array('balance'=>$new_balance));
                    if(isset($inputs[$input_email]) && $inputs[$input_email]!= '')
                    {
                        $new_email  =   new MailerController;
                        $new_email->precompose($payment_loan);
                    }
                    DB::commit();
                    return \response()->json(['res'=>true,'message'=>config('constants.msg_new_srv'),'balance'=>$new_balance,'bandera_balance'=>$bandera_balance,'correo'=>$inputs[$input_email]],200);
                }
            }
            else
            {
                DB::rollback();
                return \response()->json(['res'=>false,'message'=>$obj_validacion->errors()],200);
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return \response()->json(['res'=>false,'message'=>$e],200);
        }



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
        try
        {
            $payment    =   PaymentLoan::where('loan_id',$id)->where('estado',1);
            if($payment->count())
            {
                return \response()->json(['res'=>true,'data'=>$payment->get()],200);
            }
            else
            {
                return \response()->json(['res'=>false,'message'=>config('constants.msg_no_existe_srv')],200);
            }
        }
        catch(\Exception $e)
        {
            return \response()->json(['res'=>false,'message'=>config('constants.msg_error_srv')],200);
        }
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
        $rules = [
            'hid_estado_delete_late_fee'        =>        'required|integer|between:1,2',
            'hid_user_id_delete_late_fee'       =>        'required|exists:App\Models\User,id',
            'txta_reason_delete_late_fee'       =>        'required|string'
        ];

        DB::beginTransaction();

        try
        {
            $inputs                 =   $request->all();

            $obj_validacion         = Validator::make($inputs,$rules);

            if(!$obj_validacion->fails())
            {
                $inputs['hid_fee_late_delete_late_fee']         =   str_replace(array('US$ ',','),array('',''),$inputs['hid_fee_late_delete_late_fee']);
                $inputs['hid_balance_delete_late_fee']          =   str_replace(array('US$ ',','),array('',''),$inputs['hid_balance_delete_late_fee']);

                $payment                =   PaymentLoan::where('id',$id)
                                        ->update(
                                            array(
                                                'estado'        =>$inputs['hid_estado_delete_late_fee'],
                                                'reason_delete' =>$inputs['txta_reason_delete_late_fee'],
                                                'user_id'       =>$inputs['hid_user_id_delete_late_fee']
                                            )
                                        );

                if($payment)
                {
                    $new_balance            =   floatval($inputs['hid_balance_delete_late_fee']) - floatval($inputs['hid_fee_late_delete_late_fee']);
                    Loan::where('id',$inputs['hid_loan_id_delete_late_fee'])
                                        ->update(
                                            array(
                                                'user_id'       =>$inputs['hid_user_id_delete_late_fee'],
                                                'balance'       =>$new_balance
                                            )
                                        );
                    DB::commit();
                    return \response()->json(['res'=>true,'message'=>config('constants.msg_ok_srv'),'balance'=>$new_balance],200);
                }
                else
                {
                    DB::rollback();
                    return \response()->json(['res'=>false,'message'=>config('constants.msg_error_operacion_srv')],200);
                }
            }
            else
            {
                DB::rollback();
                return \response()->json(['res'=>false,'message'=>$obj_validacion->errors()],200);
            }




        }
        catch(\Exception $e)
        {
            DB::rollback();
            return \response()->json(['res'=>false,'message'=>$e],200);
        }
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
        try
        {
            $count_payment     =   PaymentLoan::where('id',$id)->count();

            if($count_payment > 0)
            {
                PaymentLoan::destroy($id);
                return \response()->json(['res'=>true,'message'=>config('constants.msg_ok_srv')],200);
            }
            else
            {
                return \response()->json(['res'=>true,'message'=>config('constants.msg_error_existe_srv')],200);
            }
        }
        catch(\Exception $e)
        {
            return \response()->json(['res'=>false,'message'=>config('constants.msg_error_srv')],200);
        }
    }

    public function getLastPaymentbyLoad($id)
    {
        try
        {
            $payment    =   PaymentLoan::where('loan_id',$id)->where('estado',1)->latest();
            return \response()->json(['res'=>true,'data'=>$payment->first()],200);
        }
        catch(\Exception $e)
        {
            return \response()->json(['res'=>false,'message'=>config('constants.msg_error_srv')],200);
        }
    }

    public function sendReceipt($data_id)
    {
        try
        {
            $new_email  =   new MailerController;
            $new_email->precompose($data_id);
            return \response()->json(['res'=>true],200);
        }
        catch(\Exception $e)
        {
            return \response()->json(['res'=>false],200);
        }

    }
}
