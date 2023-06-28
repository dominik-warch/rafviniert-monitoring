<?php

namespace App\Jobs;

use App\Imports\CitizensMasterImport;
use App\Models\Import;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;


class ProcessImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $upload;

    /**
     * Create a new job instance.
     */
    public function __construct(Import $upload)
    {
        $this->upload = $upload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $import = new CitizensMasterImport($this->upload->column_mapping, $this->upload->dataset_date);

        // Import the data from the uploaded file
        Excel::import($import, $this->upload->file_path);
    }
}
