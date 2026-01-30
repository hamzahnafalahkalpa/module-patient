@extends('wellmed::app')

@section('title', 'MEDICAL RECORD')

@section('css')
<style>
table {
    width: 100%;
    border-collapse: collapse;
}

@page {
    size: A4;
    margin: 120px 40px 130px 40px;
}

header {
    position: fixed;
    top: -90px;
    left: 0;
    right: 0;
    height: 60px;
    text-align: center;
}

footer {
    position: fixed;
    bottom: -70px;
    left: 0;
    right: 0;
    height: 50px;
    text-align: center;
    font-size: 12px;
}

/* DomPDF doesn't support CSS Grid → use table layout */
.header-grid {
    width: 100%;
    display: table;
}

.header-grid > div {
    display: table-cell;
    vertical-align: middle;
}

.header-grid > div:first-child {
    text-align: left;
    width: 70%;
}

.header-grid > div:last-child {
    text-align: right;
    width: 30%;
}

.text-right {
    text-align: right;
}

.text-center {
    text-align: center;
}

.text-left {
    text-align: left;
}

.font-bold {
    font-weight: bold;
}

/* Table styles for sections */
.section-table {
    margin-bottom: 20px;
    border: 1px solid #ddd;
}

.section-table th {
    background-color: #004d99;
    color: white;
    padding: 8px;
    text-align: left;
}

.section-table td {
    padding: 8px;
    border: 1px solid #ddd;
}

.info-table td {
    padding: 6px;
    vertical-align: top;
}

.info-table td:first-child {
    width: 35%;
    font-weight: bold;
}

/* Footer signature layout */
.footer-sign {
    width: 100%;
    display: table;
    table-layout: fixed;
    text-align: center;
    margin-top: 10px;
}

.footer-sign div {
    display: table-cell;
    vertical-align: top;
}

/* Repeat header on each page */
thead { display: table-header-group; }
tfoot { display: table-footer-group; }
</style>
@endsection

@section('content')
<header>
    <div class="header-grid">
        <div class="text-left font-bold">
            @if(isset($workspace->name))
                <h2 style="margin:0; font-family: 'Arial'">{{ strtoupper($workspace->name) }}</h2>
                @if(isset($workspace->address))
                    <p style="margin:0; font-size: 10px;">{{ $workspace->address }}</p>
                @endif
            @else
                <h2 style="margin:0; font-family: 'Arial'">KLINIK</h2>
            @endif
        </div>
        <div class="header-logo">
            @if(isset($workspace->logo))
                <div class="block text-right rounded-[5px] overflow-hidden ml-auto" style="height:auto;">
                    <img src="{{ $workspace->logo }}" height="40" alt="Logo">
                </div>
            @endif
        </div>
    </div>
</header>

<footer>
    <div class="footer-sign">
        <div>
            Dibuat Oleh,<br>
            <strong>{{ $workspace->name ?? 'Admin' }}</strong>
        </div>
        <div>
            Disetujui Oleh,<br>
            ______________________
        </div>
        <div>
            Dicetak Oleh,<br>
            <strong>{{ $workspace->name ?? 'Admin' }}</strong>
        </div>
    </div>
</footer>

