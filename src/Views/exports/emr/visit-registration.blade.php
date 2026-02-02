<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Certificate - {{ $visit_registration['visit_registration_code'] ?? 'N/A' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18pt;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 9pt;
            color: #666;
            margin: 2px 0;
        }

        .document-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 20px 0;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            background-color: #34495e;
            color: white;
            padding: 8px 12px;
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            width: 30%;
            padding: 5px 0;
            font-weight: bold;
            color: #555;
        }

        .info-colon {
            display: table-cell;
            width: 3%;
            padding: 5px 0;
        }

        .info-value {
            display: table-cell;
            width: 67%;
            padding: 5px 0;
            color: #333;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .table th {
            background-color: #ecf0f1;
            padding: 8px;
            text-align: left;
            border: 1px solid #bdc3c7;
            font-weight: bold;
            color: #2c3e50;
            font-size: 10pt;
        }

        .table td {
            padding: 8px;
            border: 1px solid #bdc3c7;
            font-size: 10pt;
        }

        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .vital-signs-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin: 10px 0;
        }

        .vital-card {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 4px;
            background-color: #f8f9fa;
        }

        .vital-label {
            font-size: 9pt;
            color: #666;
            margin-bottom: 3px;
        }

        .vital-value {
            font-size: 14pt;
            font-weight: bold;
            color: #2c3e50;
        }

        .vital-unit {
            font-size: 9pt;
            color: #888;
        }

        .soap-content {
            background-color: #f8f9fa;
            padding: 12px;
            border-left: 4px solid #3498db;
            margin: 8px 0;
            white-space: pre-wrap;
        }

        .diagnosis-item {
            padding: 8px;
            margin: 5px 0;
            border-left: 3px solid #e74c3c;
            background-color: #fff5f5;
        }

        .diagnosis-type {
            font-weight: bold;
            color: #c0392b;
            font-size: 9pt;
            text-transform: uppercase;
        }

        .diagnosis-code {
            font-weight: bold;
            color: #2c3e50;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9pt;
            font-weight: bold;
            margin-right: 5px;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .symptom-list, .allergy-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 10px 0;
        }

        .symptom-item, .allergy-item {
            padding: 6px 12px;
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 4px;
            font-size: 10pt;
        }

        .allergy-item {
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .footer {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-section {
            display: table;
            width: 100%;
            margin-top: 30px;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 10px;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
            font-weight: bold;
        }

        .page-break {
            page-break-after: always;
        }

        .print-info {
            text-align: center;
            font-size: 8pt;
            color: #888;
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        @media print {
            body {
                padding: 0;
            }

            .page-break {
                page-break-after: always;
            }
        }

        .alert-warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin: 10px 0;
        }

        .pain-scale {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
        }

        .pain-bar {
            height: 20px;
            background: linear-gradient(to right, #27ae60, #f39c12, #e74c3c);
            border-radius: 10px;
            position: relative;
            flex: 1;
        }

        .pain-indicator {
            position: absolute;
            top: -5px;
            width: 4px;
            height: 30px;
            background-color: #2c3e50;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>{{ $clinic_name ?? 'KLINIK KESEHATAN' }}</h1>
        <p>{{ $clinic_address ?? 'Alamat Klinik' }}</p>
        <p>Telp: {{ $clinic_phone ?? '-' }} | Email: {{ $clinic_email ?? '-' }}</p>
    </div>

    {{-- Document Title --}}
    <div class="document-title">
        Rekam Medis Elektronik
    </div>

    {{-- Patient Information --}}
    <div class="section">
        <div class="section-title">Informasi Pasien</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nama Pasien</div>
                <div class="info-colon">:</div>
                <div class="info-value">{{ $patient['name'] ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nomor Rekam Medis</div>
                <div class="info-colon">:</div>
                <div class="info-value">{{ $patient['medical_record_number'] ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">NIK</div>
                <div class="info-colon">:</div>
                <div class="info-value">{{ $patient['nik'] ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">No. IHS</div>
                <div class="info-colon">:</div>
                <div class="info-value">{{ $patient['ihs_number'] ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal Lahir</div>
                <div class="info-colon">:</div>
                <div class="info-value">{{ $patient['date_of_birth'] ?? 'N/A' }} ({{ $patient['age'] ?? 'N/A' }} tahun)</div>
            </div>
            <div class="info-row">
                <div class="info-label">Jenis Kelamin</div>
                <div class="info-colon">:</div>
                <div class="info-value">{{ $patient['gender_display'] ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    {{-- Visit Information --}}
    <div class="section">
        <div class="section-title">Informasi Kunjungan</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Kode Kunjungan</div>
                <div class="info-colon">:</div>
                <div class="info-value">{{ $visit_registration['visit_registration_code'] ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal Kunjungan</div>
                <div class="info-colon">:</div>
                <div class="info-value">{{ $visit_registration['visit_date'] ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Layanan</div>
                <div class="info-colon">:</div>
                <div class="info-value">{{ $visit_registration['medic_service']['name'] ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Dokter</div>
                <div class="info-colon">:</div>
                <div class="info-value">{{ $practitioner['name'] ?? 'N/A' }} - {{ $practitioner['profession_name'] ?? '' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status</div>
                <div class="info-colon">:</div>
                <div class="info-value">
                    <span class="badge badge-success">{{ $visit_registration['status'] ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Vital Signs --}}
    @if(isset($vital_signs) && count($vital_signs) > 0)
    <div class="section">
        <div class="section-title">Tanda-Tanda Vital</div>
        <div class="vital-signs-grid">
            @foreach($vital_signs as $vital)
                <div class="vital-card">
                    <div class="vital-label">{{ $vital['label'] ?? 'N/A' }}</div>
                    <div class="vital-value">
                        {{ $vital['value'] ?? 'N/A' }}
                        <span class="vital-unit">{{ $vital['unit'] ?? '' }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Anthropometry --}}
    @if(isset($anthropometries) && count($anthropometries) > 0)
    <div class="section">
        <div class="section-title">Antropometri</div>
        <div class="vital-signs-grid">
            @foreach($anthropometries as $anthro)
                <div class="vital-card">
                    <div class="vital-label">{{ $anthro['label'] ?? 'N/A' }}</div>
                    <div class="vital-value">
                        {{ $anthro['value'] ?? 'N/A' }}
                        <span class="vital-unit">{{ $anthro['unit'] ?? '' }}</span>
                    </div>
                    @if(isset($anthro['interpretation']))
                        <div style="font-size: 8pt; color: #e74c3c; margin-top: 3px;">
                            {{ $anthro['interpretation'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Pain Scale --}}
    @if(isset($pain_scale))
    <div class="section">
        <div class="section-title">Skala Nyeri</div>
        <div class="pain-scale">
            <strong>Skala {{ $pain_scale['scale'] ?? '0' }}/10</strong>
            <div class="pain-bar">
                <div class="pain-indicator" style="left: {{ ($pain_scale['scale'] ?? 0) * 10 }}%;"></div>
            </div>
            <span class="badge badge-warning">{{ $pain_scale['interpretation'] ?? 'N/A' }}</span>
        </div>
    </div>
    @endif

    {{-- Symptoms --}}
    @if(isset($symptoms) && count($symptoms) > 0)
    <div class="section">
        <div class="section-title">Gejala / Keluhan</div>
        <div class="symptom-list">
            @foreach($symptoms as $symptom)
                <div class="symptom-item">{{ $symptom['symptom_name'] ?? $symptom['name'] ?? 'N/A' }}</div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Allergies --}}
    @if(isset($allergies) && count($allergies) > 0)
    <div class="section">
        <div class="section-title">Alergi</div>
        @foreach($allergies as $allergy)
        <div class="alert-warning">
            <strong>{{ $allergy['allergy_name'] ?? 'N/A' }}</strong>
            @if(isset($allergy['allergen']))
                <br><small>Allergen: {{ $allergy['allergen']['substance_name'] ?? 'N/A' }}</small>
            @endif
            @if(isset($allergy['reaction']))
                <br><small>Reaksi: {{ $allergy['reaction']['reaction_name'] ?? 'N/A' }}</small>
            @endif
            @if(isset($allergy['severity']))
                <br><span class="badge badge-danger">Severity: {{ $allergy['severity'] }}</span>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    {{-- SOAP Notes --}}
    @if(isset($soap))
    <div class="section">
        <div class="section-title">Catatan SOAP</div>

        @if(isset($soap['subjective']))
        <div style="margin-bottom: 15px;">
            <strong style="color: #2c3e50;">Subjective (Keluhan Subjektif):</strong>
            <div class="soap-content">{{ $soap['subjective'] }}</div>
        </div>
        @endif

        @if(isset($soap['objective']))
        <div style="margin-bottom: 15px;">
            <strong style="color: #2c3e50;">Objective (Pemeriksaan Objektif):</strong>
            <div class="soap-content">{{ $soap['objective'] }}</div>
        </div>
        @endif

        @if(isset($soap['assessment']))
        <div style="margin-bottom: 15px;">
            <strong style="color: #2c3e50;">Assessment (Penilaian):</strong>
            <div class="soap-content">{{ $soap['assessment'] }}</div>
        </div>
        @endif

        @if(isset($soap['plan']))
        <div style="margin-bottom: 15px;">
            <strong style="color: #2c3e50;">Plan (Rencana):</strong>
            <div class="soap-content">{{ $soap['plan'] }}</div>
        </div>
        @endif
    </div>
    @endif

    {{-- Page Break for next sections --}}
    <div class="page-break"></div>

    {{-- Diagnoses --}}
    @if(isset($diagnoses) && count($diagnoses) > 0)
    <div class="section">
        <div class="section-title">Diagnosis</div>
        @foreach($diagnoses as $diagnosis)
        <div class="diagnosis-item">
            <span class="diagnosis-type">{{ $diagnosis['diagnosis_type'] ?? 'N/A' }}</span> -
            <span class="diagnosis-code">{{ $diagnosis['code'] ?? 'N/A' }}</span>:
            {{ $diagnosis['name'] ?? 'N/A' }}
        </div>
        @endforeach
    </div>
    @endif

    {{-- Prescriptions --}}
    @if(isset($prescriptions) && count($prescriptions) > 0)
    <div class="section">
        <div class="section-title">Resep Obat</div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Nama Obat</th>
                    <th style="width: 15%;">Dosis</th>
                    <th style="width: 15%;">Frekuensi</th>
                    <th style="width: 10%;">Durasi</th>
                    <th style="width: 30%;">Indikasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prescriptions as $index => $prescription)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $prescription['medicine_name'] ?? 'N/A' }}</strong></td>
                    <td>{{ $prescription['dosage'] ?? 'N/A' }} {{ $prescription['dosage_unit'] ?? '' }}</td>
                    <td>{{ $prescription['frequency'] ?? 'N/A' }}</td>
                    <td>{{ $prescription['duration'] ?? 'N/A' }} {{ $prescription['duration_unit'] ?? '' }}</td>
                    <td>{{ $prescription['indication'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Treatments --}}
    @if(isset($treatments) && count($treatments) > 0)
    <div class="section">
        <div class="section-title">Tindakan / Prosedur</div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 35%;">Nama Tindakan</th>
                    <th style="width: 15%;">Kode</th>
                    <th style="width: 45%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($treatments as $index => $treatment)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $treatment['treatment_name'] ?? 'N/A' }}</strong></td>
                    <td>{{ $treatment['code'] ?? '-' }}</td>
                    <td>{{ $treatment['notes'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Family/History Illness --}}
    @if(isset($family_illnesses) && count($family_illnesses) > 0)
    <div class="section">
        <div class="section-title">Riwayat Penyakit Keluarga</div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 35%;">Penyakit</th>
                    <th style="width: 25%;">Hubungan</th>
                    <th style="width: 35%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($family_illnesses as $index => $illness)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $illness['illness_name'] ?? 'N/A' }}</td>
                    <td>{{ $illness['relationship'] ?? '-' }}</td>
                    <td>{{ $illness['notes'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Footer with Signature --}}
    <div class="footer">
        <div class="signature-section">
            <div class="signature-box">
                <!-- Patient/Guardian signature if needed -->
            </div>
            <div class="signature-box">
                <p>{{ $visit_registration['visit_date'] ?? now()->format('d F Y') }}</p>
                <p>Dokter Pemeriksa</p>
                <div class="signature-line">
                    {{ $practitioner['name'] ?? 'N/A' }}<br>
                    <small>{{ $practitioner['sip_number'] ?? '' }}</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Print Information --}}
    <div class="print-info">
        Dokumen ini dicetak secara elektronik dan sah tanpa tanda tangan basah.<br>
        Dicetak pada: {{ now()->format('d F Y H:i:s') }} | Kode: {{ $visit_registration['visit_registration_code'] ?? 'N/A' }}
    </div>
</body>
</html>
