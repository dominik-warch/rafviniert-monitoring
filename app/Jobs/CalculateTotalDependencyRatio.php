<?php

namespace App\Jobs;

use App\Models\TotalDependencyRatio;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\CitizensMaster;
use App\Models\ReferenceGeometry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Job to calculate the mean age for given reference geometry and dataset date.
 */
class CalculateTotalDependencyRatio implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $referenceGeometry;
    protected string $dateOfDataset;

    /**
     * Create a new job instance.
     *
     * @param string $referenceGeometry The name of the reference geometry.
     * @param string $dateOfDataset The dataset date for the calculations.
     */
    public function __construct(string $referenceGeometry, string $dateOfDataset)
    {
        $this->referenceGeometry = $referenceGeometry;
        $this->dateOfDataset = $dateOfDataset;
    }

    /**
     * Execute the job.
     * @throws Throwable
     */
    public function handle(): void
    {
        // Fetch all polygons for the given reference geometry
        // Use chunking to avoid memory issues with large datasets
        ReferenceGeometry::where('name', $this->referenceGeometry)
            ->chunk(200, function ($referenceGeometries) {
                $resultsToInsert = [];

                foreach ($referenceGeometries as $feature) {
                    try {
                        // Get all citizens within the current polygon for the selected dataset year
                        $points = CitizensMaster::where('dataset_date', $this->dateOfDataset)
                            ->whereRaw(
                                'ST_Contains(?, geometry)',
                                [$feature->geometry]
                            )
                            ->cursor();

                        $ageValues = [];
                        foreach ($points as $point) {
                            $ageValues[] = $this->calculateAge($point->year_of_birth, $this->dateOfDataset);
                        }

                        if (!empty($ageValues)) {
                            $p0_14 = $this->countRangeInList($ageValues, 0, 14);
                            $p65plus = $this->countRangeInList($ageValues, 65, 120); // assuming a max age of 120
                            $p15_64 = $this->countRangeInList($ageValues, 15, 64);

                            $value = $p15_64 == 0 ? 0 : ($p0_14 + $p65plus) / $p15_64;

                            $now = now();

                            $resultsToInsert[] = [
                                'name' => 'total_dependency_ratio',
                                'value' => $value,
                                'date_of_dataset' => $this->dateOfDataset,
                                'reference_geometry' => $this->referenceGeometry,
                                'geometry' => $feature->geometry,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        }
                        Log::info('Total Dependency Ratio calculation completed successfully.');

                    } catch (Exception $e) {
                        Log::error("Error processing feature $feature->id: {$e->getMessage()}");
                    }
                }

                // Insert the calculated results into the database using a transaction
                DB::transaction(function () use ($resultsToInsert) {
                    TotalDependencyRatio::insert($resultsToInsert);
                });
            });
    }

    /**
     * Calculate age based on year of birth and dataset date.
     *
     * @param string $yearOfBirth The year of birth of the citizen.
     * @param string $datasetDate The dataset date.
     * @return int Age of the citizen.
     */
    private function calculateAge(string $yearOfBirth, string $datasetDate): int
    {
        return date('Y', strtotime($datasetDate)) - $yearOfBirth;
    }

    protected function countRangeInList($list, $min, $max): int
    {
        return count(array_filter($list, function ($value) use ($min, $max) {
            return $min <= $value && $value <= $max;
        }));
    }
}

