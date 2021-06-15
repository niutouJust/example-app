<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $result = User::all();

        return $result;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return bool
     */
    public function create(Request $request)
    {
        $user = new User();
        $user->name = $request->post('name');
        $user->user_name = $request->post('user_name');
        $user->password = md5($request->post('password'));
        $user->avatar = $request->post('avatar');
        $user->user_role = $request->post('user_role');
        $user->email = $request->post('email');
        $status = $user->save();

        if ($status){
            // send mail
            $this->sendMail($request->post('email'));
            print_r("success and check mail please ".$status);
        }else
            {
            print_r("reg fail and try again ".$status);
        }

    }

    public function sendMail($email){

        $checkStr = md5(md5($email));

        $web = "http://localhost/users/verify/";

        $url = $web . $checkStr;

        Redis::lpush($checkStr, $email);

        Mail::raw("Please Verify This Email : ".$url, function ($m) use($email) {
            $m->from('test1@kungfudingcan.com', 'User');
            $m->to($email)->subject('please check your email and verify ');
        });

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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request)
    {
        $user = new User();

        $userArray['name'] = $request->post('name');
        $userArray['user_name'] = $request->post('user_name');
        $userArray['avatar'] = $request->post('avatar');
        $userArray['user_role']  = $request->post('user_role');
        $where['id'] = $id;
        $status = $user->where($where)->update($userArray);

        if ($status){
            print_r("success update". $status);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = new User();
        $where['id'] = $id;
        $result = $user->where($where)->delete();

        print_r("Success delete ". $result);
    }

    /**
     * Verify the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function verify($str)
    {

        $strValue = Redis::lpop($str);

        if ($strValue){
            $user = new User();
            $where['email'] = $strValue;
            $result = $user->where($where)->get();
            if ($result){
                print_r("Success verify");
                $update['email_verified_at'] = date('Y-m-d H:i:s',time());
                $user->where($where)->update($update);
            }else{
                print_r("Fail verify");
            }
        }else{
            print_r("Not Verify");
        }

    }

    /**
     * Verify the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function read($id)
    {
       $user = new User();
       $where['id'] = $id;
       $result = $user->where($where)->get();

       return $result;

    }

}
