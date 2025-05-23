// fungsi membuat tiap section tersembunyi
document.addEventListener("DOMContentLoaded", function () {
  const navbarNav = document.querySelector(".navbar-nav");
  const sections = document.querySelectorAll("section");
  const navLinks = document.querySelectorAll(".navbar-nav a");

  // Sembunyikan menu saat klik di luar menu
  document.addEventListener("click", function (e) {
    if (!navbarNav.contains(e.target)) {
      navbarNav.classList.remove("active");
    }
  });

  // Fungsi untuk menyembunyikan semua section kecuali yang ditargetkan
  function hideAllSectionsExcept(targetId) {
    sections.forEach((section) => {
      if (section.id === targetId) {
        section.classList.remove("hidden");
      } else {
        section.classList.add("hidden");
      }
    });
  }

  // Tampilkan default section saat halaman pertama dibuka
  hideAllSectionsExcept("makanan");

  // Tangani klik pada link navbar
  navLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault(); // Hindari scroll default
      const targetId = this.getAttribute("href").substring(1); // Ambil id tanpa #
      hideAllSectionsExcept(targetId);
    });
  });
});

// Fungsi untuk menyembunyikan menu navbar saat di scroll
let lastScrollTop = 0;
const navbar = document.querySelector("nav");

window.addEventListener("scroll", function () {
  let currentScroll = window.pageYOffset || document.documentElement.scrollTop;

  if (currentScroll > lastScrollTop) {
    // Scroll ke bawah
    navbar.classList.add("hide");
  } else {
    // Scroll ke atas
    navbar.classList.remove("hide");
  }

  lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; // Hindari nilai negatif
});

// Fungsi untuk menampilkan increase dan decrease pada card
document.addEventListener("DOMContentLoaded", () => {
  // Handle semua card quantity
  document.querySelectorAll(".makanan-card, .minuman-card, .snack-card").forEach(card => {
    const quantityInput = card.querySelector(".quantity");
    const plusBtn = card.querySelectorAll(".btn-increase")[1]; // tombol plus (kedua)
    const minusBtn = card.querySelectorAll(".btn-increase")[0]; // tombol minus (pertama)

    plusBtn.addEventListener("click", () => {
      let value = parseInt(quantityInput.value);
      quantityInput.value = value + 1;
    });

    minusBtn.addEventListener("click", () => {
      let value = parseInt(quantityInput.value);
      if (value > 0) quantityInput.value = value - 1;
    });

    // Event untuk tombol Tambah
    const tambahBtn = card.querySelector(".btn-checkout");
    tambahBtn.addEventListener("click", () => {
      const itemName = card.querySelector("h5").innerText;
      const qty = parseInt(quantityInput.value);
      if (qty > 0) {
        alert(`Menambahkan ${qty} x ${itemName} ke keranjang`);
        // Reset quantity jika mau
        quantityInput.value = 0;

        // Di sini kamu bisa simpan ke localStorage, atau kirim via fetch() ke server
      } else {
        alert("Jumlah harus lebih dari 0");
      }
    });
  });
});