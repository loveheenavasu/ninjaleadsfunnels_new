<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvalidEmail;
use DataTables;
use Auth;

class InvalidEmailController extends Controller
{
    public function index(Request $request)
    {
    	if ($request->ajax())
		{
            $auth = Auth::user();
            if($auth->role == 'admin'){
                 $allEmails = InvalidEmail::leftjoin('users', 'users.id', '=', 'invalid_emails.user_id')
                    ->select('users.first_name as first_name','invalid_emails.*')
                    ->get();
            }
            else
            {
                $allEmails =InvalidEmail::where('user_id',Auth::user()->id)->latest();
            }
            
		    return Datatables::of($allEmails)
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
        return view('invalidemail.index');
    }
}
