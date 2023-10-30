<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WebhookCron;
use Auth;

class CronController extends Controller
{
    
    public function index()
    {
        //
        $time = WebhookCron::where('user_id',Auth::user()->id)->get()->toArray();
        if($time){
            
            return view('cron.index')->with(['time'=>$time[0]['cron_time']]);
        }
        else{

            return view('cron.index');
        }
        
    }

    public function create()
    {
        //
    }

    
    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function SetLogsResetCron(Request $request){
       $update = WebhookCron::where('user_id', Auth::user()->id)
              ->update([
               'status' => 'no',
               'cron_time'=>0
             ]); 
              if($update){
                 return 1;
              }else{
                return 0;
              }
    }
}
