
// Cerrar el menú móvil después de seleccionar una sección
document.addEventListener("DOMContentLoaded", function () {

    const enlaces = document.querySelectorAll(".navbar-nav .nav-link");
    const menu = document.getElementById("menu");

    enlaces.forEach(function (enlace) {

        enlace.addEventListener("click", function () {

            if (window.innerWidth < 992 && menu.classList.contains("show")) {

                const boton = document.querySelector(".navbar-toggler");
                boton.click();

            }

        });

    });

});
