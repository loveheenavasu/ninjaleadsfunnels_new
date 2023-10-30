<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Hash;

class UserController extends Controller
{

    public function index()
    {
        return view('users.index');
    }

    public function create()
    {
        return view('users.create');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }


    public function update_password(Request $request)
    {
        $new_password = $request->new_password;
        $user_id =$request->user_id;

        $data = User::where('id',$user_id)->update(['password'=> Hash::make($new_password)]);
        $send_data = User::where('id',$user_id)->get()->toArray();
        $first_name = $send_data[0]['first_name'];
        $last_name = $send_data[0]['last_name'];
        $email = $send_data[0]['email'];
        $password = $new_password;
        $data = Mail::send('emails.user_login_details', ['first_name' => $first_name,'last_name' => $last_name, 'email' => $email,'password' => $password], function ($message) use ($email) {
            $message->to(''.$email.'')->subject('Users Credentials');
        });
        
        
    }
}
