<div>
    <x-form-section submit="submit">
        <x-slot name="title">
            Connection data
        </x-slot>

        <x-slot name="description">
            Server credentials.
        </x-slot>

        <x-slot name="form">
            @if(Auth::user()->role == 'admin')
            <div class="col-span-3">
                <x-jet-label for="user_id" value="{{ __('Username') }}" />
                    <x-select name="user_id" class="mt-1" wire:model.defer="connection.user_id">
                        <option value=""></option>
                        @foreach($this->users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->first_name }}
                            </option>
                        @endforeach
                    </x-select>
                <x-jet-input-error for="connection.user_id" class="mt-2" />
            </div>
            @endif
            <div class="col-span-3">
                <x-jet-label for="name" value="{{ __('Name') }}" />
                <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="connection.name" />
                <x-jet-input-error for="connection.name" class="mt-2" />
                @if($this->is_user_alredy_exist == 'yes')
                <span class="mt-2" style="color:red;">User Already have a connection</span>
                @endif
            </div>

            <div class="col-span-6 sm:col-span-6">
                <x-jet-label for="base_url" value="{{ __('Url') }}" />
                <x-jet-input id="base_url" type="text" class="mt-1 block w-full" wire:model.defer="connection.base_url" />
                <x-jet-input-error for="connection.base_url" class="mt-2" />
                @if($this->url_redirection == 'true')
                <span class="mt-2" style="color:red;">Connection url is not valid</span>
                @endif
            </div>

            @if($this->connection->requiresHost())
            <div class="col-span-6 {{ $this->connection->requiresHost() ? 'sm:col-span-6' : '' }}">
                <x-jet-label for="type" value="{{ __('Type') }}" />
                <x-select name="type" class="mt-1" wire:model="connection.type">
                    <option value=""></option>
                    @foreach($this->types() as $type)
                        <option value="{{ $type }}">
                            {{ \Illuminate\Support\Str::humanize($type) }}
                        </option>
                    @endforeach
                </x-select>
                <x-jet-input-error for="connection.type" class="mt-2" />
            </div>
            @endif

            @if($this->connection->requiresUsername())
                <div class="col-span-6 sm:col-span-3">
                    <x-jet-label for="username" value="{{ __('Username') }}" />
                    <x-jet-input id="username" type="text" class="mt-1 block w-full" wire:model.defer="connection.username" />
                    <x-jet-input-error for="connection.username" class="mt-2" />
                </div>
            @endif

            @if($this->connection->requiresPassword())
                <div class="col-span-6 sm:col-span-3">
                    <x-jet-label for="password" value="{{ __('Password') }}" />
                    <x-jet-input id="password" type="password" class="mt-1 block w-full" wire:model.defer="connection.password" />
                    <x-jet-input-error for="connection.password" class="mt-2" />
                </div>
            @endif

            @if($this->connection->requiresWebhookUrl())
                <div class="col-span-6">
                    <x-jet-label for="webhook_url" value="{{ __('Zapier Webhook Url') }}" />
                    <x-jet-input id="webhook_url" type="url" class="mt-1 block w-full" wire:model.defer="connection.webhook_url" />
                    <x-jet-input-error for="connection.webhook_url" class="mt-2" />
                </div>
            @endif

            @if($this->connection->requiresCustomCode())
                <div class="col-span-6">
                    <x-jet-label for="custom_code" value="{{ __('Custom Code') }}" />
                    <textarea class="form-input rounded-md shadow-sm mt-1 block w-full" rows="10" wire:model.defer="connection.custom_code"></textarea>
                    <x-jet-input-error for="connection.custom_code" class="mt-2" />
                </div>
            @endif

            @if($this->connection->requiresLinkCustomCode())
                <div class="col-span-6">
                    <x-jet-label for="link_custom_code" value="{{ __('Link Custom Code') }}" />
                    <textarea class="form-input rounded-md shadow-sm mt-1 block w-full" rows="10" wire:model.defer="connection.link_custom_code"></textarea>
                    <x-jet-input-error for="connection.link_custom_code" class="mt-2" />
                </div>
            @endif

            <x-jet-input id="user_id" type="hidden" class="mt-1 block w-full" wire:model.defer="{{Auth::user()->id}}" />
        </x-slot>
        
        <x-slot name="actions">
            <x-jet-button>
                {{ __(!$this->connection->exists ? 'Create' : 'Update') }}
            </x-jet-button>
        </x-slot>
    </x-form-section>
</div>
