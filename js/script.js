document.addEventListener("DOMContentLoaded", () => {
  const menuData = {
    // List makanan
    makanan: [
      { name: "Mie Goreng", price: "Rp 8.000", image: "Assets/miegoreng.png" },
      { name: "Gado-Gado", price: "Rp 10.000", image: "Assets/gado.png" },
      { name: "Bakso", price: "Rp 12.000", image: "Assets/bakso.png" },
      { name: "Nasi Goreng", price: "Rp 13.000", image: "Assets/nasgor.png" },
      { name: "Soto Ayam", price: "Rp 15.000", image: "Assets/soto.png" },
      {
        name: "Ayam Bakar",
        price: "Rp 20.000",
        image: "Assets/ayam bakar.png",
      },
      { name: "Rendang", price: "Rp 22.000", image: "Assets/rendang.png" },
    ],
    // List minuman
    minuman: [
      { name: "Air Mineral", price: "Rp 3.000", image: "Assets/mineral.png" },
      { name: "Es Teh", price: "Rp 5.000", image: "Assets/esteh.png" },
      { name: "Jus Alpukat", price: "Rp 6.000", image: "Assets/alpukat.png" },
      { name: "Kopi Hitam", price: "Rp 7.000", image: "Assets/kopi.png" },
      { name: "Jus Jeruk", price: "Rp 8.000", image: "Assets/jeruk.png" },
      {
        name: "Susu Coklat",
        price: "Rp 9.000",
        image: "Assets/susu_coklat.png",
      },
      { name: "Teh Tarik", price: "Rp 10.000", image: "Assets/teh tarik.png" },
      {
        name: "Soda Gembira",
        price: "Rp 11.000",
        image: "Assets/soda gembira.png",
      },
    ],
    // List snack
    snack: [
      { name: "Bakwan", price: "Rp 5.000", image: "Assets/bakwan.png" },
      { name: "Risoles", price: "Rp 7.000", image: "Assets/risol.png" },
      {
        name: "Tahu Crispy",
        price: "Rp 8.000",
        image: "Assets/tahu_crispyB.png",
      },
      { name: "Pisang Goreng", price: "Rp 9.000", image: "Assets/pisgor2.png" },
      { name: "Cireng", price: "Rp 10.000", image: "Assets/cireng.png" },
      {
        name: "Singkong Keju",
        price: "Rp 11.000",
        image: "Assets/singkong_keju.png",
      },
      { name: "Lumpia", price: "Rp 12.000", image: "Assets/lumpia.png" },
      {
        name: "Kentang Goreng",
        price: "Rp 15.000",
        image: "Assets/kentang.png",
      },
      { name: "Sate Taichan", price: "Rp 16.000", image: "Assets/taichan.png" },
      { name: "Sate Ayam", price: "Rp 16.000", image: "Assets/sate top.png" },
    ],
  };

  // Fungsi membuat kartu menu
  const createCard = (item, category) => {
    const card = document.createElement("div");
    card.className = `${category}-card`;
    card.innerHTML = `
      <img src="${item.image}" alt="${item.name}" />
      <h5>${item.name}</h5>
      <p>${item.price}</p>
      <div class="quantity-controls">
        <button class="btn-decrease"><i data-feather="minus"></i></button>
        <input type="number" class="quantity" value="0" min="0" readonly>
        <button class="btn-increase"><i data-feather="plus"></i></button>
      </div>
      <button class="btn-checkout">Tambah</button>
    `;
    return card;
  };

  // Tampilkan kartu menu
  Object.keys(menuData).forEach((category) => {
    const container = document.getElementById(`${category}-list`);
    menuData[category].forEach((item) => {
      const card = createCard(item, category);
      container.appendChild(card);
    });
  });

  // Ganti ikon feather
  feather.replace();

  // Navigasi antar section
  const navbarNav = document.querySelector(".navbar-nav");
  const sections = document.querySelectorAll("section");
  const navLinks = document.querySelectorAll(".navbar-nav a");

  document.addEventListener("click", function (e) {
    if (!navbarNav.contains(e.target)) {
      navbarNav.classList.remove("active");
    }
  });

  function hideAllSectionsExcept(targetId) {
    sections.forEach((section) => {
      if (section.id === targetId) {
        section.classList.remove("hidden");
      } else {
        section.classList.add("hidden");
      }
    });
  }

  hideAllSectionsExcept("makanan");

  navLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const targetId = this.getAttribute("href").substring(1);
      hideAllSectionsExcept(targetId);
    });
  });

  // Event tombol +, - dan Tambah
  document
    .querySelectorAll(".makanan-card, .minuman-card, .snack-card")
    .forEach((card) => {
      const quantityInput = card.querySelector(".quantity");
      const plusBtn = card.querySelector(".btn-increase");
      const minusBtn = card.querySelector(".btn-decrease");
      const tambahBtn = card.querySelector(".btn-checkout");

      // Tambah jumlah
      plusBtn.addEventListener("click", () => {
        let value = parseInt(quantityInput.value) || 0;
        quantityInput.value = value + 1;
      });

      // Kurangi jumlah
      minusBtn.addEventListener("click", () => {
        let value = parseInt(quantityInput.value) || 0;
        if (value > 0) quantityInput.value = value - 1;
      });

      // Tambah ke keranjang
      tambahBtn.addEventListener("click", () => {
        const qty = parseInt(quantityInput.value);
        if (qty > 0) {
          const itemName = card.querySelector("h5").innerText;
          let category = "snack";
          if (card.classList.contains("makanan-card")) category = "makanan";
          else if (card.classList.contains("minuman-card"))
            category = "minuman";

          const cartData = [
            {
              name: itemName,
              category: category,
              quantity: qty,
            },
          ];

          const formData = new FormData();
          formData.append("cart", JSON.stringify(cartData));

          fetch("index.php", {
            method: "POST",
            body: formData,
          })
            .then((res) => res.json())
            .then((data) => {
              if (data.status === "success") {
                showPopup(
                  `Berhasil menambahkan ${qty} ${itemName} ke keranjang`,
                  true
                );
                quantityInput.value = 0;
              } else {
                showPopup("Gagal menambahkan ke keranjang", false);
              }
            })
            .catch(() => showPopup("Terjadi kesalahan jaringan", false));
        } else {
          showPopup("Jumlah harus lebih dari 0", false);
        }
      });
    });
});

