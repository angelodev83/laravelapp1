<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    <style>
        /* Add some basic styling */
        body {
            font-family: Arial, sans-serif;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
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
        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }
        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.top table td.title {
            font-size: 45px;
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
        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        hr {
            color: #ccc;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table>
            <tr class="details">
                <td style="text-align: center;">
                    <img src="https://home.mgmt88.com/images/mgmt88-logo.png" style="max-width: 100%; max-height: 100px;"></img>
                </td>
            </tr>
            <tr class="details">
                <td style="text-align: center;">
                    <br>
                    <h3>Delivery Received Log Form</h3>
                    <hr>
                </td>
            </tr>
            <tr class="details">
                <td>
                    <b>Date: </b> {{ date('M d, Y', strtotime($date)) }}
                </td>
            </tr>
            <tr class="details">
                <td>
                    <b>Time: </b> {{ date('h:i A', strtotime($time)) }}
                </td>
            </tr>
            <tr class="details">
                <td>
                    <b>Name of Receiver: </b> {{ $firstname }} {{ $lastname }}
                </td>
            </tr>
            <tr class="details">
                <td>
                    <b>Amount: </b> {{ number_format($amount, 2) }}
                </td>
            </tr>
            <tr class="details">
                <td>
                    <b>Signature of Receiver:</b>
                </td>
            </tr>
            <tr class="details">
                <td>
                    <img src="data:image/png;base64,{{ $signature }}" alt="Base64 Image" height="75px">
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
