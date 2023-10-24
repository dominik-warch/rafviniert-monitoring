<x-action-section>
    <x-slot name="title">
        {{ __('Benutzerkonto löschen') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Löschen Sie Ihr Benutzerkonto endgültig.') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-gray-600">
            {{ __('Nach der Löschung Ihres Benutzerkontos werden alle seine Inhalte und Daten endgültig gelöscht. Bevor Sie Ihr Benutzerkonto löschen, laden Sie bitte alle Daten und Informationen herunter, die Sie aufbewahren möchten.') }}
        </div>

        <div class="mt-5">
            <x-danger-button wire:click="confirmUserDeletion" wire:loading.attr="disabled">
                {{ __('Benutzerkonto löschen') }}
            </x-danger-button>
        </div>

        <!-- Delete User Confirmation Modal -->
        <x-dialog-modal wire:model="confirmingUserDeletion">
            <x-slot name="title">
                {{ __('Benutzerkonto löschen') }}
            </x-slot>

            <x-slot name="content">
                {{ __('Sind Sie sicher, dass Sie Ihr Benutzerkonto löschen möchten? Wenn Ihr Benutzerkonto gelöscht wird, werden alle seine Inhalte und Daten dauerhaft gelöscht. Bitte geben Sie Ihr Passwort ein, um zu bestätigen, dass Sie Ihr Benutzerkonto endgültig löschen möchten.') }}

                <div class="mt-4" x-data="{}" x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
                    <x-input type="password" class="mt-1 block w-3/4"
                                autocomplete="current-password"
                                placeholder="{{ __('Passwort') }}"
                                x-ref="password"
                                wire:model.defer="password"
                                wire:keydown.enter="deleteUser" />

                    <x-input-error for="password" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                    {{ __('Abbrechen') }}
                </x-secondary-button>

                <x-danger-button class="ml-3" wire:click="deleteUser" wire:loading.attr="disabled">
                    {{ __('Benutzerkonto löschen') }}
                </x-danger-button>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>
