<?php

namespace App\Http\Livewire;

use Illuminate\Console\Command;
use App\Models\Rule;
use Livewire\Component;
use App\Models\ListingEmail;
use App\Models\MauticLogs;
use App\Models\Email;
use App\Models\InvalidEmail;
use App\Models\EmailLogs;
use App\Models\Connection;
use App\Models\RuleAction;
use App\Models\Listing;
use Faker\Generator;
use App\Models\EmailInfo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use DB;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use App\Mail\MailtrapExample;
use Illuminate\Support\Facades\Mail;
use voku\helper\HtmlDomParser;


class RuleList extends Component
{
    use WithPagination;
    public bool $confirmingRuleDeletion = false;
    public ?Rule $ruleBeingDeleted = null;

    public function getRulesProperty()
    {
      $auth = Auth::user();
        if($auth->role == 'admin'){
            return Rule::leftjoin('users', 'users.id', '=', 'rules.user_id')
            ->select('users.first_name as first_name','rules.*')
            ->paginate(10);
        }
        else
        {
            return Rule::where('user_id',Auth::user()->id)->paginate(10);
        }  
    }

    public function startRule(Rule $rule): void
    {
        $rule->update([
            'status' => Rule::STATUS_RUNNING
        ]);

        //$this->webScraping($rule);
    }

    public function stopRule(Rule $rule): void
    {
        $rule->update([
            'status' => Rule::STATUS_STOPPED
        ]);
    }

    public function cloneRule(Rule $rule): void
    {
        $new = $rule->replicate();
        $new->name = $new->name . ' (Copy)';
        $new->status = Rule::STATUS_STOPPED;
        $new->save();
    }

    public function confirmRuleDeletion(Rule $rule): void
    {
        $this->confirmingRuleDeletion = true;
        $this->ruleBeingDeleted = $rule;
    }

    public function deleteRule(): void
    {
        $this->ruleBeingDeleted->delete();
        $this->confirmingRuleDeletion = false;
    }

    public function isSiteAvailible($checkUrl){
      // Check, if a valid url is provided
      if(!filter_var($checkUrl, FILTER_VALIDATE_URL)){
          return false;
      }

      // Initialize cURL
      $curlInit = curl_init($checkUrl);
      
      // Set options
      curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
      curl_setopt($curlInit,CURLOPT_HEADER,true);
      curl_setopt($curlInit,CURLOPT_NOBODY,true);
      curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);

      // Get response
      $response = curl_exec($curlInit);
      
