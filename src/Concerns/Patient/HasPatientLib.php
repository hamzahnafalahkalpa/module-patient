<?php

namespace Hanafalah\ModulePatient\Concerns\Patient;

use Carbon\CarbonImmutable;

trait HasPatientLib
{
    public function calculateAge(string $dob): array
    {
        // validate strict format Y-m-d using CarbonImmutable to get parsing errors
        $dt = CarbonImmutable::createFromFormat('Y-m-d', $dob);
        $errors = CarbonImmutable::getLastErrors();
        if ($dt === false || (is_bool($errors) && $errors)) {
            throw new \InvalidArgumentException('Invalid date format, expected Y-m-d');
        }

        $now = CarbonImmutable::now();
        if ($dt->greaterThan($now)) {
            throw new \InvalidArgumentException('DOB cannot be in the future');
        }

        // components
        $diff = $dt->diff($now);
        $years = (int) $diff->y;
        $months = (int) $diff->m;
        $days = (int) $diff->d;

        // totals using Carbon helpers
        $totalMonths = $dt->diffInMonths($now);
        $totalDays = $dt->diffInDays($now);
        return [
            'years' => $years,
            'months' => $months,
            'days' => $days,
            'totalMonths' => $totalMonths,
            'totalDays' => $totalDays,
        ];
    }

    public function patientSegment(string $dob): string{
        $age = $this->calculateAge($dob);
        $years = $age['years'];
        $totalMonths = $age['totalMonths'];
        /**
         * Segmentasi Umur:
         * 
         * 1. Bayi dan Balita: 0 – 59 bulan (termasuk bayi baru lahir 0–28 hari)
         * 2. Anak-anak: 60 – 119 bulan (5–9 tahun 11 bulan)
         * 3. Remaja: 10 – <18 tahun
         * 4. Dewasa: 18 – 59 tahun
         * 5. Lansia: ≥ 60 tahun
         */

        if ($totalMonths < 60) return 'bayi dan balita';
        if ($totalMonths >= 60 && $totalMonths < 120) return 'anak-anak';
        if ($years >= 10 && $years < 18) return 'remaja';
        if ($years >= 18 && $years < 60) return 'dewasa';
        return 'lansia';
    }
}
