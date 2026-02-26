<?php
return [
    'server_key'            => env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-3RAh5nBbKZtdE-x1eVKvUm-i'),
    'client_key'            => env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-YQ6BjX9sqs3xGMHr'),
    'riplabs_key'           => env('MIDTRANS_RIPLABS_KEY', 'a9s8d7bas98d7981273xbasduky8b71o247bai8f'),
    'callback_key'          => env('MIDTRANS_RIPLABS_KEY', 'a9s8d7bas98d7981273xbasduky8b71o247bai8f'),
    'order_prefix'          => env('MIDTRANS_ORDER_PREFIX', 'PPDBATIKA'),
    'sandbox'               => env('MIDTRANS_SANDBOX', true),
    'snap_js_url'           => env('MIDTRANS_SANDBOX', true)
                                 ? 'https://app.sandbox.midtrans.com/snap/snap.js'
                                 : 'https://app.midtrans.com/snap/snap.js',
    // ← endpoint khusus PPDB Atika (selalu sandbox via riplabs)
    'riplabs_snaptoken_url' => env('MIDTRANS_RIPLABS_URL', 'https://restapi.riplabs.co.id/snaptokenppdbatika/getsnaptoken'),
];