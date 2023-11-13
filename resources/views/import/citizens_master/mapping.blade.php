<x-app-layout>
    <form action="{{ route('import.citizens-master.mapping.store') }}" method="post">
        @csrf
        @php
            $fields = [
                'PLZ' => 'zip_code',
                'Ort' => 'city',
                'Straße' => 'street',
                'Hausnummer' => 'housenumber',
                'Hausnummer-Zusatz' => 'housenumber_extra',
                'Geburtsjahr/Geburtsdatum' => 'year_of_birth',
                'Geschlecht' => 'gender'
            ];
        @endphp

        <div class="space-y-12 sm:space-y-16">
            <div>
                <h2 class="text-base font-semibold leading-7 text-gray-900">Import von Melderegister Stammdaten</h2>
                <p class="mt-1 text-sm leading-6 text-gray-600">In diesem zweiten Schritt müssen Sie für jedes Datenfeld
                    in der linken Hälfte der unteren Tabelle aus dem Auswahlmenü den dazugehörigen Spaltennamen aus der
                    zu importierenden Tabelle auswählen.
                </p>

                <p class="mt-1 text-sm leading-6 text-gray-600">Bestätigen Sie anschließend die Auswahl durch einen Klick
                    auf "Importieren". Sie werden anschließend auf eine andere Seite weitergeleitet, während im Hintergrund
                    der Import der Datei durchgeführt wird. Beachten Sie, dass der Import ggf. länger dauert und Ergebnisse
                    noch nicht sofort für Sie sichtbar sind.
                </p>

                <div class="mt-10 space-y-8 border-b border-gray-900/10 pb-12 sm:space-y-0 sm:divide-y sm:divide-gray-900/10 sm:border-t sm:pb-0">

                    @foreach($fields as $label => $field)
                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                            <label for="{{ $field }}" class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">
                                {{ $label }}
                            </label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <select id="{{ $field }}" name="columns[{{ $field }}]" class="block w-full rounded-md
                                    border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2
                                    focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6">
                                    <option value="">Bitte Spalte auswählen...</option>
                                    @foreach($headers as $header)
                                        <option value="{{ $header }}">{{ $header }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="sm:col-span-6 mt-6">
                <div class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20"
                                 fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                      clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Es gab Probleme
                                &#128557
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul role="list" class="list-disc space-y-1 pl-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-6 flex items-center justify-end gap-x-6">
            <button type="submit"
                    class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white
                    shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2
                    focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Importieren
            </button>
        </div>
    </form>
</x-app-layout>
