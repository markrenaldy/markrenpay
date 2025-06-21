<?php
header('Content-Type: application/json');

// Konfigurasi API Atlantic Pedia
$apiKey = 'yX29FZiE1oUQEQFOngbcP6N3M30ybR2hYWh5VlbDBG1DLdPs6zhlnqW02UPJWC7lvMGDK7pxmq56hfULEj9bIgY0FuHtMwaXD4iz'; // Ganti dengan API key Anda
$apiUrl = 'https://api.atlantic-pedia.co.id/v1/transaction/create';

// Ambil data dari request
$data = json_decode(file_get_contents('php://input'), true);

// Validasi data
if (empty($data['name']) || empty($data['phone']) || empty($data['amount']) || empty($data['payment_method'])) {
    echo json_encode(['success' => false, 'message' => 'Semua field harus diisi']);
    exit;
}

// Format nomor telepon
$phone = $data['phone'];
if (substr($phone, 0, 2) !== '62') {
    $phone = '62' . ltrim($phone, '0');
}

// Siapkan data untuk Atlantic Pedia
$postData = [
    'key' => $apiKey,
    'type' => 'create',
    'name' => $data['name'],
    'phone' => $phone,
    'amount' => $data['amount'],
    'payment_method' => $data['payment_method'],
    'merchant_ref' => 'MR-' . time(), // Merchant reference (unik)
    'customer_name' => $data['name'],
    'callback_url' => 'https://website-anda.com/telegram-notifier.php' // URL callback untuk notifikasi
];

// Inisialisasi cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

// Eksekusi request
$response = curl_exec($ch);
curl_close($ch);

// Proses response
$result = json_decode($response, true);

if ($result && $result['status'] === 'success') {
    // Format response untuk frontend
    $responseData = [
        'success' => true,
        'message' => 'Pembayaran berhasil diproses',
        'payment_instructions' => $result['data']['instructions'] ?? null,
        'va_number' => $result['data']['va_number'] ?? null,
        'qr_code' => $result['data']['qr_code'] ?? null,
        'redirect_url' => $result['data']['checkout_url'] ?? null
    ];
    
    echo json_encode($responseData);
} else {
    echo json_encode([
        'success' => false,
        'message' => $result['message'] ?? 'Gagal memproses pembayaran'
    ]);
}
?>
