<?php

namespace App\Http\Controllers;
use App\Models\UserEmailSetting;
use Illuminate\Http\Request;

class UserEmailSettingController extends Controller
{
    
    public function index()
    {
       return view('useremailsetting.index');
    }

    public function create()
    {
        return view('useremailsetting.create');
    }

    public function edit(UserEmailSetting $useremailsetting)
    {
       return view('useremailsetting.edit', compact('useremailsetting'));
    }

}
