document.addEventListener("DOMContentLoaded", () => {
  const cartItemsContainer = document.getElementById("cart-items");
  const totalPriceDisplay = document.getElementById("total-price");

  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  function renderCart() {
    cartItemsContainer.innerHTML = "";
    let total = 0;

    cart.forEach((item, index) => {
      const itemTotal = item.price * item.quantity;
      total += itemTotal;

      const div = document.createElement("div");
      div.className = "cart-item";
      div.innerHTML = `
        <div class="cart-item-info">
          <strong>${item.name}</strong><br>
          Harga: Rp ${item.price.toLocaleString()}<br>
        </div>
        <div class="quantity-controls">
          <button onclick="updateQuantity(${index}, -1)">-</button>
          <input type="number" value="${item.quantity}" readonly>
          <button onclick="updateQuantity(${index}, 1)">+</button>
        </div>
      `;
      cartItemsContainer.appendChild(div);
    });

    totalPriceDisplay.textContent = `Total: Rp ${total.toLocaleString()}`;
  }

  window.updateQuantity = function (index, delta) {
    if (!cart[index]) return;
    cart[index].quantity += delta;
    if (cart[index].quantity <= 0) {
      cart.splice(index, 1);
    }
    localStorage.setItem("cart", JSON.stringify(cart));
    renderCart();
  };

  document.getElementById("checkout-button").addEventListener("click", () => {
    const nama = document.getElementById("nama").value.trim();
    const meja = document.getElementById("meja").value.trim();
    const metode = document.getElementById("metode").value;

    if (!nama || !meja) {
      alert("Harap isi nama dan nomor meja.");
      return;
    }

    if (cart.length === 0) {
      alert("Keranjang masih kosong!");
      return;
    }

    const order = {
      nama,
      meja,
      metode,
      cart,
      waktu: new Date().toLocaleString(),
    };

    console.log("Pesanan dikirim:", order);
    alert("Pesanan berhasil dikirim!");

    localStorage.removeItem("cart");
    window.location.reload();
  });

  renderCart();
});
