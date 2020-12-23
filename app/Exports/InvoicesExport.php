<?php

namespace App\Exports;

use App\Invoice;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;


class InvoicesExport implements FromArray,WithStyles
{
	protected $invoices;

	public function __construct(array $invoices)
	{
		$this->invoices = $invoices;
	}

	public function array(): array
	{
		return $this->invoices;
	}
	public function styles(Worksheet $sheet)
	{
		return [
            // Style the first row as bold text.
			1    => ['font' => ['bold' => true]],
			3    => ['font' => ['bold' => true]],




		];
	}
	
     
}

?>