(function($) {
  "use strict"; // Start of use strict



  // Closes responsive menu when a scroll trigger link is clicked
  $('.js-scroll-trigger').click(function() {
    $('.navbar-collapse').collapse('hide');
  });


  // Activate scrollspy to add active class to navbar items on scroll
  $('body').scrollspy({
    // offset: offsetHeight,
    target: '#mainNav',
    offset: 80
  });

  // Smooth scrolling using jQuery easing
  $('a.js-scroll-trigger[href*="#"]:not([href="#"])').click(function() {
    if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
      if (target.length) {
        $('html, body').animate({
          scrollTop: (target.offset().top - 100)
          // scrollTop: (target.offset().top + 1)
        }, 1000, "easeInOutExpo");
        return false;
      }
    }
  });


})(jQuery); // End of use strict
