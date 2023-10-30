<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Tools;
use Livewire\Component;
use Livewire\WithPagination;
use Auth;
use DB;


class ManageUserList extends Component
{
    use WithPagination;
    public bool $confirmingUserDeletion = false;
    public ?int $userIdBeingDeleted;

    public function render()
    {
        return view('livewire.manage-user-list');
    }

    public function getUsersProperty()
    {
        $auth = Auth::user();
        if($auth->role == 'admin'){
            return User::paginate(10);
        }
        
    }

    public function confirmUserDeletion($userId)
    {
        $this->confirmingUserDeletion = true;
        $this->userIdBeingDeleted = $userId;
    }

    public function deleteUser()
    {
        try{
            User::query()->findOrNew($this->userIdBeingDeleted)->delete();
            $this->confirmingUserDeletion = false;
           }catch(\Exception $e){
             $this->confirmingUserDeletion = false;
           }
    }



}
