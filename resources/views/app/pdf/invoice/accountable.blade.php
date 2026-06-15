@php
    $companyEmail = \App\Models\CompanySetting::getSetting('company_email', $invoice->company_id);
    $companyIban = \App\Models\CompanySetting::getSetting('company_iban', $invoice->company_id);
    $companyBic = \App\Models\CompanySetting::getSetting('company_bic', $invoice->company_id);
    $serviceDate = $invoice->formattedInvoiceDate;
    $customer = $invoice->customer;
    $companyAddress = $invoice->company->address;
    $billingAddress = $customer?->billingAddress;
@endphp
<!DOCTYPE html>
<html>
<head>
    <title>Rechnung - {{ $invoice->invoice_number }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    @include("app.pdf.partials.fonts")

    <style type="text/css">
        @page {
            margin: 0;
        }
        html, body {
            margin: 0;
            padding: 0;
            color: #2d2d2d;
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            font-size: 11px;
        }

        /* HEADER BAR (blue) */
        .header-bar {
            background-color: #2d6cdf;
            color: #ffffff;
            padding: 28px 48px 32px 48px;
            position: relative;
        }
        .header-bar .col {
            display: inline-block;
            vertical-align: top;
            width: 48%;
        }
        .header-bar .col.right {
            text-align: right;
            float: right;
        }
        .header-bar .label {
            font-size: 9.5px;
            font-weight: bold;
            text-transform: none;
            opacity: 0.95;
            margin: 0 0 4px 0;
        }
        .header-bar .name {
            font-size: 17px;
            font-weight: bold;
            margin: 0 0 12px 0;
            line-height: 22px;
        }
        .header-bar .addr {
            font-size: 10.5px;
            line-height: 14px;
            margin: 0 0 12px 0;
            color: #eaf0ff;
        }
        .header-bar .vat {
            font-size: 10.5px;
            line-height: 14px;
            color: #eaf0ff;
        }
        .clear { clear: both; }

        /* DOC HEADING (giant RECHNUNG word + invoice-meta) */
        .doc-heading {
            padding: 64px 48px 24px 48px;
            position: relative;
        }
        .doc-heading .word {
            font-size: 64px;
            color: #d0d0d0;
            font-weight: 300;
            letter-spacing: -2px;
            line-height: 1;
            display: inline-block;
            vertical-align: top;
            width: 50%;
        }
        .doc-heading .meta {
            display: inline-block;
            vertical-align: top;
            width: 48%;
            text-align: right;
        }
        .doc-heading .meta .invoice-num {
            font-size: 17px;
            font-weight: bold;
            margin: 0 0 18px 0;
            color: #1d1d1d;
        }
        .doc-heading .meta .row {
            font-size: 11px;
            line-height: 18px;
            color: #2d2d2d;
        }

        /* ITEMS TABLE */
        .items-wrap {
            padding: 24px 48px 0 48px;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
        }
        table.items th {
            font-weight: bold;
            font-size: 11px;
            color: #1d1d1d;
            text-align: right;
            padding: 8px 4px;
            border-bottom: 1px solid #cccccc;
        }
        table.items th.left { text-align: left; }
        table.items td {
            font-size: 11px;
            padding: 10px 4px;
            text-align: right;
            color: #2d2d2d;
        }
        table.items td.left { text-align: left; }
        table.items tr.total-row td {
            border-top: 1px solid #cccccc;
            padding-top: 14px;
            font-weight: bold;
        }
        table.items tr.total-row td.label {
            color: #555555;
            font-weight: normal;
            text-align: right;
        }

        /* RC HINT */
        .rc-hint {
            padding: 28px 48px 0 48px;
            font-size: 10.5px;
            color: #2d2d2d;
        }

        /* NOTES */
        .notes {
            padding: 18px 48px 0 48px;
            font-size: 10.5px;
            color: #555555;
            line-height: 15px;
        }

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 24px 48px;
            border-top: 1px solid #e0e0e0;
            font-size: 10px;
            color: #2d2d2d;
        }
        .footer .col {
            display: inline-block;
            vertical-align: top;
            width: 48%;
            line-height: 14px;
        }
        .footer .col.right {
            text-align: right;
            float: right;
        }
        .footer .label {
            display: inline-block;
            width: 130px;
            color: #555555;
        }
    </style>
