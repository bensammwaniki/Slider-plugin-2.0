<?php
namespace DailySlider\Widgets;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( '\Elementor\Widget_Nested_Base' ) ) {
    return;
}

class Modal_Widget extends \Elementor\Widget_Nested_Base {

    public function get_name() {
        return 'daily-slider-modal';
    }

    public function get_title() {
        return __( 'Modal Box', 'daily-slider' );
    }

    public function get_icon() {
        return 'eicon-select';
    }

    public function get_categories() {
        return [ 'DailySlider-category' ];
    }

    public function get_script_depends(): array {
        return [ 'DailySlider-modal-scripts' ];
    }

    public function get_style_depends(): array {
        return [ 'DailySlider-common-styles', 'DailySlider-modal-styles' ];
    }

    protected function get_default_children_elements() {
        return [
            [
                'elType' => 'container',
                'settings' => [
                    '_title' => __( 'Modal Body', 'daily-slider' ),
                ],
            ],
        ];
    }

    protected function get_default_children_placeholder_selector() {
        return '.daily-modal-nested-container';
    }

    protected function register_controls() {
        $this->start_controls_section( 'content_section', [
            'label' => __( 'Modal Trigger Settings', 'daily-slider' ),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control( 'trigger_anchor', [
            'label' => __( 'Trigger Anchor (no #)', 'daily-slider' ),
            'type' => Controls_Manager::TEXT,
            'description' => __( 'Use this in any link URL (e.g. #extra-curricular) to open this modal.', 'daily-slider' ),
            'default' => '',
        ]);

        $this->add_control( 'preview_modal', [
            'label'        => __( 'Preview Modal in Editor', 'daily-slider' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Show', 'daily-slider' ),
            'label_off'    => __( 'Hide', 'daily-slider' ),
            'return_value' => 'yes',
            'default'      => '',
            'description'  => __( 'Switch on to show and style the modal inside the Elementor editor.', 'daily-slider' ),
        ]);

        $this->end_controls_section();

        /* Style: Modal Box */
        $this->start_controls_section( 'modal_style_section', [
            'label' => __( 'Modal Window', 'daily-slider' ),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control( 'modal_max_width', [
            'label' => __( 'Max Width', 'daily-slider' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%', 'vw' ],
            'range' => [
                'px' => [ 'min' => 300, 'max' => 1600, 'step' => 10 ],
                '%'  => [ 'min' => 10, 'max' => 100 ],
            ],
            'selectors' => [
                '{{WRAPPER}} .daily-modal-content' => 'max-width: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control( 'modal_padding', [
            'label' => __( 'Padding', 'daily-slider' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em', 'rem' ],
            'selectors' => [
                '{{WRAPPER}} .daily-modal-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control( 'modal_bg', [
            'label' => __( 'Modal Background', 'daily-slider' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .daily-modal-content' => 'background-color: {{VALUE}};' ],
        ]);

        $this->add_responsive_control( 'modal_border_radius', [
            'label' => __( 'Border Radius', 'daily-slider' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'selectors' => [ '{{WRAPPER}} .daily-modal-content' => 'border-radius: {{SIZE}}{{UNIT}};' ],
        ]);

        $this->end_controls_section();

        /* Style: Overlay Backdrop */
        $this->start_controls_section( 'backdrop_style_section', [
            'label' => __( 'Overlay Backdrop', 'daily-slider' ),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control( 'backdrop_bg', [
            'label' => __( 'Backdrop Color', 'daily-slider' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .daily-modal-backdrop' => 'background-color: {{VALUE}};' ],
        ]);

        $this->end_controls_section();

        /* Style: Close Button */
        $this->start_controls_section( 'close_style_section', [
            'label' => __( 'Close Button', 'daily-slider' ),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control( 'close_color', [
            'label' => __( 'Color', 'daily-slider' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .daily-modal-close' => 'color: {{VALUE}};' ],
        ]);

        $this->add_control( 'close_hover_color', [
            'label' => __( 'Hover Color', 'daily-slider' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .daily-modal-close:hover' => 'color: {{VALUE}};' ],
        ]);

        $this->add_responsive_control( 'close_size', [
            'label' => __( 'Font Size', 'daily-slider' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em', 'rem' ],
            'selectors' => [
                '{{WRAPPER}} .daily-modal-close' => 'font-size: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $modal_id = 'daily-modal-' . $this->get_id();
        $anchor_base = ! empty( $settings['trigger_anchor'] ) ? sanitize_title( $settings['trigger_anchor'] ) : 'daily-modal-' . $this->get_id();
        $trigger_hash = '#' . $anchor_base;

        $is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();
        $preview_class = ( ! empty( $settings['preview_modal'] ) && $settings['preview_modal'] === 'yes' && $is_editor ) ? ' is-open preview-mode' : '';
        ?>
        <div class="daily-modal-wrapper">
            <div id="<?php echo esc_attr( $modal_id ); ?>" class="daily-modal<?php echo esc_attr( $preview_class ); ?>" data-trigger-hash="<?php echo esc_attr( $trigger_hash ); ?>">
                <div class="daily-modal-backdrop"></div>
                <div class="daily-modal-content">
                    <button type="button" class="daily-modal-close" aria-label="<?php esc_attr_e( 'Close', 'daily-slider' ); ?>">&times;</button>
                    
                    <div class="daily-modal-nested-container">
                        <?php $this->print_child( 0 ); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
