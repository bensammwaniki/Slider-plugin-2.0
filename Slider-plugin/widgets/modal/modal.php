<?php
namespace DailySlider\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Modal_Widget extends Widget_Base {

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

    private function get_elementor_templates() {
        $templates = get_posts( [
            'post_type'      => 'elementor_library',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ] );

        $options = [ '' => __( 'Select Template', 'daily-slider' ) ];

        if ( ! empty( $templates ) && ! is_wp_error( $templates ) ) {
            foreach ( $templates as $template ) {
                $options[ $template->ID ] = $template->post_title;
            }
        }

        return $options;
    }

    protected function register_controls() {
        $this->start_controls_section( 'content_section', [
            'label' => __( 'Modal Content', 'daily-slider' ),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control( 'content_source', [
            'label'   => __( 'Content Source', 'daily-slider' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'custom',
            'options' => [
                'custom'   => __( 'Predefined Fields', 'daily-slider' ),
                'template' => __( 'Elementor Template', 'daily-slider' ),
            ],
        ]);

        $this->add_control( 'template_id', [
            'label'     => __( 'Select Template', 'daily-slider' ),
            'type'      => Controls_Manager::SELECT,
            'options'   => $this->get_elementor_templates(),
            'default'   => '',
            'condition' => [
                'content_source' => 'template',
            ],
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

        $this->add_control( 'modal_title', [
            'label' => __( 'Modal Title', 'daily-slider' ),
            'type' => Controls_Manager::TEXT,
            'default' => __( 'Beyond the classroom', 'daily-slider' ),
            'condition' => [
                'content_source' => 'custom',
            ],
        ]);

        $this->add_control( 'modal_description', [
            'label' => __( 'Modal Description', 'daily-slider' ),
            'type' => Controls_Manager::WYSIWYG,
            'default' => __( 'At Serare School, we develop well-rounded learners through a rich blend of academic and extra-curricular programs that build confidence, teamwork, discipline, and creativity.', 'daily-slider' ),
            'condition' => [
                'content_source' => 'custom',
            ],
        ]);

        $this->add_control( 'trigger_anchor', [
            'label' => __( 'Trigger Anchor (no #)', 'daily-slider' ),
            'type' => Controls_Manager::TEXT,
            'description' => __( 'Use this in any link URL (e.g. #extra-curricular) to open this modal.', 'daily-slider' ),
            'default' => '',
        ]);

        $this->end_controls_section();

        /* Mini containers (cards) repeater */
        $this->start_controls_section( 'items_section', [
            'label' => __( 'Mini Containers', 'daily-slider' ),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'content_source' => 'custom',
            ],
        ]);

        $repeater = new Repeater();

        $repeater->add_control( 'item_image', [
            'label' => __( 'Image', 'daily-slider' ),
            'type' => Controls_Manager::MEDIA,
        ]);
        $repeater->add_control( 'image_as_link', [
            'label' => __( 'Image as Link', 'daily-slider' ),
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
        ]);

        $repeater->add_control( 'item_link', [
            'label' => __( 'Link', 'daily-slider' ),
            'type' => Controls_Manager::URL,
            'placeholder' => __( 'https://your-link.com', 'daily-slider' ),
            'condition' => [ 'image_as_link' => 'yes' ],
        ]);

        $repeater->add_control( 'item_title', [
            'label' => __( 'Title', 'daily-slider' ),
            'type' => Controls_Manager::TEXT,
            'default' => __( 'Music', 'daily-slider' ),
        ]);

        $repeater->add_control( 'item_description', [
            'label' => __( 'Description', 'daily-slider' ),
            'type' => Controls_Manager::TEXTAREA,
            'default' => __( 'Music nurtures imagination, rhythm, and emotional expression.', 'daily-slider' ),
        ]);
        $repeater->add_responsive_control(
            'item_description_alignment',
            [
                'label' => __( 'Alignment', 'daily-slider' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'daily-slider' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'daily-slider' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'daily-slider' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __( 'Justify', 'daily-slider' ),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'default' => 'left',
                'toggle' => true,
            ]
        );
        

        $this->add_control( 'items', [
            'label' => __( 'Items', 'daily-slider' ),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [
                [ 'item_title' => __( 'Music', 'daily-slider' ), 'item_description' => __( 'Music nurtures imagination, rhythm, and emotional expression.', 'daily-slider' ) ],
                [ 'item_title' => __( 'Ballet', 'daily-slider' ), 'item_description' => __( 'Ballet builds grace, discipline, and coordination.', 'daily-slider' ) ],
                [ 'item_title' => __( 'Soccer', 'daily-slider' ), 'item_description' => __( 'Soccer builds teamwork, discipline, and physical fitness.', 'daily-slider' ) ],
            ],
            'title_field' => '{{{ item_title }}}',
        ]);

        $this->end_controls_section();

        /* Layout: columns (controls rows indirectly) */
        $this->start_controls_section( 'layout_section', [
            'label' => __( 'Layout', 'daily-slider' ),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'content_source' => 'custom',
            ],
        ]);

        $this->add_responsive_control( 'columns', [
            'label' => __( 'Columns per row', 'daily-slider' ),
            'type' => Controls_Manager::SELECT,
            'default' => '3',
            'options' => [
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
            ],
            'selectors' => [
                '{{WRAPPER}} .daily-modal-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
            ],
        ]);

        $this->end_controls_section();

        /* Style: Modal */
        $this->start_controls_section( 'modal_style_section', [
            'label' => __( 'Modal', 'daily-slider' ),
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

        $this->add_responsive_control( 'modal_align', [
            'label' => __( 'Alignment', 'daily-slider' ),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'left' => [
                    'title' => __( 'Left', 'daily-slider' ),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => __( 'Center', 'daily-slider' ),
                    'icon' => 'eicon-text-align-center',
                ],
                'right' => [
                    'title' => __( 'Right', 'daily-slider' ),
                    'icon' => 'eicon-text-align-right',
                ],
                'justify' => [
                    'title' => __( 'Justify', 'daily-slider' ),
                    'icon' => 'eicon-text-align-justify',
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .daily-modal-title, {{WRAPPER}} .daily-modal-description' => 'text-align: {{VALUE}};',
            ],
            'condition' => [
                'content_source' => 'custom',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'modal_title_typography',
                'selector' => '{{WRAPPER}} .daily-modal-title',
                'condition' => [
                    'content_source' => 'custom',
                ],
            ]
        );
        $this->add_control( 'modal_title_color', [
            'label' => __( 'Title Color', 'daily-slider' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .daily-modal-title' => 'color: {{VALUE}};' ],
            'condition' => [
                'content_source' => 'custom',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'modal_desc_typography',
                'selector' => '{{WRAPPER}} .daily-modal-description',
                'condition' => [
                    'content_source' => 'custom',
                ],
            ]
        );
        $this->add_control( 'modal_desc_color', [
            'label' => __( 'Description Color', 'daily-slider' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .daily-modal-description' => 'color: {{VALUE}};' ],
            'condition' => [
                'content_source' => 'custom',
            ],
        ]);

        $this->add_control( 'modal_bg', [
            'label' => __( 'Modal Background', 'daily-slider' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .daily-modal-content' => 'background-color: {{VALUE}};' ],
        ]);

        $this->end_controls_section();

        /* Style: Cards */
        $this->start_controls_section( 'card_style_section', [
            'label' => __( 'Mini Containers', 'daily-slider' ),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [
                'content_source' => 'custom',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [ 'name' => 'card_title_typography', 'selector' => '{{WRAPPER}} .daily-modal-card-title' ]
        );
        $this->add_control( 'card_title_color', [
            'label' => __( 'Card Title Color', 'daily-slider' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .daily-modal-card-title' => 'color: {{VALUE}};' ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [ 'name' => 'card_desc_typography', 'selector' => '{{WRAPPER}} .daily-modal-card-desc' ]
        );
        $this->add_control( 'card_desc_color', [
            'label' => __( 'Card Description Color', 'daily-slider' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .daily-modal-card-desc' => 'color: {{VALUE}};' ],
        ]);

        $this->add_control( 'card_bg', [
            'label' => __( 'Card Background', 'daily-slider' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .daily-modal-card' => 'background-color: {{VALUE}};' ],
        ]);

        $this->add_responsive_control( 'card_border_radius', [
            'label' => __( 'Border Radius', 'daily-slider' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'selectors' => [ '{{WRAPPER}} .daily-modal-card' => 'border-radius: {{SIZE}}{{UNIT}};' ],
        ]);

        /* =========================
        Image Style Controls
        ========================= */

        $this->add_control(
            'card_image_style_heading',
            [
                'label' => __( 'Image', 'daily-slider' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        /* Width */
        $this->add_responsive_control(
            'card_image_width',
            [
                'label' => __( 'Width', 'daily-slider' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '%', 'px', 'vw' ],
                'range' => [
                    '%' => [ 'min' => 0, 'max' => 100 ],
                    'px' => [ 'min' => 0, 'max' => 1000 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .daily-modal-img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        /* Max Width */
        $this->add_responsive_control(
            'card_image_max_width',
            [
                'label' => __( 'Max Width', 'daily-slider' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '%', 'px', 'vw' ],
                'selectors' => [
                    '{{WRAPPER}} .daily-modal-img' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        /* Height */
        $this->add_responsive_control(
            'card_image_height',
            [
                'label' => __( 'Height', 'daily-slider' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'vh', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .daily-modal-img' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        /* Object Fit */
        $this->add_responsive_control(
            'card_image_object_fit',
            [
                'label' => __( 'Object Fit', 'daily-slider' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'cover' => 'Cover',
                    'contain' => 'Contain',
                    'fill' => 'Fill',
                    'none' => 'None',
                    'scale-down' => 'Scale Down',
                ],
                'default' => 'cover',
                'selectors' => [
                    '{{WRAPPER}} .daily-modal-img' => 'object-fit: {{VALUE}};',
                ],
            ]
        );

        /* Object Position */
        $this->add_responsive_control(
            'card_image_object_position',
            [
                'label' => __( 'Object Position', 'daily-slider' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'center center' => 'Center Center',
                    'top left' => 'Top Left',
                    'top center' => 'Top Center',
                    'top right' => 'Top Right',
                    'center left' => 'Center Left',
                    'center right' => 'Center Right',
                    'bottom left' => 'Bottom Left',
                    'bottom center' => 'Bottom Center',
                    'bottom right' => 'Bottom Right',
                ],
                'default' => 'center center',
                'selectors' => [
                    '{{WRAPPER}} .daily-modal-img' => 'object-position: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $modal_id = 'daily-modal-' . $this->get_id();
        $anchor_base = ! empty( $settings['trigger_anchor'] ) ? sanitize_title( $settings['trigger_anchor'] ) : 'daily-modal-' . $this->get_id();
        $trigger_hash = '#' . $anchor_base;

        $content_source = ! empty( $settings['content_source'] ) ? $settings['content_source'] : 'custom';
        $is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();
        
        $preview_class = ( ! empty( $settings['preview_modal'] ) && $settings['preview_modal'] === 'yes' && $is_editor ) ? ' is-open preview-mode' : '';
        $inline_style = empty( $preview_class ) ? ' style="display: none;"' : '';
        ?>
        <div class="daily-modal-wrapper">
            <div id="<?php echo esc_attr( $modal_id ); ?>" class="daily-modal<?php echo esc_attr( $preview_class ); ?>" data-trigger-hash="<?php echo esc_attr( $trigger_hash ); ?>"<?php echo $inline_style; ?>>
                <div class="daily-modal-backdrop"></div>
                <div class="daily-modal-content">
                    <button type="button" class="daily-modal-close" aria-label="<?php esc_attr_e( 'Close', 'daily-slider' ); ?>">&times;</button>

                    <?php if ( 'template' === $content_source ) : ?>
                        <?php if ( ! empty( $settings['template_id'] ) ) : ?>
                            <div class="daily-modal-template-content">
                                <?php echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['template_id'] ); ?>
                            </div>
                        <?php else : ?>
                            <div class="daily-modal-no-template">
                                <?php esc_html_e( 'Please select an Elementor template in the settings.', 'daily-slider' ); ?>
                            </div>
                        <?php endif; ?>
                    <?php else : ?>
                        <?php if ( ! empty( $settings['modal_title'] ) ) : ?>
                            <h3 class="daily-modal-title"><?php echo esc_html( $settings['modal_title'] ); ?></h3>
                        <?php endif; ?>

                        <?php if ( ! empty( $settings['modal_description'] ) ) : ?>
                            <div class="daily-modal-description"><?php echo do_shortcode( wp_kses_post( $settings['modal_description'] ) ); ?></div>
                        <?php endif; ?>

                        <?php if ( ! empty( $settings['items'] ) && is_array( $settings['items'] ) ) : ?>
                            <div class="daily-modal-grid">
                                <?php foreach ( $settings['items'] as $item ) : ?>
                                    <div class="daily-modal-card">
                                        <?php if ( ! empty( $item['item_image']['url'] ) ) : ?>
                                            <div class="daily-modal-card-image">
                                                <?php
                                                $img_id = $item['item_image']['id'] ?? 0;
                                                $img_url = $item['item_image']['url'] ?? '';
                                                $is_link = ! empty( $item['image_as_link'] ) && $item['image_as_link'] === 'yes';
                                                $link = $item['item_link'] ?? null;
                                                $link_url = is_array( $link ) ? ( $link['url'] ?? '' ) : '';
                                                $link_target = ( is_array( $link ) && ! empty( $link['is_external'] ) ) ? ' target="_blank"' : '';
                                                $link_nofollow = ( is_array( $link ) && ! empty( $link['nofollow'] ) ) ? ' rel="nofollow"' : '';
                                                ?>
                                                <?php if ( $is_link && ! empty( $link_url ) ) : ?>
                                                    <a href="<?php echo esc_url( $link_url ); ?>"<?php echo $link_target . $link_nofollow; ?>>
                                                <?php endif; ?>
                                                <?php if ( $img_id ) : ?>
                                                    <?php echo wp_get_attachment_image( $img_id, 'medium', false, [ 'class' => 'daily-modal-img', 'alt' => esc_attr( $item['item_title'] ?? '' ) ] ); ?>
                                                <?php else : ?>
                                                    <img class="daily-modal-img" src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $item['item_title'] ?? '' ); ?>" loading="lazy" />
                                                <?php endif; ?>
                                                <?php if ( $is_link && ! empty( $link_url ) ) : ?>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ( ! empty( $item['item_title'] ) ) : ?>
                                            <h4 class="daily-modal-card-title"><?php echo esc_html( $item['item_title'] ); ?></h4>
                                        <?php endif; ?>
                                        <?php if ( ! empty( $item['item_description'] ) ) : ?>
                                            <p class="daily-modal-card-desc"
                                            style="text-align: <?php echo esc_attr( $item['item_description_alignment'] ?? 'left' ); ?>;">
                                                <?php echo esc_html( $item['item_description'] ); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
}
