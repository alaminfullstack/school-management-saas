<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class StudentTemplateExport implements WithMultipleSheets
{
    public function __construct(
        private string  $className,
        private ?string $groupName,
        private string  $sectionName,
        private string  $sessionYear,
    ) {}

    public function sheets(): array
    {
        return [
            new StudentTemplateDataSheet(
                $this->className,
                $this->groupName,
                $this->sectionName,
                $this->sessionYear,
            ),
            new StudentTemplateInfoSheet(
                $this->className,
                $this->groupName,
                $this->sectionName,
                $this->sessionYear,
            ),
        ];
    }
}

class StudentTemplateDataSheet implements FromArray, WithHeadings, WithTitle, WithColumnFormatting
{
    public function __construct(
        private string  $className,
        private ?string $groupName,
        private string  $sectionName,
        private string  $sessionYear,
    ) {}

    public function title(): string
    {
        return 'Student Data';
    }

    public function headings(): array
    {
        return [
            // 'class',              // A
            // 'group',              // D
            // 'section',            // B
            // 'session',            // C
            //'admission_fee',      // E
            'admission_date',     // F
            'previous_school',    // G
            'previous_class',     // H
            'previous_group',     // I
            'previous_section',   // J
            'previous_session',   // K
            'last_exam_result',   // L
            'student_name',       // M
            'father_name',        // N
            'mother_name',        // O
            'mobile',             // P
            'current_division',   // Q
            'current_district',   // R
            'current_upazila',    // S
            'current_village',    // T
            'permanent_division', // U
            'permanent_district', // V
            'permanent_upazila',  // W
            'permanent_village',  // X
            'password',           // Y
            // 'guardian_name',      // Z
            // 'guardian_relation',  // AA
            // 'guardian_mobile',    // AB
            // 'guardian_division',  // AC
            // 'guardian_district',  // AD
            // 'guardian_upazila',   // AE
            // 'guardian_village',   // AF
        ];
    }

    public function array(): array
    {
        return [
            [
                // $this->className,       // class      (A) — pre-filled
                // $this->sectionName,     // section    (B) — pre-filled
                // $this->sessionYear,     // session    (C) — pre-filled
                // $this->groupName ?? '', // group      (D) — pre-filled
                //'',                     // admission_fee   (E) optional
                '2026-01-15',           // admission_date  (F) required
                '',                     // previous_school (G) optional
                '',                     // previous_class  (H) optional
                '',                     // previous_group  (I) optional
                '',                     // previous_section(J) optional
                '',                     // previous_session(K) optional
                '',                     // last_exam_result(L) optional
                'Rahim Ahmed',          // student_name    (M) required
                'Karim Ahmed',          // father_name     (N) required
                'Fatema Begum',         // mother_name     (O) required
                '01712345678',          // mobile          (P) required
                'Dhaka',                // current_division(Q) required
                'Gazipur',              // current_district(R) required
                'Gazipur Sadar',        // current_upazila (S) required
                'Rowshon Market',       // current_village (T) required
                'Dhaka',                // permanent_division(U) required
                'Gazipur',              // permanent_district(V) required
                'Gazipur Sadar',        // permanent_upazila (W) required
                'Rowshon Market',       // permanent_village (X) required
                'password123',          // password        (Y) required
                // '',                     // guardian_name   (Z) optional
                // '',                     // guardian_relation(AA) optional
                // '',                     // guardian_mobile  (AB) optional
                // '',                     // guardian_division(AC) optional
                // '',                     // guardian_district(AD) optional
                // '',                     // guardian_upazila (AE) optional
                // '',                     // guardian_village (AF) optional
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F'  => NumberFormat::FORMAT_TEXT, // admission_date
            'P'  => NumberFormat::FORMAT_TEXT, // mobile
            'Y'  => NumberFormat::FORMAT_TEXT, // password
            'AB' => NumberFormat::FORMAT_TEXT, // guardian_mobile
        ];
    }
}

class StudentTemplateInfoSheet implements FromArray, WithHeadings, WithTitle
{
    public function __construct(
        private string  $className,
        private ?string $groupName,
        private string  $sectionName,
        private string  $sessionYear,
    ) {}

    public function title(): string
    {
        return 'Template Info (Do Not Edit)';
    }

    public function headings(): array
    {
        return ['Field', 'Value'];
    }

    public function array(): array
    {
        return [
            ['Class',   $this->className],
            ['Group',   $this->groupName ?? '—'],
            ['Section', $this->sectionName],
            ['Session', $this->sessionYear],
        ];
    }
}
