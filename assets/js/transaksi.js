document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('search-barang');
    const searchResults = document.getElementById('search-results');
    const cartItems = document.getElementById('cart-items');
    const cartItemTemplate = document.getElementById('cart-item-template');
    const totalEl = document.getElementById('total');
    const bayarEl = document.getElementById('bayar');
    const kembalianEl = document.getElementById('kembalian');
    const cancelSaleBtn = document.getElementById('cancel-sale');

    let cart = {}; // Objek untuk menyimpan item di keranjang

    // 1. Pencarian Barang (AJAX)
    searchInput.addEventListener('keyup', function () {
        const query = searchInput.value;

        if (query.length < 2) {
            searchResults.innerHTML = '';
            return;
        }

        fetch(`${BASE_URL_JS}pages/ajax_search.php?q=${query}`)
            .then(response => response.json())
            .then(data => {
                searchResults.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.innerHTML = `<strong>${item.nama}</strong> (Stok: ${item.stok}) <br> Rp ${formatRupiah(item.harga_jual)}`;
                        div.classList.add('list-group-item', 'list-group-item-action');
                        div.style.cursor = 'pointer';
                        div.addEventListener('click', () => addToCart(item));
                        searchResults.appendChild(div);
                    });
                } else {
                    searchResults.innerHTML = '<div class="list-group-item">Barang tidak ditemukan</div>';
                }
            });
    });

    // 2. Tambah ke Keranjang
    function addToCart(item) {
        if (item.stok <= 0) {
            alert('Stok barang habis!');
            return;
        }

        if (cart[item.idbarang]) {
            // Jika sudah ada di keranjang, tambah qty
            cart[item.idbarang].qty++;
        } else {
            // Jika belum ada, tambahkan item baru
            cart[item.idbarang] = {
                id: item.idbarang,
                nama: item.nama,
                harga: item.harga_jual,
                stok: item.stok,
                qty: 1
            };
        }
        searchInput.value = '';
        searchResults.innerHTML = '';
        renderCart();
    }

    // 3. Render Keranjang
    function renderCart() {
        cartItems.innerHTML = '';
        for (const id in cart) {
            const item = cart[id];
            const template = cartItemTemplate.content.cloneNode(true);

            template.querySelector('.id-barang').value = item.id;
            template.querySelector('.nama-barang').textContent = item.nama;
            template.querySelector('.harga-barang').textContent = formatRupiah(item.harga);
            template.querySelector('.harga-hidden').value = item.harga;
            template.querySelector('.qty-input').value = item.qty;
            template.querySelector('.qty-input').max = item.stok; // Set max qty berdasarkan stok
            template.querySelector('.subtotal').textContent = formatRupiah(item.qty * item.harga);

            // Event listener untuk hapus item
            template.querySelector('.remove-item').addEventListener('click', () => {
                delete cart[id];
                renderCart();
            });

            // Event listener untuk ubah qty
            template.querySelector('.qty-input').addEventListener('change', (e) => {
                let newQty = parseInt(e.target.value);
                if (newQty > item.stok) {
                    alert(`Stok tidak mencukupi! Sisa stok: ${item.stok}`);
                    newQty = item.stok;
                    e.target.value = newQty;
                }
                if (newQty <= 0) {
                    delete cart[id];
                } else {
                    cart[id].qty = newQty;
                }
                renderCart();
            });

            cartItems.appendChild(template);
        }
        updateTotal();
    }

    // 4. Update Total
    function updateTotal() {
        let total = 0;
        for (const id in cart) {
            total += cart[id].qty * cart[id].harga;
        }
        totalEl.value = formatRupiah(total);
        // Simpan nilai numerik untuk form submission
        totalEl.dataset.value = total;
        calculateChange();
    }

    // 5. Hitung Kembalian
    bayarEl.addEventListener('keyup', calculateChange);

    function calculateChange() {
        const total = parseFloat(totalEl.dataset.value) || 0;
        const bayar = parseFloat(bayarEl.value.replace(/[^\d]/g, '')) || 0;
        const kembalian = bayar - total;
        kembalianEl.value = (kembalian >= 0) ? formatRupiah(kembalian) : '-';
    }

    // New function to reset the transaction state
    function resetTransaction() {
        cart = {};
        renderCart();
        bayarEl.value = '';
        kembalianEl.value = '';
        searchInput.value = '';
        searchResults.innerHTML = '';
        totalEl.dataset.value = 0; // Reset numeric total
        totalEl.value = formatRupiah(0); // Reset displayed total
    }

    // 6. Batalkan Transaksi
    cancelSaleBtn.addEventListener('click', () => {
        if (confirm('Apakah Anda yakin ingin membatalkan transaksi ini?')) {
            resetTransaction();
        }
    });

    // Helper untuk format Rupiah
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    // Menambahkan nilai numerik ke form sebelum submit
    document.querySelector('form').addEventListener('submit', function(e) {
        const totalValue = totalEl.dataset.value || 0;
        const bayarValue = bayarEl.value.replace(/[^\d]/g, '') || 0;
        
        // Hapus input total yang lama (yang diformat) dan buat yang baru
        totalEl.name = ''; // Hapus nama agar tidak terkirim
        const hiddenTotal = document.createElement('input');
        hiddenTotal.type = 'hidden';
        hiddenTotal.name = 'total_numeric';
        hiddenTotal.value = totalValue;
        this.appendChild(hiddenTotal);

        const hiddenBayar = document.createElement('input');
        hiddenBayar.type = 'hidden';
        hiddenBayar.name = 'bayar_numeric';
        hiddenBayar.value = bayarValue;
        this.appendChild(hiddenBayar);

    });

    // Check if a sale was just completed and trigger print/reset
    if (typeof SALE_SUCCESS !== 'undefined' && SALE_SUCCESS === true) {
        // Open print window
        if (typeof TRANSACTION_ID !== 'undefined' && TRANSACTION_ID !== null) {
            const printWindow = window.open(`${BASE_URL_JS}pages/cetak_struk.php?id=${TRANSACTION_ID}`, '_blank', 'width=400,height=600');
            // Optional: focus on the print window
            if (printWindow) {
                printWindow.focus();
            }
        }
        // Reset the form after successful sale and print
        resetTransaction();
        // Remove the query parameters from URL to prevent re-triggering on refresh
        window.history.replaceState({}, document.title, window.location.pathname);
    }

});