<?php

namespace App\Models;

use Clickbar\Magellan\Database\Eloquent\HasPostgisColumns;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static insert(array $resultsToInsert)
 */
class QualifyingResidentsGender extends Model
{
    use HasPostgisColumns;

    protected array $postgisColumns = [
        "geometry" => [
            "type" => "geometry",
            "srid" => 4326,
        ],
    ];

    protected $table = 'qualifying_residents_gender';

    protected $fillable = [
        "name",
        "value_gender_1",
        "value_gender_2",
        "value_gender_3",
        "date_of_dataset",
        "reference_geometry",
        "geometry"
    ];
}
