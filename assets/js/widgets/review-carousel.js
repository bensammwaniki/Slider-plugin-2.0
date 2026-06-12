(function ($) {
  "use strict";

  var elementorHookRegistered = false;

  function getSwiperFactory() {
    if (typeof elementorFrontend !== "undefined" && elementorFrontend.utils && elementorFrontend.utils.swiper) {
      return elementorFrontend.utils.swiper;
    }

    if (window.Swiper) {
      return window.Swiper;
    }

    return null;
  }

  async function waitForSwiper(maxAttempts, delay) {
    for (var attempt = 0; attempt < maxAttempts; attempt++) {
      var swiperFactory = getSwiperFactory();
      if (swiperFactory) {
        return swiperFactory;
      }

      await new Promise(function (resolve) {
        window.setTimeout(resolve, delay);
      });
    }

    throw new Error("Swiper not found");
  }

  async function createSwiperInstance(containerEl, settings) {
    var SwiperFactory = await waitForSwiper(40, 100);
    var helperFactory = typeof elementorFrontend !== "undefined" && elementorFrontend.utils
      ? elementorFrontend.utils.swiper
      : null;

    if (SwiperFactory === helperFactory) {
      try {
        return await SwiperFactory(containerEl, settings);
      } catch (error) {
        if (!error || !error.message || error.message.indexOf("cannot be invoked without 'new'") === -1) {
          throw error;
        }
      }
    }

    return new SwiperFactory(containerEl, settings);
  }

  function destroyExistingSwiper($container) {
    $container.off(".dsReview");

    var existingSwiper = $container.data("dsReviewSwiper") || ($container[0] && $container[0].swiper);
    if (existingSwiper && typeof existingSwiper.destroy === "function") {
      existingSwiper.destroy(true, true);
    }

    $container.removeData("dsReviewSwiper");
    $container.removeData("dsReviewInited");
    $container.removeData("dsReviewSettingsSignature");
  }

  function buildSwiperSettings($carousel, rawSettings) {
    if (!rawSettings) return null;

    var settings = $.extend(true, {}, rawSettings);
    var $prevButton = $carousel.find(".daily-button-prev").first();
    var $nextButton = $carousel.find(".daily-button-next").first();
    var $pagination = $carousel.find(".daily-pagination").first();

    if (settings.navigation && typeof settings.navigation === "object") {
      if ($nextButton.length) settings.navigation.nextEl = $nextButton[0];
      if ($prevButton.length) settings.navigation.prevEl = $prevButton[0];
    }

    if (settings.pagination && typeof settings.pagination === "object") {
      if ($pagination.length) settings.pagination.el = $pagination[0];
      settings.pagination.clickable = !!settings.pagination.clickable;
    }

    settings.observer = true;
    settings.observeParents = true;
    settings.watchOverflow = true;
    settings.updateOnWindowResize = true;

    if (settings.autoplay && typeof settings.autoplay === "object") {
      settings.autoplay.disableOnInteraction = false;
      settings.autoplay.pauseOnMouseEnter = !!settings.pauseOnHover;
    }

    return settings;
  }

  function resolveCarousels($scope) {
    if (!$scope || !$scope.length) return $();

    if ($scope.hasClass("ds-review-carousel")) {
      return $scope;
    }

    var $found = $scope.find(".ds-review-carousel");
    if ($found.length) return $found;

    if ($scope.is(".elementor-widget-daily-slider-review-carousel")) {
      return $scope.find(".ds-review-carousel");
    }

    return $();
  }

  function initReviewCarousel($scope) {
    var $carousels = resolveCarousels($scope);
    if (!$carousels.length) return;

    $carousels.each(function () {
      var $carousel = $(this);
      var $container = $carousel.find(".swiper").first();
      if (!$container.length) return;

      var rawSettings = $carousel.data("settings");
      if (!rawSettings) return;

      var settingsSignature = JSON.stringify(rawSettings);
      var alreadyInitialized =
        $container.data("dsReviewSettingsSignature") === settingsSignature &&
        ($container.hasClass("swiper-initialized") || $container.data("dsReviewInited"));

      if (alreadyInitialized) {
        return;
      }

      if ($container.hasClass("swiper-initialized") || $container.data("dsReviewInited")) {
        destroyExistingSwiper($container);
      }

      var settings = buildSwiperSettings($carousel, rawSettings);
      if (!settings) return;

      $container.data("dsReviewInited", true);
      $container.data("dsReviewSettingsSignature", settingsSignature);

      (async function () {
        try {
          var swiper = await createSwiperInstance($container[0], settings);
          $container.data("dsReviewSwiper", swiper);

          $carousel.off(".dsReviewHover");
          if (settings.pauseOnHover && swiper && swiper.autoplay) {
            $carousel.on("mouseenter.dsReviewHover", function () {
              if (!swiper || !swiper.autoplay) return;

              if (typeof swiper.autoplay.pause === "function") {
                swiper.autoplay.pause();
                return;
              }

              if (typeof swiper.autoplay.stop === "function") {
                swiper.autoplay.stop();
              }
            });

            $carousel.on("mouseleave.dsReviewHover", function () {
              if (!swiper || !swiper.autoplay) return;

              if (typeof swiper.autoplay.resume === "function") {
                swiper.autoplay.resume();
                return;
              }

              if (typeof swiper.autoplay.start === "function") {
                swiper.autoplay.start();
              }
            });
          }

          if (swiper && typeof swiper.update === "function") {
            swiper.update();
            window.requestAnimationFrame(function () {
              swiper.update();
            });
          }
        } catch (error) {
          $container.data("dsReviewInited", false);
          $container.removeData("dsReviewSettingsSignature");
          if (window.console && console.error) {
            console.error("DailySlider Review Carousel: Swiper init failed", error);
          }
        }
      })();
    });
  }

  function registerElementorHook() {
    if (elementorHookRegistered) return;
    if (typeof elementorFrontend === "undefined" || !elementorFrontend.hooks) return;

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/daily-slider-review-carousel.default",
      function ($scope) {
        initReviewCarousel($scope);
      }
    );

    elementorHookRegistered = true;
  }

  $(window).on("elementor/frontend/init", function () {
    registerElementorHook();
    initReviewCarousel($(document));
  });

  $(document).ready(function () {
    registerElementorHook();
    initReviewCarousel($(document));
  });

  $(window).on("load", function () {
    initReviewCarousel($(document));
  });
})(jQuery);
