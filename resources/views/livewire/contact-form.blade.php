<div>

    <div class="mx-auto max-w-2xl text-center">
        <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Support & Kontakt</h2>
        <p class="mt-2 leading-8 text-gray-600">Falls Sie auf Fehler oder Probleme stoßen oder anderweitig Rückfragen haben, kontaktieren Sie uns gerne über das nachfolgende Formular!</p>
    </div>

    <form wire:submit.prevent="contactFormSubmit" action="/" method="POST" class="mx-auto mt-8 max-w-xl">
        <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="name" class="block text-sm font-semibold leading-6 text-gray-900">Name</label>
                <div class="mt-2.5">
                    @error("name")
                    <p class="text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    <input wire:model="name" type="text" name="name" id="name" autocomplete="name" class="block w-full rounded-md border-0 px-3.5 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
            </div>

            <div class="sm:col-span-2">
                <label for="email" class="block text-sm font-semibold leading-6 text-gray-900">E-Mail-Adresse</label>
                <div class="mt-2.5">
                    @error("email")
                    <p class="text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    <input wire:model="email" type="email" name="email" id="email" autocomplete="email" class="block w-full rounded-md border-0 px-3.5 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
            </div>

            <div class="sm:col-span-2">
                <label for="content" class="block text-sm font-semibold leading-6 text-gray-900">Nachricht</label>
                <div class="mt-2.5">
                    @error("content")
                    <p class="text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    <textarea wire:model="content" name="content" id="content" rows="4" class="block w-full rounded-md border-0 px-3.5 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
                </div>
            </div>
        </div>
        <div class="mt-10">
            <button type="submit" class="block w-full rounded-md bg-indigo-600 px-3.5 py-2.5 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Absenden</button>
        </div>
    </form>
</div>
