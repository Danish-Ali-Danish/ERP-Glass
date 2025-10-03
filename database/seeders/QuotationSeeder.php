<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quotation;

class QuotationSeeder extends Seeder
{
    public function run(): void
    {
        Quotation::create([
            'quote_no'     => 'QTN-00000001',
            'date'         => now()->toDateString(),
            'to_name'      => 'Mr. John Doe',
            'company_name' => 'ABC Construction Ltd.',
            'project_name' => 'Building Renovation',
            'location'     => 'Karachi, Pakistan',
            'total'        => 5000,
            'vat'          => 250,
            'grand_total'  => 5250,
            'terms'        => 'Payment due within 30 days.',
        ]);

        Quotation::create([
            'quote_no'     => 'QTN-00000002',
            'date'         => now()->subDays(2)->toDateString(),
            'to_name'      => 'Ms. Sarah Smith',
            'company_name' => 'XYZ Engineering',
            'project_name' => 'Road Construction',
            'location'     => 'Lahore, Pakistan',
            'total'        => 10000,
            'vat'          => 500,
            'grand_total'  => 10500,
            'terms'        => '50% advance required.',
        ]);
    }
}
