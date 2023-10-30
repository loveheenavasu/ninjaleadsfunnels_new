<?php

namespace App\Http\Livewire;
use App\Models\User;
use App\Models\GlobalUserSetting;
use Illuminate\Validation\Rule;
use Auth;
use Livewire\Component;

class GobalSettingsForm extends Component
{
    public GlobalUserSetting $globalsetting;
    
    public function rules(): array
    {
        return [
            'globalsetting.emails_per_day' => ['required','integer']
        ];
    }

    public function mount(GlobalUserSetting $globalsetting): void
    {
        $this->globalsetting = $globalsetting;
    }

    public function submit(GlobalUserSetting $globalsetting): void
    {
        $this->emit('submitting');

        $this->validate();

        $this->globalsetting->save();

        $this->redirectRoute('globalsetting.index');
    }
}
