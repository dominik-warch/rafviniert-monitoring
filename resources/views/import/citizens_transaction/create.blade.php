<x-app-layout>
    <form action="{{ route('import.citizens-transaction.store') }}" method="post" enctype="multipart/form-data">
        @csrf

        <div class="space-y-12 sm:space-y-16">
            <div>
                <h2 class="text-base font-semibold leading-7 text-gray-900">Import von Melderegister-Bewegungsdaten</h2>
                <p class="mt-1 text-sm leading-6 text-gray-600">Zur Berechnung v.a. demografischer Indikatoren ist
                    der Import von Melderegisterdaten erforderlich. Über diesen zweischrittigen Prozess können die
                    Bewegungsdaten importiert werden.
                </p>

                <p class="mt-1 text-sm leading-6 text-gray-600">Zuerst wählen Sie bitte die entsprechende Datei im
                    Format .csv, .xls oder .xlsx (Excel) aus und drücken auf Hochladen.
                </p>

                <div class="mt-10 space-y-8 border-b border-gray-900/10 pb-12 sm:space-y-0 sm:divide-y sm:divide-gray-900/10 sm:border-t sm:pb-0">

                    <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                        <label for="dataset_date" class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Stichtag des Melderegisterauszugs</label>
                        <div class="mt-2 sm:col-span-2 sm:mt-0">
                            <input class="max-w-2xl w-full rounded-lg border-gray-900/25" type="date" name="dataset_date" id="dataset_date">
                        </div>
                    </div>

                    <fieldset>
                        <div class="sm:grid sm:grid-cols-3 sm:items-baseline sm:gap-4 sm:py-6">
                            <div class="text-sm font-semibold leading-6 text-gray-900">Struktur der Bewegungsdaten</div>
                            <div class="mt-1 sm:col-span-2 sm:mt-0">
                                <div class="max-w-lg">
                                    <div class="mt-2 space-y-6">
                                        <div class="flex items-center gap-x-3">
                                            <input
                                                id="single_file"
                                                name="fileType"
                                                value="single"
                                                type="radio"
                                                checked
                                                onchange="toggleTransactionTypeDropdown()"
                                                class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                            <label
                                                for="single_file"
                                                class="block text-sm font-medium leading-6 text-gray-900">
                                                Eine Tabelle für alle Vorgangsarten
                                            </label>
                                        </div>
                                        <div class="flex items-center gap-x-3">
                                            <input
                                                id="multiple_files"
                                                name="fileType"
                                                value="multiple"
                                                type="radio"
                                                onchange="toggleTransactionTypeDropdown()"
                                                class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                            <label
                                                for="multiple_files"
                                                class="block text-sm font-medium leading-6 text-gray-900">
                                                Separate Tabellen für jede Vorgangsart
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <div id="transactionTypeDropdown" style="display:none;" class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                        <label
                            for="transaction_type_table"
                            class="block text-sm font-medium leading-6 text-gray-900"
                        >Wählen Sie die Art des Vorgangs aus:</label>
                        <select
                            id="transaction_type_table"
                            name="transaction_type_table" required
                            class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        >
                            <option value="birth">Geburtsfälle</option>
                            <option value="death">Sterbefälle</option>
                            <option value="move_in">Hinzüge</option>
                            <option value="move_out">Wegzüge</option>
                        </select>
                    </div>

                    <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                        <label for="file" class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Datei</label>
                        <div class="mt-2 sm:col-span-2 sm:mt-0">
                            <div class="flex max-w-2xl justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd" />
                                    </svg>
                                    <div class="mt-4 flex text-sm leading-6 text-gray-600">
                                        <label for="file" class="relative cursor-pointer rounded-md bg-white font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                                            <input id="file" name="file" type="file" required>
                                        </label>
                                    </div>
                                    <p class="text-xs leading-5 text-gray-600">CSV, XLS oder XLSX</p>
                                </div>
                            </div>
                        </div>
                    </div>
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
            <button type="submit" class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Hochladen</button>
        </div>
    </form>

    <script>
        function toggleTransactionTypeDropdown() {
            const fileType = document.querySelector('input[name="fileType"]:checked').value;
            const dropdown = document.getElementById('transactionTypeDropdown');
            if (fileType === 'single') {
                dropdown.style.display = 'none';
            } else {
                dropdown.style.display = 'block';
            }
        }
    </script>
</x-app-layout>
