<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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
                 $cars  = Car::with(['trim','style','branch'])->get();
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
            'slc_fueltype_car'      =>          'required|integer|between:1,2',
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
                    'vin'           => $inputs['txt_vin_car'],
                    'stock_number'  => $inputs['txt_stcnumber_car'],
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
                    'url_info'      => $inputs['txt_url_car']
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
    }
}
