<?php

namespace App\Models;

use Clickbar\Magellan\Database\Eloquent\HasPostgisColumns;
use Illuminate\Database\Eloquent\Model;

class CitizensTransaction extends Model
{
    use HasPostgisColumns;

    protected array $postgisColumns = [
        'geometry' => [
            'type' => 'geometry',
            'srid' => 4326,
        ],
    ];

    protected $table = 'citizens_transaction';

    protected $fillable = [
        'transaction_type',
        'transaction_date',
        'gender',
        'year_of_birth',
        'dataset_date',
        'zip_code',
        'city',
        'street',
        'housenumber',
        'housenumber_ext',
        'geometry'
    ];
}
