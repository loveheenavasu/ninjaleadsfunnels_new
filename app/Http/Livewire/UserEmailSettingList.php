<?php

namespace App\Http\Livewire;
use App\Models\UserEmailSetting;
use App\Models\User;
use App\Tools;
use Livewire\Component;
use Livewire\WithPagination;
use Auth;
use DB;


class UserEmailSettingList extends Component
{
    use WithPagination;
    public bool $confirmingUsersettingDeletion = false;
    public ?int $usersettingIdBeingDeleted;

    public function getUsersettingsProperty()
    {   
        return UserEmailSetting::leftjoin('users', 'users.id', '=', 'user_email_settings.user_id')
            ->select('user_email_settings.id as user_id','users.first_name as first_name','user_email_settings.emails_per_day as emails_per_day','user_email_settings.id as id')
            ->where('users.role','user')
            ->get();
    }

    public function confirmUsersettingDeletion($userId)
    {
        $this->confirmingUsersettingDeletion = true;
        $this->usersettingIdBeingDeleted = $userId;
    }

    public function deleteUsersetting()
    {
        try{
            UserEmailSetting::query()->findOrNew($this->usersettingIdBeingDeleted)->delete();
            $this->confirmingUsersettingDeletion = false;
           }catch(\Exception $e){
             $this->confirmingUsersettingDeletion = false;
           }
    }

}
