<div>
    <x-action-section>
        <x-slot name="title">
            {{ __('Manage Users') }}
        </x-slot>
        
        <x-slot name="content">
            <div class="space-y-6">
                @if($this->usersettings->isNotEmpty())
                    @foreach($this->usersettings as $usersetting)
                        <div class="flex items-center justify-between">
                            <div>
                                {{ $usersetting->first_name }}
                            </div>
                            
                            <div class="flex items-center">
                                <a href="{{ route('useremailsetting.edit', ['useremailsetting' => $usersetting->id]) }}" class="cursor-pointer ml-6 text-sm text-gray-400 focus:outline-none">
                                    {{ __('Edit') }}
                                </a>

                                <button class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none" wire:click="confirmUsersettingDeletion({{ $usersetting->id }})">
                                    {{ __('Delete') }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                     
                @else
                    <div>{{ __('No Users Settings yet.') }}</div>
                @endif

            </div>
        </x-slot>
    </x-action-section>

    <!-- Delete Confirmation Modal -->
    <x-jet-confirmation-modal wire:model="confirmingUsersettingDeletion">
        <x-slot name="title">
            {{ __('Delete Users Settings') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to delete this Users Settings?') }}
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingUsersettingDeletion')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="deleteUsersetting" wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
