<div>
    <div class="md:grid md:gap-6">
        <x-jet-section-title>
                <x-slot name="title">{{ __('Manage Rules') }}</x-slot>
                <x-slot name="description">Configure rules to automate emails export.</x-slot>
        </x-jet-section-title>

        <div class="mt-5 md:mt-0">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="space-y-6">
                    @if ($this->rules->isNotEmpty())
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Name') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Created by') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Lists') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Server') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Emails') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Schedule') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Time') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Actions left') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('ETA') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50">
                                        <span class="sr-only">{{ __('Actions') }}</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($this->rules as $rule)
                                    <tr>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">
                                            <a href="{{ route('rules.show', ['rule' => $rule->id]) }}">{{ $rule->name }}</a>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">
                                            {{ $rule->first_name }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">
                                            {{ $rule->listings->pluck('name')->implode(', ') }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">
                                            @if(!empty($rule->connection->name))
                                            {{ $rule->connection->name }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">
                                            {{ $rule->emails_count }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">
                                            <div>{{ \Illuminate\Support\Str::humanize($rule->schedule) }}</div>

                                            <div>
                                                @if($rule->schedule === 'daily')
                                                    @foreach($rule->schedule_days as $day)
                                                        {{ \Illuminate\Support\Carbon::create($day)->shortDayName }}
                                                    @endforeach
                                                @elseif($rule->schedule === 'weekly')
                                                    {{ \Illuminate\Support\Carbon::create($rule->schedule_weekday)->shortDayName }}
                                                @elseif($rule->schedule === 'monthly')
                                                    @if($rule->schedule_monthday == '-1')
                                                        Last day
                                                    @else
                                                        {{ str_ordinal($rule->schedule_monthday) }}
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">
                                            <div>{{ \Illuminate\Support\Str::humanize($rule->schedule_time) }}</div>
                                            <div>
                                                @if($rule->schedule_time === 'exact_time')
                                                    {{ \Illuminate\Support\Str::padLeft($rule->schedule_hour, 2, 0) }}:00
                                                @elseif(in_array($rule->schedule_time, ['between', 'spread']))
                                                    {{ \Illuminate\Support\Str::padLeft($rule->schedule_hour_from, 2, 0) }}:00-{{ \Illuminate\Support\Str::padLeft($rule->schedule_hour_to, 2, 0) }}:00
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">
                                            {{ $rule->leftaction($rule->id) }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">
                                            ~{{ $rule->estimated_date->format('d.m.Y') }}
                                        </td>

                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($rule->status === \App\Models\Rule::STATUS_STOPPED)
                                                    <button class="cursor-pointer ml-6 text-sm text-blue-400 focus:outline-none" wire:click="startRule({{ $rule->id }})">
                                                        Start
                                                    </button>
                                                @else
                                                    <button class="cursor-pointer ml-6 text-sm text-yellow-400 focus:outline-none" wire:click="stopRule({{ $rule->id }})">
                                                        Stop
                                                    </button>
                                                @endif

                                                <button class="cursor-pointer ml-6 text-sm text-gray-400 focus:outline-none" wire:click="cloneRule({{ $rule->id }})">
                                                    Clone
                                                </button>

                                                <a href="{{ route('rules.show', ['rule' => $rule->id]) }}" class="cursor-pointer ml-6 text-sm text-gray-400 focus:outline-none">
                                                    {{ __('Details') }}
                                                </a>

                                                <button class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none" wire:click="confirmRuleDeletion({{ $rule->id }})">
                                                    {{ __('Delete') }}
                                                </button>
                                                @if(Auth::user()->role == 'admin')
                                                <button class="cursor-pointer ml-6 text-sm text-blue-400 focus:outline-none" wire:click="webScraping({{ $rule->id }})">{{ __('Test Rule') }}
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($this->rules->hasPages())
                            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                                {{ $this->rules->links() }}
                            </div>
                        @endif
                    @else
                        <div class="px-4 py-3 sm:px-6">
                            {{ __('No rules yet.') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <x-jet-confirmation-modal wire:model="confirmingRuleDeletion">
        <x-slot name="title">
            {{ __('Delete Rule') }}
        </x-slot>

        <x-slot name="content">
            @if($this->ruleBeingDeleted)
                {{ __('Are you sure you want to delete rule :name?', ['name' => $this->ruleBeingDeleted->name]) }}
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingRuleDeletion')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="deleteRule" wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
