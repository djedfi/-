<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Modelo;
use Illuminate\Support\Facades\Validator;

class ModeloController extends Controller
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
            if(count(Modelo::all()) > 0)
            {
                $modelos  = Modelo::with(['make:id,name,website'])->orderBy('name','asc')->get();
                return \response()->json(['res'=>true,'data'=>$modelos],200);
            }
            else
            {
                return \response()->json(['res'=>false,'data'=>[],'message'=>config('constants.msg_empty')],200);
            }
        }
        catch(\Exception $e)
        {
            return \response()->json(['res'=>false,'data'=>[],'message'=>config('constants.msg_error_srv')],200);
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
            'slc_brand_md'    =>       'required|exists:App\Models\Make,id',
            'txt_name_md'     =>       'required|string|max:45'
        ];

        try
        {
            $inputs              =   $request->all();

            $obj_validacion     = Validator::make($inputs,$rules);

            if(!$obj_validacion->fails())
            {
                $inputs_f['make_id']       =   $inputs['slc_brand_md'];
                $inputs_f['name']          =   $inputs['txt_name_md'];
                $modelo       =   Modelo::create($inputs_f);

                if($modelo->id > 0)
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

        try
        {
            if(Modelo::where('id',$id)->count())
            {
                $modelo = Modelo::with(['make'])->find($id);
                return \response()->json(['res'=>true,'datos'=>$modelo],200);
            }
            else
            {
                return \response()->json(['res'=>true,'datos'=>[],'message'=>config('constants.msg_no_existe_srv')],200);
            }
        }
        catch(\Exception $e)
        {
            return \response()->json(['res'=>false,'datos'=>[],'message'=>config('constants.msg_error_srv')],200);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getModeloByMake($id)
    {
        //
        try
        {
            if(Modelo::where('make_id',$id)->count())
            {
                $modelo = Modelo::where('make_id',$id)->orderBy('name','asc')->get();
                return \response()->json(['res'=>true,'datos'=>$modelo],200);
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
            'slc_brand_md_up'    =>       'required|exists:App\Models\Make,id',
            'txt_name_md_up'     =>       'required|string|max:45'
        ];

        $inputs              =   $request->all();
        try
        {
            $obj_validacion     = Validator::make($inputs,$rules);

            if(!$obj_validacion->fails())
            {
                $modelo   =   Modelo::find($id);
                if($modelo->id == $id)
                {
                    $inputs_f['make_id']        =   $inputs['slc_brand_md_up'];
                    $inputs_f['name']           =   $inputs['txt_name_md_up'];
                    $upd_modelo                 =   $modelo->update($inputs_f);
                    if($upd_modelo)
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
            $count_modelo     =   Modelo::where('id',$id)->count();

            if($count_modelo > 0)
            {
                Modelo::destroy($id);
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
