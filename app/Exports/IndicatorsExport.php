<?php

// app/Exports/IndicatorsExport.php
namespace App\Exports;

use App\Models\Indicator;
use Illuminate\Support\Facades\DB;
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
                DB::raw('MAX(departments.name) as dept_name') // ✅ เลือกอันเดียว
            ])
            ->leftJoin('categories', 'categories.id', '=', 'indicators.categorie_id')
            ->leftJoin('standards', 'standards.id', '=', 'categories.standard_id')
            ->leftJoin('assignments', 'assignments.indicator_id', '=', 'indicators.id')
            ->leftJoin('users as collector_users', 'collector_users.id', '=', 'assignments.collector')
            ->leftJoin('departments', 'departments.id', '=', 'collector_users.department_id')
            ->whereExists(fn($sq) => $sq->select(DB::raw(1))
                ->from('assignments')
                ->whereColumn('indicators.id', 'assignments.indicator_id'))
            ->groupBy([
                'indicators.id',
                'indicators.name',
                'indicators.code',
                'indicators.type',
                'indicators.year',
                'indicators.score_acc',
                'indicators.max_score',
                'indicators.status',
                'categories.name',
                'standards.name'
            ])
            ->orderBy('indicators.year')
            ->orderByRaw("
        CASE
            WHEN indicators.code LIKE 'NCS-%' THEN 1
            WHEN indicators.code LIKE 'NCP-%' THEN 2
            WHEN indicators.code LIKE 'NCO-%' THEN 3
            ELSE 99
        END
    ")
            ->orderByRaw("
        COALESCE(
            NULLIF(split_part(indicators.code, '-', array_length(string_to_array(indicators.code, '-'), 1)), ''),
            '0'
        )::int ASC
    ")
            ->orderBy('indicators.code', 'asc');
        // ✅ กันซ้ำ

        $status = request()->get('status'); // complete / incomplete / pending

        if ($status) {
            switch ($status) {
                case 'complete':
                    $q->whereIn('indicators.status', [2, 3]);
                    break;
                case 'incomplete':
                    $q->where('indicators.status', [4]);
                    break;
                case 'pending':
                    $q->whereIn('indicators.status', [0, 1]);
                    break;
            }
        }
        // ===== ฟิลเตอร์ =====
        $y   = $this->filters['year']         ?? null;
        $std = $this->filters['standard_id']  ?? null;   // อาจเป็น id หรือชื่อ
        $cat = $this->filters['category_id']  ?? null;   // (dimension) อาจเป็น id หรือชื่อ
        $st  = $this->filters['status']       ?? '';
        $dep = $this->filters['dept_id']      ?? null;   // อาจเป็น id หรือชื่อแผนก
        $code = $this->filters['code']         ?? null;

        if ($y) {
            $q->where('indicators.year', $y);
        }

        if ($std !== null && $std !== '') {
            if (is_numeric($std)) {
                $q->where('categories.standard_id', (int)$std);
            } else {
                // Postgres ใช้ ILIKE ค้นหาไม่สนตัวพิมพ์
                $q->where('standards.name', 'ILIKE', $std);
                // ถ้าต้องการแบบ contains: $q->where('standards.name', 'ILIKE', "%{$std}%");
            }
        }

        if ($cat !== null && $cat !== '') {
            if (is_numeric($cat)) {
                $q->where('indicators.categorie_id', (int)$cat);
            } else {
                $q->where('categories.name', 'ILIKE', $cat);
                // หรือ contains: $q->where('categories.name', 'ILIKE', "%{$cat}%");
            }
        }

        if ($st !== '') {
            $q->where('indicators.status', $st);
        }
        if ($dep !== null && $dep !== '') {
            if (is_numeric($dep)) {
                $q->where('collector_users.department_id', (int)$dep);
            } else {
                $q->where('departments.name', 'ILIKE', $dep);
            }
        }


        if ($code) {
            $q->where('indicators.code', 'ILIKE', "%{$code}%");
        }

        // ===== การเรียง (ปี → prefix → เลขหลังขีด) =====
        if (!$y) {
            $q->orderBy('indicators.year', 'asc');
        }


        return $q;
    }

    public function headings(): array
    {
        return [
            'ปีการประเมิน',
            'ชื่อตัวบ่งชี้',
            'รหัส',
            'ประเภทตัวชี้วัด',
            'มาตรฐานการประเมิน',
            'ด้านการประเมิน',
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
                0 => 'อยู่ระหว่างดำเนินการ',            // pending
                1 => 'ผลการดำเนินงานยังไม่ครบถ้วนตามเกณฑ์', // incomplete
                2, 3 => 'ผลการดำเนินงานครบถ้วนตามเกณฑ์มาตรการ', // complete
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
