<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class ProfilExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Voucher::with("customer")->get();
    }
    
    public function map($voucher): array
    {

        return [
            "#".$voucher->id,
            $voucher->customer->name,
            $voucher->customer->phone,
            $voucher->customer_id,
            $voucher->created_at,
            $voucher->updated_at,
            $voucher->customer->net_total()
        ];
    }
    public function headings(): array
    {
        return [
            'user_id',
            'user_phone',
            'user_first_name',
            'user_last_name',
            "user_jobTitle",
            "user_businessName",
            "location",
            "bio",
            "location"
        ];
    }
}
