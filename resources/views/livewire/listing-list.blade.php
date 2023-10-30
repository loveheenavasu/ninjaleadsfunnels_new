<div>
    <x-jet-action-section>
        <x-slot name="title">
            {{ __('Manage Lists') }}
        </x-slot>

        <x-slot name="description">
            Contains sets of emails.
        </x-slot>

        <x-slot name="content">
            <div class="space-y-6">
                @if ($this->listings->isNotEmpty())
                    @foreach ($this->listings as $listing)
                        <div class="flex items-center justify-between">
                            <div>
                                <a href="{{ route('listings.show', ['listing' => $listing->id]) }}" target="_blank">{{ $listing->name }}</a>
                                <small class="text-gray-400 ml-2">Emails total: {{ $listing->emails()->count() }}, in pool: {{ $listing->emails()->wherePivot('in_pool', true)->count() }}</small>
                            </div>
                            @if(Auth::user()->role == 'admin')
                            <div>
                                <small class="text-gray-400 ml-2">List created by: {{ $listing->first_name}}</small>
                            </div>
                            @endif
                            <div class="flex items-center">
                                @isset($listing->valid_emails)
                                <a style="color:green;" href="{{ route('listings.show', ['listing' => $listing->id]) }}" target="_blank">
                                    {{number_format((int)$listing->valid_emails/$listing->emails()->count()*100,2, '.', '')."%"}}</a>
                                @else
                                <a style="color:green;" href="{{ route('listings.show', ['listing' => $listing->id]) }}" target="_blank">
                                    {{"0.00%"}}</a>
                                @endif

                                <a href="{{ route('listings.show', ['listing' => $listing->id]) }}" class="cursor-pointer ml-6 text-sm text-gray-400 focus:outline-none">
                                    {{ __('Details') }}
                                </a>
                                <button class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none" wire:click="confirmingListingDeletion({{ $listing->id}})">
                                    {{ __('Delete') }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                    @if($this->listings->hasPages())
                        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                            {{ $this->listings->fragment('listings')->links() }}
                        </div>
                    @endif
                @else
                    <div>{{ __('No lists yet.') }}</div>
                @endif
            </div>
        </x-slot>
    </x-jet-action-section>

    <!-- Delete Confirmation Modal -->
    <x-jet-confirmation-modal wire:model="confirmingListingDeletion">
        <x-slot name="title">
            {{ __('Delete Listing') }}
        </x-slot>

        <x-slot name="content">
            @if($this->ruleRunning == true)
                {{ __("List Can't be deleteable. This list is used in current running rule. So you can not delete it.") }}
            @endif
            @if($this->listingBeingDeleted && $this->ruleRunning == false)
                {{ __('Are you sure you want to delete Listing :name?', ['name' => $this->listingBeingDeleted->name]) }}
            @endif
        </x-slot>

        <x-slot name="footer">
            @if($this->ruleRunning == true)
                <x-jet-secondary-button wire:click="$toggle('confirmingListingDeletion')" wire:loading.attr="disabled">
                    {{ __('Nevermind') }}
                </x-jet-secondary-button>
            @endif
            @if($this->ruleRunning == false)
                <x-jet-secondary-button wire:click="$toggle('confirmingListingDeletion')" wire:loading.attr="disabled">
                    {{ __('Nevermind') }}
                </x-jet-secondary-button>

                <x-jet-danger-button class="ml-2" wire:click="deleteList" wire:loading.attr="disabled">
                    {{ __('Delete') }}
                </x-jet-danger-button>
            @endif

        </x-slot>
    </x-jet-confirmation-modal>
</div>