// Sembunyikan navbar saat scroll ke bawah, munculkan saat scroll ke atas
let lastScrollTop = 0;
const navbar = document.querySelector("nav");

window.addEventListener("scroll", function () {
  let currentScroll = window.pageYOffset || document.documentElement.scrollTop;
  if (currentScroll > lastScrollTop) {
    navbar.classList.add("hide");
  } else {
    navbar.classList.remove("hide");
  }
  lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
});

// Fungsi popup
function showPopup(message, isSuccess = true) {
  const popup = document.getElementById("popup");
  const title = document.getElementById("popup-title");
  const messageText = document.getElementById("popup-message");
  const button = document.getElementById("popup-button");
  const icon = popup.querySelector(".popup-icon");

  if (isSuccess) {
    title.textContent = "Pesanan anda berhasil!";
    icon.innerHTML = `<img src="logo/Succses.png" alt="Success" style="width:120px;height:120px;">`;
    popup.className = "popup success show";
  } else {
    title.textContent = "Pesanan anda dibatalkan!";
    icon.innerHTML = `<img src="logo/Failed.png" alt="Error" style="width:120px;height:120px;">`;
    popup.className = "popup error show";
  }

  messageText.textContent = message;

  button.textContent = "Tutup";
  button.onclick = () => {
    popup.classList.remove("show");
  };
}
