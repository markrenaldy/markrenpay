document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validasi nomor WhatsApp
    const phone = document.getElementById('phone').value;
    if (!phone.startsWith('62')) {
        alert('Nomor WhatsApp harus dimulai dengan 62 (contoh: 628123456789)');
        return;
    }
    
    const formData = {
        name: document.getElementById('name').value,
        phone: phone,
        amount: document.getElementById('amount').value,
        payment_method: document.getElementById('payment_method').value
    };
    
    // Tampilkan loading
    const payButton = document.querySelector('.pay-button');
    payButton.disabled = true;
    payButton.textContent = 'Memproses...';
    
    // Kirim data ke backend
    fetch('payment-handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        const responseDiv = document.getElementById('paymentResponse');
        
        if (data.success) {
            responseDiv.className = 'success';
            responseDiv.innerHTML = `
                <h3>Pembayaran Berhasil Diproses!</h3>
                <p>Silakan selesaikan pembayaran Anda.</p>
                ${data.payment_instructions ? `<div class="instructions">${data.payment_instructions}</div>` : ''}
                ${data.va_number ? `<p>Nomor VA: <strong>${data.va_number}</strong></p>` : ''}
                ${data.qr_code ? `<div class="qr-container"><img src="${data.qr_code}" alt="QR Code"></div>` : ''}
                <p>Status pembayaran akan dikirim via WhatsApp.</p>
            `;
            
            // Jika perlu redirect ke halaman pembayaran eksternal
            if (data.redirect_url) {
                window.location.href = data.redirect_url;
            }
        } else {
            responseDiv.className = 'error';
            responseDiv.innerHTML = `
                <h3>Gagal Memproses Pembayaran</h3>
                <p>${data.message || 'Terjadi kesalahan. Silakan coba lagi.'}</p>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const responseDiv = document.getElementById('paymentResponse');
        responseDiv.className = 'error';
        responseDiv.innerHTML = `
            <h3>Terjadi Kesalahan</h3>
            <p>Silakan coba lagi nanti.</p>
        `;
    })
    .finally(() => {
        payButton.disabled = false;
        payButton.textContent = 'Bayar Sekarang';
    });
});
