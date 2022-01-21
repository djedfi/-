<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Customer;

class CustomerController extends Controller
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
            if(count(Customer::all()) > 0)
            {
                $customer  = Customer::with(['state'])->get();
                return \response()->json(['res'=>true,'data'=>$customer],200);
            }
            else
            {
                return \response()->json(['res'=>false,'message'=>config('constants.msg_empty')],200);
            }
        }
        catch(\Exception $e)
        {
            return \response()->json(['res'=>false,'message'=>$e],200);
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
         //
         $rules = [
            'txt_fname_cus'             =>      'required|max:250',
            'txt_init_cus'              =>      'required|max:4',
            'txt_lname_cus'             =>      'required|max:250',
            'txt_mobile_cus'            =>      'required|max:10',
            'txt_email_cus'             =>      'required|string|max:150|unique:customers,email',
            'txt_dlicense_cus'          =>      'required|string|max:15|unique:customers,licence',
            'slc_statelic_cus'          =>      'required|string|max:45',
            'txt_bday_cus'              =>      'required|date_format:m/d/Y',
            'txt_ssn_cus'               =>      'required|regex:/^\d{3}-\d{2}-\d{4}$/|unique:customers,ssn',
            'txt_paddress_cus'          =>      'required|max:250',
            'txt_saddress_cus'          =>      'nullable|max:150',
            'txt_city_cus'              =>      'required|max:100',
            'slc_state_cus'             =>      'required|exists:App\Models\State,id',
            'txt_zip_cus'               =>      'required|regex:/^\d{5}$/',
            'txt_resphone_cus'          =>      'nullable|string|max:20',
            'txt_business_cus'          =>      'nullable|string|max:20',
            'slc_gender_cus'            =>      'required|integer|between:1,3'
         ];

        try
        {
            $inputs                         =   $request->all();
            $inputs['txt_mobile_cus']       =   str_replace(array(' ','(',')','-'),array('','','',''),$inputs['txt_mobile_cus']);
            $inputs['txt_resphone_cus']     =   str_replace(array(' ','(',')','-'),array('','','',''),$inputs['txt_resphone_cus']);
            $inputs['txt_business_cus']     =   str_replace(array(' ','(',')','-'),array('','','',''),$inputs['txt_business_cus']);


            $obj_validacion     = Validator::make($inputs,$rules);

            if(!$obj_validacion->fails())
            {
                list($m_temp,$d_temp,$Y_temp)      =   explode('/',$inputs['txt_bday_cus']);

                $customer       =   Customer::create([
                    'state_id'          => $inputs['slc_state_cus'],
                    'customer_id'       => $this->create_customerid($inputs['txt_fname_cus'],$inputs['txt_lname_cus'],$inputs['txt_bday_cus']),
                    'licence'           => $inputs['txt_dlicense_cus'],
                    'state_licence'     => $inputs['slc_statelic_cus'],
                    'first_name'        => Str::of($inputs['txt_fname_cus'])->upper(),
                    'last_name'         => Str::of($inputs['txt_lname_cus'])->upper(),
                    'initial'           => Str::of($inputs['txt_init_cus'])->upper(),
                    'address_p'         => $inputs['txt_paddress_cus'],
                    'address_s'         => $inputs['txt_saddress_cus'],
                    'city'              => $inputs['txt_city_cus'],
                    'zip'               => $inputs['txt_zip_cus'],
                    'telephone_res'     => $inputs['txt_resphone_cus'],
                    'telephone_bus'     => $inputs['txt_business_cus'],
                    'cellphone'         => $inputs['txt_mobile_cus'],
                    'email'             => Str::of($inputs['txt_email_cus'])->lower(),
                    'birthday'          => $Y_temp.'-'.$m_temp.'-'.$d_temp,
                    'ssn'               => str_replace('-','',$inputs['txt_ssn_cus']),
                    'gender'            => $inputs['slc_gender_cus']
                ]);

                if($customer->id > 0)
                {
                    return \response()->json(['res'=>true,'message'=>config('constants.msg_new_srv')],200);
                }
                else
                {
                    return \response()->json(['res'=>false,'message'=>config('constants.msg_error_operacion_srv')],200);
                }
            }
            else
            {
                return \response()->json(['res'=>false,'message'=>$obj_validacion->errors()],200);
            }

        }
        catch(\Exception $e)
        {
            return \response()->json(['res'=>false,'message'=>$e],200);
        }

    }

    private function create_customerid($fname,$lname,$birthday)
    {
        $year_actual        =    date('Y');
        $last_year_actual   =   Str::of(substr($year_actual,-2))->upper();
        $f_fname            =   Str::of(substr($fname,0,1))->upper();
        $f_lname            =   substr($lname,0,1);
        $caracter_paridad    =   $this->getLetraRandom();
        list($m,$d,$Y)      =   explode('/',$birthday);
        return $f_fname.$f_lname.$last_year_actual.substr($Y,-2).$m.$caracter_paridad;
    }

    private function getLetraRandom($length = 1)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
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
            if(Customer::where('id',$id)->count())
            {
                $customer = Customer::with(['state'])->get()->find($id);
                return \response()->json(['res'=>true,'datos'=>$customer],200);
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
        $input_f    = array();
        $rules = [
            'txt_fname_cus_upd'             =>      'required|max:250',
            'txt_init_cus_upd'              =>      'required|max:4',
            'txt_lname_cus_upd'             =>      'required|max:250',
            'txt_mobile_cus_upd'            =>      'required|max:10',
            'txt_email_cus_upd'             =>      'required|string|max:150|unique:customers,email,'.$id,
            'txt_dlicense_cus_upd'          =>      'required|string|max:15|unique:customers,licence,'.$id,
            'slc_statelic_cus_upd'          =>      'required|string|max:45',
            'txt_bday_cus_upd'              =>      'required|date_format:m/d/Y',
            'txt_ssn_cus_upd'               =>      'required|regex:/^\d{3}-\d{2}-\d{4}$/|unique:customers,ssn,'.$id,
            'txt_paddress_cus_upd'          =>      'required|max:250',
            'txt_saddress_cus_upd'          =>      'nullable|max:150',
            'txt_city_cus_upd'              =>      'required|max:100',
            'slc_state_cus_upd'             =>      'required|exists:App\Models\State,id',
            'txt_zip_cus_upd'               =>      'required|regex:/^\d{5}$/',
            'txt_resphone_cus_upd'          =>      'nullable|string|max:20',
            'txt_business_cus_upd'          =>      'nullable|string|max:20',
            'slc_gender_cus_upd'            =>      'required|integer|between:1,3'
         ];

        try
        {
            $inputs                         =   $request->all();
            $inputs['txt_mobile_cus_upd']       =   str_replace(array(' ','(',')','-'),array('','','',''),$inputs['txt_mobile_cus_upd']);
            $inputs['txt_resphone_cus_upd']     =   str_replace(array(' ','(',')','-'),array('','','',''),$inputs['txt_resphone_cus_upd']);
            $inputs['txt_business_cus_upd']     =   str_replace(array(' ','(',')','-'),array('','','',''),$inputs['txt_business_cus_upd']);

            $obj_validacion     = Validator::make($inputs,$rules);

            if(!$obj_validacion->fails())
            {
                list($m_temp,$d_temp,$Y_temp)      =   explode('/',$inputs['txt_bday_cus_upd']);
                $customer   =   Customer::find($id);

                if($customer->id == $id)
                {
                    $input_f['state_id']        = $inputs['slc_state_cus_upd'];
                    $input_f['licence']         = $inputs['txt_dlicense_cus_upd'];
                    $input_f['state_licence']   = $inputs['slc_statelic_cus_upd'];
                    $input_f['first_name']      = Str::of($inputs['txt_fname_cus_upd'])->upper();
                    $input_f['last_name']       = Str::of($inputs['txt_lname_cus_upd'])->upper();
                    $input_f['initial']         = Str::of($inputs['txt_init_cus_upd'])->upper();
                    $input_f['address_p']       = $inputs['txt_paddress_cus_upd'];
                    $input_f['address_s']       = $inputs['txt_saddress_cus_upd'];
                    $input_f['city']            = $inputs['txt_city_cus_upd'];
                    $input_f['zip']             = $inputs['txt_zip_cus_upd'];
                    $input_f['telephone_res']   = $inputs['txt_resphone_cus_upd'];
                    $input_f['telephone_bus']   = $inputs['txt_business_cus_upd'];
                    $input_f['cellphone']       = $inputs['txt_mobile_cus_upd'];
                    $input_f['email']           = Str::of($inputs['txt_email_cus_upd'])->lower();
                    $input_f['birthday']        = $Y_temp.'-'.$m_temp.'-'.$d_temp;
                    $input_f['ssn']             = str_replace('-','',$inputs['txt_ssn_cus_upd']);
                    $input_f['gender']          = $inputs['slc_gender_cus_upd'];
                    $update_customer            = $customer->update($input_f);

                    if($update_customer)
                    {
                        return \response()->json(['res'=>true,'message'=>config('constants.msg_new_srv')],200);
                    }
                    else
                    {
                        return \response()->json(['res'=>false,'message'=>config('constants.msg_error_operacion_srv')],200);
                    }
                }
                else
                {
                    return \response()->json(['res'=>false,'message'=>config('constants.msg_error_operacion_srv')],200);
                }
            }
            else
            {
                return \response()->json(['res'=>false,'message'=>$obj_validacion->errors()],200);
            }
        }
        catch(\Exception $e)
        {
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
    }

    public function CheckDriverL(Request $request,$id)
    {
        if($id == 0)
        {
            $driver_lic    = $request->txt_dlicense_cus;

            if(Customer::where('licence','=',$driver_lic)->count() > 0)
            {
                return \response()->json(['res'=>true],200);
            }
            else
            {
                return \response()->json(['res'=>false],200);
            }
        }
        else if($id > 0)
        {
            $driver_lic          =   $request->txt_dlicense_cus;
            $customer            =   Customer::where('id',$id)->first();

            if($customer && ($customer->licence == $driver_lic))
            {
                return \response()->json(['res'=>false],200);
            }
            else
            {
                $license_check       =   Customer::where('licence',$driver_lic)->first();
                if($license_check)
                {
                    return \response()->json(['res'=>true],200);
                }
                else
                {
                    return \response()->json(['res'=>false],200);
                }
            }
        }
    }

    public function CheckEmail(Request $request,$id)
    {
        if($id == 0)
        {
            $email    = $request->txt_email_cus;

            if(Customer::where('email','=',$email)->count() > 0)
            {
                return \response()->json(['res'=>true],200);
            }
            else
            {
                return \response()->json(['res'=>false],200);
            }
        }
        else if($id > 0)
        {
            $email              =   $request->txt_email_cus;
            $customer           =   Customer::where('id',$id)->first();

            if($customer && ($customer->email == $email))
            {
                return \response()->json(['res'=>false],200);
            }
            else
            {
                $email_check       =   Customer::where('email',$email)->first();
                if($email_check)
                {
                    return \response()->json(['res'=>true],200);
                }
                else
                {
                    return \response()->json(['res'=>false],200);
                }
            }
        }
    }

    public function CheckDateBirth(Request $request)
    {
        $dbirth    = $request->txt_bday_cus;
        list($m,$d,$y)  = explode('/',$dbirth);

        if(checkdate($m,$d,$y))
        {
            return \response()->json(['res'=>true],200);
        }
        else
        {
            return \response()->json(['res'=>false],200);
        }
    }

    public function CheckSSN(Request $request,$id)
    {
        if($id == 0)
        {
            $ssn    = str_replace('-','',$request->txt_ssn_cus);

            if(Customer::where('ssn','=',$ssn)->count() > 0)
            {
                return \response()->json(['res'=>true],200);
            }
            else
            {
                return \response()->json(['res'=>false],200);
            }
        }
        else if($id > 0)
        {
            $ssn                =   str_replace('-','',$request->txt_ssn_cus);
            $customer           =   Customer::where('id',$id)->first();

            if($customer && ($customer->ssn == $ssn))
            {
                return \response()->json(['res'=>false],200);
            }
            else
            {
                $ssn_check       =   Customer::where('ssn',$ssn)->first();
                if($ssn_check)
                {
                    return \response()->json(['res'=>true],200);
                }
                else
                {
                    return \response()->json(['res'=>false],200);
                }
            }
        }
    }

}
