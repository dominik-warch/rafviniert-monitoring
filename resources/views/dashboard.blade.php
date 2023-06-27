<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <!--<x-welcome />-->
                <label for="columns">Column Mapping:</label>
                <select id="columns" name="columns[]" multiple>
                    <option value="zip_code">Zip Code</option>
                    <option value="city">City</option>
                    <option value="street">Street</option>
                    <option value="housenumber">House Number</option>
                    <option value="housenumber_extra">House Number Extra</option>
                    <option value="year_of_birth">Year of Birth</option>
                    <option value="sex">Sex</option>
                </select>
            </div>
        </div>
    </div>
</x-app-layout>
