<?php
namespace App\Services;

use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class DataParsingService
{
    /**
     * @param $rawDate
     * @param $fileExtension
     * @return int|null
     */
    public function parseYearOfBirth($rawDate, $fileExtension): ?int
    {
        try {
            if (in_array($fileExtension, ["csv", "txt"])) {
                if (is_numeric($rawDate)) {
                    return intval($rawDate);
                } else if (is_string($rawDate) and strlen($rawDate) == 4) {
                    return (int) $rawDate;
                }

                $date = Carbon::createFromFormat("d.m.Y", $rawDate);
                return $date->year;
            } elseif (in_array($fileExtension, ["xlsx", "xls"])) {
                // Todo: ExcelToDateTimeObject may return false on failure, so check for it
                $date = ExcelDate::excelToDateTimeObject($rawDate);
                if ($date) {
                    return (int) $date->format("Y");
                }
            }
        } catch (Exception $e) {
            Log::error("Error parsing date: " . $e->getMessage());
        }

        // Handle the case where the date couldn't be parsed
        Log::warning("Could not parse year of birth for raw date '$rawDate' with file extension '$fileExtension'");
        return null; // Or return a default value like current year or a sentinel value like 0
    }

    /**
     * @param $rawDate
     * @param $fileExtension
     * @return Carbon|DateTime|false|null
     */
    public function parseDate($rawDate, $fileExtension): Carbon|DateTime|false|null
    {
        try {
            if (in_array($fileExtension, ["csv", "txt"])) {
                return Carbon::createFromFormat("d.m.Y", $rawDate);
            } elseif (in_array($fileExtension, ["xlsx", "xls"])) {
                // Todo: ExcelToDateTimeObject may return false on failure, so check for it
                return ExcelDate::excelToDateTimeObject($rawDate);
            }
        } catch (Exception $e) {
            Log::error("Error parsing date: " . $e->getMessage());
        }

        // Handle the case where the date couldn't be parsed
        Log::warning("Could not parse year of birth for raw date '$rawDate' with file extension '$fileExtension'");
        return null; // Or return a default value like current year or a sentinel value like 0
    }
}
