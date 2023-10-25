<x-app-layout>
    <!-- change action route -->
    <form action="{{ route('calculations.calculate') }}" method="post" enctype="multipart/form-data">
        @csrf

        <div class="space-y-12 sm:space-y-16">
            <div>
                <h2 class="text-base font-semibold leading-7 text-gray-900">Indikatorenberechnung</h2>
                <p class="mt-1 text-sm leading-6 text-gray-600">
                    Hier können die Berechnungen der jeweiligen Indikatoren angestoßen werden. Dazu wählen Sie bitte aus
                    den beiden Auswahlmenüs die Einwohnermeldedaten aus, die als Berechnungsgrundlage dienen sollen -
                    gekennzeichnet durch deren Stichtagsdatum. Außerdem müssen Sie eine sogenannte Referenzgeometrie
                    auswählen, die als Aggregationsebene der Berechnungen dient.
                </p>



                <div class="mt-10 space-y-8 border-b border-gray-900/10 pb-12 sm:space-y-0 sm:divide-y sm:divide-gray-900/10 sm:border-t sm:pb-0">

                    <label
                        for="reference_geometry"
                        class="block text-sm font-medium leading-6 text-gray-900"
                    >Referenzgeometrie</label>
                    <select
                        name="reference_geometry" required
                        class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    >
                        @foreach($referenceGeometries as $geometry)
                            <option value="{{ $geometry }}">{{ $geometry }}</option>
                        @endforeach
                    </select>

                    <label
                        for="date_of_dataset"
                        class="block text-sm font-medium leading-6 text-gray-900"
                    >Stichtag der Einwohnermeldedaten:</label>
                    <select
                        name="date_of_dataset" required
                        class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    >
                        @foreach($datasetDates as $date)
                            <option value="{{ $date }}">{{ $date }}</option>
                        @endforeach
                    </select>
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
            <button type="submit" name="calculation_type" value="median" class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Medianalter berechnen</button>

            <button type="submit" name="calculation_type" value="mean" class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Durchschnittsalter berechnen</button>

            <button type="submit" name="calculation_type" value="greying_index" class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Greying Index berechnen</button>
        </div>
    </form>
</x-app-layout>