</head>
<body>

<div class="header-bar">
    <div class="col left">
        <div class="label">An</div>
        <div class="name">{{ $customer?->name }}</div>
        @if ($billingAddress)
            <div class="addr">
                {{ $billingAddress->address_street_1 }}@if($billingAddress->address_street_2) {{ $billingAddress->address_street_2 }}@endif<br/>
                {{ $billingAddress->zip }} {{ $billingAddress->city }}<br/>
                {{ $billingAddress->country?->name ?? $billingAddress->state }}
            </div>
        @endif
        @if ($customer?->tax_id)
            <div class="vat">USt.-IdNr: {{ $customer->tax_id }}</div>
        @endif
    </div>
    <div class="col right">
        <div class="label">Von</div>
        <div class="name">{{ $invoice->company?->name }}</div>
        @if ($companyAddress)
            <div class="addr">
                {{ $companyAddress->address_street_1 }}@if($companyAddress->address_street_2) {{ $companyAddress->address_street_2 }}@endif<br/>
                {{ $companyAddress->zip }} {{ $companyAddress->city }}<br/>
                {{ $companyAddress->country?->name ?? $companyAddress->state }}
            </div>
        @endif
        @if ($invoice->company?->vat_id)
            <div class="vat">USt.-IdNr: {{ $invoice->company->vat_id }}</div>
        @endif
    </div>
    <div class="clear"></div>
</div>

<div class="doc-heading">
    <div class="word">RECHNUNG</div>
    <div class="meta">
        <div class="invoice-num">RECHNUNG {{ $invoice->invoice_number }}</div>
        <div class="row">Erstellt: {{ $invoice->formattedInvoiceDate }}</div>
        <div class="row">Leistungsdatum: {{ $invoice->formattedInvoiceDate }}</div>
        <div class="row">Zahlungsziel: {{ $invoice->formattedDueDate }}</div>
    </div>
    <div class="clear"></div>
</div>

<div class="items-wrap">
    <table class="items">
        <thead>
            <tr>
                <th class="left">Beschreibung</th>
                <th>Preis (ohne USt.)</th>
                <th>Anzahl</th>
                <th>Gesamt</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr>
                    <td class="left">
                        {{ $item->name }}
                        @if ($item->description)
                            <br/><span style="font-size:9.5px; color:#666;">{{ $item->description }}</span>
                        @endif
                    </td>
                    <td>{!! format_money_pdf($item->price, $invoice->customer->currency) !!}</td>
                    <td>{{ rtrim(rtrim(number_format($item->quantity / 1, 2, ',', '.'), '0'), ',') }}</td>
                    <td>{!! format_money_pdf($item->total, $invoice->customer->currency) !!}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" class="label">Gesamtbetrag</td>
                <td>{!! format_money_pdf($invoice->total, $invoice->customer->currency) !!}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="rc-hint">
    Gemäß §3a Abs. 2 UStG steuerbar am Ort des Empfängers
</div>

@if ($notes)
    <div class="notes">
        {!! $notes !!}
    </div>
@endif

<div class="footer">
    <div class="col left">
        @if ($companyIban)
            <div><span class="label">IBAN:</span>{{ $companyIban }}</div>
        @endif
        @if ($companyBic)
            <div><span class="label">BIC:</span>{{ $companyBic }}</div>
        @endif
        <div><span class="label">VERWENDUNGSZWECK:</span>{{ $invoice->invoice_number }}</div>
    </div>
    <div class="col right">
        @if ($companyEmail)
            <div>{{ $companyEmail }}</div>
        @endif
        @if ($companyAddress?->phone)
            <div>{{ $companyAddress->phone }}</div>
        @endif
    </div>
    <div class="clear"></div>
</div>

</body>
</html>
