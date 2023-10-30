<div>
    <x-action-section>
        <x-slot name="title">
            {{ __('Manage Connections') }}
        </x-slot>

        <x-slot name="description">
            Contains servers credentials.
        </x-slot>
        <?php
        //echo "<pre>";print_r(Auth::user()->id);die;

        ?>
        <x-slot name="content">
            <div class="space-y-6">
                @if ($this->connections->isNotEmpty())
                    @foreach ($this->connections as $connection)
                        <div class="flex items-center justify-between">
                            <div>
                                <a href="{{ route('connections.show', ['connection' => $connection->id]) }}">
                                    {{ $connection->name }}
                                    @if($connection->base_url)
                                        <span class="text-gray-500 text-sm">{{ $connection->base_url }}</span>
                                    @endif
                                </a>
                            </div>
                            @if(Auth::user()->role == 'admin')
                            <div class="items-center text-gray-500 text-sm">
                                {{ __('Created by :') }}
                                <span class="text-gray-500 text-sm">{{ $connection->first_name }}</span>
                            </div>
                            @endif
                            <div class="flex items-center">
                                @if(\App\Actions\TestConnection::isTestableType($connection->type))
                                    <button class="cursor-pointer ml-6 text-sm text-gray-400 focus:outline-none" wire:click="testConnection({{ $connection->id }})">
                                        @isset($this->testedConnections[$connection->id])
                                            @if($this->testedConnections[$connection->id] === true)
                                                <span class="text-green-400">{{ __('Connected') }}</span>
                                            @else
                                                <span class="text-red-400">{{ $this->testedConnections[$connection->id] }}</span>
                                            @endif
                                        @else
                                            {{ __('Test Connection') }}
                                        @endif

                                        <x-jet-input-error for="connections.{{ $connection->id }}" class="mt-2" />
                                    </button>
                                @endif
                                <a href="{{ route('connections.show', ['connection' => $connection->id]) }}" class="cursor-pointer ml-6 text-sm text-gray-400 focus:outline-none">
                                    {{ __('Details') }}
                                </a>

                                <button class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none" wire:click="confirmConnectionDeletion({{ $connection->id }})">
                                    {{ __('Delete') }}
                                </button>
                            </div>
                        </div>
                    
                    @endforeach
                    @if($this->connections->hasPages())
                        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                            {{ $this->connections->fragment('')->links() }}
                        </div>
                    @endif
                @else
                    <div>{{ __('No connections yet.') }}</div>
                @endif
            </div>
        </x-slot>
    </x-action-section>

    <!-- Delete Confirmation Modal -->
    <x-jet-confirmation-modal wire:model="confirmingConnectionDeletion">
        <x-slot name="title">
            {{ __('Delete Connection') }}
        </x-slot>

        <x-slot name="content">
            @if($this->connectionBeingDeleted)
                {{ __('Are you sure you want to delete connection :name?', ['name' => $this->connectionBeingDeleted->name]) }}
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingConnectionDeletion')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="deleteConnection" wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
