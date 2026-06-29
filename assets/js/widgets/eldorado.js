(function ($) {
  "use strict";

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
    $container.off(".dsEldorado");

    var existingSwiper = $container.data("dsEldoradoSwiper") || ($container[0] && $container[0].swiper);
    if (existingSwiper && typeof existingSwiper.destroy === "function") {
      existingSwiper.destroy(true, true);
    }

    var resizeObserver = $container.data("dsEldoradoResizeObserver");
    if (resizeObserver && typeof resizeObserver.disconnect === "function") {
      resizeObserver.disconnect();
    }

    $container.removeData("dsEldoradoSwiper");
    $container.removeData("dsEldoradoInited");
    $container.removeData("dsEldoradoSettingsSignature");
    $container.removeData("dsEldoradoResizeObserver");
  }

  function getLayoutSignature($container, settings) {
    var width = $container[0] ? Math.round($container[0].getBoundingClientRect().width) : 0;
    return JSON.stringify(settings) + "::" + width;
  }

  function getUsableWidth($container) {
    return $container[0] ? Math.round($container[0].getBoundingClientRect().width) : 0;
  }

  async function waitForUsableWidth($container, maxAttempts, delay) {
    for (var attempt = 0; attempt < maxAttempts; attempt++) {
      var width = getUsableWidth($container);
      if (width > 0) {
        return width;
      }

      await new Promise(function (resolve) {
        window.setTimeout(resolve, delay);
      });
    }

    return getUsableWidth($container);
  }

  function refreshSwiper(swiper) {
    if (!swiper) return;

    if (typeof swiper.update === "function") {
      swiper.update();
    }
  }

  function resolveResponsiveParams(settings, width) {
    var resolved = {
      slidesPerView: typeof settings.slidesPerView !== "undefined" ? settings.slidesPerView : 1,
      spaceBetween: typeof settings.spaceBetween !== "undefined" ? settings.spaceBetween : 0
    };

    var breakpoints = settings.breakpoints || {};
    Object.keys(breakpoints)
      .map(function (key) {
        return parseInt(key, 10);
      })
      .filter(function (value) {
        return !isNaN(value);
      })
      .sort(function (a, b) {
        return a - b;
      })
      .forEach(function (breakpoint) {
        if (width >= breakpoint) {
          var breakpointSettings = breakpoints[breakpoint];
          if (typeof breakpointSettings.slidesPerView !== "undefined") {
            resolved.slidesPerView = breakpointSettings.slidesPerView;
          }
          if (typeof breakpointSettings.spaceBetween !== "undefined") {
            resolved.spaceBetween = breakpointSettings.spaceBetween;
          }
        }
      });

    return resolved;
  }

  function applyResponsiveParamsToSwiper(swiper, rawSettings, width) {
    if (!swiper || !rawSettings) return;

    var resolved = resolveResponsiveParams(rawSettings, width);
    var nextSpaceBetween = parseInt(resolved.spaceBetween, 10) || 0;

    if (swiper.params.spaceBetween !== nextSpaceBetween) {
      swiper.params.spaceBetween = nextSpaceBetween;
      if (swiper.originalParams) swiper.originalParams.spaceBetween = nextSpaceBetween;
    }

    refreshSwiper(swiper);
  }

  function applySlideLayout($container, rawSettings, width) {
    if (!rawSettings || !$container[0]) return;

    var resolved = resolveResponsiveParams(rawSettings, width);
    var columns = Math.max(1, parseInt(resolved.slidesPerView, 10) || 1);
    var gap = Math.max(0, parseInt(resolved.spaceBetween, 10) || 0);

    $container[0].style.setProperty("--ds-eldorado-columns", String(columns));
    $container[0].style.setProperty("--ds-eldorado-gap", gap + "px");
  }

  function attachResizeObserver($carousel, $container, swiper) {
    if (typeof ResizeObserver === "undefined" || !$container[0]) {
      return;
    }

      var previousWidth = Math.round($container[0].getBoundingClientRect().width);
    var observer = new ResizeObserver(function (entries) {
      if (!entries.length) return;

      var nextWidth = Math.round(entries[0].contentRect.width);
      if (nextWidth === previousWidth) return;

      previousWidth = nextWidth;
      applySlideLayout($container, $carousel.data("settings"), nextWidth);
      applyResponsiveParamsToSwiper(swiper, $carousel.data("settings"), nextWidth);
    });

    observer.observe($container[0]);
    $container.data("dsEldoradoResizeObserver", observer);
  }

  function buildSwiperSettings($carousel, rawSettings, width) {
    if (!rawSettings) {
      return null;
    }

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
    }

    var resolved = resolveResponsiveParams(settings, width);
    settings.slidesPerView = "auto";
    settings.spaceBetween = parseInt(resolved.spaceBetween, 10) || 0;
    delete settings.breakpoints;
    settings.observer = true;
    settings.observeParents = true;
    settings.watchOverflow = true;
    settings.updateOnWindowResize = true;

    if (settings.autoplay && typeof settings.autoplay === "object") {
      settings.autoplay.disableOnInteraction = false;
      settings.autoplay.pauseOnMouseEnter = !!settings.pauseOnHover;
    }

    if (settings.smoothScroll) {
      settings.loop = true;
      settings.grabCursor = true;
      settings.allowTouchMove = true;
      settings.speed = Math.max(1000, (parseInt(settings.smoothScrollSpeed, 10) || 30) * 1000);
      settings.autoplay = {
        delay: 1,
        disableOnInteraction: false,
        pauseOnMouseEnter: !!settings.pauseOnHover,
        reverseDirection: settings.smoothScrollDirection === "right"
      };
    }

    return settings;
  }

  function initEldorado($scope) {
    var $carousels = $scope.hasClass && $scope.hasClass("ds-eldorado")
      ? $scope
      : $scope.find(".ds-eldorado");
    if (!$carousels.length) return;

    var isEditMode = typeof elementorFrontend !== "undefined"
      && elementorFrontend.isEditMode
      && elementorFrontend.isEditMode();

    $carousels.each(function () {
      var $carousel = $(this);
      var $container = $carousel.find(".swiper").first();
      if (!$container.length) return;

      if (isEditMode) {
        $carousel.addClass("ds-eldorado-editor-mode");
      }

      (async function () {
        var width = await waitForUsableWidth($container, 30, 100);
        if (!width) {
          $container.data("dsEldoradoInited", false);
          return;
        }

        var settings = buildSwiperSettings($carousel, $carousel.data("settings"), width);
        if (!settings) {
          $container.data("dsEldoradoInited", false);
          return;
        }

        var settingsSignature = getLayoutSignature($container, settings);
        if ($container.data("dsEldoradoSettingsSignature") === settingsSignature && ($container.hasClass("swiper-initialized") || $container.data("dsEldoradoInited"))) {
          return;
        }

        if ($container.hasClass("swiper-initialized") || $container.data("dsEldoradoInited")) {
          destroyExistingSwiper($container);
        }

        $container.data("dsEldoradoInited", true);
        $container.data("dsEldoradoSettingsSignature", settingsSignature);

        try {
          applySlideLayout($container, $carousel.data("settings"), width);
          var swiper = await createSwiperInstance($container[0], settings);
          $container.data("dsEldoradoSwiper", swiper);
          attachResizeObserver($carousel, $container, swiper);
          $container.off(".dsEldorado");

          refreshSwiper(swiper);
          window.requestAnimationFrame(function () {
            refreshSwiper(swiper);
          });
          window.setTimeout(function () {
            refreshSwiper(swiper);
          }, 100);
          window.setTimeout(function () {
            refreshSwiper(swiper);
          }, 300);

          if (settings.pauseOnHover && swiper && swiper.autoplay) {
            $carousel.off(".dsEldoradoHover");

            $carousel.on("mouseenter.dsEldoradoHover", function () {
              if (!swiper || !swiper.autoplay) return;

              if (settings.smoothScroll) {
                if (typeof swiper.autoplay.stop === "function") {
                  swiper.autoplay.stop();
                }
                if (typeof swiper.getTranslate === "function" && typeof swiper.setTranslate === "function") {
                  swiper.setTranslate(swiper.getTranslate());
                  if (typeof swiper.setTransition === "function") {
                    swiper.setTransition(0);
                  }
                }
                swiper.animating = false;
              } else {
                if (typeof swiper.autoplay.pause === "function") {
                  swiper.autoplay.pause();
                  return;
                }
                if (typeof swiper.autoplay.stop === "function") {
                  swiper.autoplay.stop();
                }
              }
            });
            $carousel.on("mouseleave.dsEldoradoHover", function () {
              if (!swiper || !swiper.autoplay) return;

              if (settings.smoothScroll) {
                swiper.animating = false;
                if (typeof swiper.setTransition === "function") {
                  swiper.setTransition(swiper.params.speed);
                }
                if (swiper.params.autoplay && swiper.params.autoplay.reverseDirection) {
                  if (typeof swiper.slidePrev === "function") {
                    swiper.slidePrev(swiper.params.speed, true, true);
                  }
                } else {
                  if (typeof swiper.slideNext === "function") {
                    swiper.slideNext(swiper.params.speed, true, true);
                  }
                }
                if (typeof swiper.autoplay.start === "function") {
                  swiper.autoplay.start();
                }
              } else {
                if (typeof swiper.autoplay.resume === "function") {
                  swiper.autoplay.resume();
                  return;
                }
                if (typeof swiper.autoplay.start === "function") {
                  swiper.autoplay.start();
                }
              }
            });
          }
        } catch (error) {
          $container.data("dsEldoradoInited", false);
          $container.removeData("dsEldoradoSettingsSignature");
          if (window.console && console.error) {
            console.error("DailySlider Eldorado: Swiper init failed", error);
          }
        }
      })();
    });
  }

  function bindElementorHook() {
    if (typeof elementorFrontend !== "undefined" && elementorFrontend.hooks) {
      elementorFrontend.hooks.addAction(
        "frontend/element_ready/daily-slider-eldorado.default",
        function ($scope) {
          initEldorado($scope);
        }
      );
      return true;
    }
    return false;
  }

  $(window).on("elementor/frontend/init", function () {
    bindElementorHook();

    if (!(typeof elementorFrontend !== "undefined" && elementorFrontend.isEditMode && elementorFrontend.isEditMode())) {
      initEldorado($(document));
    }
  });

  if (bindElementorHook()) {
    // Handle late-loaded scripts in the editor where elementor/frontend/init may have already fired.
    if (typeof elementorFrontend !== "undefined" && elementorFrontend.isEditMode && elementorFrontend.isEditMode()) {
      initEldorado($(document));
    }
  }

  $(document).ready(function () {
    if (!(typeof elementorFrontend !== "undefined" && elementorFrontend.isEditMode && elementorFrontend.isEditMode())) {
      initEldorado($(document));
    }
  });

  $(window).on("load", function () {
    if (!(typeof elementorFrontend !== "undefined" && elementorFrontend.isEditMode && elementorFrontend.isEditMode())) {
      initEldorado($(document));
    }
  });
})(jQuery);
