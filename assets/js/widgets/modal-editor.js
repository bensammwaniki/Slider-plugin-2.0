(function() {
    var registerNestedModal = function() {
        if (window.elementor && elementor.modules && elementor.modules.nestedElements) {
            elementor.modules.nestedElements.add('daily-slider-modal', {
                isWidgetSupportNesting: function() {
                    return true;
                }
            });
            return true;
        }
        return false;
    };

    if (!registerNestedModal()) {
        jQuery(window).on('elementor/init', registerNestedModal);
    }
})();
