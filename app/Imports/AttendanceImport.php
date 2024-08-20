<?php

namespace App\Imports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AttendanceImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Attendance([
            'code' => $row['name'],
            'datetime' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['datetime'])->format('Y-m-d H:i:s'),
        ]);
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function map($row): array
    {
        return [
            'name' => $row['name'],
            'datetime' => $row['datetime'],
        ];
    }
}