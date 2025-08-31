<!-- resources/views/payment_form.blade.php (New view for auto-submit form) -->

<form action="{{ $paymentData['payment_url'] }}" method="post" id="payment-form">
    @foreach ($paymentData as $key => $value)
        @if ($key !== 'payment_url')
            <input type="hidden" name="{{ strtoupper($key) }}" value="{{ $value }}">
        @endif
    @endforeach
</form>

<script>
    document.getElementById('payment-form').submit();
</script>