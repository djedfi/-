<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Config;
use Illuminate\Support\Facades\Validator;

class ConfigController extends Controller
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
            if(count(Config::all()) > 0)
            {
                return \response()->json(['res'=>true,'data'=>Config::all()],200);
            }
            else
            {
                return \response()->json(['res'=>false,'data'=>[],'message'=>config('constants.msg_empty')],200);
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
        $inputs_f       = array();
        //
        $rules = [
            'hid_branch_id_cfg'         =>       'required|exists:App\Models\Branch,id',
            'rdo_long_term_cfg'         =>       'required|integer|between:1,6',
            'txt_down_rate_cfg'         =>       'required|regex:/^\d+(\.\d{1,2})?$/',
            'txt_interest_rate_cfg'     =>       'required|regex:/^\d+(\.\d{1,2})?$/',
            'txt_latedays_fee_cfg'      =>       'required|regex:/^\d+(\.\d{1,2})?$/',
            'txt_latedays_cfg'          =>       'required|integer',
            'txt_taxes_cfg'             =>       'required|regex:/^\d+(\.\d{1,2})?$/',
        ];

        try
        {
            $inputs              =   $request->all();
            $inputs['txt_down_rate_cfg']        =   str_replace(array(' %',','),array('',''),$inputs['txt_down_rate_cfg']);
            $inputs['txt_interest_rate_cfg']    =   str_replace(array(' %',','),array('',''),$inputs['txt_interest_rate_cfg']);
            $inputs['txt_taxes_cfg']            =   str_replace(array(' %',','),array('',''),$inputs['txt_taxes_cfg']);
            $inputs['txt_latedays_fee_cfg']     =   str_replace(array('US$ ',','),array('',''),$inputs['txt_latedays_fee_cfg']);

            $obj_validacion     = Validator::make($inputs,$rules);

            if(!$obj_validacion->fails())
            {
                $inputs_f['branch_id']              =   $inputs['hid_branch_id_cfg'];
                $inputs_f['long_term_default']      =   $inputs['rdo_long_term_cfg'];
                $inputs_f['porc_downpay_default']   =   $inputs['txt_down_rate_cfg'];
                $inputs_f['int_rate_default']       =   $inputs['txt_interest_rate_cfg'];
                $inputs_f['latefee_default']        =   $inputs['txt_latedays_fee_cfg'];
                $inputs_f['dayslate_default']       =   $inputs['txt_latedays_cfg'];
                $inputs_f['taxes_rate_default']     =   $inputs['txt_taxes_cfg'];

                $config       =   Config::create($inputs_f);

                if($config->id > 0)
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
            return \response()->json(['res'=>false,'message'=>config('constants.msg_error_srv')],200);
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
            if(Config::where('branch_id',$id)->count())
            {
                $config = Config::where('branch_id',$id)->get();
                //$config = Config::get()->find($id);
                return \response()->json(['res'=>true,'datos'=>$config],200);
            }
            else
            {
                return \response()->json(['res'=>false,'datos'=>[],'message'=>config('constants.msg_no_existe_srv')],200);
            }
        }
        catch(\Exception $e)
        {
            return \response()->json(['res'=>false,'datos'=>[],'message'=>config('constants.msg_error_srv')],200);
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
        //
        $inputs_f       = array();
        $rules = [
            'hid_branch_id_cfg'         =>       'required|exists:App\Models\Branch,id',
            'rdo_long_term_cfg'         =>       'required|integer|between:1,6',
            'txt_down_rate_cfg'         =>       'required|regex:/^\d+(\.\d{1,2})?$/',
            'txt_interest_rate_cfg'     =>       'required|regex:/^\d+(\.\d{1,2})?$/',
            'txt_latedays_fee_cfg'      =>       'required|regex:/^\d+(\.\d{1,2})?$/',
            'txt_latedays_cfg'          =>       'required|integer',
            'txt_taxes_cfg'             =>       'required|regex:/^\d+(\.\d{1,2})?$/',
        ];

        $inputs              =   $request->all();
        try
        {
            $inputs['txt_down_rate_cfg']        =   str_replace(array(' %',','),array('',''),$inputs['txt_down_rate_cfg']);
            $inputs['txt_interest_rate_cfg']    =   str_replace(array(' %',','),array('',''),$inputs['txt_interest_rate_cfg']);
            $inputs['txt_taxes_cfg']            =   str_replace(array(' %',','),array('',''),$inputs['txt_taxes_cfg']);
            $inputs['txt_latedays_fee_cfg']     =   str_replace(array('US$ ',','),array('',''),$inputs['txt_latedays_fee_cfg']);


            $obj_validacion     = Validator::make($inputs,$rules);

            if(!$obj_validacion->fails())
            {
                $config   =   Config::find($id);
                if($config->id == $id)
                {
                    $inputs_f['branch_id']              =   $inputs['hid_branch_id_cfg'];
                    $inputs_f['long_term_default']      =   $inputs['rdo_long_term_cfg'];
                    $inputs_f['porc_downpay_default']   =   $inputs['txt_down_rate_cfg'];
                    $inputs_f['int_rate_default']       =   $inputs['txt_interest_rate_cfg'];
                    $inputs_f['latefee_default']        =   $inputs['txt_latedays_fee_cfg'];
                    $inputs_f['dayslate_default']       =   $inputs['txt_latedays_cfg'];
                    $inputs_f['taxes_rate_default']     =   $inputs['txt_taxes_cfg'];

                    $upd_config               =   $config->update($inputs_f);
                    if($upd_config)
                    {
                        return \response()->json(['res'=>true,'message'=>config('constants.msg_ok_srv')],200);
                    }
                    else
                    {
                        return \response()->json(['res'=>false,'message'=>config('constants.msg_error_operacion_srv')],200);
                    }
                }
                else
                {
                    return \response()->json(['res'=>true,'message'=>config('constants.msg_error_existe_srv')],200);
                }
            }
            else
            {
                return \response()->json(['res'=>true,'message'=>$obj_validacion->errors()],200);
            }
        }
        catch(\Exception $e)
        {
            return \response()->json(['res'=>false,'message'=>config('constants.msg_error_srv')],200);
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
            $count_config     =   Config::where('id',$id)->count();

            if($count_config > 0)
            {
                Config::destroy($id);
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
}
