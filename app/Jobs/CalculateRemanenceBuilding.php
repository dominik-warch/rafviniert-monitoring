<?php

namespace App\Jobs;

use App\Models\RemanenceBuilding;
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
 * Job to calculate remanence buildings for given reference geometry and dataset date.
 * Note: The only meaningful reference geometry are building polygons.
 * Note: Remanence buildings are buildings in which only 1-2 people over the age of 60 live (Schaffert 2018)
 */
class CalculateRemanenceBuilding implements ShouldQueue
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

                        $elderlyCount = 0;
                        $allResidentsAreElderly = true;

                        foreach ($points as $point) {
                            $age = $this->calculateAge($point->year_of_birth, $this->dateOfDataset);
                            if ($age >= 60) {
                                $elderlyCount++;
                            } else {
                                $allResidentsAreElderly = false;
                                break; // Stop processing as soon as we find a resident under 75
                            }
                        }

                        if (($elderlyCount == 1 || $elderlyCount == 2) && $allResidentsAreElderly) {
                            $now = now();

                            $resultsToInsert[] = [
                                'name' => 'remanence_building',
                                'value' => $elderlyCount,
                                'date_of_dataset' => $this->dateOfDataset,
                                'reference_geometry' => $this->referenceGeometry,
                                'geometry' => $feature->geometry,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        }
                        Log::info('Remanence building completed successfully.');

                    } catch (Exception $e) {
                        Log::error("Error processing feature $feature->id: {$e->getMessage()}");
                    }
                }

                try {
                    DB::transaction(function () use ($resultsToInsert) {
                        RemanenceBuilding::insert($resultsToInsert);
                    });
                } catch (Exception $e) {
                    Log::error("Error importing: {$e->getMessage()}");
                }

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
}

