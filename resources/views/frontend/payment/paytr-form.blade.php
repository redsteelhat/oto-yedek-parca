@extends('layouts.app')

@section('title', 'Ödeme')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-4">Güvenli Ödeme</h1>
        
        <div id="paytr-form-container" class="mt-6">
            <iframe src="https://www.paytr.com/odeme/guvenli/{{ $token }}" 
                    id="paytriframe" 
                    width="100%" 
                    height="500" 
                    scrolling="no" 
                    style="border:0;">
            </iframe>
        </div>
    </div>
</div>

@push('scripts')
<script>
// PayTR iframe message handler
window.addEventListener('message', function(e) {
    if (e.origin === 'https://www.paytr.com') {
        if (e.data === 'success') {
            // Payment successful
            window.location.href = '{{ route("payment.success", ["order" => "ORDER_ID"]) }}';
        } else if (e.data === 'fail') {
            // Payment failed
            window.location.href = '{{ route("payment.fail") }}';
        }
    }
});
</script>
@endpush
@endsection

