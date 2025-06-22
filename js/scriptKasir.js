document.addEventListener("DOMContentLoaded", function () {
  feather.replace();

  // ===== SOLUSI 1: Fungsi untuk menunggu elemen tersedia =====
  function waitForElement(selector, timeout = 5000) {
    return new Promise((resolve, reject) => {
      const element = document.getElementById(selector);
      if (element) {
        resolve(element);
        return;
      }

      const observer = new MutationObserver((mutations) => {
        const element = document.getElementById(selector);
        if (element) {
          observer.disconnect();
          resolve(element);
        }
      });

      observer.observe(document.body, {
        childList: true,
        subtree: true,
      });

      // Timeout fallback
      setTimeout(() => {
        observer.disconnect();
        reject(new Error(`Element ${selector} not found within ${timeout}ms`));
      }, timeout);
    });
  }

  // ===== SOLUSI 2: Fungsi untuk memastikan section dan tab aktif =====
  function ensureSectionAndTabVisible() {
    // Pastikan section laporan-penjualan terlihat
    const laporanSection = document.getElementById("laporan-penjualan");
    if (laporanSection) {
      laporanSection.classList.remove("section-hidden");
    }

    // Pastikan tab laporan aktif
    const tabLaporan = document.getElementById("tab-laporan");
    if (tabLaporan) {
      // Hapus active dari semua tab-pane
      document.querySelectorAll(".tab-pane").forEach((tab) => {
        tab.classList.remove("active");
      });

      // Aktifkan tab laporan
      tabLaporan.classList.add("active");
    }

    // Pastikan tab button laporan aktif
    const tabButtons = document.querySelectorAll(".tab-btn");
    tabButtons.forEach((btn) => {
      btn.classList.remove("active");
      if (btn.dataset.tab === "laporan") {
        btn.classList.add("active");
      }
    });
  }

  // ===== SOLUSI 3: Fungsi debug elemen =====
  function debugElements() {
    const elementIds = [
      "total-pendapatan-laporan",
      "total-transaksi-laporan",
      "rata-transaksi",
      "total-menu-terjual",
      "menu-terlaris",
      "metode-pembayaran",
    ];

    console.log("=== DEBUGGING ELEMENTS ===");
    elementIds.forEach((id) => {
      const element = document.getElementById(id);
      console.log(`${id}:`, element ? "✓ Found" : "❌ Not found");

      if (!element) {
        // Cek apakah ada di dalam section yang hidden
        const hiddenSections = document.querySelectorAll(".section-hidden");
        hiddenSections.forEach((section) => {
          const hiddenElement = section.querySelector(`#${id}`);
          if (hiddenElement) {
            console.log(`  └─ Found in hidden section:`, section.id);
          }
        });

        // Cek apakah ada di dalam tab yang tidak aktif
        const inactiveTabs = document.querySelectorAll(
          ".tab-pane:not(.active)"
        );
        inactiveTabs.forEach((tab) => {
          const tabElement = tab.querySelector(`#${id}`);
          if (tabElement) {
            console.log(`  └─ Found in inactive tab:`, tab.id);
          }
        });
      }
    });
    console.log("=== END DEBUGGING ===");
  }

  // ===== TAB NAVIGATION =====
  const tabButtons = document.querySelectorAll(".tab-btn");
  const tabPanes = document.querySelectorAll(".tab-pane");

  console.log("Tab buttons found:", tabButtons.length);
  console.log("Tab panes found:", tabPanes.length);

  // Event listener untuk tab navigation
  if (tabButtons.length > 0 && tabPanes.length > 0) {
    tabButtons.forEach((button) => {
      button.addEventListener("click", function () {
        const targetTab = this.getAttribute("data-tab");
        console.log("Tab clicked:", targetTab);

        // Remove active class from all buttons and panes
        tabButtons.forEach((btn) => btn.classList.remove("active"));
        tabPanes.forEach((pane) => pane.classList.remove("active"));

        // Add active class to clicked button and corresponding pane
        this.classList.add("active");
        const targetPane = document.getElementById(`tab-${targetTab}`);
        if (targetPane) {
          targetPane.classList.add("active");
          console.log("Tab pane activated:", `tab-${targetTab}`);

          // PERBAIKAN: Pastikan section laporan-penjualan aktif jika mengakses tab laporan
          if (targetTab === "laporan") {
            const laporanSection = document.getElementById("laporan-penjualan");
            if (
              laporanSection &&
              laporanSection.classList.contains("section-hidden")
            ) {
              laporanSection.classList.remove("section-hidden");
              console.log("Laporan section activated");

              // Update URL hash untuk menunjukkan section laporan aktif
              history.replaceState(null, null, "#laporan-penjualan");
            }

            // PERBAIKAN: Tunggu lebih lama agar DOM sepenuhnya ter-render
            setTimeout(() => {
              // Auto-generate laporan setelah section aktif
              const btnGenerateLaporan = document.getElementById(
                "btn-generate-laporan"
              );
              if (btnGenerateLaporan) {
                console.log("Auto-generating laporan...");
                btnGenerateLaporan.click();
              } else {
                console.error("Generate laporan button not found");
              }
            }, 500); // Tunggu 500ms agar DOM sepenuhnya ter-render
          }
        } else {
          console.error("Target pane not found:", `tab-${targetTab}`);
        }
      });
    });
  }

  // Navigasi antar halaman dengan anchor link
  const navLinks = document.querySelectorAll(".navbar-extra a[href^='#']");
  const sections = document.querySelectorAll("main section");

  function showSectionByHash(hash) {
    sections.forEach((section) => {
      section.classList.toggle("section-hidden", `#${section.id}` !== hash);
    });
    const navbarUser = document.getElementById("navbar-user");
    const searchMenuBar = document.getElementById("search-menu-form");
    searchMenuBar.style.display =
      hash === "#menambahkan-menu" ? "flex" : "none";
    navbarUser.style.display = hash === "#list-pesanan" ? "block" : "none";
  }

  const initialHash = window.location.hash || "#list-pesanan";
  showSectionByHash(initialHash);

  navLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const targetHash = this.getAttribute("href");
      history.replaceState(null, null, targetHash);
      showSectionByHash(targetHash);
    });
  });

  const navbar = document.querySelector(".navbar-extra");
  const menuButton = document.querySelector("#menu");

  menuButton.onclick = (e) => {
    e.stopPropagation(); // Cegah propagasi agar tidak ditangkap document
    if (navbar.style.display === "inline") {
      // Jika sudah tampil, sembunyikan
      navbar.style.display = "none";
      navbar.classList.remove("hidden");
    } else {
      // Jika belum tampil, tampilkan
      navbar.style.display = "inline";
      navbar.classList.add("hidden");
    }
  };

  // Klik di luar menu akan menutup sidebar
  document.addEventListener("click", (e) => {
    const isClickInsideMenu = navbar.contains(e.target);
    const isClickOnMenuButton = menuButton.contains(e.target);

    if (!isClickInsideMenu && !isClickOnMenuButton) {
      navbar.style.display = "none";
      navbar.classList.remove("hidden");
    }
  });
  // fungsi pop up notifikasi
  function showPopupMessage(message) {
    const popup = document.getElementById("popup-notifikasi");
    const msg = document.getElementById("popup-message");
    msg.textContent = message;
    popup.classList.add("show");

    setTimeout(() => {
      popup.classList.remove("show");
    }, 2000); // tampilkan selama 2 detik
  }

  // Tampilkan rincian pesanan
  const rincianPesanan = document.querySelector(".rincian-pesanan");
  const overlay = document.createElement("div");
  overlay.classList.add("overlay");
  document.body.appendChild(overlay);

  // Pengecekan elemen pesanan-container sebelum menambahkan event listener
  const pesananContainerElement = document.querySelector(".pesanan-container");
  if (pesananContainerElement) {
    pesananContainerElement.addEventListener("click", function (e) {
      if (e.target.closest(".btn-rincian")) {
        const button = e.target.closest(".btn-rincian");
        const orderId = button.dataset.orderId;
        const card = button.closest(".pesanan-card");

        const customerName =
          card.querySelector(".customer-info h3").textContent;
        const tableNumber = card
          .querySelector(".customer-info p")
          .textContent.replace("Meja ", "");
        const orderTime = card.querySelector(".order-time").textContent;
        const totalPrice = card.querySelector(".total-price").textContent;

        const items = [];
        card.querySelectorAll(".order-item").forEach((item) => {
          items.push({
            name: item.querySelector(".item-name").textContent,
            qty: item.querySelector(".item-qty").textContent.replace("x", ""),
            price: item.dataset.price,
            image: item.dataset.image,
          });
        });

        updateOrderDetail(
          orderId,
          customerName,
          tableNumber,
          orderTime,
          totalPrice,
          items
        );
        rincianPesanan.classList.add("active");
        overlay.classList.add("active");
      }
    });
  }

  // Tutup rincian
  const closeRincianBtn = document.querySelector(".close-rincian");
  if (closeRincianBtn) {
    closeRincianBtn.addEventListener("click", closeDetailView);
  }
  overlay.addEventListener("click", closeDetailView);

  // Tombol aksi selesai/batal pesanan
  const btnSelesai = document.querySelector(".btn-selesai");
  if (btnSelesai) {
    btnSelesai.addEventListener("click", function () {
      const orderId =
        document.querySelector(".rincian-content").dataset.orderId;
      if (confirm("Apakah Anda yakin ingin menyelesaikan pesanan ini?")) {
        // Kirim request untuk menyelesaikan pesanan
        const formData = new FormData();
        formData.append("pesanan_id", orderId);
        formData.append("status", "diterima");

        fetch("selesaiPesanan.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.status === "success") {
              showPopupMessage("Pesanan telah diselesaikan");
              closeDetailView();
              location.reload();
            } else {
              showPopupMessage("Gagal menyelesaikan pesanan: " + data.message);
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            showPopupMessage("Terjadi kesalahan saat menyelesaikan pesanan");
          });
      }
    });
  }

  const btnBatal = document.querySelector(".btn-batal");
  if (btnBatal) {
    btnBatal.addEventListener("click", function () {
      const orderId =
        document.querySelector(".rincian-content").dataset.orderId;
      if (confirm("Apakah Anda yakin ingin membatalkan pesanan ini?")) {
        // Kirim request untuk membatalkan pesanan
        const formData = new FormData();
        formData.append("pesanan_id", orderId);
        formData.append("status", "dibatalkan");

        fetch("selesaiPesanan.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.status === "success") {
              showPopupMessage("Pesanan telah dibatalkan");
              closeDetailView();
              location.reload();
            } else {
              showPopupMessage("Gagal membatalkan pesanan: " + data.message);
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            showPopupMessage("Terjadi kesalahan saat membatalkan pesanan");
          });
      }
    });
  }

  // Fitur pencarian pesanan berdasarkan ID
  const searchForm = document.querySelector(".search-box form");
  if (searchForm) {
    searchForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const searchTerm = document
        .querySelector("#search-input")
        .value.toLowerCase();
      let matchFound = false;

      document.querySelectorAll(".pesanan-card").forEach((card) => {
        const orderId = card
          .querySelector(".order-id")
          .textContent.toLowerCase();
        if (orderId.includes(searchTerm)) {
          card.style.display = "block";
          matchFound = true;
        } else {
          card.style.display = "none";
        }
      });

      const notFoundMessage = document.getElementById("not-found-message");
      if (notFoundMessage) {
        notFoundMessage.style.display = matchFound ? "none" : "block";
      }
    });
  }

  // Fungsi update detail tampilan pesanan
  function updateOrderDetail(
    orderId,
    customerName,
    tableNumber,
    orderTime,
    totalPrice,
    items
  ) {
    const rincianContent = document.querySelector(".rincian-content");
    rincianContent.innerHTML = "";
    rincianContent.dataset.orderId = orderId;

    const customerHTML = `
      <div class="customer-details">
        <h3>${customerName}</h3>
        <p>Meja ${tableNumber}</p>
      </div>
    `;
    rincianContent.insertAdjacentHTML("beforeend", customerHTML);

    items.forEach((item) => {
      const itemHTML = `
        <div class="rincian-item">
          <h3 class="item-nama">${item.name}</h3>
          <div class="rincian-layout">
            <div class="item-image"><img src="${item.image}" alt="${
        item.name
      }"></div>
            <div class="item-jumlah"><p>x${item.qty}</p></div>
            <div class="item-harga">
              <p>Rp ${(
                parseInt(item.price) * parseInt(item.qty)
              ).toLocaleString("id-ID")}</p>
            </div>
        </div>
      `;
      rincianContent.insertAdjacentHTML("beforeend", itemHTML);
    });

    const totalHTML = `<div class="total-harga"><p>Total: ${totalPrice}</p></div>`;
    rincianContent.insertAdjacentHTML("beforeend", totalHTML);
  }

  function closeDetailView() {
    rincianPesanan.classList.remove("active");
    overlay.classList.remove("active");
  }

  // Fungsi untuk mengambil dan menampilkan menu berdasarkan kategori
  const menuContainer = document.getElementById("menu-container");
  const popup = document.getElementById("popup-tambah-menu");
  const btnTambah = document.getElementById("btn-tambah-menu");
  const btnCancel = document.getElementById("btn-cancel");
  const formTambah = document.getElementById("form-tambah-menu");
  const overlayBg = document.getElementById("popup-overlay");

  if (formTambah) {
    formTambah.addEventListener("submit", async function (e) {
      e.preventDefault();
      const currentCategory =
        document.querySelector(".btn-filter.active")?.dataset.category ||
        "makanan";

      const formData = new FormData(formTambah);

      const response = await fetch("tambahMenu.php", {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (result.status === "success") {
        showPopupMessage("Menu berhasil ditambahkan");
        loadMenuByCategory(currentCategory);

        // Tutup popup dan reset form
        popup.classList.add("hidden");
        overlayBg.classList.remove("active");
        btnTambah.classList.remove("animate-hide");
        formTambah.reset();
      } else {
        showPopupMessage("Gagal menambahkan menu: " + result.message);
      }
    });
  }

  const hargaInput = document.querySelector("input[name='harga']");
  if (hargaInput) {
    hargaInput.addEventListener("input", function (e) {
      // Hapus karakter selain angka
      this.value = this.value.replace(/[^\d]/g, "");
    });
  }

  async function loadMenuByCategory(category) {
    const response = await fetch(`getMenuByCategory.php?kategori=${category}`);
    const data = await response.json();
    menuContainer.innerHTML = "";

    if (data.length === 0) {
      menuContainer.innerHTML = `<p style="text-align:center">Tidak ada menu ditemukan.</p>`;
      return;
    }

    data.forEach((item) => {
      const card = document.createElement("div");
      card.className = "menu-card";
      card.innerHTML = `
        <img src="${item.gambar}" alt="${item.nama}" class="menu-img"/>
        <h4>${item.nama}</h4>
        <p>Rp ${parseInt(item.harga).toLocaleString("id-ID")}</p>
        <div class="menu-actions">
          <button class="btn-edit" data-id="${
            item.id_menu
          }"><i data-feather="edit"></i></button>
          <button class="btn-delete" data-id="${
            item.id_menu
          }"><i data-feather="trash-2"></i></button>
        </div>
      `;
      menuContainer.appendChild(card);
    });
    feather.replace();
  }

  // Tombol tambah menu tampilkan popup
  if (btnTambah) {
    btnTambah.addEventListener("click", () => {
      popup.classList.remove("hidden");
      overlayBg.classList.add("active");
      btnTambah.classList.add("animate-hide");
    });
  }

  if (btnCancel) {
    btnCancel.addEventListener("click", () => {
      popup.classList.add("hidden");
      overlayBg.classList.remove("active");
      btnTambah.classList.remove("animate-hide");
    });
  }

  // Filter menu berdasarkan kategori
  const filterButtons = document.querySelectorAll(".btn-filter");
  if (filterButtons.length > 0) {
    filterButtons.forEach((btn) => {
      btn.addEventListener("click", () => {
        const kategori = btn.dataset.category;
        loadMenuByCategory(kategori);
      });
    });
  }

  // Search menu berdasarkan nama
  const searchFormMenu = document.getElementById("search-menu-form");
  const searchInput = document.getElementById("search-menu-input");

  if (searchFormMenu && searchInput) {
    searchInput.value = "";
    searchFormMenu.addEventListener("submit", async function (e) {
      e.preventDefault();
      const keyword = searchInput.value.trim();

      let url = "";
      if (keyword === "") {
        const currentCategory =
          document.querySelector(".btn-filter.active")?.dataset.category ||
          "makanan";
        url = `getMenuByCategory.php?kategori=${currentCategory}`;
      } else {
        url = `getMenuByCategory.php?search=${encodeURIComponent(keyword)}`;
      }

      const response = await fetch(url);
      const data = await response.json();

      menuContainer.innerHTML = "";

      if (data.length === 0) {
        menuContainer.innerHTML = `
        <div class="no-menu-found">
          <p>Menu tidak ditemukan.</p>
        </div>`;
        return;
      }

      data.forEach((item) => {
        const card = document.createElement("div");
        card.className = "menu-card";
        card.innerHTML = `
        <img src="${item.gambar}" alt="${item.nama}" class="menu-img"/>
        <h4>${item.nama}</h4>
        <p>Rp ${parseInt(item.harga).toLocaleString("id-ID")}</p>
        <div class="menu-actions">
          <button class="btn-edit" data-id="${
            item.id_menu
          }"><i data-feather="edit"></i></button>
          <button class="btn-delete" data-id="${
            item.id_menu
          }"><i data-feather="trash-2"></i></button>
        </div>
      `;
        menuContainer.appendChild(card);
      });

      feather.replace(); // refresh icon
    });
  }

  // Menambahkan event listener untuk edit dan hapus menu
  if (menuContainer) {
    menuContainer.addEventListener("click", (e) => {
      const popupEdit = document.getElementById("popup-edit-menu");
      const formEdit = document.getElementById("form-edit-menu");
      const cancelEdit = document.getElementById("btn-cancel-edit");
      const inputEditId = document.getElementById("edit-id-menu");
      const inputEditNama = document.getElementById("edit-nama");
      const inputEditHarga = document.getElementById("edit-harga");
      const popupHapus = document.getElementById("popup-konfirmasi-hapus");
      const confirmHapusBtn = document.getElementById("confirm-hapus");
      const cancelHapusBtn = document.getElementById("cancel-hapus");

      let idMenuToDelete = null;

      // Tombol edit ditekan
      if (e.target.closest(".btn-edit")) {
        const btn = e.target.closest(".btn-edit");
        const card = btn.closest(".menu-card");
        const id = btn.dataset.id;
        const nama = card.querySelector("h4").textContent;
        const harga = card.querySelector("p").textContent.replace(/\D/g, "");
        const imgSrc = card.querySelector("img").getAttribute("src");

        inputEditId.value = id;
        inputEditNama.value = nama;
        inputEditHarga.value = harga;
        document.getElementById("preview-edit-gambar").src = imgSrc;

        popupEdit.classList.remove("hidden");
        popupEdit.classList.add("show");
        overlayBg.classList.add("active");

        document
          .getElementById("edit-gambar")
          .addEventListener("change", function () {
            const file = this.files[0];
            if (file) {
              const reader = new FileReader();
              reader.onload = function (e) {
                document.getElementById("preview-edit-gambar").src =
                  e.target.result;
              };
              reader.readAsDataURL(file);
            }
          });

        cancelEdit.addEventListener("click", () => {
          popupEdit.classList.remove("show");
          overlayBg.classList.remove("active");
          setTimeout(() => popupEdit.classList.add("hidden"), 300);
        });
      }

      // Submit Edit Menu
      formEdit.addEventListener("submit", async function (e) {
        e.preventDefault();

        const formData = new FormData(formEdit);

        const response = await fetch("editMenu.php", {
          method: "POST",
          body: formData,
        });

        const result = await response.json();

        if (result.status === "success") {
          showPopupMessage(result.message);
          popupEdit.classList.remove("show");
          overlayBg.classList.remove("active");
          setTimeout(() => popupEdit.classList.add("hidden"), 300);
          const currentCategory =
            document.querySelector(".btn-filter.active")?.dataset.category ||
            "makanan";
          loadMenuByCategory(currentCategory);
        } else {
          showPopupMessage("Gagal edit menu: " + result.message);
        }
      });

      inputEditHarga.addEventListener("input", function () {
        this.value = this.value.replace(/[^\d]/g, "");
      });

      // Tombol hapus ditekan
      if (e.target.closest(".btn-delete")) {
        const btn = e.target.closest(".btn-delete");
        idMenuToDelete = btn.dataset.id;

        // Tampilkan popup konfirmasi hapus
        popupHapus.classList.remove("hidden");
        overlayBg.classList.add("active");
      }

      confirmHapusBtn.addEventListener("click", () => {
        if (!idMenuToDelete) return;

        fetch("hapusMenu.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `id_menu=${idMenuToDelete}`,
        })
          .then((res) => res.json())
          .then((result) => {
            if (result.status === "success") {
              showPopupMessage(result.message);
              const currentCategory =
                document.querySelector(".btn-filter.active")?.dataset
                  .category || "makanan";
              loadMenuByCategory(currentCategory);
            } else {
              showPopupMessage("Gagal menghapus menu: " + result.message);
            }
            popupHapus.classList.add("hidden");
            overlayBg.classList.remove("active");
            idMenuToDelete = null;
          })
          .catch((error) => {
            showPopupMessage("Terjadi kesalahan saat menghapus.");
            console.error(error);
          });
      });

      cancelHapusBtn.addEventListener("click", () => {
        popupHapus.classList.add("hidden");
        overlayBg.classList.remove("active");
        idMenuToDelete = null;
      });
    });
  }
  // Load awal menu makanan
  if (menuContainer) {
    loadMenuByCategory("makanan");
  }

  // ===== FUNGSI UNTUK ORDER SUMMARY SCROLL =====
  function checkOrderSummaryScroll() {
    const orderSummaries = document.querySelectorAll(".order-summary");
    if (orderSummaries.length > 0) {
      orderSummaries.forEach((summary) => {
        if (summary.scrollHeight > summary.clientHeight) {
          summary.classList.add("has-scroll");
        } else {
          summary.classList.remove("has-scroll");
        }
      });
    }
  }

  // Panggil fungsi saat halaman dimuat
  checkOrderSummaryScroll();

  // Panggil fungsi saat window di-resize
  window.addEventListener("resize", checkOrderSummaryScroll);

  // Panggil fungsi setelah data pesanan dimuat (untuk pesanan yang sudah ada di DOM)
  setTimeout(checkOrderSummaryScroll, 100);

  // Nonaktifkan tombol print saat halaman pertama kali dimuat
  const btnPrintLaporan = document.getElementById("btn-print-laporan");
  if (btnPrintLaporan) {
    btnPrintLaporan.disabled = true;
  }

  // Observer untuk memantau perubahan pada pesanan container
  const pesananContainerObserver = document.querySelector(".pesanan-container");
  if (pesananContainerObserver) {
    const observer = new MutationObserver(() => {
      setTimeout(checkOrderSummaryScroll, 50);
    });

    observer.observe(pesananContainerObserver, {
      childList: true,
      subtree: true,
    });
  }

  // ===== LAPORAN & RIWAYAT PENJUALAN =====

  // ===== TAB 1: RIWAYAT TRANSAKSI =====
  const formRiwayatTanggal = document.getElementById("form-riwayat-tanggal");
  const transaksiList = document.getElementById("transaksi-list");
  const riwayatSummary = document.getElementById("riwayat-summary");

  if (formRiwayatTanggal && transaksiList) {
    formRiwayatTanggal.addEventListener("submit", function (e) {
      e.preventDefault();
      const tanggal = document.getElementById("tanggal-riwayat").value;

      if (!tanggal) {
        showPopupMessage("Silakan pilih tanggal");
        return;
      }

      // Tampilkan loading
      transaksiList.innerHTML = `
        <div class="empty-state">
          <i data-feather="loader" class="empty-icon"></i>
          <p>Memuat data...</p>
        </div>
      `;
      feather.replace();

      // Kirim request AJAX
      const formData = new FormData();
      formData.append("tanggal", tanggal);

      fetch("getHistoriPenjualan.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            displayRiwayatData(data);
          } else {
            showPopupMessage("Gagal memuat data riwayat");
            transaksiList.innerHTML = `
              <div class="empty-state">
                <i data-feather="alert-triangle" class="empty-icon"></i>
                <p>Gagal memuat data riwayat</p>
              </div>
            `;
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          showPopupMessage("Terjadi kesalahan saat memuat data");
          transaksiList.innerHTML = `
            <div class="empty-state">
              <i data-feather="alert-triangle" class="empty-icon"></i>
              <p>Terjadi kesalahan saat memuat data</p>
            </div>
          `;
        });
    });

    // Fungsi untuk menampilkan data riwayat
    function displayRiwayatData(data) {
      const riwayat = data.data;

      // Update summary dengan pengecekan elemen
      const totalTransaksiElement = document.getElementById("total-transaksi");
      const totalPendapatanHariElement = document.getElementById(
        "total-pendapatan-hari"
      );

      if (totalTransaksiElement) {
        totalTransaksiElement.textContent = data.total_transaksi;
      }
      if (totalPendapatanHariElement) {
        totalPendapatanHariElement.textContent =
          "Rp " + data.total_pendapatan.toLocaleString("id-ID");
      }

      riwayatSummary.style.display = "block";

      // Tampilkan data riwayat
      if (riwayat.length === 0) {
        transaksiList.innerHTML = `
          <div class="empty-state">
            <i data-feather="calendar" class="empty-icon"></i>
            <p>Tidak ada transaksi pada tanggal yang dipilih</p>
          </div>
        `;
      } else {
        let riwayatHTML = "";
        riwayat.forEach((order) => {
          const waktuPesan = new Date(order.waktu_pesan).toLocaleTimeString(
            "id-ID",
            {
              hour: "2-digit",
              minute: "2-digit",
            }
          );
          const waktuSelesai = new Date(order.waktu_selesai).toLocaleTimeString(
            "id-ID",
            {
              hour: "2-digit",
              minute: "2-digit",
            }
          );

          // Status badge
          const statusIcon =
            order.status === "diterima" ? "check-circle" : "x-circle";
          const statusColor =
            order.status === "diterima" ? "success" : "danger";
          const statusText =
            order.status === "diterima" ? "Diterima" : "Dibatalkan";

          let itemsHTML = "";
          order.items.forEach((item) => {
            itemsHTML += `
              <div class="riwayat-item">
                <span class="item-name">${item.nama_menu}</span>
                <span class="item-qty">x${item.jumlah}</span>
                <span class="item-price">Rp ${item.harga.toLocaleString(
                  "id-ID"
                )}</span>
              </div>
            `;
          });

          riwayatHTML += `
            <div class="riwayat-card status-${order.status}">
              <div class="riwayat-card-header">
                <div class="riwayat-header-left">
                  <div class="riwayat-order-info">
                    <span class="riwayat-order-id">#${order.id}</span>
                    <span class="riwayat-time">${waktuPesan} - ${waktuSelesai}</span>
                  </div>
                  <div class="riwayat-customer-info">
                    <span class="riwayat-customer-name">${
                      order.nama_pelanggan
                    }</span>
                    <span class="riwayat-table">Meja ${order.no_meja}</span>
                  </div>
                </div>
                <div class="riwayat-status-badge status-${statusColor}">
                  <i data-feather="${statusIcon}"></i>
                  <span>${statusText}</span>
                </div>
              </div>
              <div class="riwayat-card-body">
                <div class="riwayat-payment-info">
                  <p><strong>Metode Pembayaran:</strong> ${order.pembayaran}</p>
                  <p><strong>Kasir:</strong> ${order.nama_kasir}</p>
                </div>
                <div class="riwayat-items">
                  ${itemsHTML}
                </div>
              </div>
              <div class="riwayat-card-footer">
                <span class="riwayat-total-price">
                  Rp ${order.total_harga.toLocaleString("id-ID")}
                </span>
                <span class="riwayat-kasir">Diproses oleh: ${
                  order.nama_kasir
                }</span>
              </div>
            </div>
          `;
        });

        transaksiList.innerHTML = riwayatHTML;
      }

      feather.replace();
    }
  }

  // ===== TAB 2: REKAP LAPORAN =====
  const periodeLaporan = document.getElementById("periode-laporan");
  const customDateGroup = document.getElementById("custom-date-group");
  const btnGenerateLaporan = document.getElementById("btn-generate-laporan");

  console.log("Periode laporan element:", periodeLaporan);
  console.log("Custom date group element:", customDateGroup);
  console.log("Generate laporan button:", btnGenerateLaporan);

  if (periodeLaporan && customDateGroup && btnGenerateLaporan) {
    console.log("All laporan elements found, setting up event listeners...");

    // Toggle custom date inputs
    periodeLaporan.addEventListener("change", function () {
      if (this.value === "custom") {
        customDateGroup.style.display = "flex";
      } else {
        customDateGroup.style.display = "none";
      }
    });

    // Generate laporan
    btnGenerateLaporan.addEventListener("click", function () {
      console.log("Generate laporan button clicked");

      // PERBAIKAN: Pastikan tab laporan aktif sebelum generate
      const tabLaporan = document.getElementById("tab-laporan");
      const laporanSection = document.getElementById("laporan-penjualan");

      if (!tabLaporan.classList.contains("active")) {
        console.log("⚠️ Tab laporan not active, activating now...");

        // Aktifkan tab laporan
        document.querySelectorAll(".tab-pane").forEach((tab) => {
          tab.classList.remove("active");
        });
        tabLaporan.classList.add("active");

        // Aktifkan tab button
        document.querySelectorAll(".tab-btn").forEach((btn) => {
          btn.classList.remove("active");
          if (btn.dataset.tab === "laporan") {
            btn.classList.add("active");
          }
        });

        // Aktifkan section
        if (laporanSection) {
          laporanSection.classList.remove("section-hidden");
        }

        console.log("✓ Tab and section activated");
      }

      const periode = periodeLaporan.value;
      const tanggalMulai = document.getElementById("tanggal-mulai").value;
      const tanggalAkhir = document.getElementById("tanggal-akhir").value;

      console.log("Periode:", periode);
      console.log("Tanggal mulai:", tanggalMulai);
      console.log("Tanggal akhir:", tanggalAkhir);

      // Debug elemen sebelum request
      debugElements();

      // Validasi untuk custom date
      if (periode === "custom" && (!tanggalMulai || !tanggalAkhir)) {
        showPopupMessage("Silakan pilih tanggal mulai dan akhir");
        return;
      }

      // Tampilkan loading
      const laporanContent = document.getElementById("laporan-content");
      if (laporanContent) {
        laporanContent.innerHTML = `
          <div class="empty-state">
            <i data-feather="loader" class="empty-icon"></i>
            <p>Memuat laporan...</p>
          </div>
        `;
        feather.replace();
      }

      // Kirim request AJAX
      const formData = new FormData();
      formData.append("periode", periode);
      if (periode === "custom") {
        formData.append("tanggal_mulai", tanggalMulai);
        formData.append("tanggal_akhir", tanggalAkhir);
      }

      console.log("Sending AJAX request to getLaporanPenjualan.php...");

      fetch("getLaporanPenjualan.php", {
        method: "POST",
        body: formData,
        headers: {
          Accept: "application/json",
        },
      })
        .then((response) => {
          console.log("Response status:", response.status);
          console.log("Response headers:", response.headers);

          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then((data) => {
          console.log("Data received:", data);
          if (data.status === "success") {
            updateLaporanElements(data.data);
          } else {
            showPopupMessage(
              "Gagal memuat laporan: " + (data.message || "Unknown error")
            );
            if (laporanContent) {
              laporanContent.innerHTML = `
              <div class="empty-state">
                <i data-feather="alert-triangle" class="empty-icon"></i>
                <p>Gagal memuat laporan</p>
              </div>
            `;
            }

            // Nonaktifkan tombol print jika gagal memuat laporan
            const btnPrint = document.getElementById("btn-print-laporan");
            if (btnPrint) {
              btnPrint.disabled = true;
            }
          }
        })
        .catch((error) => {
          console.error("Fetch error:", error);
          showPopupMessage(
            "Terjadi kesalahan saat memuat laporan: " + error.message
          );
          if (laporanContent) {
            laporanContent.innerHTML = `
            <div class="empty-state">
              <i data-feather="alert-triangle" class="empty-icon"></i>
              <p>Terjadi kesalahan saat memuat laporan</p>
              <small>Error: ${error.message}</small>
            </div>
          `;
          }

          // Nonaktifkan tombol print jika terjadi error
          const btnPrint = document.getElementById("btn-print-laporan");
          if (btnPrint) {
            btnPrint.disabled = true;
          }
        });
    });

    // ===== SOLUSI 3: Fungsi updateLaporanElements yang lebih robust =====
    async function updateLaporanElements(data) {
      console.log("Starting updateLaporanElements with data:", data);

      // Pastikan container ada
      let laporanContent = document.querySelector(
        "#laporan-penjualan #tab-laporan #laporan-content"
      );

      if (!laporanContent) {
        console.log("Laporan content not found, creating dynamically...");
        const tabLaporan = document.querySelector(
          "#laporan-penjualan #tab-laporan"
        );
        if (tabLaporan) {
          laporanContent = document.createElement("div");
          laporanContent.id = "laporan-content";
          laporanContent.className = "laporan-content";
          tabLaporan.appendChild(laporanContent);
        }
      }

      if (laporanContent) {
        // Buat struktur HTML secara dinamis
        laporanContent.innerHTML = `
          <div class="laporan-cards">
            <div class="laporan-card">
              <div class="card-icon">
                <i data-feather="dollar-sign"></i>
              </div>
              <div class="card-content">
                <h3>Total Pendapatan</h3>
                <p id="total-pendapatan-laporan">Rp ${data.ringkasan.total_pendapatan.toLocaleString(
                  "id-ID"
                )}</p>
              </div>
            </div>

            <div class="laporan-card">
              <div class="card-icon">
                <i data-feather="shopping-bag"></i>
              </div>
              <div class="card-content">
                <h3>Total Transaksi</h3>
                <p id="total-transaksi-laporan">${
                  data.ringkasan.total_transaksi
                }</p>
              </div>
            </div>

            <div class="laporan-card">
              <div class="card-icon">
                <i data-feather="trending-up"></i>
              </div>
              <div class="card-content">
                <h3>Rata-rata per Transaksi</h3>
                <p id="rata-transaksi">Rp ${Math.round(
                  data.ringkasan.rata_transaksi
                ).toLocaleString("id-ID")}</p>
              </div>
            </div>

            <div class="laporan-card">
              <div class="card-icon">
                <i data-feather="users"></i>
              </div>
              <div class="card-content">
                <h3>Menu Terjual</h3>
                <p id="total-menu-terjual">${
                  data.ringkasan.total_menu_terjual
                } item</p>
              </div>
            </div>
          </div>

          <div class="laporan-detail">
            <div class="detail-section">
              <h3>Menu Terlaris</h3>
              <div id="menu-terlaris" class="menu-list">
                ${
                  data.menu_terlaris && data.menu_terlaris.length > 0
                    ? data.menu_terlaris
                        .map(
                          (menu, index) => `
                    <div class="menu-item">
                      <span class="rank">${index + 1}</span>
                      <span class="menu-name">${menu.nama_menu}</span>
                      <span class="sold-count">${
                        menu.total_terjual
                      } terjual</span>
                    </div>
                  `
                        )
                        .join("")
                    : `<div class="empty-state">
                    <i data-feather="package" class="empty-icon"></i>
                    <p>Belum ada data menu terlaris</p>
                  </div>`
                }
              </div>
            </div>

            <div class="detail-section">
              <h3>Metode Pembayaran</h3>
              <div id="metode-pembayaran" class="payment-stats">
                ${
                  data.metode_pembayaran && data.metode_pembayaran.length > 0
                    ? data.metode_pembayaran
                        .map(
                          (metode) => `
                    <div class="payment-item">
                      <span class="payment-method">${metode.pembayaran}</span>
                      <span class="payment-count">${metode.jumlah_transaksi} transaksi</span>
                    </div>
                  `
                        )
                        .join("")
                    : `<div class="empty-state">
                    <i data-feather="credit-card" class="empty-icon"></i>
                    <p>Belum ada data pembayaran</p>
                  </div>`
                }
              </div>
            </div>
          </div>
        `;

        // Re-render feather icons
        if (typeof feather !== "undefined") {
          feather.replace();
        }

        console.log("✅ Laporan content created and updated successfully");

        // Aktifkan tombol print setelah laporan berhasil dimuat
        const btnPrint = document.getElementById("btn-print-laporan");
        if (btnPrint) {
          btnPrint.disabled = false;
        }
      }
    }

    // ===== FUNGSI PRINT LAPORAN =====
    function printLaporan() {
      const btnPrint = document.getElementById("btn-print-laporan");

      // Tampilkan loading state
      if (btnPrint) {
        btnPrint.classList.add("loading");
        btnPrint.disabled = true;
      }

      // Ambil data periode untuk header
      const periode = document.getElementById("periode-laporan").value;
      const tanggalMulai = document.getElementById("tanggal-mulai").value;
      const tanggalAkhir = document.getElementById("tanggal-akhir").value;

      let periodeText = "";
      if (periode === "hari") {
        periodeText = "Hari Ini";
      } else if (periode === "minggu") {
        periodeText = "Minggu Ini";
      } else if (periode === "bulan") {
        periodeText = "Bulan Ini";
      } else if (periode === "custom") {
        periodeText = `${tanggalMulai} - ${tanggalAkhir}`;
      }

      // Buat window print baru
      const printWindow = window.open("", "_blank");

      // HTML untuk print
      const printHTML = `
        <!DOCTYPE html>
        <html>
        <head>
          <title>Laporan Penjualan - AngkringAan</title>
          <style>
            body {
              font-family: Arial, sans-serif;
              margin: 0;
              padding: 20px;
              background: white;
              color: black;
            }
            .print-header {
              text-align: center;
              margin-bottom: 30px;
              border-bottom: 2px solid #000;
              padding-bottom: 20px;
            }
            .print-header h1 {
              font-size: 24px;
              font-weight: bold;
              margin: 0 0 10px 0;
            }
            .print-header p {
              font-size: 14px;
              margin: 5px 0;
            }
            .laporan-cards {
              display: grid;
              grid-template-columns: repeat(2, 1fr);
              gap: 20px;
              margin-bottom: 30px;
            }
            .laporan-card {
              border: 1px solid #000;
              padding: 15px;
              text-align: center;
            }
            .card-content h3 {
              font-size: 14px;
              margin: 0 0 10px 0;
              font-weight: normal;
            }
            .card-content p {
              font-size: 20px;
              font-weight: bold;
              margin: 0;
            }
            .laporan-detail {
              display: grid;
              grid-template-columns: 1fr 1fr;
              gap: 30px;
            }
            .detail-section {
              border: 1px solid #000;
              padding: 20px;
            }
            .detail-section h3 {
              font-size: 16px;
              border-bottom: 1px solid #000;
              padding-bottom: 10px;
              margin: 0 0 20px 0;
            }
            .menu-item, .payment-item {
              display: flex;
              justify-content: space-between;
              padding: 8px 0;
              border-bottom: 1px solid #ccc;
            }
            .menu-item:last-child, .payment-item:last-child {
              border-bottom: none;
            }
            .menu-name, .payment-method {
              font-weight: bold;
            }
            .sold-count, .payment-count {
              font-weight: bold;
            }
            .print-footer {
              text-align: center;
              margin-top: 30px;
              border-top: 1px solid #000;
              padding-top: 20px;
              font-size: 12px;
            }
            @media print {
              body { margin: 0; }
              .laporan-cards { page-break-inside: avoid; }
              .laporan-detail { page-break-inside: avoid; }
            }
          </style>
        </head>
        <body>
          <div class="print-header">
            <h1>LAPORAN PENJUALAN ANGRINGAAN</h1>
            <p>Periode: ${periodeText}</p>
            <p>Tanggal Cetak: ${new Date().toLocaleDateString("id-ID")}</p>
            <p>Kasir: ${document
              .querySelector(".navbar-user h6")
              .textContent.replace("Selamat datang ", "")}</p>
          </div>
          
          ${document.getElementById("laporan-content").innerHTML}
          
          <div class="print-footer">
            <p>Laporan ini dicetak pada ${new Date().toLocaleString(
              "id-ID"
            )}</p>
            <p>AngkringAan - Sistem Manajemen Penjualan</p>
          </div>
        </body>
        </html>
      `;

      printWindow.document.write(printHTML);
      printWindow.document.close();

      // Tunggu sebentar agar konten ter-load, lalu print
      setTimeout(() => {
        printWindow.print();
        printWindow.close();

        // Hapus loading state
        if (btnPrint) {
          btnPrint.classList.remove("loading");
          btnPrint.disabled = false;
        }

        showPopupMessage("Laporan berhasil dicetak!");
      }, 500);
    }

    // Event listener untuk tombol print
    const btnPrintLaporan = document.getElementById("btn-print-laporan");
    if (btnPrintLaporan) {
      btnPrintLaporan.addEventListener("click", printLaporan);
    }

    // Fungsi untuk mendapatkan teks periode
    function getPeriodeText(mulai, akhir) {
      const startDate = new Date(mulai);
      const endDate = new Date(akhir);

      if (mulai === akhir) {
        return startDate.toLocaleDateString("id-ID", {
          weekday: "long",
          year: "numeric",
          month: "long",
          day: "numeric",
        });
      } else {
        return `${startDate.toLocaleDateString(
          "id-ID"
        )} - ${endDate.toLocaleDateString("id-ID")}`;
      }
    }
  }
});
