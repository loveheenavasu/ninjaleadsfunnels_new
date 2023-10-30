<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\EmailLogs;
use App\Tools;
use Auth;
use Livewire\WithPagination;

class EmailLogsList extends Component
{
	use WithPagination;
    public function getEmaillogsProperty()
    {
        $auth = Auth::user();
        if($auth->role == 'admin'){
            return EmailLogs::latest('id')->paginate(100);
        }
        else
        {
            return EmailLogs::where('user_id',Auth::user()->id)->latest('id')->paginate(100);
        } 
        
    }
}