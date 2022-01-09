<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserOption;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{

    //
    public function register(Request $request)
    {
        $array_final   = array();

        $rules = [
            'txt_fname_user'     =>       'required|string|max:50',
            'txt_lname_user'     =>       'required|string|max:50',
            'txt_email_user'     =>       'required|string|max:150|unique:users,email|confirmed'
        ];
        $inputs      =   $request->all();

        $hid_id_option  =    $inputs['hid_options_usr'];
        $collect_hid    =    Str::of($hid_id_option)->explode(',');
        DB::beginTransaction();
        try
        {
            $obj_validacion     = Validator::make($inputs,$rules);
            if(!$obj_validacion->fails())
            {
                $temp_password = Str::random(8);
                $temp_password_hash = Hash::make($temp_password);

                $user       =   User::create([
                    'first_name'    => $inputs['txt_fname_user'],
                    'last_name'     => $inputs['txt_lname_user'],
                    'email'         => $inputs['txt_email_user'],
                    'password'      => $temp_password_hash
                ]);

                if($user->id > 0)
                {
                    foreach($collect_hid as $valor)
                    {
                        UserOption::create([
                            'user_id' => $user->id,
                            'option_id' => $valor
                        ]);
                    }
                    //enviar correo electronico

                    DB::commit();
                    return \response()->json(['res'=>true,'message'=>config('constants.msg_new_srv'),'pass'=>$temp_password],200);
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


    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return \response()->json(['res'=>true,'message'=>'SESSION DESTROYED'],200);
    }


    public function login(Request $request)
    {
        $rules = [
            'txt_email_usr'          =>       'required|string|max:150',
            'txt_pass_usr'           =>       'required|string'
        ];
        $inputs      =   $request->all();

        try
        {
            $obj_validacion     = Validator::make($inputs,$rules);
            if(!$obj_validacion->fails())
            {
                //check emial
                $user       =   User::where('email',$inputs['txt_email_usr'])->first();
                //check pass
                if(!$user || !Hash::check($inputs['txt_pass_usr'],$user->password))
                {
                    return \response()->json(['res'=>false,'message'=>'Incorrect information'],200);
                }
                else
                {
                    $token = $user->createToken(env('APP_KEY'))->plainTextToken;
                    return \response()->json(['res'=>true,'user'=>$user,'token'=>$token],200);
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

}
