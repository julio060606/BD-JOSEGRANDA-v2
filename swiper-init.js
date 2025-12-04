document.addEventListener("DOMContentLoaded", function () {
  new Swiper(".brandSwiper", {
    slidesPerView: 3,      // Cantidad de logos visibles
    spaceBetween: 30,      // Espacio entre logos
    loop: true,            // Para que sea infinito
    autoplay: {
      delay: 2000,         // Tiempo en ms (2 segundos)
      disableOnInteraction: false, // Que no se detenga si alguien toca
    },
    breakpoints: {
      320: { slidesPerView: 2, spaceBetween: 20 },
      768: { slidesPerView: 3, spaceBetween: 30 },
      1024: { slidesPerView: 5, spaceBetween: 40 },
    },
  });
});
