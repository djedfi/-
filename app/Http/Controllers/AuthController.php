<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Validator;

class AuthController extends Controller
{

    //
    public function regiter(Request $request)
    {
        $rules = [
            'first_name'     =>       'required|string|max:50',
            'last_name'      =>       'required|string|max:50',
            'email'          =>       'required|string|max:150|unique:users,email',
            'password'       =>       'required|string|confirmed'
        ];
        $inputs      =   $request->all();

        try
        {
            $obj_validacion     = Validator::make($inputs,$rules);
            if(!$obj_validacion->fails())
            {
                $user       =   User::create([
                    'first_name'    => $inputs['first_name'],
                    'last_name'     => $inputs['last_name'],
                    'email'         => $inputs['email'],
                    'password'      => bcrypt($inputs['password'])
                ]);
                $token = $user->createToken(env('APP_KEY'))->plainTextToken;

                if($user->id > 0)
                {
                    return \response()->json(['res'=>true,'message'=>config('constants.msg_new_srv'),'token'=>$token],200);
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
