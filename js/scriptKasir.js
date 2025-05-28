// fungsi untuk sidebar
const navbar = document.querySelector(".navbar-extra");
document.querySelector(".navbar-menu").onclick = () => {
  navbar.classList.toggle("hidden"); // tanpa titik
};

document.addEventListener('DOMContentLoaded', function() {
  feather.replace();
  
  // Variabel global
  const rincianPesanan = document.querySelector('.rincian-pesanan');
  const overlay = document.createElement('div');
  overlay.classList.add('overlay');
  document.body.appendChild(overlay);
  
  // Event listener untuk tombol rincian
  document.querySelectorAll('.btn-rincian').forEach(btn => {
    btn.addEventListener('click', function() {
      // Ambil data pesanan dari baris yang diklik
      const row = this.closest('tr');
      const idPesanan = row.cells[0].textContent;
      const namaPemesan = row.cells[1].textContent;
      const mejaPesanan = row.cells[2].textContent;
      
      // Update konten aside
      document.querySelector('.rincian-header h2').textContent = `Rincian Pesanan (${namaPemesan})`;
      
      // TODO: Tambahkan logika untuk mengambil detail pesanan dari database
      // Contoh data dummy untuk demo
      const dummyItems = [
        { nama: "Nasi Goreng", harga: 25000, jumlah: 2 },
        { nama: "Es Teh", harga: 5000, jumlah: 1 }
      ];
      
      const totalHarga = dummyItems.reduce((total, item) => total + (item.harga * item.jumlah), 0);
      
      // Render items
      const rincianContent = document.querySelector('.rincian-content');
      rincianContent.innerHTML = '';
      
      dummyItems.forEach(item => {
        const itemHTML = `
          <div class="rincian-item">
            <div class="item-info">
              <img src="Assets/menu-placeholder.png" alt="${item.nama}" />
              <div class="item-details">
                <h3>${item.nama}</h3>
                <p>Jumlah: ${item.jumlah}</p>
                <p>Harga: Rp ${item.harga.toLocaleString('id-ID')}</p>
              </div>
            </div>
          </div>
        `;
        rincianContent.insertAdjacentHTML('beforeend', itemHTML);
      });
      
      // Tambahkan total harga
      const totalHTML = `
        <div class="total-harga">
          <p><strong>Total Harga: Rp ${totalHarga.toLocaleString('id-ID')}</strong></p>
        </div>
      `;
      rincianContent.insertAdjacentHTML('beforeend', totalHTML);
      
      // Tampilkan aside dan overlay
      rincianPesanan.classList.add('active');
      overlay.classList.add('active');
    });
  });
  
  // Event listener untuk tombol close
  document.querySelector('.close-rincian').addEventListener('click', function() {
    rincianPesanan.classList.remove('active');
    overlay.classList.remove('active');
  });
  
  // Event listener untuk overlay
  overlay.addEventListener('click', function() {
    rincianPesanan.classList.remove('active');
    this.classList.remove('active');
  });
  
  // Event listener untuk tombol aksi
  document.querySelector('.btn-selesai').addEventListener('click', function() {
    if (confirm('Apakah Anda yakin ingin menyelesaikan pesanan ini?')) {
      // TODO: Tambahkan logika untuk menyelesaikan pesanan
      alert('Pesanan telah diselesaikan');
      rincianPesanan.classList.remove('active');
      overlay.classList.remove('active');
      // Refresh halaman atau update tabel
      location.reload();
    }
  });
  
  document.querySelector('.btn-batal').addEventListener('click', function() {
    if (confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')) {
      // TODO: Tambahkan logika untuk membatalkan pesanan
      alert('Pesanan telah dibatalkan');
      rincianPesanan.classList.remove('active');
      overlay.classList.remove('active');
      // Refresh halaman atau update tabel
      location.reload();
    }
  });
});