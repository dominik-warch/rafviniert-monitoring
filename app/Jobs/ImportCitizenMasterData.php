<?php

namespace App\Jobs;

use App\Imports\CitizensMasterImport;
use App\Models\Import;
use App\Services\ExternalGeocodingService;
use App\Services\LocalGeocodingService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;


class ImportCitizenMasterData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Import $upload;

    /**
     * Create a new job instance.
     */
    public function __construct(Import $upload)
    {
        $this->upload = $upload;
    }

    /**
     * Execute the job.
     * @throws Exception
     */
    public function handle(): void
    {
        $localGeocodingService = new LocalGeocodingService();
        $externalGeocodingService = new ExternalGeocodingService();
        $fileExtension = pathinfo($this->upload->file_path, PATHINFO_EXTENSION);

        try {
            Log::info("Starting the import");
            $import = new CitizensMasterImport(
                $this->upload->column_mapping,
                $this->upload->dataset_date,
                $fileExtension,
                $localGeocodingService,
                $externalGeocodingService
            );

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
