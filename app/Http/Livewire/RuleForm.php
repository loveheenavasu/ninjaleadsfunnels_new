<?php

namespace App\Http\Livewire;

use App\Models\Connection;
use App\Models\Listing;
use App\Models\Rule;
use App\Models\User;
use App\Models\UsersSetting;
use App\Tools;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Component;
use App\Models\GlobalUserSetting;
use Auth;

use Illuminate\Validation\Rule as ValidationRule;

class RuleForm extends Component
{
    public Rule $rule;
    public $isDisabled = false;
    public array $list_ids = [];

    public array $webhook_ids = [];

    public function rules(): array
    {
        return [
            'rule.user_id'=>[''],
            'rule.name' => ['required', 'string','unique:rules,name'],
            'rule.connection_id' => ['required', 'exists:connections,id'],
            'rule.stage_id' => [
                ValidationRule::requiredIf(fn () => $this->rule->requiresStage())
            ],
            // 'rule.stage_id'  => ['required_if:rule.connection_id,Chassetonboss'],
            'rule.emails_count' => ['required', 'numeric', 'min:1'],
            'rule.randomize_emails_order' => ['boolean'],
            'rule.timezone' => ['required', ValidationRule::in($this->timezones)],
            'rule.schedule' => ['required', ValidationRule::in(Rule::schedules())],
            'rule.schedule_days' => ['array', ValidationRule::requiredIf(fn () => $this->rule->schedule === 'daily')],
            'rule.schedule_days.*' => ['integer', 'min:1', 'max:7'],
            'rule.schedule_time' => [
                'required', ValidationRule::in(Rule::scheduleTimes())
            ],
            'rule.schedule_hour_from' => ['min:0', 'max:23', ValidationRule::requiredIf(fn () => in_array($this->rule->schedule_time, ['between', 'spread']))],
            'rule.schedule_hour_to' => ['min:0', 'max:23', ValidationRule::requiredIf(fn () => in_array($this->rule->schedule_time, ['between', 'spread']))],
            'rule.notes' => ['nullable', 'string'],
            'list_ids' => ['array', 'min:1'],
            'list_ids.*' => ['required', 'exists:listings,id'],
            
        ];
    }

    public function getConnectionsProperty()
    {
        $auth = Auth::user();
        if($auth->role == 'admin'){
            return Connection::all();
        }
        else
        {
            return Connection::where('user_id',Auth::user()->id)->get();
        } 
    }

    public function getUsersProperty()
    {
        return User::get();
    }
    public function getListingsProperty()
    {
        $auth = Auth::user();
        if($auth->role == 'admin'){
            return Listing::all();
        }
        else
        {
            return Listing::where('user_id',Auth::user()->id)->get();
        } 
    }

    public function getWeekPeriodProperty()
    {
        return new CarbonPeriod(
            Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()
        );
    }

    public function getHoursProperty(): array
    {
        return range(0, 23);
    }

    public function getTimezonesProperty(): array
    {
        return timezone_identifiers_list();
    }

    public function getStagesProperty()
    {
        return [
            1 => 'Discovery',
            2 => 'Engaged',
            3 => 'Proposal',
            4 => 'Bought'
        ];
    }

    public function getWebhooksProperty()
    {
        return [
            1 => 'webhook',
        ];
    }

    public function updatedRuleConnectionId(string $connectionId): void
    {
        if ($connection = Connection::query()->find($connectionId)) {
            $this->rule->connection()->associate($connection);
        }
    }

    public function mount(Rule $rule): void
    {
        $this->rule = $rule;

        $user_data = User::select('users.id as u_id','users.first_name as u_name','user_email_settings.id as id','user_email_settings.emails_per_day as emails_count')
            ->leftJoin('user_email_settings', 'users.id', '=', 'user_email_settings.user_id')
            ->where('role','user')
            ->where('users.id',Auth::user()->id)
            ->first();
        $this->rule->user_id = Auth::user()->id;
        $globalData = GlobalUserSetting::get()->toArray();
            if(!empty($user_data['emails_count']) && $user_data['u_id'] == Auth::user()->id){
                $this->rule->emails_count =$user_data['emails_count'];
                $this->isDisabled = true;
            }
            else{
                $this->rule->emails_count = $globalData[0]['emails_per_day'];
                $this->isDisabled = true;
            }
        $this->list_ids = $rule->listings()
            ->pluck('id')
            ->map(fn ($id) => (string)$id)
            ->toArray();
        $this->webhook_ids = $rule->webhooks()
            ->pluck('id')
            ->map(fn ($id) => (string)$id)
            ->toArray();

        $this->rule->schedule = $this->rule->schedule ?? 'daily';
        $this->rule->schedule_time = $this->rule->schedule_time ?? 'random';
        $this->rule->timezone = $this->rule->timezone ?? now()->tzName;
        $this->rule->randomize_emails_order = $this->rule->randomize_emails_order ?? false;
    }

    public function save(): void
    {
        if($this->rule->id == ''){
            $this->validate();
        }

        $this->rule->save();
        $this->rule->listings()->sync($this->list_ids);
        $this->rule->webhooks()->sync($this->webhook_ids);  

        if ($this->rule->wasRecentlyCreated) {
            $this->redirectRoute('rules.index');
        } else {
            $this->redirectRoute('rules.index');
        }


        $this->emit('saved');
    }

    
    public function getwebhookListProperty(){
        $allWebhooks = Connection::where('type','webhook')->get();
        return $allWebhooks;
    }

}
