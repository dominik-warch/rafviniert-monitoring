<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upload') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <form action="{{ route('store_mapping') }}" method="post">
                @csrf
                @php
                    $fields = ['zip_code', 'city', 'street', 'housenumber', 'housenumber_extra', 'year_of_birth', 'gender'];
                @endphp
                @foreach($fields as $field)
                    <div class="form-group">
                        <label for="{{ $field }}">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                        <select id="{{ $field }}" name="columns[{{ $field }}]">
                            <option value="">Select column...</option>
                            @foreach($headers as $header)
                                <option value="{{ $header }}">{{ $header }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>
</x-app-layout>
