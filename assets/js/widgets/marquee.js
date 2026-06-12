(function ($) {
  "use strict";

  var elementorHookRegistered = false;

  function parseSettings(raw) {
    if (!raw) {
      return {
        direction: "left",
        speed: 30,
        pauseOnHover: true
      };
    }

    try {
      var parsed = JSON.parse(raw);
      return {
        direction: parsed.direction === "right" ? "right" : "left",
        speed: Math.max(1, parseInt(parsed.speed, 10) || 30),
        pauseOnHover: !!parsed.pauseOnHover
      };
    } catch (error) {
      if (window.console && console.error) {
        console.error("DailySlider Marquee: failed to parse settings", error);
      }

      return {
        direction: "left",
        speed: 30,
        pauseOnHover: true
      };
    }
  }

  function resolveWidgets($scope) {
    if (!$scope || !$scope.length) return $();

    if ($scope.hasClass("daily-slider-marquee-slider")) {
      return $scope;
    }

    var $found = $scope.find(".daily-slider-marquee-slider");
    if ($found.length) return $found;

    if ($scope.is(".elementor-widget-daily-slider-marquee")) {
      return $scope.find(".daily-slider-marquee-slider");
    }

    return $();
  }

  function DailyMarquee(element) {
    this.element = element;
    this.settings = parseSettings(element.getAttribute("data-settings"));
    this.track = element.querySelector(".daily-slider-marquee-slider__track");
    this.content = element.querySelector(".daily-slider-marquee-slider__content");

    this.clone = null;
    this.styleEl = null;
    this.resizeObserver = null;
    this.resizeTimeout = null;
    this.initTimeout = null;
    this.refreshRetryTimeout = null;
    this.refreshAttempts = 0;

    this.boundMouseEnter = this.pause.bind(this);
    this.boundMouseLeave = this.resume.bind(this);
    this.boundWindowResize = this.handleResize.bind(this);

    this.uniqueId = "daily-slider-marquee-" + Math.random().toString(36).slice(2, 11);
    this.element.setAttribute("data-marquee-id", this.uniqueId);
  }

  DailyMarquee.prototype.calculateDuration = function (contentWidth) {
    var baseSpeed = 100; // pixels per second
    var duration = contentWidth / baseSpeed;
    return Math.max(4, duration * (this.settings.speed / 30));
  };

  DailyMarquee.prototype.updateStyles = function (contentWidth) {
    var duration = this.calculateDuration(contentWidth);
    var startPosition = this.settings.direction === "right" ? "-" + contentWidth + "px" : "0";
    var endPosition = this.settings.direction === "right" ? "0" : "-" + contentWidth + "px";

    if (!this.styleEl) {
      this.styleEl = document.createElement("style");
      this.styleEl.setAttribute("data-ds-marquee-style", this.uniqueId);
      this.element.appendChild(this.styleEl);
    }

    this.styleEl.textContent =
      "[data-marquee-id=\"" + this.uniqueId + "\"] .daily-slider-marquee-slider__track {" +
      "display:flex;" +
      "animation:daily-slider-marquee-" + this.uniqueId + " " + duration + "s linear infinite;" +
      "animation-play-state:running;" +
      "}" +
      "@keyframes daily-slider-marquee-" + this.uniqueId + " {" +
      "from { transform: translateX(" + startPosition + "); }" +
      "to { transform: translateX(" + endPosition + "); }" +
      "}" +
      "[data-marquee-id=\"" + this.uniqueId + "\"] .daily-slider-marquee-slider__content {" +
      "flex-shrink:0;display:flex;align-items:center;gap:var(--content-gap, 1rem);" +
      "}";
  };

  DailyMarquee.prototype.ensureClone = function () {
    var existingClones = this.track.querySelectorAll(".daily-slider-marquee-slider__content[data-ds-marquee-clone='1']");
    existingClones.forEach(function (cloneNode) {
      cloneNode.remove();
    });

    this.clone = this.content.cloneNode(true);
    this.clone.setAttribute("aria-hidden", "true");
    this.clone.setAttribute("data-ds-marquee-clone", "1");
    this.track.appendChild(this.clone);
  };

  DailyMarquee.prototype.pause = function () {
    this.track.style.animationPlayState = "paused";
  };

  DailyMarquee.prototype.resume = function () {
    this.track.style.animationPlayState = "running";
  };

  DailyMarquee.prototype.bindHover = function () {
    this.track.removeEventListener("mouseenter", this.boundMouseEnter);
    this.track.removeEventListener("mouseleave", this.boundMouseLeave);

    if (!this.settings.pauseOnHover) {
      return;
    }

    this.track.addEventListener("mouseenter", this.boundMouseEnter);
    this.track.addEventListener("mouseleave", this.boundMouseLeave);
  };

  DailyMarquee.prototype.handleResize = function () {
    var self = this;
    window.clearTimeout(this.resizeTimeout);
    this.resizeTimeout = window.setTimeout(function () {
      self.refresh();
    }, 200);
  };

  DailyMarquee.prototype.bindResize = function () {
    if (typeof ResizeObserver !== "undefined") {
      if (this.resizeObserver) {
        this.resizeObserver.disconnect();
      }

      this.resizeObserver = new ResizeObserver(this.handleResize.bind(this));
      this.resizeObserver.observe(this.element);
      return;
    }

    window.removeEventListener("resize", this.boundWindowResize);
    window.addEventListener("resize", this.boundWindowResize);
  };

  DailyMarquee.prototype.refresh = function () {
    if (!this.track || !this.content || !this.element.isConnected) {
      return;
    }

    this.ensureClone();

    var contentWidth = this.content.scrollWidth || this.content.offsetWidth || 0;
    if (!contentWidth) {
      var self = this;
      if (this.refreshAttempts < 10) {
        this.refreshAttempts += 1;
        window.clearTimeout(this.refreshRetryTimeout);
        this.refreshRetryTimeout = window.setTimeout(function () {
          self.refresh();
        }, 120);
      } else {
        // Keep content visible even when runtime sizing cannot be computed.
        this.track.style.opacity = "1";
      }
      return;
    }

    this.refreshAttempts = 0;
    this.updateStyles(contentWidth);

    var self = this;
    window.requestAnimationFrame(function () {
      self.track.style.opacity = "1";
      self.track.style.transition = "opacity 0.3s ease";
    });
  };

  DailyMarquee.prototype.init = function () {
    if (!this.track || !this.content) {
      return;
    }

    this.track.style.opacity = "0";
    this.bindHover();
    this.bindResize();

    var self = this;
    this.initTimeout = window.setTimeout(function () {
      self.refresh();
    }, 80);
  };

  DailyMarquee.prototype.destroy = function () {
    window.clearTimeout(this.initTimeout);
    window.clearTimeout(this.resizeTimeout);
    window.clearTimeout(this.refreshRetryTimeout);

    if (this.resizeObserver) {
      this.resizeObserver.disconnect();
      this.resizeObserver = null;
    }

    window.removeEventListener("resize", this.boundWindowResize);
    if (this.track) {
      this.track.removeEventListener("mouseenter", this.boundMouseEnter);
      this.track.removeEventListener("mouseleave", this.boundMouseLeave);
      this.track.style.animationPlayState = "running";
    }

    if (this.styleEl && this.styleEl.parentNode) {
      this.styleEl.parentNode.removeChild(this.styleEl);
    }
    this.styleEl = null;

    if (this.track) {
      var clones = this.track.querySelectorAll(".daily-slider-marquee-slider__content[data-ds-marquee-clone='1']");
      clones.forEach(function (cloneNode) {
        cloneNode.remove();
      });
    }

    this.element.removeAttribute("data-marquee-id");
  };

  function initMarquee($scope) {
    var $widgets = resolveWidgets($scope);
    if (!$widgets.length) return;

    $widgets.each(function () {
      var element = this;
      var settingsSignature = element.getAttribute("data-settings") || "";
      var existingSignature = element.getAttribute("data-ds-marquee-signature") || "";
      var existingInstance = element.__dsMarqueeInstance;

      if (existingInstance && existingSignature === settingsSignature) {
        return;
      }

      if (existingInstance && typeof existingInstance.destroy === "function") {
        existingInstance.destroy();
      }

      var instance = new DailyMarquee(element);
      instance.init();

      element.__dsMarqueeInstance = instance;
      element.setAttribute("data-ds-marquee-signature", settingsSignature);
    });
  }

  function registerElementorHook() {
    if (elementorHookRegistered) return false;
    if (typeof elementorFrontend === "undefined" || !elementorFrontend.hooks) return false;

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/daily-slider-marquee.default",
      function ($scope) {
        initMarquee($scope);
      }
    );

    elementorHookRegistered = true;
    return true;
  }

  $(window).on("elementor/frontend/init", function () {
    registerElementorHook();
    initMarquee($(document));
  });

  if (registerElementorHook()) {
    // Handle late-loaded scripts in the editor where elementor/frontend/init may have already fired.
    if (typeof elementorFrontend !== "undefined" && elementorFrontend.isEditMode && elementorFrontend.isEditMode()) {
      initMarquee($(document));
    }
  }

  $(document).ready(function () {
    registerElementorHook();
    initMarquee($(document));
  });

  $(window).on("load", function () {
    initMarquee($(document));
  });
})(jQuery);
