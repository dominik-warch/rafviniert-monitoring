<?php

namespace App\Jobs;

use App\Models\QualifyingResidentsGender;
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
 * Job to calculate the percentage share of the qualifying residents of the three main age groups in the total
 * qualifying residents for given reference geometry and dataset date.
 */
class CalculateQualifyingResidentsGender implements ShouldQueue
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

                        $gender1Count = 0; // m
                        $gender2Count = 0; // w
                        $gender3Count = 0; // d
                        $totalResidentsCount = count($points);

                        foreach ($points as $point) {
                            if ($point->gender == "m") {
                                $gender1Count++;
                            } else if ($point->gender == "w") {
                                $gender2Count++;
                            } else {
                                $gender3Count++;
                            }
                        }

                        if (!empty($totalResidentsCount)) {
                            $now = now();

                            $resultsToInsert[] = [
                                'name' => 'qualifying_residents_gender',
                                'value_gender_1' => $gender1Count * 100 / $totalResidentsCount,
                                'value_gender_2' => $gender2Count * 100 / $totalResidentsCount,
                                'value_gender_3' => $gender3Count * 100 / $totalResidentsCount,
                                'date_of_dataset' => $this->dateOfDataset,
                                'reference_geometry' => $this->referenceGeometry,
                                'geometry' => $feature->geometry,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        }
                        Log::info('Qualifying Residents (Gender) calculation completed successfully.');

                    } catch (Exception $e) {
                        Log::error("Error processing feature $feature->id: {$e->getMessage()}");
                    }
                }

                // Insert the calculated results into the database using a transaction
                DB::transaction(function () use ($resultsToInsert) {
                    QualifyingResidentsGender::insert($resultsToInsert);
                });
            });
    }
}

