<!DOCTYPE html>
<html>
<head>
    <title>Start Payment Session</title>
</head>
<body>
    <h1>Start a New Payment Session</h1>
    <form action="/start-payment-session" method="POST">
        @csrf
        <label for="amount">Amount:</label>
        <input type="number" step="0.01" name="amount" id="amount" required>
        <br>
        <label for="currency">Currency:</label>
        <select name="currency" id="currency" required>
            <option value="TRX">TRX</option>
            <option value="JST">JST (TRC20)</option>
        </select>
        <br>
        <button type="submit">Start Payment Session</button>
    </form>
</body>
</html>