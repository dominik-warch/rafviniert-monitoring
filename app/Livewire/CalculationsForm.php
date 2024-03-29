<?php

namespace App\Livewire;

use App\Jobs\BackCalculatePopulation;
use App\Jobs\CalculateAgedDependencyRatio;
use App\Jobs\CalculateChildDependencyRatio;
use App\Jobs\CalculateGreyingIndex;
use App\Jobs\CalculateMeanAge;
use App\Jobs\CalculateMedianAge;
use App\Jobs\CalculateQualifyingResidentsAgeGroup;
use App\Jobs\CalculateQualifyingResidentsGender;
use App\Jobs\CalculateRemanenceBuilding;
use App\Jobs\CalculateTotalDependencyRatio;
use App\Jobs\CalculateNetMigration;
use App\Jobs\CalculateMigrationVolume;
use App\Models\CitizensMaster;
use App\Models\CitizensTransaction;
use App\Models\ReferenceGeometry;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class CalculationsForm extends Component
{
    public Collection $referenceGeometries;
    public Collection $datasetDates;
    public string $referenceGeometry;
    public string $dateOfDataset;
    public string $calculationType;
    public Collection $transactionDatasetDates;
    public string $dateOfTransactionDataset;
    public Collection $transactionYears;
    public string $transactionYear;

    public function mount(): void
    {
        $this->referenceGeometries = ReferenceGeometry::distinct('name')->pluck('name');
        $this->datasetDates = CitizensMaster::distinct('dataset_date')->pluck('dataset_date');
        $this->transactionDatasetDates = CitizensTransaction::distinct('dataset_date')->pluck('dataset_date');
        $this->transactionYears = new Collection();

        $this->dateOfDataset = $this->datasetDates->first() ?? '';
        $this->referenceGeometry = $this->referenceGeometries->first() ?? '';

        $this->startYear;
        $this->endYear;

        // Only call updatedDateOfTransactionDataset if transactionDatasetDates is not empty
        if ($this->transactionDatasetDates->isNotEmpty()) {
            $this->dateOfTransactionDataset = $this->transactionDatasetDates->first();
            $this->updatedDateOfTransactionDataset($this->dateOfTransactionDataset);
        } else {
            $this->dateOfTransactionDataset = '';
        }
    }

    public function updatedDateOfTransactionDataset($value): void
    {
        $parsedDate = Carbon::createFromFormat("Y-m-d", $value);
        $this->transactionYears = CitizensTransaction::whereYear("dataset_date", $parsedDate)
            ->distinct()
            ->get(['transaction_date'])
            ->map(function ($item) {
                return Carbon::createFromFormat('Y-m-d', $item->transaction_date)->year;
            })->unique();
    }

    private function getValidationRules(): array
    {
        $validationRules = [
            'calculationType' => 'required|in:median,mean,greying_index,child_dependency_ratio,aged_dependency_ratio,total_dependency_ratio,remanence_building,qualifying_residents_age_group,qualifying_residents_gender,net_migration, project_population, backcalculate_population',
        ];

        if (in_array($this->calculationType, ["median", "mean", "greying_index", "child_dependency_ratio", "aged_dependency_ratio", "total_dependency_ratio", "remanence_building", "qualifying_residents_age_group", "qualifying_residents_gender"])) {
            $validationRules['dateOfDataset'] = 'required|date';
            $validationRules['referenceGeometry'] = 'required|exists:reference_geometries,name';
        } elseif (in_array($this->calculationType, ["net_migration"])) {
            $validationRules['transactionYear'] = 'required|integer';
            $validationRules['referenceGeometry'] = 'required|exists:reference_geometries,name';
        } elseif (in_array($this->calculationType, ["project_population"])) {
            $validationRules['startYear'] = 'required|integer';
            $validationRules['endYear'] = 'required|integer|gte:startYear';
            $validationRules['referenceGeometry'] = 'required|exists:reference_geometries,name';
        } elseif (in_array($this->calculationType, ["backcalculate_population"])) {
            $validationRules['startYear'] = 'required|integer';
            $validationRules['endYear'] = 'required|integer|gte:startYear';
        } 

        return $validationRules;
    }

    private function dispatchCalculationJob()
    {
        switch ($this->calculationType) {
            case "median":
                CalculateMedianAge::dispatch($this->referenceGeometry, $this->dateOfDataset);
                break;
            case "mean":
                CalculateMeanAge::dispatch($this->referenceGeometry, $this->dateOfDataset);
                break;
            case "greying_index":
                CalculateGreyingIndex::dispatch($this->referenceGeometry, $this->dateOfDataset);
                break;
            case "child_dependency_ratio":
                CalculateChildDependencyRatio::dispatch($this->referenceGeometry, $this->dateOfDataset);
                break;
            case "aged_dependency_ratio":
                CalculateAgedDependencyRatio::dispatch($this->referenceGeometry, $this->dateOfDataset);
                break;
            case "total_dependency_ratio":
                CalculateTotalDependencyRatio::dispatch($this->referenceGeometry, $this->dateOfDataset);
                break;
            case "remanence_building":
                CalculateRemanenceBuilding::dispatch($this->referenceGeometry, $this->dateOfDataset);
                break;
            case "qualifying_residents_age_group":
                CalculateQualifyingResidentsAgeGroup::dispatch($this->referenceGeometry, $this->dateOfDataset);
                break;
            case "qualifying_residents_gender":
                CalculateQualifyingResidentsGender::dispatch($this->referenceGeometry, $this->dateOfDataset);
                break;
            case "net_migration":
                CalculateNetMigration::dispatch($this->referenceGeometry, $this->transactionYear);
                break;
            case "migration_volume":
                CalculateMigrationVolume::dispatch($this->referenceGeometry, $this->transactionYear);
                break;
            case "project_population":
                ProjectPopulation::dispatch($this->referenceGeometry, $this->startYear, $this->endYear);
                break;
            case "backcalculate_population":
                BackCalculatePopulation::dispatch($this->referenceGeometry, $this->startYear, $this->endYear);
                break;
            default:
                Log::error('Unknown calculation type.');
                return Redirect::route('dashboard')->error("Oh, da hat etwas nicht funktioniert!");
        }
    }

    public function calculate()
    {
        $this->validate($this->getValidationRules());

        try {
            $this->dispatchCalculationJob();
        } catch (Exception $e) {
            Log::error('Failed to start calculation: ' . $e->getMessage());
            Toaster::error('Oh, da hat etwas nicht funktioniert!');
        }

        Toaster::success('Berechnung angestoßen!');
    }

    public function render()
    {
        return view('livewire.calculations-form');
    }
}
