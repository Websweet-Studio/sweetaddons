(function ($) {
  "use strict";

  /**
   * All of the code for your public-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */

  $(function () {
    // WhatsApp Widget Ripple Hover Animation using GSAP
    var $waWidget = $("#sweetaddons-whatsapp-widget");
    var $waLink = $waWidget.find(".sweetaddons-wa-link");
    var $rippleContainer = $waWidget.find(".sweetaddons-wa-ripple-container");

    if ($waWidget.length && window.gsap) {
      $waLink.on("mouseenter", function (e) {
        // Create ripple element
        var ripple = $('<div class="sweetaddons-wa-ripple"></div>');
        $rippleContainer.append(ripple);

        // Get relative cursor position
        var relX = e.pageX - $(this).offset().left;
        var relY = e.pageY - $(this).offset().top;

        // Calculate maximum dimension for the circle to cover the whole element
        var width = $(this).outerWidth();
        var height = $(this).outerHeight();
        var maxDim = Math.max(width, height) * 2.5;

        // Set initial position
        gsap.set(ripple, {
          width: maxDim,
          height: maxDim,
          left: relX - maxDim / 2,
          top: relY - maxDim / 2,
          scale: 0,
          opacity: 1,
        });

        // Animate expansion
        gsap.to(ripple, {
          scale: 1,
          duration: 0.6,
          ease: "power2.out",
        });

        // Also scale the link slightly
        gsap.to(this, {
          scale: 1.05,
          duration: 0.3,
        });
      });

      $waLink.on("mouseleave", function () {
        var $ripples = $rippleContainer.find(".sweetaddons-wa-ripple");

        // Fade out all ripples and remove them
        gsap.to($ripples, {
          opacity: 0,
          duration: 0.4,
          onComplete: function () {
            $(this.targets()).remove();
          },
        });

        // Reset scale
        gsap.to(this, {
          scale: 1,
          duration: 0.3,
        });
      });
    }
  });
})(jQuery);
