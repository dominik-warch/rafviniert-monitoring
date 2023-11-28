<?php

namespace App\Http\Controllers;

use App\Jobs\ImportCitizenMasterData;
use App\Models\Import;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class ImportCitizensMasterController extends Controller
{
    public function create(): View
    {
        return view('import.citizens_master.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'dataset_date' => 'required|date',
        ]);

        $path = $request->file('file')->store('import');
        $headers = Excel::toArray(new HeadingRowImport, $path)[0][0];

        session(['headers' => $headers, 'file_path' => $path, 'dataset_date' => $request->input('dataset_date')]);

        // Redirect to the mapping form
        return redirect()->route('import.citizens-master.mapping.create');

    }

    public function mapping()
    {
        // Retrieve the headers from the session
        $headers = session('headers');

        // Pass the headers to the mapping view
        return view('import.citizens_master.mapping', ['headers' => $headers]);
    }

    public function storeMapping(Request $request)
    {
        // Create a new upload record
        $upload = Import::create([
            'file_path' => session('file_path'),
            'dataset_date' => session('dataset_date'),
            'column_mapping' => $request->input('columns'),
        ]);

        // Dispatch the job to process the upload
        ImportCitizenMasterData::dispatch($upload);
        Log::info("Dispatched job: Import of citizen master data");

        // Redirect to a success page
        return Redirect::route('import.citizens-master.create')->info('Import angestoßen, das kann etwas dauern.');
    }
}
