// fungsi menampilkan sidebar menu
feather.replace();
const menuBtn = document.getElementById("menu");
const navbarIcons = document.getElementById("navbar-icons");

menuBtn.addEventListener("click", function (e) {
	e.preventDefault();
    navbarIcons.classList.toggle("active");
});
