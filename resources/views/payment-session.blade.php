<!DOCTYPE html>
<html>
<head>
    <title>Payment Session</title>
</head>
<body>
    <h1>Payment Session</h1>
    <p>Send <strong>{{ $paymentSession->amount }} {{ $paymentSession->currency }}</strong> to the address below:</p>
    <p><strong>{{ $paymentSession->wallet->address }}</strong></p>
    <p>Status: <strong>{{ $paymentSession->status }}</strong></p>
    <p>Received: <strong>{{ $paymentSession->received_amount }} {{ $paymentSession->currency }}</strong></p>
</body>
</html>
