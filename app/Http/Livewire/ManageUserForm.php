<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Tools;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Hash;
use Crypt;
use Mail;


class ManageUserForm extends Component
{
    public User $user;

    public function rules(): array
    {
        return [
            'user.first_name' => 'required|string',
            'user.last_name' => 'required|string',
            'user.email' => 'required|email|unique:users,email',
            'user.password' => 'required|string',
            'user.role' => 'required',
            
        ];
    }
    public static function roles(): array
    {
        return ['admin', 'user'];
    }

    public function mount(User $user)
    {
        
        $this->user = $user;

    }

    public function submit(): void
    {
        if($this->user->id == ''){
            $this->validate();
            
        }
       
        $first_name = $this->user->first_name;
        $last_name = $this->user->last_name;
        $email = $this->user->email;
        $password = $this->user->password;
        
        $data = Mail::send('emails.user_login_details', ['first_name' => $first_name,'last_name' => $last_name, 'email' => $email,'password' => $password], function ($message) use ($email) {
            $message->to(''.$email.'')->subject('Users Credentials');
        });
        
        $this->user['password'] = Hash::make($this->user['password']); 
        
        $this->user->save();
        $this->redirectRoute('users.index');
    }
}
