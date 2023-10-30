<?php

namespace App\Http\Livewire;
use App\Models\User;
use App\Models\GlobalUserSetting;
use Livewire\Component;
use Livewire\WithPagination;

class GobalSettingsList extends Component
{

    use WithPagination;
    public bool $confirmingGlobalsettingDeletion = false;
    public ?int $globalsettingIdBeingDeleted;

    public function getSettingsProperty()
    {    
        return GlobalUserSetting::paginate(10);
    }

    public function confirmGlobalsettingDeletion($settingId)
    {
        $this->confirmingGlobalsettingDeletion = true;
        $this->globalsettingIdBeingDeleted = $settingId;
    }

    public function deleteGlobalsetting()
    {
        try{
            GlobalUserSetting::query()->findOrNew($this->globalsettingIdBeingDeleted)->delete();
            $this->confirmingGlobalsettingDeletion = false;
           }catch(\Exception $e){
             $this->confirmingGlobalsettingDeletion = false;
           }
    }
}
