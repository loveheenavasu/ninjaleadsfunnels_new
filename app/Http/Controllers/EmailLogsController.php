<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailLogs;
use App\Models\InvalidEmail;
use App\Models\WebhookCron;
use DataTables;
use Auth;
use Illuminate\Support\Facades\File; 


class EmailLogsController extends Controller
{
    public function index(Request $request)
    {
    	if ($request->ajax())
		{
            $auth = Auth::user();
            if($auth->role == 'admin'){
                $allEmails = EmailLogs::leftjoin('users', 'users.id', '=', 'emails_logs.user_id')
                    ->select('users.first_name as first_name','emails_logs.*')
                    ->get();
            }
            else
            {
                $allEmails =EmailLogs::where('user_id',Auth::user()->id)->latest();
            }
		    //$allEmails = EmailLogs::latest();

		    return DataTables::of($allEmails)
                ->addColumn('id', function ($row) {
                    return $row->id;
                })
                ->addColumn('email', function ($row) {
                    return $row->email;
                })
                ->addColumn('first_name', function ($row) {
                    return $row->first_name;
                })
                ->addColumn('status', function ($row) {
                    return $row->status;
                })
                ->addColumn('timezone', function ($row) {
                    return $row->timezone;
                })
                ->addColumn('rule_number', function ($row) {
                    return $row->rule_number;
                })
                ->addColumn('rule_name', function ($row) {
                    return $row->rule_name;
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at;
                })
               ->make(true);
        }
        return view('emaillogs.index');
    }

    public function DeleteEmailLogs(Request $request){
            $auth = Auth::user();
            if($auth->role == 'admin'){
                $status = EmailLogs::whereDate('created_at','=',$request->date)->delete();
            }
            else
            {
                $status = EmailLogs::whereDate('created_at','=',$request->date)
               ->where('user_id',Auth::user()->id)
                ->delete();
            }
        if($status){
            return 1;
        }else{
            return 0;
        }
    }

     public function SetLogsDeleteCron(Request $request){
         $hours = $request['hours'];
         $user_id = Auth::user()->id;
         $exits = WebhookCron::where('user_id',$user_id)->first();
         if($exits){
            $update = WebhookCron::where('user_id',$user_id)
              ->update([
               'cron_time' => $hours,
               'status'  =>'yes'
             ]);
            
              if($update){
                 return 1;
              }else{
                return 0;
              }
            
         }else{
        $status = WebhookCron::create(array('user_id'=>$user_id,'cron_time'=>$hours));
        
        if($status){
            return 1;
        }else{
            return 0;
        }
      }
    }

     public function DeleteEmailLogs_invalid_email(Request $request){
        $auth = Auth::user();
            if($auth->role == 'admin'){
                $status = InvalidEmail::whereDate('created_at','=',$request->date)->delete();
            }
            else
            {
                $status = InvalidEmail::whereDate('created_at','=',$request->date)
               ->where('user_id',Auth::user()->id)
              ->delete();
            }
        
        if($status){
            return 1;
        }else{
            return 0;
        }
    }

    public function DeletelogsManaully(Request $request){
         if(File::exists(public_path('uploads/errorlogs.txt'))){
           //$file =  File::delete(public_path('uploads/errorlogs.txt'));
          $unlink = unlink(public_path('uploads/errorlogs.txt'));
           return $unlink;
        }else{
            return 0;
        }
    }
}
