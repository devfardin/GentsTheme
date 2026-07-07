var swiper = new Swiper('.mySwiper', {
        slidesPerView: 1,
        spaceBetween: 10,
        navigation: {
          nextEl: '.button-next',
          prevEl: '.button-prev',
        },
         autoplay: {
          delay: 2000,
          disableOnInteraction: false,
        },
        breakpoints: {
          368: {
            slidesPerView: 2,
            spaceBetween: 15,
          },
          576: {
            slidesPerView: 3,
            spaceBetween: 15,
          },
          1024: {
            slidesPerView: 4,
            spaceBetween: 15,
          },
        },
      });