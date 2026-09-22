<!DOCTYPE html>
<html>
<head>
    <link rel="shortcut icon" href="{{ asset('images/logorj.ico') }}" type="image/x-icon">
    <title>Pembayaran Tryout</title>
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" 
            data-client-key="{{ $clientKey }}"></script>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: #f5f5f5; 
            margin: 0; 
            padding: 20px; 
        }
        .container { 
            max-width: 500px; 
            margin: 50px auto; 
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        .btn-primary { 
            background: #3b82f6; 
            color: white; 
            padding: 12px 24px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 16px; 
            width: 100%; 
        }
        .btn-primary:hover { background: #2563eb; }
        .transaction-info { 
            background: #f8f9fa; 
            padding: 15px; 
            border-radius: 5px; 
            margin-bottom: 20px; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="text-align: center; color: #333;">Pembayaran Tryout</h2>
        
        <div class="transaction-info">
            <h3>{{ $transaction->tryout->title }}</h3>
            <p><strong>Total:</strong> Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
            <p><strong>Order ID:</strong> {{ $transaction->order_id }}</p>
        </div>

        <button id="pay-button" class="btn-primary">Bayar Sekarang</button>
        
        <p style="text-align: center; margin-top: 20px; color: #666;">
            Anda akan diarahkan ke halaman pembayaran Midtrans
        </p>
    </div>

    <script type="text/javascript">
        var payButton = document.getElementById('pay-button');
        
        payButton.addEventListener('click', function () {
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result){
                    window.location.href = '{{ route("payment.finish") }}?order_id=' + result.order_id;
                },
                onPending: function(result){
                    window.location.href = '{{ route("payment.pending") }}?order_id=' + result.order_id;
                },
                onError: function(result){
                    window.location.href = '{{ route("payment.error") }}';
                },
                onClose: function(){
                    // User closed the popup without finishing the payment
                    alert('Silakan selesaikan pembayaran Anda sebelum menutup halaman ini.');
                }
            });
        });

        // Auto trigger payment popup on page load
        document.addEventListener('DOMContentLoaded', function() {
            payButton.click();
        });
    </script>
</body>
</html>