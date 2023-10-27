<?php

namespace App\Http\Controllers;

use App\Jobs\ImportCitizenTransactionData;
use App\Models\ImportTransaction;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class ImportCitizensTransactionController extends Controller
{
    public function create(): View
    {
        return view('import.citizens_transaction.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'transaction_type_table' => 'nullable|string',
            'dataset_date' => 'required|date',
        ]);

        $path = $request->file('file')->store('import');

        $headers = array_map('strtolower', Excel::toArray(new HeadingRowImport, $path)[0][0]);
        $headers = array_map(function($header) {
            return preg_replace('/[^a-z]/', '', strtolower($header));
        }, $headers);


        session([
            'headers' => $headers,
            'file_path' => $path,
            'transaction_type_table' => $request->input('transaction_type_table'),
            'dataset_date' => $request->input('dataset_date')
        ]);

        // Redirect to the mapping form
        return redirect()->route('import.citizens-transaction.mapping.create');

    }

    public function mapping()
    {
        // Retrieve the headers from the session
        $headers = session('headers');

        // Pass the headers to the mapping view
        return view('import.citizens_transaction.mapping', ['headers' => $headers]);
    }

    public function storeMapping(Request $request)
    {
        // Create a new upload record
        $upload = ImportTransaction::create([
            'file_path' => session('file_path'),
            'dataset_date' => session('dataset_date'),
            'transaction_type' => session('transaction_type_table'),
            'column_mapping' => $request->input('columns'),
        ]);

        // Dispatch the job to process the upload
        ImportCitizenTransactionData::dispatch($upload);
        Log::info("Dispatched job: Import of citizen transaction data");

        // Redirect to a success page
        return redirect()->route('import.citizens-transaction.create');
    }
}
