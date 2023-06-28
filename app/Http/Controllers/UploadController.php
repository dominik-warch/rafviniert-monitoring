<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessUploadJob;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class UploadController extends Controller
{
    public function create(): View
    {
        return view('uploads.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'dataset_date' => 'required|date',
        ]);

        $path = $request->file('file')->store('uploads');
        $headers = Excel::toArray(new HeadingRowImport, $path)[0][0];

        session(['headers' => $headers, 'file_path' => $path, 'dataset_date' => $request->input('dataset_date')]);

        // Redirect to the mapping form
        return redirect()->route('mapping_form');

    }

    public function mapping()
    {
        // Retrieve the headers from the session
        $headers = session('headers');

        // Pass the headers to the mapping view
        return view('uploads.mapping', ['headers' => $headers]);
    }

    public function storeMapping(Request $request)
    {
        // Create a new upload record
        $upload = Upload::create([
            'file_path' => session('file_path'),
            'dataset_date' => session('dataset_date'),
            'column_mapping' => $request->input('columns'),
        ]);

        // Dispatch the job to process the upload
        ProcessUploadJob::dispatch($upload);

        // Redirect to a success page
        return redirect()->route('upload_form');
    }
}
