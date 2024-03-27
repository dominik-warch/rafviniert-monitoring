<?php

namespace App\Models;

use Clickbar\Magellan\Database\Eloquent\HasPostgisColumns;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static distinct(string $string)
 * @method static whereYear(string $string, \Carbon\Carbon|false $parsedDate)
 */
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
        'old_zip_code',
        'old_city',
        'old_street',
        'old_housenumber',
        'old_housenumber_extra',
        'geometry'
    ];
}
