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
