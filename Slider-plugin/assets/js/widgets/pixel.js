(function ($) {
  "use strict";

  var ELEMENT_READY_HOOK = "frontend/element_ready/daily-slider-pixel.default";
  var hookBound = false;
  var responsiveHooksBound = false;
  var resizeTimer = null;

  function getSwiperFactory() {
    if (typeof elementorFrontend !== "undefined" && elementorFrontend.utils && elementorFrontend.utils.swiper) {
      return elementorFrontend.utils.swiper;
    }

    if (window.Swiper) {
      return window.Swiper;
    }

    return null;
  }

  function waitForSwiperFactory(maxAttempts, delayMs) {
    return new Promise(function (resolve, reject) {
      var attempts = 0;

      (function poll() {
        var factory = getSwiperFactory();
        if (factory) {
          resolve(factory);
          return;
        }

        attempts += 1;
        if (attempts >= maxAttempts) {
          reject(new Error("Swiper not found"));
          return;
        }

        window.setTimeout(poll, delayMs);
      })();
    });
  }

  function waitForUsableWidth($container, maxAttempts, delayMs) {
    return new Promise(function (resolve) {
      var attempts = 0;

      (function poll() {
        var width = $container[0] ? Math.round($container[0].getBoundingClientRect().width) : 0;
        if (width > 0) {
          resolve(width);
          return;
        }

        attempts += 1;
        if (attempts >= maxAttempts) {
          resolve(width);
          return;
        }

        window.setTimeout(poll, delayMs);
      })();
    });
  }

  async function createSwiper(containerEl, settings) {
    var factory = await waitForSwiperFactory(40, 100);
    var elementorSwiperHelper = typeof elementorFrontend !== "undefined" && elementorFrontend.utils
      ? elementorFrontend.utils.swiper
      : null;

    if (factory === elementorSwiperHelper) {
      try {
        return await factory(containerEl, settings);
      } catch (error) {
        if (!error || !error.message || error.message.indexOf("cannot be invoked without 'new'") === -1) {
          throw error;
        }
      }
    }

    return new factory(containerEl, settings);
  }

  function parseRawSettings(rawSettings) {
    if (!rawSettings) {
      return null;
    }

    if (typeof rawSettings === "object") {
      return rawSettings;
    }

    if (typeof rawSettings === "string") {
      try {
        return JSON.parse(rawSettings);
      } catch (error) {
        return null;
      }
    }

    return null;
  }

  function normalizeDeviceMode(mode) {
    if (typeof mode !== "string") {
      return "desktop";
    }

    if (mode.indexOf("mobile") === 0) {
      return "mobile";
    }

    if (mode.indexOf("tablet") === 0) {
      return "tablet";
    }

    return "desktop";
  }

  function getCurrentDeviceMode() {
    if (typeof elementorFrontend !== "undefined" && typeof elementorFrontend.getCurrentDeviceMode === "function") {
      return normalizeDeviceMode(elementorFrontend.getCurrentDeviceMode());
    }

    var width = window.innerWidth || document.documentElement.clientWidth || 0;
    var mobileMax = 767;
    var tabletMax = 1024;

    if (typeof elementorFrontend !== "undefined" && elementorFrontend.config && elementorFrontend.config.breakpoints) {
      var breakpoints = elementorFrontend.config.breakpoints;
      if (breakpoints.md) {
        mobileMax = parseInt(breakpoints.md, 10) - 1;
      }
      if (breakpoints.lg) {
        tabletMax = parseInt(breakpoints.lg, 10) - 1;
      }
    }

    if (width <= mobileMax) {
      return "mobile";
    }

    if (width <= tabletMax) {
      return "tablet";
    }

    return "desktop";
  }

  function getTabletBreakpointKey(breakpoints) {
    if (!breakpoints || typeof breakpoints !== "object") {
      return null;
    }

    var keys = Object.keys(breakpoints)
      .map(function (key) {
        return parseInt(key, 10);
      })
      .filter(function (key) {
        return !isNaN(key) && key > 0;
      })
      .sort(function (a, b) {
        return a - b;
      });

    if (!keys.length) {
      return null;
    }

    return String(keys[0]);
  }

  function applyResponsiveDeviceOverrides(settings, deviceMode) {
    if (!settings || typeof settings !== "object") {
      return settings;
    }

    if (!settings.breakpoints || typeof settings.breakpoints !== "object") {
      return settings;
    }

    var device = normalizeDeviceMode(deviceMode);
    var mobileParams = settings.breakpoints["0"] || settings.breakpoints[0] || null;
    var tabletKey = getTabletBreakpointKey(settings.breakpoints);
    var tabletParams = tabletKey ? settings.breakpoints[tabletKey] : null;

    if (device === "mobile" && mobileParams && typeof mobileParams === "object") {
      settings = $.extend(true, {}, settings, mobileParams);
    } else if (device === "tablet" && tabletParams && typeof tabletParams === "object") {
      settings = $.extend(true, {}, settings, tabletParams);
    }

    // Swiper breakpoints don't support changing all params (e.g. effect/loop) reliably.
    // We resolve device overrides before init and remove breakpoints to prevent conflicts.
    delete settings.breakpoints;

    return settings;
  }

  function clearGrabbingState($container) {
    if (!$container || !$container.length) {
      return;
    }

    $container.removeClass("swiper-grabbing");
    if ($container[0] && $container[0].style) {
      $container[0].style.cursor = "";
    }
  }

  function bindCursorReset($container) {
    $container.off(".dsPixelCursor");

    var release = function () {
      clearGrabbingState($container);
    };

    $container.on("mouseup.dsPixelCursor mouseleave.dsPixelCursor touchend.dsPixelCursor touchcancel.dsPixelCursor pointerup.dsPixelCursor pointercancel.dsPixelCursor", release);

    $(window)
      .off("mouseup.dsPixelCursor pointerup.dsPixelCursor blur.dsPixelCursor")
      .on("mouseup.dsPixelCursor pointerup.dsPixelCursor blur.dsPixelCursor", release);

    $(document)
      .off("visibilitychange.dsPixelCursor")
      .on("visibilitychange.dsPixelCursor", function () {
        if (document.hidden) {
          release();
        }
      });
  }

  function destroySlider($container) {
    $container.off(".dsPixelHover .dsPixelCursor");

    var swiper = $container.data("dsPixelSwiper") || ($container[0] && $container[0].swiper);
    if (swiper && typeof swiper.destroy === "function") {
      swiper.destroy(true, true);
    }

    clearGrabbingState($container);

    $container.removeData("dsPixelSwiper");
    $container.removeData("dsPixelInited");
    $container.removeData("dsPixelSettingsSignature");
  }

  function buildSwiperSettings($root, rawSettings, deviceMode) {
    var parsedRawSettings = parseRawSettings(rawSettings);
    if (!parsedRawSettings) {
      return null;
    }

    var settings = $.extend(true, {}, parsedRawSettings);
    settings = applyResponsiveDeviceOverrides(settings, deviceMode);

    if (settings.pagination && typeof settings.pagination === "object" && settings.pagination.el) {
      var $pagination = $root.find(".swiper-pagination").first();
      if ($pagination.length) {
        settings.pagination.el = $pagination[0];
      }
      settings.pagination.clickable = !!settings.pagination.clickable;
    }

    if (settings.navigation && typeof settings.navigation === "object") {
      var $next = $root.find(".daily-button-next").first();
      var $prev = $root.find(".daily-button-prev").first();

      if ($next.length) {
        settings.navigation.nextEl = $next[0];
      }
      if ($prev.length) {
        settings.navigation.prevEl = $prev[0];
      }
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

  function applyTransitionTiming($container, settings) {
    var timingFunction = settings && typeof settings.transitionTimingFunction === "string"
      ? settings.transitionTimingFunction
      : "ease";

    $container.find(".swiper-wrapper").css({
      "transition-timing-function": timingFunction,
      "-webkit-transition-timing-function": timingFunction
    });
  }

  function syncSlideVideos($container, swiper) {
    var $videos = $container.find(".swiper-slide video.daily-video");
    if (!$videos.length) {
      return;
    }

    $videos.each(function () {
      try {
        this.pause();
        this.currentTime = 0;
      } catch (error) {
        // no-op
      }
    });

    var activeSlide = swiper && swiper.slides && swiper.slides.length
      ? swiper.slides[swiper.activeIndex]
      : $container.find(".swiper-slide-active")[0];

    if (!activeSlide) {
      return;
    }

    var activeVideo = $(activeSlide).find("video.daily-video").get(0);
    if (!activeVideo) {
      return;
    }

    activeVideo.muted = true;
    activeVideo.setAttribute("muted", "muted");

    var playPromise = activeVideo.play();
    if (playPromise && typeof playPromise.catch === "function") {
      playPromise.catch(function () {
        // autoplay can be blocked in some contexts; keep silent.
      });
    }
  }

  function bindPauseOnHover($container, swiper, settings) {
    $container.off(".dsPixelHover");

    if (!(settings && settings.pauseOnHover && swiper && swiper.autoplay)) {
      return;
    }

    $container.on("mouseenter.dsPixelHover", function () {
      if (typeof swiper.autoplay.pause === "function") {
        swiper.autoplay.pause();
        return;
      }
      swiper.autoplay.stop();
    });

    $container.on("mouseleave.dsPixelHover", function () {
      if (typeof swiper.autoplay.resume === "function") {
        swiper.autoplay.resume();
        return;
      }
      swiper.autoplay.start();
    });
  }

  function resolveSliderRoots($scope) {
    if (!$scope || !$scope.length) {
      return $();
    }

    if ($scope.hasClass("ds-pixel-slider")) {
      return $scope;
    }

    return $scope.find(".ds-pixel-slider");
  }

  function initializePixelSlider($scope) {
    var $roots = resolveSliderRoots($scope);
    if (!$roots.length) {
      return;
    }

    $roots.each(function () {
      var $root = $(this);
      var $container = $root.find(".swiper").first();
      if (!$container.length) {
        return;
      }

      (async function () {
        var deviceMode = getCurrentDeviceMode();
        var rawSettings = $root.data("settings");
        var parsedRawSettings = parseRawSettings(rawSettings);
        var settingsSignature = parsedRawSettings ? (JSON.stringify(parsedRawSettings) + "::" + deviceMode) : "";
        var alreadyInitialized =
          $container.data("dsPixelSettingsSignature") === settingsSignature &&
          ($container.hasClass("swiper-initialized") || $container.data("dsPixelInited"));

        if (alreadyInitialized) {
          applyTransitionTiming($container, parsedRawSettings || {});
          bindCursorReset($container);
          syncSlideVideos($container, $container.data("dsPixelSwiper"));
          return;
        }

        if ($container.hasClass("swiper-initialized") || $container.data("dsPixelInited")) {
          destroySlider($container);
        }

        var width = await waitForUsableWidth($container, 30, 100);
        if (!width) {
          $container.data("dsPixelInited", false);
          return;
        }

        var settings = buildSwiperSettings($root, parsedRawSettings, deviceMode);
        if (!settings) {
          return;
        }

        $container.data("dsPixelInited", true);
        $container.data("dsPixelSettingsSignature", settingsSignature);

        try {
          var swiper = await createSwiper($container[0], settings);
          $container.data("dsPixelSwiper", swiper);

          applyTransitionTiming($container, settings);
          bindPauseOnHover($container, swiper, settings);
          bindCursorReset($container);
          syncSlideVideos($container, swiper);

          if (swiper && typeof swiper.on === "function") {
            swiper.on("setTransition", function () {
              applyTransitionTiming($container, settings);
            });

            swiper.on("touchEnd", function () {
              clearGrabbingState($container);
            });

            swiper.on("slideChangeTransitionEnd", function () {
              syncSlideVideos($container, swiper);
            });

            swiper.on("init", function () {
              syncSlideVideos($container, swiper);
            });
          }

          if (swiper && typeof swiper.update === "function") {
            swiper.update();
            window.requestAnimationFrame(function () {
              swiper.update();
            });
            window.setTimeout(function () {
              swiper.update();
            }, 120);
          }
        } catch (error) {
          $container.data("dsPixelInited", false);
          $container.removeData("dsPixelSettingsSignature");

          if (window.console && typeof console.error === "function") {
            console.error("DailySlider Pixel: Swiper init failed", error);
          }
        }
      })();
    });
  }

  function registerElementorHook() {
    if (hookBound) {
      return;
    }

    if (typeof elementorFrontend === "undefined" || !elementorFrontend.hooks) {
      return;
    }

    elementorFrontend.hooks.addAction(ELEMENT_READY_HOOK, function ($scope) {
      initializePixelSlider($scope);
    });

    hookBound = true;
  }

  function registerResponsiveReinitHooks() {
    if (responsiveHooksBound) {
      return;
    }

    $(window).on("resize.dsPixelReinit orientationchange.dsPixelReinit", function () {
      window.clearTimeout(resizeTimer);
      resizeTimer = window.setTimeout(function () {
        initializePixelSlider($(document));
      }, 140);
    });

    if (typeof elementor !== "undefined" && elementor.channels && elementor.channels.deviceMode && typeof elementor.channels.deviceMode.on === "function") {
      elementor.channels.deviceMode.on("change", function () {
        initializePixelSlider($(document));
      });
    }

    responsiveHooksBound = true;
  }

  function bootstrap() {
    registerElementorHook();
    registerResponsiveReinitHooks();
    initializePixelSlider($(document));
  }

  $(window).on("elementor/frontend/init", bootstrap);
  $(document).ready(bootstrap);
  $(window).on("load", function () {
    initializePixelSlider($(document));
  });
})(jQuery);
