<?php
// Konfigurasi Bot Telegram
$telegramBotToken = '7940665502:AAGZmVPTGzEssqipKsbbC9Lf51MDMPwW27Y'; // Ganti dengan token bot Telegram Anda
$telegramChatID = '6263193579'; // Ganti dengan chat ID tujuan

// Ambil data callback dari Atlantic Pedia
$callbackData = json_decode(file_get_contents('php://input'), true);

// Verifikasi bahwa ini adalah callback yang valid
if (isset($callbackData['status']) && $callbackData['status'] === 'PAID') {
    // Siapkan pesan notifikasi
    $message = "🔔 *PEMBAYARAN DIKONFIRMASI* 🔔\n\n";
    $message .= "✅ Pembayaran telah diterima\n";
    $message .= "📛 Nama: " . $callbackData['customer_name'] . "\n";
    $message .= "📞 WhatsApp: " . $callbackData['phone'] . "\n";
    $message .= "💳 Metode: " . $callbackData['payment_method'] . "\n";
    $message .= "💰 Jumlah: Rp " . number_format($callbackData['amount'], 0, ',', '.') . "\n";
    $message .= "🆔 Referensi: " . $callbackData['merchant_ref'] . "\n";
    $message .= "⏱ Waktu: " . date('d/m/Y H:i:s', strtotime($callbackData['paid_at'])) . "\n";
    
    // Kirim notifikasi ke Telegram
    $telegramUrl = "https://api.telegram.org/bot{$telegramBotToken}/sendMessage";
    $postData = [
        'chat_id' => $telegramChatID,
        'text' => $message,
        'parse_mode' => 'Markdown'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $telegramUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    $telegramResponse = curl_exec($ch);
    curl_close($ch);
    
    // Beri response ke Atlantic Pedia
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);
} else {
    // Jika bukan callback yang valid
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid callback']);
}
?>
