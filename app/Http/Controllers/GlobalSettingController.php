<?php

namespace App\Http\Controllers;
use App\Models\GlobalUserSetting;
use Illuminate\Http\Request;

class GlobalSettingController extends Controller
{
    
    public function index()
    {
       return view('globalsetting.index');
    }
    
    public function create()
    {
        return view('globalsetting.create');
    }

    public function edit(GlobalUserSetting $globalsetting)
    {
        return view('globalsetting.edit', compact('globalsetting'));
    }

    
}
