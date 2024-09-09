<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>{{$customer->first_name}} {{$customer->last_name}} - {{ $invoice->invoice_id }}</title>

    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 14px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(6) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 40px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(6) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .invoice-box.rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .invoice-box.rtl table {
            text-align: right;
        }

        .invoice-box.rtl table tr td:nth-child(6) {
            text-align: left;
        }
    </style>
</head>

<body>
<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="6">
                <table>
                    <tr>
                        <td class="title">
                            <img
                                src="{{ public_path('images/test3.png') }}" alt="Company Logo"
                                style="width: 100%; max-width: 200px"
                            />
                        </td>
                        <td>

                        </td>
                        <td>

                        </td>
                        <td>

                        </td>

                        <td>
                            Invoice #: {{ $invoice->invoice_id }}<br />
                            Created: {{$todayDate}}<br />
                            Payment Due Date: {{$invoice->payment_due_date}} <br />
                            Billing Period: {{$invoice->from_date}} - {{$invoice->to_date}}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="information">
            <td colspan="6">
                <table>
                    <tr>
                        <td>
                            {{$customer->company_name}}<br />
                            {{$customer->address}} <br />
                            {{$customer->zip}}, {{$customer->city}}
                        </td>
                        <td></td>
                        <td>

                        </td>
                        <td>

                        </td>
                        <td>

                        </td>
                        <td>
                            {{$customer->first_name}} {{$customer->last_name}}<br />
                            {{$customer->phone}}<br />
                            {{$customer->email}}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="heading">
            <td>Discount</td>
            <td>Service</td>
            <td></td>
            <td></td>
            <td>Amount</td>
            <td>Percentage</td>

        </tr>

        @foreach($discountDetails as $detail)
            <tr class="item">
                <td>{{ $detail -> discount_name}}</td>
                @if(isset($detail ->discount_amount))
                <td>{{$detail ->discount_on_service_name}} MKD</td>
                @else
                    <td></td>
                @endif
                <td></td>
                <td></td>

                @if(isset($detail ->discount_amount))
                    <td>{{$detail ->discount_amount}} MKD</td>
                @else
                    <td></td>
                @endif

                @if(isset($detail ->discount_percentage))
                    <td>{{$detail ->discount_percentage}} % </td>
                @else
                    <td></td>
                @endif
            </tr>
        @endforeach

        <tr class="heading">
            <td>Service</td>
            <td>Price</td>
            <td>Billed period</td>
            <td>Prorated price</td>
            <td>Discount</td>
            <td>Final price</td>
        </tr>

        @foreach($serviceDetails as $detail)
            <tr class="item">
                <td>{{ $detail -> service_name}}</td>
                <td>{{ number_format($detail ->price_per_month, 2) }} MKD</td>
                <td>{{$detail ->from_date}} - {{$detail ->to_date}}</td>
                <td>{{$detail ->prorated_price}} MKD</td>
                <td>{{$detail ->service_discount}} MKD</td>
                <td>{{$detail ->total_service_price}} MKD</td>
            </tr>
        @endforeach

        <tr class="total">
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>Total: {{$invoice->total_amount}} MKD</td>
        </tr>

        <br/>
        <br/>
        <tr class="information" style="border: 1px solid darkgray; margin-top: 20px;">
            <td colspan="5" style="width:100%; vertical-align: middle; text-align: center;">
                <h4>Scan the QR code to pay the invoice</h4>
            </td>
            <td>
                <img src="{{$qrCode}}" width="100px" height="100px">
            </td>
        </tr>
    </table>
</div>
</body>
</html>
