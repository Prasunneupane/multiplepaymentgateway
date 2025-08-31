<!-- resources/views/welcome.blade.php (Updated with selection) -->

<form action="{{ route('payment.initiate') }}" method="post">
    @csrf
    <label for="payment_method">Choose Payment Method:</label>
    <select name="payment_method" id="payment_method">
        <option value="fonepay">FonePay</option>
        <option value="esewa">eSewa</option>
    </select>
    <br>
    <label for="amount">Amount:</label>
    <input type="number" name="amount" value="10" min="1">
    <br>
    <button type="submit">Initiate Payment</button>
</form>