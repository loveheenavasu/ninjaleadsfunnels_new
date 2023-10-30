<div>
    <x-form-section submit="submit">
        <x-slot name="title">
            Global data
        </x-slot>

        <x-slot name="description">
            Global Settings
        </x-slot>
        <x-slot name="form">
            <div class="col-span-6">
                    <x-jet-label for="emails_per_day" value="{{ __('Email_Per_Day For All Users') }}" />
                    <x-jet-input name="emails_per_day" type="number" class="mt-1 block w-full" wire:model.defer="globalsetting.emails_per_day" />
                    <x-jet-input-error for="globalsetting.emails_per_day" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-jet-button>
                {{ __(!$this->globalsetting->exists ? 'Create' : 'Update') }}
            </x-jet-button>
        </x-slot>
    </x-form-section>
</div>
