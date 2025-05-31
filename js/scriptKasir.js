document.addEventListener('DOMContentLoaded', function() {
  feather.replace();
  
  // Sidebar toggle
  const navbar = document.querySelector(".navbar-extra");
  document.querySelector("#menu").onclick = () => {
    navbar.classList.toggle("hidden");
  };

  // Order detail functionality
  const rincianPesanan = document.querySelector('.rincian-pesanan');
  const overlay = document.createElement('div');
  overlay.classList.add('overlay');
  document.body.appendChild(overlay);
  
  // Event delegation for order detail buttons
  document.querySelector('.pesanan-container').addEventListener('click', function(e) {
    if (e.target.closest('.btn-rincian')) {
      const button = e.target.closest('.btn-rincian');
      const orderId = button.dataset.orderId;
      const card = button.closest('.pesanan-card');
      
      // Get order details from card
      const customerName = card.querySelector('.customer-info h3').textContent;
      const tableNumber = card.querySelector('.customer-info p').textContent.replace('Meja ', '');
      const orderTime = card.querySelector('.order-time').textContent;
      const totalPrice = card.querySelector('.total-price').textContent;
      
      // Get order items
      const items = [];
      card.querySelectorAll('.order-item').forEach(item => {
        items.push({
          name: item.querySelector('.item-name').textContent,
          qty: item.querySelector('.item-qty').textContent.replace('x', '')
        });
      });
      
      // Update order detail view
      updateOrderDetail(orderId, customerName, tableNumber, orderTime, totalPrice, items);
      
      // Show detail view
      rincianPesanan.classList.add('active');
      overlay.classList.add('active');
    }
  });
  
  // Close detail view
  document.querySelector('.close-rincian').addEventListener('click', closeDetailView);
  overlay.addEventListener('click', closeDetailView);
  
  // Order actions
  document.querySelector('.btn-selesai').addEventListener('click', function() {
    const orderId = document.querySelector('.rincian-content').dataset.orderId;
    if (confirm('Apakah Anda yakin ingin menyelesaikan pesanan ini?')) {
      // In a real app, you would send an AJAX request here
      console.log(`Pesanan ${orderId} selesai`);
      alert('Pesanan telah diselesaikan');
      closeDetailView();
      // In a real app, you would update the UI without reloading
      location.reload();
    }
  });
  
  document.querySelector('.btn-batal').addEventListener('click', function() {
    const orderId = document.querySelector('.rincian-content').dataset.orderId;
    if (confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')) {
      // In a real app, you would send an AJAX request here
      console.log(`Pesanan ${orderId} dibatalkan`);
      alert('Pesanan telah dibatalkan');
      closeDetailView();
      // In a real app, you would update the UI without reloading
      location.reload();
    }
  });
  
  // Search functionality
  const searchForm = document.querySelector('.search-box form');
  searchForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const searchTerm = document.querySelector('#search-input').value.toLowerCase();
    
    document.querySelectorAll('.pesanan-card').forEach(card => {
      const orderId = card.querySelector('.order-id').textContent.toLowerCase();
      if (orderId.includes(searchTerm)) {
        card.style.display = 'block';
      } else {
        card.style.display = 'none';
      }
    });
  });
  
  // Helper functions
  function updateOrderDetail(orderId, customerName, tableNumber, orderTime, totalPrice, items) {
    const rincianContent = document.querySelector('.rincian-content');
    rincianContent.innerHTML = '';
    rincianContent.dataset.orderId = orderId;
    
    // Update header
    document.querySelector('.rincian-header h2').textContent = `Pesanan #${orderId}`;
    
    // Add customer info
    const customerHTML = `
      <div class="customer-details">
        <h3>${customerName}</h3>
        <p>Meja ${tableNumber}</p>
        <p>Waktu: ${orderTime}</p>
      </div>
      <div class="order-items-header">
        <h4>Detail Pesanan</h4>
      </div>
    `;
    rincianContent.insertAdjacentHTML('beforeend', customerHTML);
    
    // Add items
    items.forEach(item => {
      const itemHTML = `
        <div class="rincian-item">
          <div class="item-info">
            <div class="item-details">
              <h3>${item.name}</h3>
              <p>Jumlah: ${item.qty}</p>
            </div>
          </div>
        </div>
      `;
      rincianContent.insertAdjacentHTML('beforeend', itemHTML);
    });
    
    // Add total
    const totalHTML = `
      <div class="total-harga">
        <p>Total: ${totalPrice}</p>
      </div>
    `;
    rincianContent.insertAdjacentHTML('beforeend', totalHTML);
  }
  
  function closeDetailView() {
    rincianPesanan.classList.remove('active');
    overlay.classList.remove('active');
  }
});