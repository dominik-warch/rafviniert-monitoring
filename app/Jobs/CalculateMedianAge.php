<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\CitizensMaster;
use App\Models\ReferenceGeometry;
use App\Models\MedianAge;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Job to calculate the median age for given reference geometry and dataset date.
 */
class CalculateMedianAge implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $referenceGeometry;
    protected $dateOfDataset;

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
     */
    public function handle()
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
                            $medianAge = $this->calculateMedian($ageValues);
                            $now = now();

                            $resultsToInsert[] = [
                                'name' => 'median_age',
                                'value' => $medianAge,
                                'date_of_dataset' => $this->dateOfDataset,
                                'reference_geometry' => $this->referenceGeometry,
                                'geometry' => $feature->geometry,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        }
                    } catch (\Exception $e) {
                        Log::error("Error processing feature {$feature->id}: {$e->getMessage()}");
                    }
                }

                // Insert the calculated results into the database using a transaction
                DB::transaction(function () use ($resultsToInsert) {
                    MedianAge::insert($resultsToInsert);
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

    /**
     * Calculate the median from a list of numbers.
     *
     * @param array $numbers The list of numbers.
     * @return float The median value.
     */
    private function calculateMedian(array $numbers): float
    {
        sort($numbers);
        $count = count($numbers);
        if ($count % 2 === 0) {
            return ($numbers[$count / 2 - 1] + $numbers[$count / 2]) / 2;
        }

        return $numbers[($count - 1) / 2];
    }
}

