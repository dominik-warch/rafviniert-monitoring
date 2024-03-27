<?php
namespace App\Jobs;

use App\Models\CitizensMaster;
use App\Models\CitizensTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackCalculatePopulation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $startYear;
    protected $endYear;

    public function __construct(int $startYear, int $endYear)
    {
        $this->startYear = $startYear;
        $this->endYear = $endYear;
    }

    /**
     * Execute the job.
     * @throws Throwable
     */
    public function handle(): void
    {
        try {
            // Retrieve the CitizensMaster dataset for the end year
            $citizensMaster = CitizensMaster::whereYear('dataset_date', $this->endYear)->get();

            // Iterate over the years from endYear to startYear
            for ($year = $this->endYear - 1; $year >= $this->startYear; $year--) {
                // Create a new collection for the current year
                $backCalculatedData = $citizensMaster->map(function ($record) use ($year) {
                    return [
                        'gender' => $record->gender,
                        'year_of_birth' => $record->year_of_birth,
                        'dataset_date' => $year . '-01-01',
                        'zip_code' => $record->zip_code,
                        'city' => $record->city,
                        'street' => $record->street,
                        'housenumber' => $record->housenumber,
                        'housenumber_ext' => $record->housenumber_ext,
                        'geometry' => $record->geometry,
                    ];
                });

                // Retrieve the CitizensTransaction data for the current year
                $transactions = CitizensTransaction::whereYear('transaction_date', $year)->get();

                // Process each transaction
                foreach ($transactions as $transaction) {
                    switch ($transaction->transaction_type) {
                        case 'Geburtsfall':
                            // Remove records with 'birth' transactions from the back-calculated data
                            $backCalculatedData = $backCalculatedData->reject(function ($record) use ($transaction) {
                                return $record['gender'] == $transaction->gender
                                    && $record['year_of_birth'] == $transaction->year_of_birth
                                    && $record['zip_code'] == $transaction->zip_code
                                    && $record['city'] == $transaction->city
                                    && $record['street'] == $transaction->street
                                    && $record['housenumber'] == $transaction->housenumber
                                    && $record['housenumber_ext'] == $transaction->housenumber_ext;
                            });
                            break;

                        case 'Sterbefall':
                            // Add records with 'death' transactions to the back-calculated data
                            $backCalculatedData->push([
                                'gender' => $transaction->gender,
                                'year_of_birth' => $transaction->year_of_birth,
                                'dataset_date' => $year . '-01-01',
                                'zip_code' => $transaction->zip_code,
                                'city' => $transaction->city,
                                'street' => $transaction->street,
                                'housenumber' => $transaction->housenumber,
                                'housenumber_ext' => $transaction->housenumber_ext,
                                'geometry' => $transaction->geometry,
                            ]);
                            break;

                        case 'Zuzug':
                            // Remove records with 'move_in' transactions from the back-calculated data
                            $backCalculatedData = $backCalculatedData->reject(function ($record) use ($transaction) {
                                return $record['gender'] == $transaction->gender
                                    && $record['year_of_birth'] == $transaction->year_of_birth
                                    && $record['zip_code'] == $transaction->zip_code
                                    && $record['city'] == $transaction->city
                                    && $record['street'] == $transaction->street
                                    && $record['housenumber'] == $transaction->housenumber
                                    && $record['housenumber_ext'] == $transaction->housenumber_ext;
                            });
                            break;

                        case 'Wegzug':
                            // Add records with 'move_out' transactions to the back-calculated data
                            $backCalculatedData->push([
                                'gender' => $transaction->gender,
                                'year_of_birth' => $transaction->year_of_birth,
                                'dataset_date' => $year . '-01-01',
                                'zip_code' => $transaction->zip_code,
                                'city' => $transaction->city,
                                'street' => $transaction->street,
                                'housenumber' => $transaction->housenumber,
                                'housenumber_ext' => $transaction->housenumber_extra,
                                'geometry' => $transaction->geometry,
                            ]);
                            break;

                        case 'Umzug':
                            // Update the address of existing entries in the back-calculated data for moves within the dataset
                            $backCalculatedData = $backCalculatedData->map(
                                function ($record) use ($transaction) {
                                    if ($record['gender'] == $transaction->gender
                                        && $record['year_of_birth'] == $transaction->year_of_birth
                                        && $record['zip_code'] == $transaction->new_zip_code
                                        && $record['city'] == $transaction->new_city
                                        && $record['street'] == $transaction->new_street
                                        && $record['housenumber'] == $transaction->new_housenumber
                                        && $record['housenumber_ext'] == $transaction->new_housenumber_ext) {
                                            $record['zip_code'] = $transaction->old_zip_code;
                                            $record['city'] = $transaction->old_city;
                                            $record['street'] = $transaction->old_street;
                                            $record['housenumber'] = $transaction->old_housenumber;
                                            $record['housenumber_ext'] = $transaction->old_housenumber_extra;
                                        }
                                    return $record;
                                    });
                            break;
                    }
                }

                // Insert the back-calculated CitizensMaster data for the current year using a transaction
                DB::transaction(function () use ($backCalculatedData) {
                    CitizensMaster::insert($backCalculatedData->toArray());
                });

                Log::info("Back-calculated CitizensMaster data for year {$year} inserted successfully.");
            }
        } catch (\Exception $e) {
            Log::error("Error in BackCalculatePopulation job: " . $e->getMessage());
            throw $e;
        }
    }
}