<main>
    <h2 class="text-center font-bold" style="margin-bottom: 20px;">REKAM MEDIS ELEKTRONIK</h2>

    {{-- Visit Registration Information --}}
    <table class="section-table">
        <thead>
            <tr>
                <th colspan="2">INFORMASI KUNJUNGAN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 35%; font-weight: bold;">Kode Kunjungan</td>
                <td>{{ $visit_registration->code ?? '-' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Tanggal Kunjungan</td>
                <td>{{ isset($visit_registration->created_at) ? \Carbon\Carbon::parse($visit_registration->created_at)->format('d/m/Y H:i') : '-' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Status</td>
                <td>{{ $visit_registration->status ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Patient Information --}}
    <table class="section-table">
        <thead>
            <tr>
                <th colspan="2">INFORMASI PASIEN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 35%; font-weight: bold;">Nama Pasien</td>
                <td>{{ $patient->name ?? '-' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">No. Rekam Medis</td>
                <td>{{ $patient->code ?? '-' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">NIK</td>
                <td>{{ $patient->nik ?? '-' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Tanggal Lahir</td>
                <td>{{ isset($patient->birth_date) ? \Carbon\Carbon::parse($patient->birth_date)->format('d/m/Y') : '-' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Jenis Kelamin</td>
                <td>{{ $patient->gender ?? '-' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Alamat</td>
                <td>{{ $patient->address ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Examination Summary --}}
    @if(isset($examination_summary))
    <table class="section-table">
        <thead>
            <tr>
                <th colspan="2">RINGKASAN PEMERIKSAAN</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($examination_summary->chief_complaint))
            <tr>
                <td style="width: 35%; font-weight: bold;">Keluhan Utama</td>
                <td>{{ $examination_summary->chief_complaint }}</td>
            </tr>
            @endif
            @if(isset($examination_summary->history))
            <tr>
                <td style="font-weight: bold;">Riwayat</td>
                <td>{{ $examination_summary->history }}</td>
            </tr>
            @endif
            @if(isset($examination_summary->physical_examination))
            <tr>
                <td style="font-weight: bold;">Pemeriksaan Fisik</td>
                <td>{{ $examination_summary->physical_examination }}</td>
            </tr>
            @endif
            @if(isset($examination_summary->diagnosis))
            <tr>
                <td style="font-weight: bold;">Diagnosis</td>
                <td>{{ $examination_summary->diagnosis }}</td>
            </tr>
            @endif
        </tbody>
    </table>
    @endif

    {{-- Assessments (SOAP) --}}
    @if(isset($assessments) && count($assessments) > 0)
    <table class="section-table">
        <thead>
            <tr>
                <th colspan="4">ASESMEN MEDIS (SOAP)</th>
            </tr>
            <tr style="background-color: #b3d9ff;">
                <th style="width: 10%;">No</th>
                <th style="width: 20%;">Tanggal</th>
                <th style="width: 35%;">Subjektif / Objektif</th>
                <th style="width: 35%;">Asesmen / Plan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assessments as $index => $assessment)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ isset($assessment->created_at) ? \Carbon\Carbon::parse($assessment->created_at)->format('d/m/Y H:i') : '-' }}</td>
                <td>
                    @if(isset($assessment->subjective))
                        <strong>S:</strong> {{ $assessment->subjective }}<br>
                    @endif
                    @if(isset($assessment->objective))
                        <strong>O:</strong> {{ $assessment->objective }}
                    @endif
                </td>
                <td>
                    @if(isset($assessment->assessment))
                        <strong>A:</strong> {{ $assessment->assessment }}<br>
                    @endif
                    @if(isset($assessment->plan))
                        <strong>P:</strong> {{ $assessment->plan }}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Treatments and Procedures --}}
    @if(isset($treatments) && count($treatments) > 0)
    <table class="section-table">
        <thead>
            <tr>
                <th colspan="4">TINDAKAN DAN PROSEDUR</th>
            </tr>
            <tr style="background-color: #b3d9ff;">
                <th style="width: 10%;">No</th>
                <th style="width: 40%;">Nama Tindakan</th>
                <th style="width: 30%;">Tanggal</th>
                <th style="width: 20%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($treatments as $index => $treatment)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $treatment->name ?? '-' }}</td>
                <td>{{ isset($treatment->created_at) ? \Carbon\Carbon::parse($treatment->created_at)->format('d/m/Y H:i') : '-' }}</td>
                <td>{{ $treatment->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Medications and Prescriptions --}}
    @if(isset($medications) && count($medications) > 0)
    <table class="section-table">
        <thead>
            <tr>
                <th colspan="5">OBAT DAN RESEP</th>
            </tr>
            <tr style="background-color: #b3d9ff;">
                <th style="width: 10%;">No</th>
                <th style="width: 30%;">Nama Obat</th>
                <th style="width: 20%;">Dosis</th>
                <th style="width: 20%;">Frekuensi</th>
                <th style="width: 20%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($medications as $index => $medication)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $medication->name ?? '-' }}</td>
                <td>{{ $medication->dosage ?? '-' }}</td>
                <td>{{ $medication->frequency ?? '-' }}</td>
                <td>{{ $medication->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

</main>
@endsection
