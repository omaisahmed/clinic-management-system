<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prescription {{ $prescription->prescription_number }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
            max-width: 720px;
            margin: 0 auto;
            padding: 24px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #111827;
            padding-bottom: 12px;
        }

        .clinic-name {
            font-size: 20px;
            font-weight: bold;
        }

        .rx-number {
            font-size: 14px;
            color: #374151;
            text-align: right;
            line-height: 1.5;
        }

        .meta {
            margin-top: 20px;
        }

        .meta-row {
            margin-bottom: 4px;
            font-size: 14px;
        }

        .meta-row span {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 24px;
        }

        th, td {
            border: 1px solid #9ca3af;
            padding: 8px;
            font-size: 14px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f3f4f6;
        }

        .notes {
            margin-top: 24px;
            font-size: 14px;
        }

        .notes p {
            white-space: pre-line;
            margin: 6px 0 0;
        }

        .print-btn {
            margin-bottom: 24px;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
        }

        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print</button>

    <div class="header">
        <div class="clinic-name">{{ setting('clinic.name', 'Clinic') }}</div>
        <div class="rx-number">
            Rx #: {{ $prescription->prescription_number }}<br>
            {{ $prescription->created_at->format('M d, Y') }}
        </div>
    </div>

    <div class="meta">
        <div class="meta-row"><span>Patient:</span> {{ $prescription->patient?->full_name ?: '—' }}</div>
        <div class="meta-row"><span>Doctor:</span> {{ $prescription->doctor?->name ?: '—' }}</div>
        <div class="meta-row"><span>Status:</span> {{ $prescription->status?->label() ?? '—' }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>Name</th>
                <th>Dosage</th>
                <th>Frequency</th>
                <th>Duration</th>
                <th>Instructions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($prescription->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->dosage ?: '—' }}</td>
                    <td>{{ $item->frequency ?: '—' }}</td>
                    <td>{{ $item->duration ?: '—' }}</td>
                    <td>{{ $item->instructions ?: '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No medications.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($prescription->notes)
        <div class="notes">
            <strong>Notes:</strong>
            <p>{{ $prescription->notes }}</p>
        </div>
    @endif
</body>
</html>
