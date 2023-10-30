<div>
    <x-action-section>
        <x-slot name="title">
            {{ __('Manage Users') }}
        </x-slot>

        <x-slot name="content">
            <div class="space-y-6">
                @if($this->users->isNotEmpty())
                    @foreach($this->users as $user)
                        <div class="flex items-center justify-between">
                            <div>
                                {{ $user->first_name }}
                            </div>

                            <div class = "disp_role">
                                <span class="bg-blue-400 text-gray-50 p-1 text-xs rounded">{{ $user->role }}</span>
                            </div>

                            <div class="flex items-center">
                                <a href="{{ route('users.edit', ['user' => $user->id]) }}" class="cursor-pointer ml-6 text-sm text-gray-400 focus:outline-none">
                                    {{ __('Edit') }}
                                </a>

                                <button class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none" wire:click="confirmUserDeletion({{ $user->id }})">
                                    {{ __('Delete') }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                     @if($this->users->hasPages())
                        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                            {{ $this->users->fragment('')->links() }}
                        </div>
                    @endif
                @else
                    <div>{{ __('No Users yet.') }}</div>
                @endif

            </div>
        </x-slot>
    </x-action-section>

    <!-- Delete Confirmation Modal -->
    <x-jet-confirmation-modal wire:model="confirmingUserDeletion">
        <x-slot name="title">
            {{ __('Delete User') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to delete this User?') }}
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="deleteUser" wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
