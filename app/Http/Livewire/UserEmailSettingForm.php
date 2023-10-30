<?php

namespace App\Http\Livewire;
use App\Models\UserEmailSetting;
use App\Models\User;
use App\Tools;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Hash;
use Auth;


class UserEmailSettingForm extends Component
{
    public $editcase = 'no';
    public $ediatble_id = '';
    public UserEmailSetting $useremailsetting;

    public function rules(): array
    {
        return [
            'useremailsetting.user_id' => 'required||unique:user_email_settings,user_id',
            'useremailsetting.emails_per_day' => 'required|string',
        ];
    }

    public function getUsersProperty()
    {
        return User::where('role','user')->get();
        
    }

    public function mount(UserEmailSetting $useremailsetting)
    {   
       
        $this->useremailsetting = $useremailsetting;
        if(!empty($useremailsetting->user_id)){
            $this->editcase = 'yes';
            $this->ediatble_id = $useremailsetting->user_id;
        }
        
     
    }
    public function submit()
    {

        if($this->useremailsetting->id == ''){
            $this->validate();
        }
        
        $this->useremailsetting->save();
        $this->redirectRoute('useremailsetting.index');
    }

}
