document.addEventListener("DOMContentLoaded", function () {
  feather.replace();

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

  document
    .querySelector(".pesanan-container")
    .addEventListener("click", function (e) {
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

  // Tutup rincian
  document
    .querySelector(".close-rincian")
    .addEventListener("click", closeDetailView);
  overlay.addEventListener("click", closeDetailView);

  // Tombol aksi selesai/batal pesanan
  document.querySelector(".btn-selesai").addEventListener("click", function () {
    const orderId = document.querySelector(".rincian-content").dataset.orderId;
    if (confirm("Apakah Anda yakin ingin menyelesaikan pesanan ini?")) {
      showPopupMessage("Pesanan telah diselesaikan");
      closeDetailView();
      location.reload();
    }
  });

  document.querySelector(".btn-batal").addEventListener("click", function () {
    const orderId = document.querySelector(".rincian-content").dataset.orderId;
    if (confirm("Apakah Anda yakin ingin membatalkan pesanan ini?")) {
      showPopupMessage("Pesanan telah dibatalkan");
      closeDetailView();
      location.reload();
    }
  });

  // Fitur pencarian pesanan berdasarkan ID
  const searchForm = document.querySelector(".search-box form");
  searchForm.addEventListener("submit", function (e) {
    e.preventDefault();
    const searchTerm = document
      .querySelector("#search-input")
      .value.toLowerCase();
    let matchFound = false;

    document.querySelectorAll(".pesanan-card").forEach((card) => {
      const orderId = card.querySelector(".order-id").textContent.toLowerCase();
      if (orderId.includes(searchTerm)) {
        card.style.display = "block";
        matchFound = true;
      } else {
        card.style.display = "none";
      }
    });

    const notFoundMessage = document.getElementById("not-found-message");
    notFoundMessage.style.display = matchFound ? "none" : "block";
  });

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

  document
    .querySelector("input[name='harga']")
    .addEventListener("input", function (e) {
      // Hapus karakter selain angka
      this.value = this.value.replace(/[^\d]/g, "");
    });

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
  btnTambah.addEventListener("click", () => {
    popup.classList.remove("hidden");
    overlayBg.classList.add("active");
    btnTambah.classList.add("animate-hide");
  });

  btnCancel.addEventListener("click", () => {
    popup.classList.add("hidden");
    overlayBg.classList.remove("active");
    btnTambah.classList.remove("animate-hide");
  });

  // Filter menu berdasarkan kategori
  document.querySelectorAll(".btn-filter").forEach((btn) => {
    btn.addEventListener("click", () => {
      const kategori = btn.dataset.category;
      loadMenuByCategory(kategori);
    });
  });

  // Search menu berdasarkan nama
  const searchFormMenu = document.getElementById("search-menu-form");
  const searchInput = document.getElementById("search-menu-input");
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

  // Menambahkan event listener untuk edit dan hapus menu
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
              document.querySelector(".btn-filter.active")?.dataset.category ||
              "makanan";
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
  // Load awal menu makanan
  loadMenuByCategory("makanan");
});
