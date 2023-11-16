<?php

namespace App\Models;

use Clickbar\Magellan\Database\Eloquent\HasPostgisColumns;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static insert(array $resultsToInsert)
 */
class QualifyingResidentsAgeGroup extends Model
{
    use HasPostgisColumns;

    protected array $postgisColumns = [
        "geometry" => [
            "type" => "geometry",
            "srid" => 4326,
        ],
    ];

    protected $table = 'qualifying_residents_age_group';

    protected $fillable = [
        "name",
        "value_age_group_1",
        "value_age_group_2",
        "value_age_group_3",
        "date_of_dataset",
        "reference_geometry",
        "geometry"
    ];
}
