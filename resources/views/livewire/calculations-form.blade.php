<form wire:submit="calculate">
    <div class="space-y-12">
        <div class="border-b border-gray-900/10 pb-12">
            <h2 class="text-base font-semibold leading-7 text-gray-900">Indikatorenberechnung</h2>
            <p class="mt-1 text-sm leading-6 text-gray-600">
                Hier können die Berechnungen der jeweiligen Indikatoren angestoßen werden. Dazu wählen Sie bitte aus
                den beiden Auswahlmenüs die Einwohnermeldedaten aus, die als Berechnungsgrundlage dienen sollen -
                gekennzeichnet durch deren Stichtagsdatum. Außerdem müssen Sie eine sogenannte Referenzgeometrie
                auswählen, die als Aggregationsebene der Berechnungen dient.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3">
            <div>
                <h2 class="text-base font-semibold leading-7 text-gray-900">Basisinformationen</h2>
                <p class="mt-1 text-sm leading-6 text-gray-600">Platzhalter</p>
            </div>

            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                <div class="col-span-full">
                    <label for="calculation_type" class="block text-sm font-medium leading-6 text-gray-900">
                        Indikator
                    </label>
                    <div class="mt-2">
                        <select
                            wire:model.fill.live="calculationType"
                            id="calculation_type"
                            name="calculation_type" required
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset
                            ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm
                            sm:leading-6"
                        >
                            <option value="median">Medianalter</option>
                            <option value="mean">Durchschnittsalter</option>
                            <option value="greying_index">Greying-Index</option>
                            <option value="child_dependency_ratio">Jugendquotient</option>
                            <option value="aged_dependency_ratio">Altenquotient</option>
                            <option value="total_dependency_ratio">Abhängigenquotient</option>
                            <option value="remanence_building">Remanenzgebäude</option>
                            <option value="qualifying_residents_age_group">Wohnberechtigte Einwohner (Altersgruppe)</option>
                            <option value="qualifying_residents_gender">Wohnberechtigte Einwohner (Geschlecht)</option>
                            <option value="net_migration">Wanderungssaldo</option>
                            <option value="project_population">Bevölkerungsprognose</option>
                            <option value="backcalculate_population">Rückrechnung der Bevölkerung</option>
                        </select>
                    </div>
                </div>

                <div class="col-span-full">
                    <label for="reference_geometry" class="block text-sm font-medium leading-6 text-gray-900">
                        Referenzgeometrie
                    </label>
                    <div class="mt-2">
                        <select
                            wire:model.fill="referenceGeometry"
                            id="reference_geometry"
                            name="reference_geometry" required
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset
                            ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm
                            sm:leading-6"
                        >
                            @foreach($referenceGeometries as $geometry)
                                <option value="{{ $geometry }}">{{ $geometry }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>
        </div>

        @if(in_array($calculationType,
            ["median", "mean", "greying_index", "child_dependency_ratio", "aged_dependency_ratio", "total_dependency_ratio", "remanence_building", "qualifying_residents_age_group", "qualifying_residents_gender"]
        ))
        <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3">
            <div>
                <h2 class="text-base font-semibold leading-7 text-gray-900">Zustandsindikatoren (Melderegister)</h2>
                <p class="mt-1 text-sm leading-6 text-gray-600">
                    Demographische Indikatoren, die einen Zustand zu einem bestimmten Zeitpunkt beschreibe und auf Basis
                    des Stammdatensatzes der Einwohnermeldedaten berechnet werden.
                </p>
            </div>

            <div class="max-w-2xl space-y-10 md:col-span-2">
                <div class="col-span-full">
                    <label for="date_of_dataset" class="block text-sm font-medium leading-6 text-gray-900">
                        Stichtag der Einwohnermeldedaten (Stammdaten)
                    </label>
                    <div class="mt-2">
                        <select
                            wire:model.fill.change="dateOfDataset"
                            id="date_of_dataset"
                            name="date_of_dataset" required
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset
                            ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm
                            sm:leading-6"
                        >
                            @foreach($datasetDates as $date)
                                <option value="{{ $date }}">{{ $date }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Transactional demographic indicators -->
        @if(in_array($calculationType,
            ["net_migration", "migration_volume"]
        ))
            <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3">
                <div>
                    <h2 class="text-base font-semibold leading-7 text-gray-900">Veränderungsindikatoren (Melderegister)</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-600">
                        Demographische Indikatoren, die einen Zustand zu einem bestimmten Zeitpunkt beschreibe und auf Basis
                        des Stammdatensatzes der Einwohnermeldedaten berechnet werden.
                    </p>
                </div>

                <div class="max-w-2xl space-y-10 md:col-span-2">
                    <div class="col-span-full">
                        <label for="date_of_dataset" class="block text-sm font-medium leading-6 text-gray-900">
                            Stichtag der Einwohnermeldedaten (Stammdaten)
                        </label>
                        <div class="mt-2">
                            <select
                                wire:model.fill.change="dateOfDataset"
                                id="date_of_dataset"
                                name="date_of_dataset" required
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset
                                ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm
                                sm:leading-6"
                            >
                                @foreach($datasetDates as $date)
                                    <option value="{{ $date }}">{{ $date }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Project Population -->
        @if(in_array($calculationType,
            ["project_population"]
        ))
            <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3">
                <div>
                    <h2 class="text-base font-semibold leading-7 text-gray-900">Bevölkerungsprognose</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-600">
                        Platzhalter
                    </p>
                </div>

                <div class="max-w-2xl space-y-10 md:col-span-2">
                    <div class="col-span-full">
                        <label for="start_year" class="block text-sm font-medium leading-6 text-gray-900">
                            Beginn der Prognose (Jahr)
                        </label>
                        <div class="mt-2">
                            <select
                                wire:model.fill="startYear"
                                id="start_year"
                                name="start_year" required
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset
                            ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm
                            sm:leading-6"
                            >
                                @foreach($datasetDates as $date)
                                    <option value="{{ $date }}">{{ $date }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- TODO: Int for target year -->
                </div>
            </div>
        @endif

         <!-- Backcalculate Population -->
        @if(in_array($calculationType,
            ["project_population"]
        ))
            <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3">
                <div>
                    <h2 class="text-base font-semibold leading-7 text-gray-900">Rückrechnung von Bevölkerungsdaten</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-600">
                        Platzhalter
                    </p>
                </div>

                <div class="max-w-2xl space-y-10 md:col-span-2">
                    <div class="col-span-full">
                        <label for="start_year" class="block text-sm font-medium leading-6 text-gray-900">
                            Ausgangsjahr der Rückrechnung
                        </label>
                        <div class="mt-2">
                            <select
                                wire:model.fill="startYear"
                                id="start_year"
                                name="start_year" required
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset
                            ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm
                            sm:leading-6"
                            >
                                @foreach($datasetDates as $date)
                                    <option value="{{ $date }}">{{ $date }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-span-full">
                        <label for="start_year" class="block text-sm font-medium leading-6 text-gray-900">
                            Platzhalter (Transaktionsjahre)
                        </label>
                        <div class="mt-2">
                            <select
                                wire:model.fill="startYear"
                                id="start_year"
                                name="start_year" required
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset
                            ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm
                            sm:leading-6"
                            >
                                @foreach($transactionYears as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>

    <div class="mt-6 flex items-center justify-end gap-x-6">
        <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Indikator berechnen</button>
    </div>
</form>

@if(session()->has('message'))
    <div>{{ session('message') }}</div>
@endif
