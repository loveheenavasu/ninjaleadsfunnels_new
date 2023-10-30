<div>
    <x-action-section>
        <x-slot name="title">
            {{ __('Manage Global Settings') }}
        </x-slot>

        <x-slot name="content">
            <div class="space-y-6">
                @if($this->settings->isNotEmpty())
                    @foreach($this->settings as $setting)
                        <div class="flex items-center justify-between">
                            <div class="col-span-6">
                                Emails Per Day
                            </div>

                            <div class="col-span-2">
                                {{$setting->emails_per_day}}
                            </div>
                            <div class="flex items-center">
                                <a href="{{ route('globalsetting.edit', ['globalsetting' => $setting->id]) }}" class="cursor-pointer ml-6 text-sm text-gray-400 focus:outline-none">
                                    {{ __('Edit') }}
                                </a>

                                <button class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none" wire:click="confirmGlobalsettingDeletion({{ $setting->id }})">
                                    {{ __('Delete') }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                     @if($this->settings->hasPages())
                        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                            {{ $this->settings->fragment('')->links() }}
                        </div>
                    @endif
                @else
                    <div>{{ __('No GlobalSettings yet.') }}</div>
                @endif

            </div>
        </x-slot>
    </x-action-section>

    <!-- Delete Confirmation Modal -->
    <x-jet-confirmation-modal wire:model="confirmingGlobalsettingDeletion">
        <x-slot name="title">
            {{ __('Delete GlobalSettings') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to delete this GlobalSetting?') }}
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingGlobalsettingDeletion')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="deleteGlobalsetting" wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
