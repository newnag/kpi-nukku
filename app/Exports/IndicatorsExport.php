<?php

// app/Exports/IndicatorsExport.php
namespace App\Exports;

use App\Models\Indicator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Events\AfterSheet;

class IndicatorsExport implements FromCollection, WithEvents
{
    private $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        // คืน collection ว่าง เพื่อให้เราควบคุม output ใน AfterSheet
        return new Collection([]);
    }

    private function getData()
    {
        $q = Indicator::query()
            ->select([
                'indicators.id',
                'indicators.name',
                'indicators.code',
                'indicators.year',
                'categories.name as category_name',
                'standards.name as standard_name'
            ])
            ->leftJoin('categories', 'categories.id', '=', 'indicators.categorie_id')
            ->leftJoin('standards', 'standards.id', '=', 'categories.standard_id');

        // ===== ฟิลเตอร์ =====
        if ($y = $this->filters['year'] ?? null) {
            $q->where('indicators.year', $y);
        }
        if ($std = $this->filters['standard_id'] ?? null) {
            $q->where('categories.standard_id', $std);
        }
        if ($cat = $this->filters['category_id'] ?? null) {
            $q->where('indicators.categorie_id', $cat);
        }
        if ($st = $this->filters['status'] ?? null) {
            $q->where('indicators.status', $st);
        }
        if ($dep = $this->filters['dept_id'] ?? null) {
            $q->where('collector_users.department_id', $dep);
        }
        if ($code = $this->filters['code'] ?? null) {
            $q->where('indicators.code', 'ILIKE', "%{$code}%");
        }

        return $q->get()->groupBy('standard_name');
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $e) {
                $s = $e->sheet->getDelegate();
                $standards = $this->getData();

                // ลำดับด้านที่ fix
                $categoryOrder = [
                    'ด้านองค์กรและการบริหารองค์กร',
                    'ด้านบุคลากร',
                    'ด้านการจัดการศึกษา',
                    'ด้านการวิจัยและนวัตกรรมและผลผลิตทางวิชาการ',
                    'ด้านการบริการวิชาการ/วิชาชีพแก่สังคม',
                    'ด้านการทำนุบำรุงศิลปะและวัฒนธรรม',
                    'ด้านนิสิต/นักศึกษา',
                ];

                $row = 1;

                foreach ($standards as $stdName => $cats) {
                    // ===== หัวมาตรฐาน =====
                    $s->mergeCells("A{$row}:F{$row}")
                        ->setCellValue("A{$row}", "มาตรฐาน: {$stdName}");
                    $s->getStyle("A{$row}")->getFont()->setBold(true);
                    $row++;

                    // ===== loop ด้านตามลำดับ fix =====
                    foreach ($categoryOrder as $catName) {
                        $inds = $cats->where('category_name', $catName);

                        // หัวด้าน
                        $s->mergeCells("A{$row}:F{$row}")
                            ->setCellValue("A{$row}", "ด้าน: {$catName}");
                        $row++;

                        // หัวตาราง
                        $s->mergeCells("A{$row}:A" . ($row + 1))->setCellValue("A{$row}", 'ข้อ');
                        $s->mergeCells("B{$row}:B" . ($row + 1))->setCellValue("B{$row}", 'เกณฑ์มาตรฐาน');
                        $s->mergeCells("C{$row}:D{$row}")->setCellValue("C{$row}", 'ผลการดำเนินงาน');
                        $s->setCellValue("C" . ($row + 1), 'มี');
                        $s->setCellValue("D" . ($row + 1), 'ไม่มี');
                        $s->mergeCells("E{$row}:E" . ($row + 1))->setCellValue("E{$row}", 'รายงานผลการดำเนินงาน');
                        $s->mergeCells("F{$row}:F" . ($row + 1))->setCellValue("F{$row}", 'เอกสาร/หลักฐาน');
                        $row += 2;

                        // เติม Indicators ถ้ามี
                        $i = 1;
                        if ($inds->count() > 0) {
                            foreach ($inds as $ind) {
                                $s->setCellValue("A{$row}", $i);
                                $s->setCellValue("B{$row}", $ind->name ?? '');
                                $s->setCellValue("C{$row}", $ind->code ?? '');
                                $s->setCellValue("D{$row}", '');
                                $s->setCellValue("E{$row}", $ind->year ?? '');
                                $s->setCellValue("F{$row}", '-');
                                $row++;
                                $i++;
                            }
                        } else {
                            // ถ้าไม่มี indicator → แสดง "ไม่มีข้อมูล"
                            $s->setCellValue("A{$row}", '-');
                            $s->mergeCells("B{$row}:F{$row}")
                                ->setCellValue("B{$row}", 'ไม่มีข้อมูล');
                            $row++;
                        }

                        // ===== เกณฑ์การให้คะแนน + การประเมินตนเอง =====
                        $start = $row + 1;
                        $s->mergeCells("A{$start}:D{$start}")->setCellValue("A{$start}", 'เกณฑ์การให้คะแนน');
                        $s->mergeCells("E{$start}:F{$start}")->setCellValue("E{$start}", 'การประเมินตนเอง');

                        for ($j = 1; $j <= 5; $j++) {
                            $r = $start + $j;
                            $s->mergeCells("A{$r}:C{$r}")->setCellValue("A{$r}", '............................');
                            $s->setCellValue("D{$r}", '........... คะแนน');
                            $s->mergeCells("E{$r}:F{$r}")->setCellValue("E{$r}", $j == 3 ? '✓' : '');
                        }

                        $s->getStyle("A{$start}:F" . ($start + 5))->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                ],
                            ],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                                'wrapText' => true,
                            ],
                        ]);

                        $row = $start + 7;
                    }

                    $row += 2; // เว้นบรรทัดก่อนมาตรฐานใหม่
                }

                // ปรับความกว้างคอลัมน์
                $s->getColumnDimension('A')->setWidth(5);
                $s->getColumnDimension('B')->setWidth(50);
                $s->getColumnDimension('C')->setWidth(12);
                $s->getColumnDimension('D')->setWidth(12);
                $s->getColumnDimension('E')->setWidth(20);
                $s->getColumnDimension('F')->setWidth(25);

                // เส้นกรอบรวม
                $s->getStyle("A1:F{$row}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);
            },
        ];
    }
}
