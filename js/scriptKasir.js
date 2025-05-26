// fungsi tampilan popup
function showPopup(message, isSuccess = false) {
  const popup = document.getElementById("popup");
  const title = document.getElementById("popup-title");
  const messageText = document.getElementById("popup-message");
  const button = document.getElementById("popup-button");
  const icon = popup.querySelector(".popup-icon");

  if (!isSuccess) {
    title.textContent = "Login Gagal!";
    icon.innerHTML = `<img src="logo/Failed.png" alt="Error" style="width:120px;height:120px;">`;
    popup.className = "popup error show";
  }

  messageText.textContent = message;

  button.textContent = "Tutup";
  button.onclick = () => {
    popup.classList.remove("show");
  };
}
// fungsi untuk menampilkan popup saat halaman dimuat
document.addEventListener("DOMContentLoaded", function () {
  const urlParams = new URLSearchParams(window.location.search);
  const errorMessage = urlParams.get("error");

  if (errorMessage) {
    showPopup(errorMessage, false);
  }
});

// fungsi untuk sidebar
const navbar = document.querySelector(".navbar-extra");
document.querySelector(".navbar-menu").onclick = () => {
  navbar.classList.toggle("hidden"); // tanpa titik
};
