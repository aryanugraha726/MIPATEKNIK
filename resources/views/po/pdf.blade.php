<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Purchase Order - {{ $po->no_po }}</title>
    <style>
        /* ── A4 PORTRAIT: 210mm × 297mm ─────────────────────── */
        @page {
            size: A4 portrait;
            margin: 10mm 12mm 10mm 12mm;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 7.5pt;
            color: #000;
            margin: 0 auto;
            text-align: center;
        }
        .wrapper {
            width: 186mm;
            margin: 0 auto;
            text-align: left;
        }

        /* ─── TITLE ──────────────────────────────────────────────── */
        .title-bar {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            border: 1.5px solid #000;
            border-bottom: none;
            padding: 4px 0 3px 0;
            letter-spacing: 2px;
        }

        /* ─── INFO TABLE ─────────────────────────────────────────── */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
        }
        .info-table td {
            padding: 1px 3px;
            vertical-align: top;
            font-size: 7pt;
            line-height: 1.3;
        }
        .info-section { width: 50%; }
        .info-inner   { width: 100%; border-collapse: collapse; }
        .lbl  { width: 48px; white-space: nowrap; }
        .col  { width: 7px;  text-align: center; }
        .val  { color: #00008b; }
        .info-divider { border-left: 1px solid #000; }

        /* ─── ITEMS TABLE ────────────────────────────────────────── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 7px;
            border: 1.5px solid #000;
        }
        .items-table th {
            font-weight: bold;
            font-size: 7.5pt;
            border: 1px solid #000;
            padding: 2.5px 2px;
            text-align: center;
            background: #fff;
        }
        .items-table td {
            border: 1px solid #000;
            padding: 2px 2px;
            font-size: 7pt;
            vertical-align: middle;
        }

        /* Column widths — total must fit 186mm (A4 portrait) */
        .c-no    { width: 14px;  text-align: center; }
        .c-desc  { width: auto; }
        .c-job   { width: 34px;  text-align: center; }
        .c-curr  { width: 22px;  text-align: center; }
        .c-qty   { width: 16px;  text-align: center; }
        .c-unit  { width: 20px;  text-align: center; }
        .c-price { width: 52px;  text-align: right; }
        .c-total { width: 52px;  text-align: right; }

        /* ─── FOOTER TABLE ───────────────────────────────────────── */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
            border-top: none;
        }
        .footer-table td {
            padding: 2px 3px;
            font-size: 7pt;
            border: 1px solid #000;
            vertical-align: middle;
        }
        .terbilang-cell {
            font-style: italic;
            font-weight: bold;
            width: 55%;
        }
        .sum-label { text-align: right; font-weight: bold; width: 22%; }
        .sum-value { text-align: right; width: 13%; }
        .pph-label { text-align: right; font-weight: bold; color: #cc0000; }
        .pph-value { text-align: right; color: #cc0000; }
        .gt-label  { text-align: right; font-weight: bold; }
        .gt-value  { text-align: right; font-weight: bold; }
    </style>
</head>
<body>
<div class="wrapper">

@php
    $vendor  = $po->vendor;
    $lokasi  = $po->shippingAddress;
    $project = $po->workOrderRelease?->project ?? null;

    /* ── Hitung total ──────────────────────────── */
    $totalBarang = 0;
    $rows = [];
    foreach ($po->details as $d) {
        $b       = $d->barang;
        $harga   = $d->harga ?? ($b ? $b->harga : 0);
        $subtot  = $harga * $d->qty_po;
        $totalBarang += $subtot;
        $rows[] = [
            'nama'    => $b->nama_barang ?? $d->id_barang,
            'job'     => $project ? $project->job_id : 'STOCK',
            'curr'    => $po->currency ?? 'IDR',
            'qty'     => $d->qty_po,
            'satuan'  => strtoupper($d->satuan->nama_satuan ?? $d->id_satuan),
            'harga'   => $harga,
            'subtot'  => $subtot,
        ];
    }

    $ppnRate = $po->ppn ?? 0;
    $pphRate = $po->pph ?? 0;
    $nilaiPpn = ($ppnRate / 100) * $totalBarang;
    $nilaiPph = ($pphRate / 100) * $totalBarang;
    $grandTotal = $totalBarang + $nilaiPpn - $nilaiPph;

    $terbilang = terbilang((int) $grandTotal) . ' Rupiah';
@endphp

{{-- ── TITLE ──────────────────────────────────────────────── --}}
<div class="title-bar">PURCHASE ORDER</div>

{{-- ── HEADER INFO ─────────────────────────────────────────── --}}
<table class="info-table">
    <tr>
        {{-- LEFT --}}
        <td class="info-section">
            <table class="info-inner">
                <tr><td class="lbl">Number</td><td class="col">:</td><td class="val">{{ $po->no_po }}</td></tr>
                <tr><td class="lbl">Vendor</td><td class="col">:</td><td class="val">{{ $vendor->nama_vendor ?? '-' }}</td></tr>
                <tr><td class="lbl">Address</td><td class="col">:</td><td class="val">{{ $vendor->alamat_vendor ?? '-' }}</td></tr>
                <tr><td class="lbl">Phone</td><td class="col">:</td><td class="val">{{ $vendor->phone_vendor ?? '-' }}</td></tr>
                <tr><td class="lbl">Contact</td><td class="col">:</td><td class="val">{{ $vendor->cp_vendor ?? '-' }}</td></tr>
                <tr><td class="lbl">Reference</td><td class="col">:</td><td class="val">Surat Penawaran</td></tr>
            </table>
        </td>

        {{-- RIGHT --}}
        <td class="info-section info-divider">
            <table class="info-inner">
                <tr><td class="lbl">Date</td><td class="col">:</td><td class="val">{{ \Carbon\Carbon::parse($po->tgl_po)->format('d/m/Y') }}</td></tr>
                <tr><td class="lbl">Ship To</td><td class="col">:</td><td class="val">{{ $lokasi->nama_lokasi ?? '-' }}</td></tr>
                <tr><td class="lbl">Address</td><td class="col">:</td><td class="val">{{ $lokasi->alamat_mipa ?? '-' }}</td></tr>
                <tr><td class="lbl">Phone</td><td class="col">:</td><td class="val">{{ $lokasi->phone_mipa ?? '-' }}</td></tr>
                <tr><td class="lbl">Contact</td><td class="col">:</td><td class="val">{{ $lokasi->cp_mipa ?? '-' }}</td></tr>
                <tr><td class="lbl">E-Mail</td><td class="col">:</td><td class="val" style="color:#0000cc;">{{ $lokasi->email_mipa ?? '-' }}</td></tr>
                <tr><td class="lbl">Job No</td><td class="col">:</td><td class="val">{{ $project ? ($project->job_id . ' – ' . $project->nama_project) : '' }}</td></tr>
            </table>
        </td>
    </tr>
</table>

{{-- ── ITEMS TABLE ──────────────────────────────────────────── --}}
<table class="items-table">
    <thead>
        <tr>
            <th class="c-no">No</th>
            <th class="c-desc">Description</th>
            <th class="c-job">Job</th>
            <th class="c-curr">Curr</th>
            <th class="c-qty">Qty</th>
            <th class="c-unit">Unit</th>
            <th class="c-price">Unit Price</th>
            <th class="c-total">Total Price</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rows as $i => $row)
        <tr>
            <td class="c-no">{{ $i + 1 }}</td>
            <td class="c-desc">{{ $row['nama'] }}</td>
            <td class="c-job">{{ $row['job'] }}</td>
            <td class="c-curr">{{ $row['curr'] }}</td>
            <td class="c-qty">{{ $row['qty'] }}</td>
            <td class="c-unit">{{ $row['satuan'] }}</td>
            <td class="c-price">{{ number_format($row['harga'], 0, ',', '.') }}</td>
            <td class="c-total">{{ number_format($row['subtot'], 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center; padding:6px;">Tidak ada item</td></tr>
        @endforelse

        {{-- Baris kosong pengisi agar tabel terlihat penuh (min 5 baris) --}}
        @for($e = count($rows); $e < 5; $e++)
        <tr>
            <td class="c-no">&nbsp;</td>
            <td class="c-desc">&nbsp;</td>
            <td class="c-job">&nbsp;</td>
            <td class="c-curr">&nbsp;</td>
            <td class="c-qty">&nbsp;</td>
            <td class="c-unit">&nbsp;</td>
            <td class="c-price">&nbsp;</td>
            <td class="c-total">&nbsp;</td>
        </tr>
        @endfor
    </tbody>
</table>

{{-- ── FOOTER: TERBILANG + RINGKASAN ──────────────────────── --}}
<table class="footer-table">
    <tr>
        <td class="terbilang-cell" rowspan="4">
            Terbilang : {{ $terbilang }}
        </td>
        <td class="sum-label">Total Purchase</td>
        <td class="sum-value">{{ number_format($totalBarang, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td class="sum-label">PPN {{ $ppnRate > 0 ? (int)$ppnRate.'%' : '11%' }}</td>
        <td class="sum-value">{{ $nilaiPpn > 0 ? number_format($nilaiPpn, 0, ',', '.') : '' }}</td>
    </tr>
    <tr>
        <td class="pph-label">PPH 23 {{ $pphRate > 0 ? (int)$pphRate.'%' : '2%' }}</td>
        <td class="pph-value">{{ $nilaiPph > 0 ? '- '.number_format($nilaiPph, 0, ',', '.') : '-' }}</td>
    </tr>
    <tr>
        <td class="gt-label">Grand Total</td>
        <td class="gt-value">{{ number_format($grandTotal, 0, ',', '.') }}</td>
    </tr>
</table>

</div>
</body>
</html>
