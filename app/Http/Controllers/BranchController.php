<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Branch;


class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        //
        try
        {
            if(count(Branch::all()) > 0)
            {
                $trims  = Branch::with(['company'])->get();
                return \response()->json(['res'=>true,'data'=>$trims],200);
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
            'cmb_company_br'     =>       'required|exists:App\Models\Company,id',
            'txt_name_br'        =>       'required|string|max:45',
            'txt_addressp_br'    =>       'required|string|max:250',
            'txt_addresss_br'    =>       'string|max:150',
            'txt_telephone_br'   =>       'required|string|max:10',
            'txt_cellphone_br'   =>       'required|string|max:10',
            'txt_city_br'        =>       'required|string|max:45'
        ];

        try
        {
            $inputs              =   $request->all();
            //return $inputs;
            $obj_validacion      =   Validator::make($inputs,$rules);

            if(!$obj_validacion->fails())
            {
                $inputs_f['company_id']         =   $inputs['cmb_company_br'];
                $inputs_f['name']               =   $inputs['txt_name_br'];
                $inputs_f['address_p']          =   $inputs['txt_addressp_br'];
                $inputs_f['address_s']          =   $inputs['txt_addresss_br'];
                $inputs_f['telephone']          =   $inputs['txt_telephone_br'];
                $inputs_f['cellphone']          =   $inputs['txt_cellphone_br'];
                $inputs_f['city']               =   'LA';


                $branch                 =   Branch::create($inputs_f);
                if($branch->id > 0)
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
            if(Branch::where('id',$id)->count())
            {
                $branch = Branch::with(['company'])->find($id);
                return \response()->json(['res'=>true,'datos'=>$branch],200);
            }
            else
            {
                return \response()->json(['res'=>false,'data'=>[],'message'=>config('constants.msg_no_existe_srv')],200);
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
        //
        $inputs_f       = array();
        $rules = [
            'cmb_company_br'     =>       'required|exists:App\Models\Company,id',
            'txt_name_br'        =>       'required|string|max:45',
            'txt_addressp_br'    =>       'required|string|max:250',
            'txt_addresss_br'    =>       'string|max:150',
            'txt_telephone_br'   =>       'required|string|max:10',
            'txt_cellphone_br'   =>       'required|string|max:10',
            'txt_city_br'        =>       'required|string|max:45'
        ];

        $inputs              =   $request->all();
        try
        {
            $obj_validacion     = Validator::make($inputs,$rules);

            if(!$obj_validacion->fails())
            {
                $branch   =   Branch::find($id);
                if($branch->id == $id)
                {
                    $inputs_f['company_id']         =   $inputs['cmb_company_br'];
                    $inputs_f['name']               =   $inputs['txt_name_br'];
                    $inputs_f['address_p']          =   $inputs['txt_addressp_br'];
                    $inputs_f['address_s']          =   $inputs['txt_addresss_br'];
                    $inputs_f['telephone']          =   $inputs['txt_telephone_br'];
                    $inputs_f['cellphone']          =   $inputs['txt_cellphone_br'];
                    $inputs_f['city']               =   $inputs['txt_city_br'];
                    $upd_branch                =    $branch->update($inputs_f);
                    if($upd_branch)
                    {
                        return \response()->json(['res'=>true,'message'=>config('constants.msg_ok_srv')],200);
                    }
                    else
                    {
                        return \response()->json(['res'=>true,'message'=>config('constants.msg_error_operacion_srv')],200);
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
            $count_branch     =   Branch::where('id',$id)->count();
            if($count_branch > 0)
            {
                Branch::destroy($id);
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
