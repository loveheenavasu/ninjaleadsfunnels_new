<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Rule;
use App\Models\Event;
use App\Models\WebhookCron;
use DB;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\DripFeedEmailCron::class,    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Remove Invalid and Valid Logs start 
        $cron_time  = WebhookCron::first();
  
        if($cron_time->status == 'yes'){
          if($cron_time->cron_time == 24){
           $schedule->command('logs_delete:cron')
                     ->cron('* 12 * * */1');
           }
           if($cron_time->cron_time == 48){
           $schedule->command('logs_delete:cron')
                     ->cron('* 12 * * */2');
           }
           if($cron_time->cron_time == 72){
           $schedule->command('logs_delete:cron')
                     ->cron('* 12 * * */3');
           }
        }
        // Remove Invalid and Valid Logs end

        // Drip Feed cron start 

        $allRules = Rule::get();
        foreach ($allRules as $allRule) {
            if($allRule['status'] == 'running'){
              if($allRule['schedule_time']=='random'){
                $id = $allRule['id'];
                $total_hours = 24;
                $mins = 60;
                $total_mins = $total_hours*$mins;
                $email_per_day = $allRule['emails_count'];
                $allScheduleDays = $allRule['schedule_days'];
                $array = array();
                mt_srand($id);
                while(sizeof($array)<$email_per_day){
                  $number = mt_rand(0,$total_mins);
                  if(!array_key_exists($number,$array)){
                    $array[$number] = $number;
                  }
                }
                foreach ($array as $value) {
                  $time_diff = intdiv($value, 60).':'. ($value % 60);
                  foreach ($allScheduleDays as $allScheduleDay) {
                     switch ($allScheduleDay) {
                          case '1':
                              $schedule->command('dripfeed:cron',[$id])
                                      ->weeklyOn($allScheduleDay,$time_diff)
                                      ->timezone($allRule['timezone']);
                          break;

                          case '2':
                             $schedule->command('dripfeed:cron',[$id])
                                      ->weeklyOn($allScheduleDay,$time_diff)
                                      ->timezone($allRule['timezone']);
                          break;

                          case '3':
                             $schedule->command('dripfeed:cron',[$id])
                                      ->weeklyOn($allScheduleDay,$time_diff)
                                      ->timezone($allRule['timezone']);
                          break;

                          case '4':
                             $schedule->command('dripfeed:cron',[$id])
                                      ->weeklyOn($allScheduleDay,$time_diff)
                                      ->timezone($allRule['timezone']);
                          break;

                          case '5':
                             $schedule->command('dripfeed:cron',[$id])
                                      ->weeklyOn($allScheduleDay,$time_diff)
                                      ->timezone($allRule['timezone']);
                          break;

                          case '6':
                             $schedule->command('dripfeed:cron',[$id])
                                      ->weeklyOn($allScheduleDay,$time_diff)
                                      ->timezone($allRule['timezone']);
                          break;

                          case '7':
                             $schedule->command('dripfeed:cron',[$id])
                                      ->weeklyOn($allScheduleDay,$time_diff)
                                      ->timezone($allRule['timezone']);
                          break;
                     }
                  }
                }
              }else{
                $id = $allRule['id'];
                $email_per_day = $allRule['emails_count'];
                $total_hours = abs( $allRule['schedule_hour_from'] - $allRule['schedule_hour_to'] );
                $mins = 60;
                $allScheduleDays = $allRule['schedule_days'];
                $total_mins = $total_hours*$mins;
                $array = array();
                mt_srand(10);
                while(sizeof($array)<$email_per_day){
                  $number = mt_rand(0,$total_mins);
                  if(!array_key_exists($number,$array)){
                     $array[$number] = $number;
                  }
                }
                foreach ($array as $value) {
                  $time_diff = intdiv($value, 60).'.'. ($value % 60);
                  $spreadTime = $allRule['schedule_hour_from']+$time_diff;
                  $finalMins = str_replace('.', ':', $spreadTime);
                  foreach ($allScheduleDays as $allScheduleDay) {
                     switch ($allScheduleDay) {
                          case '1':
                              $schedule->command('dripfeed:cron',[$id])
                                      ->weeklyOn($allScheduleDay,$finalMins)
                                      ->timezone($allRule['timezone']);
                          break;

                          case '2':
                             $schedule->command('dripfeed:cron',[$id])
                                      ->weeklyOn($allScheduleDay,$finalMins)
                                      ->timezone($allRule['timezone']);
                          break;

                          case '3':
                             $schedule->command('dripfeed:cron',[$id])
                                      ->weeklyOn($allScheduleDay,$finalMins)
                                      ->timezone($allRule['timezone']);
                          break;

                          case '4':
                             $schedule->command('dripfeed:cron',[$id])
                                      ->weeklyOn($allScheduleDay,$finalMins)
                                      ->timezone($allRule['timezone']);
                          break;

                          case '5':
                             $schedule->command('dripfeed:cron',[$id])
                                      ->weeklyOn($allScheduleDay,$finalMins)
                                      ->timezone($allRule['timezone']);
                          break;

                          case '6':
                             $schedule->command('dripfeed:cron',[$id])
                                      ->weeklyOn($allScheduleDay,$finalMins)
                                      ->timezone($allRule['timezone']);
                          break;

                          case '7':
                             $schedule->command('dripfeed:cron',[$id])
                                      ->weeklyOn($allScheduleDay,$finalMins)
                                      ->timezone($allRule['timezone']);
                          break;
                     }
                  }
                }
              }
            }
        }
        // Drip Feed cron end 
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
