

// fungsi untuk sidebar
feather.replace();
const navbar = document.querySelector(".navbar-extra");
document.querySelector(".navbar-menu").onclick = () => {
  navbar.classList.toggle("hidden"); // tanpa titik
};
