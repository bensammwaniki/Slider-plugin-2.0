(function ($) {
  "use strict";

  var elementorHookRegistered = false;

  function initModals($scope) {
    var $modals = $scope ? $scope.find(".daily-modal") : $(".daily-modal");
    if ($scope && $scope.hasClass("daily-modal")) {
      $modals = $modals.add($scope);
    }

    $modals.each(function () {
      var $modal = $(this);
      if ($modal.data("dailyModalInited")) return;
      $modal.data("dailyModalInited", true);

      $modal.css("display", "");

      var hash = $modal.attr("data-trigger-hash") || "#" + $modal.attr("id");
      if (!hash || hash === "#") return;

      function openModal() {
        $modal.addClass("is-open");
        $("body").addClass("daily-modal-open");
      }

      function closeModal() {
        $modal.removeClass("is-open");
        $("body").removeClass("daily-modal-open");
        if (typeof history !== "undefined" && history.replaceState) {
          history.replaceState(null, null, " ");
        }
      }

      $modal.find(".daily-modal-close").off("click").on("click", closeModal);
      $modal.find(".daily-modal-backdrop").off("click").on("click", closeModal);

      $modal.off("keydown").on("keydown", function (e) {
        if (e.key === "Escape") closeModal();
      });

      // Bind link clicks. Using namespaced event on document to avoid duplicate handlers.
      var eventKey = "click.dailyModal_" + hash.replace(/[^a-z0-9]/gi, "_");
      $(document).off(eventKey).on(eventKey, 'a[href="' + hash + '"]', function (e) {
        e.preventDefault();
        openModal();
      });

      if (window.location.hash === hash) {
        openModal();
      }
    });
  }

  function registerElementorHook() {
    if (elementorHookRegistered) return false;
    if (typeof elementorFrontend === "undefined" || !elementorFrontend.hooks) return false;

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/daily-slider-modal.default",
      function ($scope) {
        initModals($scope);
      }
    );

    elementorHookRegistered = true;
    return true;
  }

  $(window).on("hashchange", function () {
    $(".daily-modal").each(function () {
      var $m = $(this);
      var hash = $m.attr("data-trigger-hash");
      if (hash && window.location.hash === hash) {
        $m.addClass("is-open");
        $("body").addClass("daily-modal-open");
      }
    });
  });

  $(window).on("elementor/frontend/init", function () {
    registerElementorHook();
    initModals($(document));
  });

  if (registerElementorHook()) {
    // Handle late-loaded scripts in the editor preview iframe.
    if (typeof elementorFrontend !== "undefined" && elementorFrontend.isEditMode && elementorFrontend.isEditMode()) {
      initModals($(document));
    }
  }

  $(document).ready(function () {
    registerElementorHook();
    initModals($(document));
  });

  $(window).on("load", function () {
    initModals($(document));
  });
})(jQuery);
