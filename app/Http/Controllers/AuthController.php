<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserOption;
use App\Models\PasswordReset;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\MailerController;

class AuthController extends Controller
{

    //
    public function register(Request $request)
    {
        $array_final   = array();

        $rules = [
            'slc_branch_user'   =>        'required|exists:App\Models\Branch,id',
            'txt_fname_user'     =>       'required|string|max:50',
            'txt_lname_user'     =>       'required|string|max:50',
            'txt_email_user'     =>       'required|string|max:150|unique:users,email|confirmed',
            'txt_position_user'  =>       'required|string|max:150'
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

                $user       =   User::create([
                    'branch_id'     => $inputs['slc_branch_user'],
                    'first_name'    => $inputs['txt_fname_user'],
                    'last_name'     => $inputs['txt_lname_user'],
                    'email'         => $inputs['txt_email_user'],
                    'cargo'         => $inputs['txt_position_user']
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
                    $url_token      =   Crypt::encryptString($inputs['txt_email_user']);
                    $user_id        =   $user->id;
                    $user_name      =   $inputs['txt_fname_user'].' '.$inputs['txt_lname_user'];
                    $user_email     =   $inputs['txt_email_user'];

                    //$user->update(array('password'=>NULL));

                    $new_rpassword = new PasswordReset;
                    $new_rpassword->user_id             =   $user_id;
                    $new_rpassword->token_signature     =   $url_token;
                    $new_rpassword->token_type          =   20;
                    $new_rpassword->expires_at          =   Carbon::now()->addDays(7);
                    $new_rpassword->save();

                    $new_email  =   new MailerController;

                    $new_email->composeEmail($user_email,$user_name,3,env('APP_URL').'/frontend-auto/?mod=create_password&token='.$url_token);
                    DB::commit();
                    return \response()->json(['res'=>true,'message'=>config('constants.msg_new_srv')],200);
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
                $user       =   User::with(['user_options:id,name,path_option,group_option,icono'])->where('email',$inputs['txt_email_usr'])->first();
                //check pass
                if(!$user || !Hash::check($inputs['txt_pass_usr'],$user->password))
                {
                    return \response()->json(['res'=>false,'message'=>'Incorrect information'],200);
                }
                else
                {
                    $token = $user->createToken(env('APP_KEY'))->plainTextToken;
                    Log::info("Hace login el usuario: ".$user." con token ".$token);
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


    public function reset_password(Request $request)
    {
        $rules = [
            'txt_email_rst'          =>       'required|string|max:150'
        ];
        $inputs      =   $request->all();
        DB::beginTransaction();
        try
        {
            $obj_validacion     = Validator::make($inputs,$rules);
            if(!$obj_validacion->fails())
            {
                //check emial
                $user       =   User::where('email',$inputs['txt_email_rst'])->first();

                if(!$user)
                {
                    return \response()->json(['res'=>false,'message'=>'Incorrect information'],200);
                }
                else
                {

                    $url_token      =   Crypt::encryptString($inputs['txt_email_rst']);
                    $user_id        =   $user->id;
                    $user_name      =   $user->first_name.' '.$user->last_name;
                    $user_email     =   $user->email;

                    //$user->update(array('password'=>NULL));

                    $new_rpassword = new PasswordReset;
                    $new_rpassword->user_id             =   $user_id;
                    $new_rpassword->token_signature     =   $url_token;
                    $new_rpassword->token_type          =   10;
                    $new_rpassword->expires_at          =   Carbon::now()->addMinutes(30);
                    $new_rpassword->save();

                    $new_email  =   new MailerController;

                    $new_email->composeEmail($user_email,$user_name,1,env('APP_URL').'/frontend-auto/?mod=reset_password&token='.$url_token);
                    DB::commit();
                    return \response()->json(['res'=>true,'message'=>'We have sent an email with the instructions'],200);
                }
            }
            else
            {
                return \response()->json(['res'=>false,'message'=>$obj_validacion->errors()],200);
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return \response()->json(['res'=>false,'message'=>$e],200);
        }
    }

    public function save_password(Request $request)
    {
        $inputs      =   $request->all();
        $rules =    [
                        'hid_tipo_token_rst' =>
                            [
                                'required','integer',
                                Rule::in([10,20]),
                            ],
                        'txt_pass_rst' =>
                            [
                                'required','string',
                                Password::min(8)
                                    ->mixedCase()
                                    ->numbers(),
                                'confirmed'
                            ]
                    ];
        DB::beginTransaction();

        try
        {
            $obj_validacion     = Validator::make($inputs,$rules);
            if(!$obj_validacion->fails())
            {
                $email_des =    Crypt::decryptString($inputs['hid_token_rst']);
                $user      =    User::where('email',$email_des)->first();

                if($user)
                {
                    $passwordreset  = PasswordReset::where('user_id',$user->id)->where('token_signature',$inputs['hid_token_rst'])->where('token_type',$inputs['hid_tipo_token_rst'])->first();

                    if($passwordreset->used_token != '')
                    {
                        return \response()->json(['res'=>false,'message'=>'The password reset code was used before'],200);
                    }
                    else if(Carbon::now()->greaterThan($passwordreset->expires_at))
                    {
                        return \response()->json(['res'=>false,'message'=>'The password reset code given has expired'],200);
                    }
                    else
                    {
                        $user->update(array('password'=>Hash::make($inputs['txt_pass_rst'])));
                        $passwordreset->update(array('used_token'=>$user->id));

                        $new_email  =   new MailerController;
                        $new_email->composeEmail($user->email,$user->first_name.' '.$user->last_name,2,env('APP_URL').'/frontend-auto/?mod=login');
                        DB::commit();
                        return \response()->json(['res'=>true,'message'=>'Your password has been updated succesfully.'],200);
                    }
                }
                else
                {
                    DB::rollback();
                    return \response()->json(['res'=>false,'message'=>'Invalid password reset code'],200);
                }
            }
            else
            {
                DB::rollback();
                return \response()->json(['res'=>false,'message'=>'Error: The form was changed'],200);
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return \response()->json(['res'=>false,'message'=>'Your token does not exist.'],200);
        }
    }


    public function CheckEmail(Request $request,$id)
    {
        if($id == 0)
        {
            $email    = $request->txt_email_user;

            if(User::where('email','=',$email)->count() > 0)
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
            $email_input=   $request->txt_email_user;
            $user       =   User::where('id',$id)->first();


            if($user && ($user->email == $email_input))
            {
                return \response()->json(['res'=>false],200);
            }
            else
            {
                $user_check       =   User::where('email',$email_input)->first();
                if($user_check)
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
