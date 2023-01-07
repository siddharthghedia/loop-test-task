<?php

namespace App\Imports;

use App\Models\Customer;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $registeredSince = explode(',', $row['registered_since']);
        $registeredSinceDate = $registeredSince[1].','.$registeredSince[2];

        return new Customer([
            'job_title' => $row['job_title'],
            'email' => strtolower($row['email_address']),
            'name' => $row['firstname_lastname'],
            'day' => $registeredSince[0],
            'registered_since' => Carbon::parse($registeredSinceDate)->format('Y-m-d'),
            'phone' => $row['phone']
        ]);
    }
}
