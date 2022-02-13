<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\UserOption;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class UserController extends Controller
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
            if(count(User::all()) > 0)
            {
                return \response()->json(['res'=>true,'data'=>User::with(['user_options:id,name'])->get()],200);
            }
            else
            {
                return \response()->json(['res'=>false,'data'=>[],'message'=>config('constants.msg_empty')],200);
            }
        }
        catch(\Exception $e)
        {
            return \response()->json(['res'=>false,'data'=>[],'message'=>$e],200);
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
        $rules = [
            'branch_id'     =>        'required|exists:App\Models\Branch,id',
            'first_name'     =>       'required|string|max:50',
            'last_name'      =>       'required|string|max:50',
            'email'          =>       'required|max:150|unique:users,email',
            'password'       =>       'required|string|confirmed'
        ];

        try
        {
            $obj_validacion     = Validator::make($request->all(),$rules);

            if(!$obj_validacion->fails())
            {
                $input              =   $request->all();
                $auto       =   User::create($input);

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
        try
        {
            if(User::where('id',$id)->count())
            {
                $user = User::with(['user_options:id,name'])->get()->find($id);
                return \response()->json(['res'=>true,'datos'=>$user],200);
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
        $rules = [
            'slc_branch_update_user'        =>       'required|exists:App\Models\Branch,id',
            'txt_fname_update_user'         =>       'required|string|max:50',
            'txt_lname_update_user'         =>       'required|string|max:50',
            'txt_email_update_user'         =>       'required|max:150|unique:users,email,'.$id,
            'txt_position_update_user'      =>       'required|string|max:150'
        ];

        $input_f            =   array();
        $input              =   $request->all();
        $hid_id_option      =   $input['hid_options_update_usr'];
        $collect_hid        =   Str::of($hid_id_option)->explode(',');

        DB::beginTransaction();
        try
        {
            $obj_validacion     = Validator::make($input,$rules);

            if(!$obj_validacion->fails())
            {
                $user   =   User::find($id);
                if($user->id == $id)
                {
                    $input_f['branch_id']   =   $input['slc_branch_update_user'];
                    $input_f['first_name']  =   $input['txt_fname_update_user'];
                    $input_f['last_name']   =   $input['txt_lname_update_user'];
                    $input_f['email']       =   $input['txt_email_update_user'];
                    $input_f['cargo']       =   $input['txt_position_update_user'];
                    $auto_user              =   $user->update($input_f);

                    if($auto_user)
                    {
                        UserOption::where('user_id',$id)->delete();

                        foreach($collect_hid as $valor)
                        {
                            UserOption::create([
                                'user_id' => $user->id,
                                'option_id' => $valor
                            ]);
                        }
                        DB::commit();
                        return \response()->json(['res'=>true,'message'=>config('constants.msg_ok_srv')],200);
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
                    return \response()->json(['res'=>false,'message'=>config('constants.msg_error_existe_srv')],200);
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
        try
        {
            $count_user     =   User::where('id',$id)->count();

            if($count_user > 0)
            {
                User::destroy($id);
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
