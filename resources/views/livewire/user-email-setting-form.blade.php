<div>
    <x-form-section submit="submit">
        <x-slot name="title">
            Users data
        </x-slot>

        <x-slot name="description">
            Users Credentials
        </x-slot>
        <x-slot name="form">
            <div class="col-span-4">
                <x-jet-label for="user_id" value="{{ __('User_Name') }}" />
                @php
                  $ediatble = $this->ediatble_id;
                @endphp
                    <x-select name="user_id" class="mt-1" wire:model.defer="useremailsetting.user_id">
                        <option value=""></option>
                        @if($this->editcase =='no')
                        @foreach($this->users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->first_name }}
                            </option>
                        @endforeach
                        @else
                        @foreach($this->users as $user)
                             @if($user->id == $ediatble)
                            <option value="{{ $user->id }}">
                                {{ $user->first_name }}
                            </option>
                            @endif
                        @endforeach
                        @endif
                    </x-select>
                <x-jet-input-error for="useremailsetting.user_id" class="mt-2" />
            </div>
            <div class="col-span-2">
                    <x-jet-label for="emails_per_day" value="{{ __('Emails_per_day') }}" />
                    <x-jet-input name="emails_per_day" type="text" class="mt-1 block w-full" wire:model.defer="useremailsetting.emails_per_day" />
                    <x-jet-input-error for="useremailsetting.emails_per_day" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-jet-button>
                {{ __(!$this->useremailsetting->exists ? 'Create' : 'Update') }}
            </x-jet-button>
        </x-slot>
    </x-form-section>
</div>