      // Close a cURL session
      curl_close($curlInit);
      return $response?true:false;
    }
    

  public function webScraping(Rule $rule){
      $this->rule = $rule;
      $group = $this->rule->id;
      $allRules = Rule::where('id',$group)->get();
      $valid_count = '';
      $neverBounce_key = env('NEVERBOUNCE_API_KEY');
      if(!empty($allRules)){
        foreach ($allRules as $key => $allRule) {
          $checkUrl = Connection::where('id',$allRule->connection_id)->first()->base_url;
          //$checkUrl = 'https://mwilson.lpages.co/free-course/';
          if($this->isSiteAvailible($checkUrl)){
            if($allRule->status == 'running'){
              $connection_id = $allRule['connection_id'];
              $rule_id = $allRule['id'];
              $emails_count = $allRule['emails_count']; 
              $final_array = [];
              $allEmails = [];
              $valid_emails=[];
              $invalid_emails=[];
              $all_lists = DB::table('listing_rule')
                                      ->leftjoin('rules as r','r.id','=','listing_rule.rule_id')
                                      ->where('rule_id',$rule_id)
                                      ->get();

              if(!empty($all_lists)){
                foreach ($all_lists as $key => $all_list) { 
                  $allEmailsInfos = ListingEmail::where('listing_id',$all_list->listing_id)
                      ->join('emails as e','e.id','=','listing_email.email_id')
                      ->leftjoin('email_infos as ef','ef.email_id','=','e.id')
                      ->where('e.sync_status','no')
                      ->select('e.id as email_id','e.email as email','ef.value','ef.type as type','listing_id as listing_id')->get()->toArray();
                      foreach ($allEmailsInfos as $allEmailsInfo) {
                      $final_array[$allEmailsInfo['email']][$allEmailsInfo['type']] = $allEmailsInfo['value'];
                      $final_array[$allEmailsInfo['email']]['rule_number'] = $all_list->rule_id;
                      $final_array[$allEmailsInfo['email']]['rule_name'] = $all_list->name;
                      $final_array[$allEmailsInfo['email']]['timezone'] = $all_list->timezone;
                      $final_array[$allEmailsInfo['email']]['listing_id'] = $allEmailsInfo['listing_id'];
                      $final_array[$allEmailsInfo['email']]['email_id'] = $allEmailsInfo['email_id'];
                    }
                    $checkemail = 0;
                    $calEmail = 1;
                    $allEmailLogsArray = [];
                    $allInvalidEmailArray = [];
                    $allEmailLogsArray = EmailLogs::where('rule_number', $rule_id)->whereDate('created_at', Carbon::today())->get()->toArray();
                    $allInvalidEmailArray = InvalidEmail::where('rule_number', $rule_id)->whereDate('created_at', Carbon::today())->get()->toArray();
                    $get_allemail = array_merge($allEmailLogsArray,$allInvalidEmailArray);
                    $allSyncMail = count($get_allemail);
                    $checkActionExist = RuleAction::where('rule_id',$rule_id)->whereDate('created_at', Carbon::today())->get()->first();
                    $actionStatus = array('rule_id'=>$rule_id,'emails_count' => $emails_count);
                    if(!empty($checkActionExist)){
                      RuleAction::where('id',$checkActionExist->id)->update($actionStatus);
                    }else{
                        if($allSyncMail == $emails_count){
                          RuleAction::create($actionStatus);
                        }
                    }

                    foreach ($final_array as $key => $value) {
                      if($checkemail  < $calEmail && $allSyncMail <= $emails_count){ 
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                          CURLOPT_URL => 'https://api.neverbounce.com/v4/single/check?key='.$neverBounce_key.'&email='.$key.' ',
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_ENCODING => '',
                          CURLOPT_MAXREDIRS => 10,
                          CURLOPT_TIMEOUT => 0,
                          CURLOPT_FOLLOWLOCATION => true,
                          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                          CURLOPT_CUSTOMREQUEST => 'GET',
                        ));
                        $response = curl_exec($curl);
                        
                        curl_close($curl);
                        $validation_check = json_decode($response);
                        if($validation_check->result == 'invalid'){
                          $invalid_emails = array('email'=>$key,'status'=>$validation_check->result,'rule_number' => $value['rule_number'],'rule_name' => $value['rule_name'],'timezone' => $value['timezone'],'user_id'=> Auth::user()->id
                          );
                          $checkMail = InvalidEmail::where([
                                                          ['email', '=', $key],
                                                          ['user_id','=', Auth::user()->id],
                                                          ['rule_number', '=', $value['rule_number']]
                                                          ])->first();
                          $listemail_status = array('in_pool'=>0);
                          DB::table('listing_email')
                              ->where('email_id',$value['email_id'])
                              ->update($listemail_status);

                          if(empty($checkMail)){
                                $InvalidEmail = InvalidEmail::create($invalid_emails);
                                $sync_status = array('sync_status'=>'yes');
                                  Email::where('email',$key)
                                    ->update($sync_status);
                            }else{
                                $InvalidEmail = InvalidEmail::where('id',$checkMail->id)->update($invalid_emails);
                                $sync_status = array('sync_status'=>'yes');
                                  Email::where('email',$key)
                                    ->update($sync_status);
                            }
                        }
                        else{
                            $channel = isset($value['channel']) ? $value['channel'] : '';
                          $profession = isset($value['profession']) ? $value['profession'] : '';
                          $country = isset($value['country']) ? $value['country'] : '';
                          $state = isset($value['state']) ? $value['state'] : '';
                          $city = isset($value['city']) ? $value['city'] : '';
                          $rule_number = isset($value['rule_number']) ? $value['rule_number'] : '';

                          $rule_name = isset($value['rule_name']) ? $value['rule_name'] : '';

                          $timezone = isset($value['timezone']) ? $value['timezone'] : '';
                          $listing_id = isset($value['listing_id']) ? $value['listing_id'] : '';
                          $email_id = isset($value['email_id']) ? $value['email_id'] : '';

                         $valid_emails[] = array('email'=>$key,'channel' => $channel,'profession'=>$profession,'country'=>$country,'city'=>$city,'state' => $state,'rule_number' => $rule_number,'rule_name' => $rule_name,'timezone' => $timezone,'listing_id' =>$listing_id,'email_id' => $email_id);
                          }
                        }
                      }
                      foreach($valid_emails as $values){
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $checkUrl);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $result = curl_exec($ch);
                        curl_close($ch);
                        $html = HtmlDomParser::str_get_html($result);
                        
                        $titles = array();
                          $i = 0 ;
                          foreach($html->find('form') as $post) { 
                              $Url = $post->action;    
                          }
                          $i = 0 ;
                          foreach($html->find('input[name=EMAIL]') as $input) {      
                              $titles[$i]['EMAIL'] = $input->value=$values['email'];             
                              $i++;
                          }
                          $i = 0 ;
                          foreach($html->find('input[name=CITY]') as $input) {      
                              $titles[$i]['CITY'] = $input->value=$values['city'];             
                              $i++;
                          }
                          $i = 0 ;
                          foreach($html->find('input[name=STATE]') as $input) {      
                              $titles[$i]['STATE'] = $input->value=$values['state'];             
                              $i++;
                          }
                          $i = 0 ;
                          foreach($html->find('input[name=COUNTRY]') as $input) {
                              $titles[$i]['COUNTRY'] = $input->value=$values['country'];             
                              $i++;
                          }
                          $i = 0 ;
                          foreach($html->find('input[name=PROFESSION]') as $input) {
                              $titles[$i]['PROFESSION'] = $input->value=$values['profession'];             
                              $i++;
                          }
                          $i = 0 ;
                          foreach($html->find('input[name=CHANNEL]') as $input) { 
                              $titles[$i]['CHANNEL'] = $input->value=$values['channel'];             
                              $i++;
                          }
                         
                          $ch = curl_init($Url);
                          curl_setopt($ch, CURLOPT_POST, true);
                          curl_setopt($ch, CURLOPT_POSTFIELDS, $titles[0]);
                          curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
                          curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);    
                          $response1 = curl_exec($ch);
                            if($response1){
                              $user_id = Auth::user()->id;
                              $emailLogs = array('user_id'=> $user_id,'email' => $values['email'],'status' => 'sucesss','rule_name' => $values['rule_name'],'rule_number' => $values['rule_number'],'timezone' => $values['timezone']);
                                EmailLogs::create($emailLogs);
                
                                $sync_status = array('sync_status'=>'yes');
                                Email::where('email',$values['email'])
                                ->update($sync_status);

                                $listemail_status = array('in_pool'=>0);
                                DB::table('listing_email')
                                    ->where('email_id',$values['email_id'])
                                    ->update($listemail_status);

                                $updateList = Listing::where('id',$all_list->listing_id)->first();

                                if(isset($updateList->valid_emails)){
                                  $valid_count = $updateList->valid_emails+1;
                                  $valid_array = array('valid_emails'=>$valid_count);
                                  Listing::where('id',$all_list->listing_id)->update($valid_array);
                                }else{
                                  $valid_count = 1;
                                  $valid_array = array('valid_emails'=>$valid_count);
                                  Listing::where('id',$all_list->listing_id)->update($valid_array);
                                }

                            }
                            else{
                              Mail::send('emails.form_error', ['email_id' => $values['email']], function ($message){
                                $message->to('mebongue@hotmail.com')
                                      ->subject('Drip Feed Sync Error');
                              }); 
                            }
                      }
                    }   

                  }
                }

              }else{
                  Mail::send('emails.email_template', ['site_url' => $checkUrl], function ($message){
                  $message->to('mebongue@hotmail.com')
                        ->subject('Drip Feed Sync Error');
                }); 
            }
          }
        }
    }
}
