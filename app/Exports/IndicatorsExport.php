<?php

// app/Exports/IndicatorsExport.php
namespace App\Exports;

use App\Models\Indicator;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Events\AfterSheet;

class IndicatorsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    public function __construct(private array $filters = []) {}

    public function query()
    {
        $q = Indicator::query()
            ->whereHas('assignments')
            ->leftJoin('categories', 'categories.id', '=', 'indicators.categorie_id')
            ->leftJoin('standards', 'standards.id', '=', 'categories.standard_id')
            ->leftJoin('assignments', 'assignments.indicator_id', '=', 'indicators.id')
            ->leftJoin('users as collector_users', 'collector_users.id', '=', 'assignments.collector')
            ->leftJoin('departments', 'departments.id', '=', 'collector_users.department_id')
            ->with([
                'category:id,name,standard_id',
                'category.standard:id,name',
                'assignments.collectorUser' => fn($q) => $q->select('id', 'name', 'department_id'),
                'assignments.collectorUser.department:id,name',
            ])
            ->select([
                'indicators.id',
                'indicators.name',
                'indicators.code',
                'indicators.type',
                'indicators.year',
                'indicators.score_acc',
                'indicators.max_score',
                'indicators.status',
                'categories.name as category_name',
                'standards.name as standard_name',
                'departments.name as dept_name',
            ])
            ->groupBy(
                'indicators.id',
                'indicators.name',
                'indicators.code',
                'indicators.type',
                'indicators.year',
                'indicators.score_acc',
                'indicators.max_score',
                'indicators.status',
                'categories.name',
                'standards.name',
                'departments.name'
            );

        if ($y = $this->filters['year'] ?? null)         $q->where('indicators.year', $y);
        if ($s = $this->filters['standard_id'] ?? null)  $q->where('categories.standard_id', $s);
        if ($c = $this->filters['category_id'] ?? null)  $q->where('indicators.categorie_id', $c);
        if (($st = $this->filters['status'] ?? '') !== '') $q->where('indicators.status', $st);
        if ($d = $this->filters['dept_id'] ?? null)      $q->where('collector_users.department_id', $d);
        if ($code = $this->filters['code'] ?? null)      $q->where('indicators.code', 'like', "%$code%");

        return $q->orderBy('indicators.year')->orderBy('indicators.code');
    }

    public function headings(): array
    {
        return [
            'ปีการประเมิน',
            'ชื่อตัวบ่งชี้',
            'รหัส',
            'ประเภทตัวชี้วัด',
            'มาตรฐาน',
            'มิติ/หมวด',
            'หน่วยงานที่รับผิดชอบ',
            'ผลลัพธ์ (score_acc)',
            'คะแนนรวม (max_score)',
            'สถานะตัวชี้วัด',
        ];
    }

    public function map($row): array
    {
        $deptName = $row->dept_name
            ?? optional(optional($row->assignments->first())->collectorUser?->department)->name
            ?? '-';

        return [
            (string)($row->year ?? ''),
            (string)($row->name ?? ''),
            (string)($row->code ?? ''),
            (string)($row->type ?? ''),
            (string)($row->standard_name ?? optional($row->category?->standard)->name ?? ''),
            (string)($row->category_name ?? optional($row->category)->name ?? ''),
            (string)$deptName,
            (float)($row->score_acc ?? 0),
            (float)($row->max_score ?? 0),
            match ((int)($row->status ?? -1)) {
                0 => 'อยู่ระหว่างดำเนินการ',
                1 => 'ยังไม่ครบถ้วนตามเกณฑ์',
                2, 3 => 'ครบถ้วนตามเกณฑ์มาตรการ',
                default => 'ไม่ระบุ',
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $e) {
                $s = $e->sheet->getDelegate();
                // สไตล์หัวตาราง
                $s->getStyle('A1:J1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E5F3E8'],
                    ],
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);

                // Freeze header + AutoFilter
                $s->freezePane('A2');
                $s->setAutoFilter('A1:J1');

                // ปรับความกว้างเฉพาะบางคอลัมน์
                $s->getColumnDimension('B')->setWidth(50);
                $s->getColumnDimension('G')->setWidth(30);
                $s->getColumnDimension('J')->setWidth(28);
            },
        ];
    }
}
