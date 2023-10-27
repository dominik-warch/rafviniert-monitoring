<?php

namespace App\Jobs;

use App\Imports\CitizensTransactionImport;
use App\Models\ImportTransaction;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;


class ImportCitizenTransactionData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ImportTransaction $upload;

    /**
     * Create a new job instance.
     */
    public function __construct(ImportTransaction $upload)
    {
        $this->upload = $upload;
    }

    /**
     * Execute the job.
     * @throws Exception
     */
    public function handle(): void
    {
        try {
            $import = new CitizensTransactionImport($this->upload->column_mapping, $this->upload->dataset_date, $this->upload->transaction_type);

            // Import the data from the uploaded file
            Excel::import($import, $this->upload->file_path);
            Log::info("Finished import");
        } catch (Exception $e) {
            Log::error('Error during Excel import: ' . $e->getMessage());
            throw $e;  // Re-throw the exception to ensure it's handled by the global exception handler or any subsequent code.
        }

    }

    /**
     * The maximum number of seconds the job can run before timing out.
     *
     * @var int
     */
    public int $timeout = 7200;
}
