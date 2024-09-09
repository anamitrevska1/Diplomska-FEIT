<!DOCTYPE html>
<html>
<head>
    <title>Payment Due Reminder</title>
</head>
<body>
<p>Dear {{ $customer->name }},</p>
<p>This is a reminder that your payment for Invoice #{{ $invoice->invoice_id }} is due on {{ $invoice->payment_due_date }}.</p>
<p>Please make your payment at your earliest convenience.</p>
<p>Thank you!</p>
</body>
</html>
