<?php

namespace App\Models;

use Clickbar\Magellan\Database\Eloquent\HasPostgisColumns;
use Illuminate\Database\Eloquent\Model;

class MedianAge extends Model
{
    use HasPostgisColumns;

    protected array $postgisColumns = [
        'geometry' => [
            'type' => 'geometry',
            'srid' => 4326,
        ],
    ];

    protected $table = 'median_ages';

    protected $fillable = [
        'name',
        'value',
        'date_of_dataset',
        'reference_geometry',
        'geometry'
    ];
}
