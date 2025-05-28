document.addEventListener("DOMContentLoaded", async () => {
  try {
    // Pertama, muat data menu
    await loadMenuData();
    // Setelah data dimuat, baru tambahkan event listeners
    setupEventListeners();
    // Inisialisasi feather icons untuk elemen yang baru dibuat
    feather.replace();
  } catch (err) {
    console.error("Gagal memuat menu:", err);
  }
});

async function loadMenuData() {
  const response = await fetch("tampilkanMenu.php");
  const menuData = await response.json();

  const createCard = (item, category) => {
    const card = document.createElement("div");
    card.className = `${category}-card`;
    card.dataset.itemId = item.id;
    card.dataset.priceValue = item.price_value;
    card.dataset.image = item.image;
    card.innerHTML = `
      <img src="${item.image}" alt="${item.name}" />
      <h5>${item.name}</h5>
      <p>${item.price}</p>
      <div class="quantity-controls">
        <button class="btn-decrease"><i data-feather="minus"></i></button>
        <input type="number" class="quantity" value="0" min="0" readonly>
        <button class="btn-increase"><i data-feather="plus"></i></button>
      </div>
      <button class="btn-checkout">Tambah</button>`;
    return card;
  };

  Object.keys(menuData).forEach((category) => {
    const container = document.getElementById(`${category}-list`);
    menuData[category].forEach((item) => {
      const card = createCard(item, category);
      container.appendChild(card);
    });
  });
}

function setupEventListeners() {
  // Event delegation untuk tombol +, -, dan Tambah
  document.addEventListener("click", function (e) {
    const card = e.target.closest(".makanan-card, .minuman-card, .snack-card");
    if (!card) return;

    const quantityInput = card.querySelector(".quantity");

    // Tombol +
    if (e.target.closest(".btn-increase")) {
      let value = parseInt(quantityInput.value) || 0;
      quantityInput.value = value + 1;
    }

    // Tombol -
    else if (e.target.closest(".btn-decrease")) {
      let value = parseInt(quantityInput.value) || 0;
      if (value > 0) {
        quantityInput.value = value - 1;
      }
    }

    // Tombol Tambah
    else if (e.target.closest(".btn-checkout")) {
      const qty = parseInt(quantityInput.value);
      if (qty > 0) {
        const itemName = card.querySelector("h5").innerText;
        let category = "snack";
        if (card.classList.contains("makanan-card")) category = "makanan";
        else if (card.classList.contains("minuman-card")) category = "minuman";

        const cartData = [
          {
            id: card.dataset.itemId,
            name: itemName,
            category: category,
            quantity: qty,
            price_value: parseInt(card.dataset.priceValue),
            image: card.dataset.image,
          },
        ];

        console.log("Mengirim data:", cartData); //ngecek data yang akan dikirim

        fetch("index.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `cart=${encodeURIComponent(JSON.stringify(cartData))}`,
        })
          .then((res) => {
            console.log("Status response:", res.status); 
            return res.json(); 
          })
          .then((data) => {
            console.log("Data dari server:", data);
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
    }
  });

  // Navigasi antar section
  const navbarNav = document.querySelector(".navbar-nav");
  const sections = document.querySelectorAll("section");
  const navLinks = document.querySelectorAll(".navbar-nav a");

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

  // Fungsi popup
  function showPopup(message, isSuccess = false) {
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
}
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
