document.addEventListener("DOMContentLoaded", () => {
  // Initialize Feather icons
  if (typeof feather !== 'undefined') {
    feather.replace();
  }

  // Auto-hide alert messages after 3 seconds
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(function(alert) {
    setTimeout(function() {
      alert.style.opacity = '0';
      alert.style.transform = 'translateX(-50%) translateY(-20px)';
      setTimeout(function() {
        alert.remove();
      }, 300);
    }, 3000);
  });

  // Session storage functions for form data
  function saveFormData() {
    const nama = document.getElementById('nama')?.value || '';
    const meja = document.getElementById('meja')?.value || '';
    const pembayaran = document.getElementById('pembayaran')?.value || '';
    
    sessionStorage.setItem('cartForm_nama', nama);
    sessionStorage.setItem('cartForm_meja', meja);
    sessionStorage.setItem('cartForm_pembayaran', pembayaran);
  }

  function restoreFormData() {
    const nama = sessionStorage.getItem('cartForm_nama') || '';
    const meja = sessionStorage.getItem('cartForm_meja') || '';
    const pembayaran = sessionStorage.getItem('cartForm_pembayaran') || '';
    
    const namaField = document.getElementById('nama');
    const mejaField = document.getElementById('meja');
    const pembayaranField = document.getElementById('pembayaran');
    
    if (namaField) namaField.value = nama;
    if (mejaField) mejaField.value = meja;
    if (pembayaranField) pembayaranField.value = pembayaran;
  }

  // Auto-save form data when user types
  function setupFormAutoSave() {
    const namaField = document.getElementById('nama');
    const mejaField = document.getElementById('meja');
    const pembayaranField = document.getElementById('pembayaran');
    
    if (namaField) {
      namaField.addEventListener('input', saveFormData);
    }
    if (mejaField) {
      mejaField.addEventListener('input', saveFormData);
    }
    if (pembayaranField) {
      pembayaranField.addEventListener('change', saveFormData);
    }
  }

  // Restore form data on page load
  restoreFormData();
  setupFormAutoSave();

  // Handle cart updates via AJAX
  function updateCart(action, itemId, callback) {
    // Save form data before making AJAX request
    saveFormData();
    
    // Also store current form values as backup
    const currentFormData = {
      nama: document.getElementById('nama')?.value || '',
      meja: document.getElementById('meja')?.value || '',
      pembayaran: document.getElementById('pembayaran')?.value || ''
    };
    
    const formData = new FormData();
    formData.append('action', action);
    formData.append('item_id', itemId);

    fetch('update_cart.php', {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Update the page content without refresh
        updatePageContent(data, currentFormData);
        if (callback) callback(true);
      } else {
        showAlert('error', data.message || 'Terjadi kesalahan saat memperbarui keranjang');
        if (callback) callback(false);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showAlert('error', 'Terjadi kesalahan jaringan');
      if (callback) callback(false);
    });
  }

  // Update page content with new data
  function updatePageContent(data, backupFormData = null) {
    // Update order items
    const orderListContainer = document.querySelector('.order-items-container');
    if (orderListContainer && data.orderItems) {
      orderListContainer.innerHTML = data.orderItems;
      // Re-initialize Feather icons for new content
      if (typeof feather !== 'undefined') {
        feather.replace();
      }
    }

    // Update total price
    const totalHargaElement = document.getElementById('total-harga');
    if (totalHargaElement && data.totalHarga !== undefined) {
      totalHargaElement.textContent = `Rp ${parseInt(data.totalHarga).toLocaleString('id-ID')}`;
    }

    // Update checkout button state
    const checkoutButton = document.getElementById('btn-checkout');
    if (checkoutButton) {
      checkoutButton.disabled = data.itemCount === 0;
    }

    // Restore form data with multiple fallback methods
    setTimeout(() => {
      // First try to restore from session storage
      restoreFormData();
      
      // If backup data is provided, use it as additional fallback
      if (backupFormData) {
        const namaField = document.getElementById('nama');
        const mejaField = document.getElementById('meja');
        const pembayaranField = document.getElementById('pembayaran');
        
        if (namaField && !namaField.value && backupFormData.nama) {
          namaField.value = backupFormData.nama;
        }
        if (mejaField && !mejaField.value && backupFormData.meja) {
          mejaField.value = backupFormData.meja;
        }
        if (pembayaranField && !pembayaranField.value && backupFormData.pembayaran) {
          pembayaranField.value = backupFormData.pembayaran;
        }
      }
      
      setupFormAutoSave();
    }, 10);

    // Show success message
    if (data.message) {
      showAlert('success', data.message);
    }
  }

  // Show alert messages
  function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());

    // Create new alert
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    alert.style.opacity = '0';
    alert.style.transform = 'translateX(-50%) translateY(-20px)';

    // Insert alert after navbar
    const navbar = document.querySelector('.navbar');
    if (navbar) {
      navbar.parentNode.insertBefore(alert, navbar.nextSibling);
    }

    // Animate in
    setTimeout(() => {
      alert.style.opacity = '1';
      alert.style.transform = 'translateX(-50%) translateY(0)';
    }, 10);

    // Auto-hide after 3 seconds
    setTimeout(() => {
      alert.style.opacity = '0';
      alert.style.transform = 'translateX(-50%) translateY(-20px)';
      setTimeout(() => {
        alert.remove();
      }, 300);
    }, 3000);
  }

  // Add event listeners for cart actions
  document.addEventListener('click', function(e) {
    // Handle decrease button
    if (e.target.classList.contains('btn-decrease')) {
      e.preventDefault();
      const form = e.target.closest('form');
      const itemId = form.querySelector('input[name="item_id"]').value;
      
      updateCart('decrease', itemId, (success) => {
        if (success) {
          // Button click handled by AJAX
        }
      });
    }

    // Handle increase button
    if (e.target.classList.contains('btn-increase')) {
      e.preventDefault();
      const form = e.target.closest('form');
      const itemId = form.querySelector('input[name="item_id"]').value;
      
      updateCart('increase', itemId, (success) => {
        if (success) {
          // Button click handled by AJAX
        }
      });
    }

    // Handle delete button
    if (e.target.classList.contains('btn-hapus') || e.target.closest('.btn-hapus')) {
      e.preventDefault();
      const form = e.target.closest('form');
      const itemId = form.querySelector('input[name="item_id"]').value;
      
      if (confirm('Apakah Anda yakin ingin menghapus item ini dari keranjang?')) {
        updateCart('delete', itemId, (success) => {
          if (success) {
            // Button click handled by AJAX
          }
        });
      }
    }
  });

  // Handle form submission for checkout
  const checkoutForm = document.querySelector('form[action="checkout.php"]');
  if (checkoutForm) {
    checkoutForm.addEventListener('submit', function(e) {
      const nama = document.getElementById('nama').value.trim();
      const meja = document.getElementById('meja').value.trim();
      const pembayaran = document.getElementById('pembayaran').value;

      if (!nama || !meja) {
        e.preventDefault();
        showAlert('error', 'Harap isi nama dan nomor meja');
        return;
      }

      if (!pembayaran) {
        e.preventDefault();
        showAlert('error', 'Harap pilih metode pembayaran');
        return;
      }

      // Clear session storage on successful checkout
      sessionStorage.removeItem('cartForm_nama');
      sessionStorage.removeItem('cartForm_meja');
      sessionStorage.removeItem('cartForm_pembayaran');

      // Form will submit normally if validation passes
    });
  }
});
