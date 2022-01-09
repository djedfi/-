<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Make;
use Illuminate\Support\Facades\Validator;

class MakeController extends Controller
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
             if(count(Make::all()) > 0)
             {
                 return \response()->json(['res'=>true,'data'=>Make::all()],200);
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
        $inputs_f       = array();
        //
        $rules = [
            'txt_name_mk'     =>       'required|string|max:45',
            'txt_url_mk'      =>       'required|url|max:150'
        ];

        try
        {
            $inputs              =   $request->all();
            $inputs['txt_url_mk']=   'https://'.$inputs['txt_url_mk'];
            $obj_validacion     = Validator::make($inputs,$rules);

            if(!$obj_validacion->fails())
            {
                $inputs_f['name']       =   $inputs['txt_name_mk'];
                $inputs_f['website']    =   $inputs['txt_url_mk'];
                $auto       =   Make::create($inputs_f);

                if($auto->id > 0)
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
            if(Make::where('id',$id)->count())
            {
                $make = Make::get()->find($id);
                return \response()->json(['res'=>true,'datos'=>$make],200);
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
        //
        $inputs_f       = array();
        $rules = [
            'txt_name_mk'     =>       'required|string|max:45',
            'txt_url_mk'      =>       'required|url|max:150'
        ];

        $inputs              =   $request->all();
        try
        {
            $inputs['txt_url_mk']=   'https://'.$inputs['txt_url_mk'];
            $obj_validacion     = Validator::make($inputs,$rules);

            if(!$obj_validacion->fails())
            {
                $make   =   Make::find($id);
                if($make->id == $id)
                {
                    $inputs_f['name']       =   $inputs['txt_name_mk'];
                    $inputs_f['website']    =   $inputs['txt_url_mk'];
                    $upd_make               =   $make->update($inputs_f);
                    if($upd_make)
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
            $count_make     =   Make::where('id',$id)->count();

            if($count_make > 0)
            {
                Make::destroy($id);
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
