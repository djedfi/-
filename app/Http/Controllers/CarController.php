<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Models\Car;

class CarController extends Controller
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
             if(count(Car::all()) > 0)
             {
                 $cars  = Car::with(['trim','style','branch:id,name'])->get();
                 return \response()->json(['res'=>true,'data'=>$cars],200);
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
            'slc_trim_car'          =>          'required|exists:App\Models\Trim,id',
            'txt_year_car'          =>          'required|digits:4|integer|min:1990|max:'.(date('Y')+1),
            'txt_vin_car'           =>          'required|string|unique:cars,vin|max:17',
            'txt_stcnumber_car'     =>          'required|string|unique:cars,stock_number|max:8',
            'txt_ndoors_car'        =>          'required|integer|min:1|max:10',
            'txt_price_car'         =>          'required|regex:/^\d+(\.\d{1,2})?$/',
            'slc_branch_car'        =>          'required|exists:App\Models\Branch,id',
            'slc_style_car'         =>          'required|exists:App\Models\Style,id',
            'slc_transmi_car'       =>          'required|integer|min:1|max:3',
            'slc_condicion_car'     =>          'required|integer|between:1,2',
            'slc_fueltype_car'      =>          'required|integer|between:1,4',
            'txt_mileage_car'       =>          'required|integer',
            'hid_color_car'         =>          'required|string|max:7',
            'txt_engineinfo_car'    =>          'nullable|string|max:45',
            'txt_drivetrain_car'    =>          'nullable|string|max:45',
            'txt_fuelecono_car'     =>          'nullable|string|max:45',
            'txt_wheelsize_car'     =>          'nullable|string|max:45',
            'txt_url_car'           =>          'nullable|string|max:150'
        ];

        try
        {
            $inputs                     =   $request->all();
            $inputs['txt_price_car']    =   str_replace(array('US$ ',','),array('',''),$inputs['txt_price_car']);
            $inputs['txt_url_car']      =   'https://'.$inputs['txt_url_car'];
            $inputs['txt_mileage_car']  =   str_replace(',','',$inputs['txt_mileage_car']);

            $obj_validacion     = Validator::make($inputs,$rules);

            if(!$obj_validacion->fails())
            {
                $car       =   Car::create([
                    'trim_id'       => $inputs['slc_trim_car'],
                    'style_id'      => $inputs['slc_style_car'],
                    'branch_id'     => $inputs['slc_branch_car'],
                    'vin'           => Str::of($inputs['txt_vin_car']),
                    'stock_number'  => Str::of($inputs['txt_stcnumber_car']),
                    'year'          => $inputs['txt_year_car'],
                    'precio'          => $inputs['txt_price_car'],
                    'doors'         => $inputs['txt_ndoors_car'],
                    'color'         => $inputs['hid_color_car'],
                    'mileage'       => $inputs['txt_mileage_car'],
                    'transmission'  => $inputs['slc_transmi_car'],
                    'condition_car' => $inputs['slc_condicion_car'],
                    'fuel_type'     => $inputs['slc_fueltype_car'],
                    'fuel_economy'  => $inputs['txt_fuelecono_car'],
                    'engine'        => $inputs['txt_engineinfo_car'],
                    'drivetrain'    => $inputs['txt_drivetrain_car'],
                    'wheel_size'    => $inputs['txt_wheelsize_car'],
                    'url_info'      => $inputs['txt_url_car'],
                    'estado'        => 1
                ]);

                if($car->id > 0)
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
            if(Car::where('id',$id)->count())
            {
                $car = Car::with(['trim','style','branch:id,name'])->get()->find($id);
                return \response()->json(['res'=>true,'datos'=>$car],200);
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
            'slc_trim_car_upd'          =>          'required|exists:App\Models\Trim,id',
            'txt_year_car_upd'          =>          'required|digits:4|integer|min:1990|max:'.(date('Y')+1),
            'txt_vin_car_upd'           =>          'required|string|max:17|unique:cars,vin,'.$id,
            'txt_stcnumber_car_upd'     =>          'required|string|max:8|unique:cars,stock_number,'.$id,
            'txt_ndoors_car_upd'        =>          'required|integer|min:1|max:10',
             'txt_price_car_upd'         =>          'required|regex:/^\d+(\.\d{1,2})?$/',
            'slc_branch_car_upd'        =>          'required|exists:App\Models\Branch,id',
            'slc_style_car_upd'         =>          'required|exists:App\Models\Style,id',
            'slc_transmi_car_upd'       =>          'required|integer|min:1|max:3',
            'slc_condicion_car_upd'     =>          'required|integer|between:1,2',
            'slc_fueltype_car_upd'      =>          'required|integer|between:1,4',
             'txt_mileage_car_upd'       =>          'required|integer',
            'hid_color_car_upd'         =>          'required|string|max:7',
            'txt_engineinfo_car_upd'    =>          'nullable|string|max:45',
            'txt_drivetrain_car_upd'    =>          'nullable|string|max:45',
            'txt_fuelecono_car_upd'     =>          'nullable|string|max:45',
            'txt_wheelsize_car_upd'     =>          'nullable|string|max:45',
             'txt_url_car_upd'           =>          'nullable|string|max:150'
        ];

        try
        {
            $inputs                         =   $request->all();
            $inputs['txt_price_car_upd']    =   str_replace(array('US$ ',','),array('',''),$inputs['txt_price_car_upd']);
            $inputs['txt_url_car_upd']      =   'https://'.$inputs['txt_url_car_upd'];
            $inputs['txt_mileage_car_upd']  =   str_replace(',','',$inputs['txt_mileage_car_upd']);

            $obj_validacion     = Validator::make($inputs,$rules);

            if(!$obj_validacion->fails())
            {
                $car   =   Car::find($id);
                if($car->id == $id)
                {
                    $input_f['trim_id']       = $inputs['slc_trim_car_upd'];
                    $input_f['style_id']      = $inputs['slc_style_car_upd'];
                    $input_f['branch_id']     = $inputs['slc_branch_car_upd'];
                    $input_f['vin']           = Str::of($inputs['txt_vin_car_upd']);
                    $input_f['stock_number']  = Str::of($inputs['txt_stcnumber_car_upd']);
                    $input_f['year']          = $inputs['txt_year_car_upd'];
                    $input_f['precio']        = $inputs['txt_price_car_upd'];
                    $input_f['doors']         = $inputs['txt_ndoors_car_upd'];
                    $input_f['color']         = $inputs['hid_color_car_upd'];
                    $input_f['mileage']       = $inputs['txt_mileage_car_upd'];
                    $input_f['transmission']  = $inputs['slc_transmi_car_upd'];
                    $input_f['condition_car'] = $inputs['slc_condicion_car_upd'];
                    $input_f['fuel_type']     = $inputs['slc_fueltype_car_upd'];
                    $input_f['fuel_economy']  = $inputs['txt_fuelecono_car_upd'];
                    $input_f['engine']        = $inputs['txt_engineinfo_car_upd'];
                    $input_f['drivetrain']    = $inputs['txt_drivetrain_car_upd'];
                    $input_f['wheel_size']    = $inputs['txt_wheelsize_car_upd'];
                    $input_f['url_info']      = $inputs['txt_url_car_upd'];
                    $update_car               = $car->update($input_f);

                    if($update_car)
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


    public function CheckVIN(Request $request,$id)
    {
        if($id == 0)
        {
            $vin    = Str::of($request->txt_vin_car)->upper();
            if(Car::where('vin','=',$vin)->count() > 0)
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
            $vin_input        =   Str::of($request->txt_vin_car)->upper();
            $car        =   Car::where('id',$id)->first();

            if($car && ($car->vin == $vin_input))
            {
                return \response()->json(['res'=>false],200);
            }
            else
            {
                $car_check       =   Car::where('vin',$vin_input)->first();
                if($car_check)
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

    public function CheckSckNumber(Request $request,$id)
    {
        if($id == 0)
        {
            $stock    = Str::of($request->txt_stcnumber_car)->upper();

            if(Car::where('stock_number','=', $stock)->count() > 0)
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
            $scknumber_input        =   Str::of($request->txt_stcnumber_car)->upper();
            $car                    =   Car::where('id',$id)->first();
            if($car && ($car->stock_number == $scknumber_input))
            {
                return \response()->json(['res'=>false],200);
            }
            else
            {
                $car_check       =   Car::where('stock_number',$scknumber_input)->first();
                if($car_check)
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

    public function getCarTable()
    {
        try
        {
                $cars  =   DB::table('makes as mk')
                        ->join('modelos as md','md.make_id', '=', 'mk.id')
                        ->join('trims as tr', 'tr.modelo_id', '=', 'md.id')
                        ->join('cars as cr', 'cr.trim_id', '=', 'tr.id')
                        ->select('mk.id as id_make','md.id as id_modelo','tr.id as id_trim','cr.id as id_car','mk.name as name_make','md.name as name_modelo','tr.name as name_trim','cr.condition_car','cr.transmission','cr.fuel_type','cr.precio','cr.estado as estado_car')
                        ->get();

            if($cars->count())
            {
                return \response()->json(['res'=>true,'data'=>$cars],200);
            }
            else
            {
                return \response()->json(['res'=>true,'message'=>config('constants.msg_no_existe_srv')],200);
            }
        }
        catch(\Exception $e)
        {
            return \response()->json(['res'=>false,'message'=>$e],200);
        }
    }

    public function getFullCar($id)
    {
        try
        {
            $cars  =   DB::table('makes as mk')
                    ->join('modelos as md','md.make_id', '=', 'mk.id')
                    ->join('trims as tr', 'tr.modelo_id', '=', 'md.id')
                    ->join('cars as cr', 'cr.trim_id', '=', 'tr.id')
                    ->select('mk.id as id_make','md.id as id_modelo','tr.id as id_trim','cr.id as id_car','mk.name as name_make','md.name as name_modelo','tr.name as name_trim','cr.*')
                    ->where('cr.id',$id)
                    ->get();

            if($cars->count())
            {
                return \response()->json(['res'=>true,'data'=>$cars],200);
            }
            else
            {
                return \response()->json(['res'=>true,'message'=>config('constants.msg_no_existe_srv')],200);
            }
        }
        catch(\Exception $e)
        {
            return \response()->json(['res'=>false,'message'=>$e],200);
        }
    }

}
