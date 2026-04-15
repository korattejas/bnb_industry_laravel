<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Product::leftJoin('product_categories as sc', 'sc.id', '=', 'products.category_id')
            ->select(
                'products.id',
                'sc.name as category_name',
                'products.name',
                'products.price',

                'products.description',
                'products.includes',
                'products.status',
                'products.is_popular'
            )
            ->get()
            ->map(function ($row) {
                // $price = (float) $row->price;
                // $discount = (float) $row->discount_price;
                // $total = $discount > 0 ? $price - $discount : $price;
                // $percent = ($price > 0 && $discount > 0) ? round(($discount / $price) * 100, 2) : 0;

                return [
                    'ID'            => $row->id,
                    'Category'      => $row->category_name,
                    'Name'          => $row->name,
                    'Price'         => $row->price ?? 0,

                    'Description'   => $row->description,
                    'Includes'      => $row->includes ? implode(', ', json_decode($row->includes, true)) : '-',
                    'Status'        => $row->status ? 'Active' : 'Inactive',
                    'Is Popular'    => $row->is_popular ? 'Yes' : 'No',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Category',
            'Name',
            'Price',

            'Description',
            'Includes',
            'Status',
            'Is Popular',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '000000'],
                ],
                'alignment' => [
                    'horizontal' => 'center',
                ],
            ],
        ];
    }
}
