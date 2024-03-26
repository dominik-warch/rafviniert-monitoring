<?php

namespace App\Jobs;

use App\Models\CitizensTransaction;
use App\Models\ReferenceGeometry;
use App\Models\NetMigration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Job to calculate the net migration for given reference geometry and year.
 */
class CalculateNetMigration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $referenceGeometry;
    protected $year;

    /**
     * Create a new job instance.
     *
     * @param string $referenceGeometry The name of the reference geometry.
     * @param int $year The year for which the calculations are performed.
     */
    public function __construct(string $referenceGeometry, int $year)
    {
        $this->referenceGeometry = $referenceGeometry;
        $this->year = $year;
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
                        $inflows = CitizensTransaction::whereYear('transaction_date', $this->year)
                            ->where('transaction_type', 'Hinzüge')
                            ->whereRaw('ST_Contains(?, geometry)', [$feature->geometry])
                            ->count();

                        $outflows = CitizensTransaction::whereYear('transaction_date', $this->year)
                            ->where('transaction_type', 'Wegzüge')
                            ->whereRaw('ST_Contains(?, geometry)', [$feature->geometry])
                            ->count();

                        $netMigration = $inflows - $outflows;

                        $now = now();
                        $resultsToInsert[] = [
                            'name' => 'net_migration',
                            'value' => $netMigration,
                            'year' => $this->year,
                            'reference_geometry' => $this->referenceGeometry,
                            'geometry' => $feature->geometry,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];

                        Log::info('Net Migration calculation completed successfully.');
                    } catch (\Exception $e) {
                        Log::error("Error processing feature {$feature->id}: {$e->getMessage()}");
                    }
                }

                DB::transaction(function () use ($resultsToInsert) {
                    NetMigration::insert($resultsToInsert);
                });
            });
    }
}
