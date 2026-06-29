<?php
namespace DailySlider\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class ReviewCarousel_Widget extends Widget_Base {

    public function get_name() {
        return 'daily-slider-review-carousel';  // Unique name for Review Carousel widget
    }
    
    public function get_title() {
        return __('Review Carousel', 'daily-slider');
    }

    public function get_icon() {
        return 'eicon-button';
    }

    public function get_categories() {
        return ['DailySlider-category']; // Custom category defined in the main plugin file
    }

    public function get_script_depends(): array {
        return [ 'DailySlider-review-carousel-scripts' ];
    }

    public function get_style_depends(): array {
		return [ 'e-swiper', 'widget-image-carousel', 'DailySlider-common-styles', 'DailySlider-review-carousel-styles' ];
	}

    protected function register_controls() {

        // ── Google Reviews Source Section ────────────────────────────────────────
        $this->start_controls_section(
            'google_reviews_section',
            [
                'label' => __( 'Google Reviews Source', 'daily-slider' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'data_source',
            [
                'label'   => __( 'Data Source', 'daily-slider' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'static',
                'options' => [
                    'static' => __( '✏️ Static (Repeater)', 'daily-slider' ),
                    'google' => __( '🌐 Google Reviews (Live)', 'daily-slider' ),
                ],
            ]
        );

        $this->add_control(
            'google_api_key',
            [
                'label'       => __( 'Google Places API Key', 'daily-slider' ),
                'type'        => Controls_Manager::TEXT,
                'input_type'  => 'password',
                'placeholder' => 'AIza...',
                'description' => __( 'Create a key at console.cloud.google.com → APIs → Places API.', 'daily-slider' ),
                'label_block' => true,
                'condition'   => [ 'data_source' => 'google' ],
            ]
        );

        $this->add_control(
            'google_place_id',
            [
                'label'       => __( 'Google Place ID or CID', 'daily-slider' ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => 'e.g. ChIJN1t_tDeuEmsRUsoyG83frY4 or 10281119596374313554',
                'description' => __( 'Enter your Google Place ID, Google Map CID (decimal or hex), or paste the full Google Maps browser URL / sharing link.', 'daily-slider' ),
                'label_block' => true,
                'condition'   => [ 'data_source' => 'google' ],
            ]
        );

        $this->add_control(
            'google_limit',
            [
                'label'     => __( 'Number of Reviews', 'daily-slider' ),
                'type'      => Controls_Manager::NUMBER,
                'default'   => 5,
                'min'       => 1,
                'max'       => 5,
                'step'      => 1,
                'description' => __( 'Google Places API returns up to 5 reviews.', 'daily-slider' ),
                'condition' => [ 'data_source' => 'google' ],
            ]
        );

        $this->add_control(
            'google_min_rating',
            [
                'label'     => __( 'Minimum Star Rating', 'daily-slider' ),
                'type'      => Controls_Manager::NUMBER,
                'default'   => 4,
                'min'       => 1,
                'max'       => 5,
                'step'      => 1,
                'description' => __( 'Only show reviews with this rating or higher (1–5 ★).', 'daily-slider' ),
                'condition' => [ 'data_source' => 'google' ],
            ]
        );

        $this->add_control(
            'google_cache_notice',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => '<div style="background:#f0f6ff;border-left:3px solid #4A90E2;padding:8px 10px;font-size:12px;line-height:1.5;">'.
                                     '<strong>⏱ Cache:</strong> Reviews are cached for <strong>6 hours</strong> to avoid unnecessary API calls. '.
                                     'Re-save the page to force a refresh if the transient has expired.</div>',
                'content_classes' => 'elementor-descriptor',
                'condition'       => [ 'data_source' => 'google' ],
            ]
        );

        $this->end_controls_section();
        // ── End Google Reviews Source Section ────────────────────────────────────

        // Genarel
        $this->start_controls_section(
            'genarel_section',
            [
                'label' => __('General', 'daily-slider'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'item_direction',
            [
                'label' => __('Direction', 'daily-slider'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'daily-avatar-bottom' => [
                        'title' => __('Bottom', 'daily-slider'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                    'daily-avatar-top' => [
                        'title' => __('Top', 'daily-slider'),
                        'icon' => 'eicon-v-align-top',
                    ],
                ],
                'default' => 'title',
                'toggle' => true,
            ]
        );

        // switcher control
        $this->add_control(
            'show_avatar_image',
            [
                'label' => __('Show Avatar', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'return_value' => 'yes',
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'show_name',
            [
                'label' => __('Show Name', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_headline',
            [
                'label' => __('Show Review Heading', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_dagination',
            [
                'label' => __('Show Dagination', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_star_rating',
            [
                'label' => __('Show Rating', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_text',
            [
                'label' => __('Show Description', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        // alignment control
        $this->add_control(
            'content_align',
            [
                'label' => __('Alignment', 'daily-slider'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'daily-content-left' => [
                        'title' => __('Left', 'daily-slider'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'daily-content-center' => [
                        'title' => __('Center', 'daily-slider'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'daily-content-right' => [
                        'title' => __('Right', 'daily-slider'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'toggle' => true,
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        // Swiper Slides Section
        $this->start_controls_section(
            'swiper_slides_section',
            [
                'label' => __('Review Items', 'daily-slider'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
    
        // Repeater for slides
        $repeater = new Repeater();
    
        $repeater->add_control(
            'name',
            [
                'label' => __('Name', 'daily-slider'),
                'type' => Controls_Manager::TEXT,
                'default' => __('John Doe', 'daily-slider'),
                'label_block' => true,
            ]
        );
    
        $repeater->add_control(
            'dagination',
            [
                'label' => __('Dagination', 'daily-slider'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Web Developer', 'daily-slider'),
                'label_block' => true,
            ]
        );
    
        $repeater->add_control(
            'text',
            [
                'label' => __('Description', 'daily-slider'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __('Content Here', 'daily-slider'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
			'rating',
			[ 
				'label'      => __( 'Rating', 'daily-slider' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [ 
					'size' =>5,
				],
				'range'      => [ 
					'px' => [ 
						'min'  => 0,
						'max'  => 5,
						'step' => 1,
					],
				],
			]
		);

        $repeater->add_control(
            'avatar_image',
            [
                'label' => __('Avatar Image', 'daily-slider'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'id' => '', // Default to no image ID
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );
    
        $this->add_control(
            'swiper_slides',
            [
                'label' => __('Review Item', 'daily-slider'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'prevent_empty' => false,
                'default' => [
                    [
                        'name' => __('John D.', 'daily-slider'),
                        'dagination' => __('Web Developer', 'daily-slider'),
                        'text' => __('This plugin transformed our website. It’s easy to use and provides excellent features. Highly recommend!', 'daily-slider'),
                        'rating'      => ['size' => 5],
                        'avatar_image' => ['url' => plugin_dir_url(__FILE__) . '../../assets/images/item-1.svg'],
                    ],
                    [
                        'name' => __('Sarah M.', 'daily-slider'),
                        'dagination' => __('Business Owner', 'daily-slider'),
                        'text' => __('I was looking for a plugin like this for weeks. It works smoothly, and the support team is very responsive.', 'daily-slider'),
                        'rating'      => ['size' => 5],
                        'avatar_image' => ['url' => plugin_dir_url(__FILE__) . '../../assets/images/item-2.svg'],
                    ],
                    [
                        'name' => __('Mike T.', 'daily-slider'),
                        'dagination' => __('Web Designer', 'daily-slider'),
                        'text' => __('The customer service was fantastic. They helped me set up everything step-by-step. Love the features!', 'daily-slider'),
                        'rating'      => ['size' => 5],
                        'avatar_image' => ['url' => plugin_dir_url(__FILE__) . '../../assets/images/item-3.svg'],
                    ],
                    [
                        'name' => __('Maria Garcia', 'daily-slider'),
                        'dagination' => __('Society Secretary', 'daily-slider'),
                        'text' => __('This plugin has transformed the way I showcase my products. My sales have increased since I started using it. Thank you!', 'daily-slider'),
                        'rating'      => ['size' => 5],
                        'avatar_image' => ['url' => plugin_dir_url(__FILE__) . '../../assets/images/item-1.svg'],
                    ],
                    [
                        'name' => __('Emily Brown', 'daily-slider'),
                        'dagination' => __('Member', 'daily-slider'),
                        'text' => __('Absolutely love the features! Highly recommend this plugin to anyone looking for flexibility.', 'daily-slider'),
                        'rating'      => ['size' => 5],
                        'avatar_image' => ['url' => plugin_dir_url(__FILE__) . '../../assets/images/item-2.svg'],
                    ],
                ],
                'title_field' => '{{{ name }}}',
            ]
        );
    
        $this->end_controls_section(); // Close swiper_slides_section
    
         // Review Settings Section (Combined Carousel & Marquee Settings)
        $this->start_controls_section(
            'review_settings_section',
            [
                'label' => __('Review Settings', 'daily-slider'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'display_mode',
            [
                'label'   => __( 'Display Mode', 'daily-slider' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'carousel',
                'options' => [
                    'carousel' => __( '▶ Carousel (Swiper)', 'daily-slider' ),
                    'marquee'  => __( '↔ Marquee (Infinite Scroll)', 'daily-slider' ),
                ],
            ]
        );

        $this->add_control(
            'swiper_effect',
            [
                'label' => __('Effect', 'daily-slider'),
                'type' => Controls_Manager::SELECT,
                'default' => 'slide',
                'options' => [
                    'slide' => __('Slide', 'daily-slider'),
                    'coverflow' => __('Coverflow', 'daily-slider'),
                ],
                'condition' => [ 'display_mode' => 'carousel' ],
            ]
        );

        $this->add_responsive_control(
			'columns',
			[
				'label'          => __('Columns', 'daily-slider'),
				'type'           => Controls_Manager::SELECT,
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'options'        => [
					1 => '1',
					2 => '2',
					3 => '3',
					4 => '4',
					5 => '5',
					6 => '6',
				],
                'condition' => [ 'display_mode' => 'carousel' ],
			]
		);

		$this->add_responsive_control(
			'item_gap',
			[
				'label'   => __('Column Gap', 'daily-slider'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'tablet_default' => [
					'size' => 20,
				],
				'mobile_default' => [
					'size' => 20,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
                'condition' => [ 'display_mode' => 'carousel' ],
			]
		);
        
        $this->add_control(
            'swiper_autoplay',
            [
                'label' => __('Autoplay', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'default' => 'yes',
                'separator' => 'before',
                'condition' => [ 'display_mode' => 'carousel' ],
            ]
        );
    
        $this->add_control(
            'swiper_autoplay_delay',
            [
                'label' => __('Autoplay Delay (ms)', 'daily-slider'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3000,
                'condition' => [
                    'display_mode' => 'carousel',
                    'swiper_autoplay' => 'yes',
                ],
            ]
        );
    
        $this->add_control(
            'swiper_loop',
            [
                'label' => __('Infinite loop', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'default' => 'yes',
                'condition' => [ 'display_mode' => 'carousel' ],
            ]
        );

        $this->add_control(
            'swiper_center_slide',
            [
                'label' => __('Centered', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'default' => 'no',
                'condition' => [ 'display_mode' => 'carousel' ],
            ]
        );
    
        $this->add_control(
            'swiper_speed',
            [
                'label' => __('Transition Speed (ms)', 'daily-slider'),
                'type' => Controls_Manager::NUMBER,
                'default' => 600,
                'condition' => [ 'display_mode' => 'carousel' ],
            ]
        );

        $this->add_control(
            'swiper_navigation',
            [
                'label' => __('Navigation', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'default' => 'no',
                'condition' => [ 'display_mode' => 'carousel' ],
            ]
        );

        $this->add_control(
            'swiper_pagination',
            [
                'label' => __('Pagination', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'default' => 'yes',
                'condition' => [ 'display_mode' => 'carousel' ],
            ]
        );

        $this->add_control(
            'marquee_rows',
            [
                'label'       => __( 'Number of Rows', 'daily-slider' ),
                'type'        => Controls_Manager::NUMBER,
                'default'     => 2,
                'min'         => 1,
                'max'         => 4,
                'step'        => 1,
                'description' => __( 'How many scrolling rows to show (1–4).', 'daily-slider' ),
                'condition'   => [ 'display_mode' => 'marquee' ],
            ]
        );

        $this->add_control(
            'marquee_direction',
            [
                'label'     => __( 'Row Direction', 'daily-slider' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'alternate',
                'options'   => [
                    'alternate' => __( '↔ Alternate (odd=left, even=right)', 'daily-slider' ),
                    'left'      => __( '← All scroll left', 'daily-slider' ),
                    'right'     => __( '→ All scroll right', 'daily-slider' ),
                ],
                'condition' => [ 'display_mode' => 'marquee' ],
            ]
        );

        $this->add_control(
            'marquee_speed',
            [
                'label'       => __( 'Scroll Speed (seconds)', 'daily-slider' ),
                'type'        => Controls_Manager::NUMBER,
                'default'     => 35,
                'min'         => 5,
                'max'         => 120,
                'step'        => 5,
                'description' => __( 'Lower = faster. Recommended: 25–45.', 'daily-slider' ),
                'condition'   => [ 'display_mode' => 'marquee' ],
            ]
        );

        $this->add_control(
            'marquee_pause_hover',
            [
                'label'       => __( 'Pause on Hover', 'daily-slider' ),
                'type'        => Controls_Manager::SWITCHER,
                'label_on'    => __( 'Yes', 'daily-slider' ),
                'label_off'   => __( 'No', 'daily-slider' ),
                'return_value' => 'yes',
                'default'     => 'yes',
                'condition'   => [ 'display_mode' => 'marquee' ],
            ]
        );

        $this->add_control(
            'marquee_card_width',
            [
                'label'      => __( 'Card Width', 'daily-slider' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'default'    => [ 'size' => 320 ],
                'range'      => [ 'px' => [ 'min' => 200, 'max' => 600, 'step' => 10 ] ],
                'condition'  => [ 'display_mode' => 'marquee' ],
            ]
        );

        $this->add_control(
            'marquee_gap',
            [
                'label'      => __( 'Gap Between Cards', 'daily-slider' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'default'    => [ 'size' => 24 ],
                'range'      => [ 'px' => [ 'min' => 0, 'max' => 80, 'step' => 4 ] ],
                'condition'  => [ 'display_mode' => 'marquee' ],
            ]
        );

        $this->add_control(
            'marquee_row_gap',
            [
                'label'      => __( 'Gap Between Rows', 'daily-slider' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'default'    => [ 'size' => 18 ],
                'range'      => [ 'px' => [ 'min' => 0, 'max' => 60, 'step' => 4 ] ],
                'condition'  => [ 'display_mode' => 'marquee' ],
            ]
        );

        $this->add_control(
            'marquee_fade_color',
            [
                'label'       => __( 'Edge Fade Colour', 'daily-slider' ),
                'type'        => Controls_Manager::COLOR,
                'default'     => 'rgba(0,0,0,0)',
                'description' => __( 'Match this to your section background to create a smooth fade at the left/right edges. Use transparent to disable.', 'daily-slider' ),
                'condition'   => [ 'display_mode' => 'marquee' ],
            ]
        );

        $this->end_controls_section();

        // Title Style Section

        
        $this->start_controls_section(
            'swiper_slider_item_section',
            [
                'label' => __('Items', 'daily-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'item_background',
                'label' => __('Background', 'daily-slider'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .daily-slide-item',
            ]
        );

        $this->add_responsive_control(
            'item_padding',
            [
                'label' => __('Padding', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-slide-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'item_border',
                'label' => __('Border', 'daily-slider'),
                'selector' => '{{WRAPPER}} .daily-slide-item',
            ]
        );

        $this->add_responsive_control(
            'item_border_radius',
            [
                'label' => __('Border Radius', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-slide-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'item_box_shadow',
                'selector' => '{{WRAPPER}} .daily-slide-item',
            ]
        );

        // space between items

        $this->add_responsive_control(
            'items_gap',
            [
                'label' => __('Space Between', 'daily-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-slide-item' => 'gap: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );
        
        $this->end_controls_section();

        // Name Style Section
        $this->start_controls_section(
            'avatar_image_style_section',
            [
                'label' => __('Avatar', 'daily-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_avatar_image' => 'yes',
                ],
            ]
        ); 

        // avatar image size control

        $this->add_responsive_control(
            'avatar_image_size',
            [
                'label' => __('Size', 'daily-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-image-wrap' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // avatar image border control

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'avatar_image_border',
                'label' => __('Border', 'daily-slider'),
                'selector' => '{{WRAPPER}} .daily-image-wrap',
            ]
        );

        // avatar image border radius control

        $this->add_responsive_control(
            'avatar_image_border_radius',
            [
                'label' => __('Border Radius', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-image-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // avatar image box shadow control

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'avatar_image_box_shadow',
                'selector' => '{{WRAPPER}} .daily-image-wrap',
            ]
        );

        // avatar image padding control

        $this->add_responsive_control(
            'avatar_image_padding',
            [
                'label' => __('Padding', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-image-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // space between avatar image and content

        $this->add_responsive_control(
            'avatar_image_gap',
            [
                'label' => __('Gap', 'daily-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-img-content-wrap' => 'gap: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        // Name Style Section
        $this->start_controls_section(
            'name_style_section',
            [
                'label' => __('Name', 'daily-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_name' => 'yes',
                ],
            ]
        );        
    
        $this->add_control(
            'name_color',
            [
                'label' => __('Color', 'daily-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .daily-avatar-name' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'text_stroke',
                'selector' => '{{WRAPPER}} .daily-avatar-name',
            ]
        );
        
        // Typography control
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'name_typography',
                'label' => __('Typography', 'daily-slider'),
                'selector' => '{{WRAPPER}} .daily-avatar-name',
            ]
        );

        // Margin control
        $this->add_responsive_control(
            'name_margin',
            [
                'label' => __('Margin', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-avatar-name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
    
        $this->end_controls_section(); // Close title_style_section

        // Sub Title Style Section
        $this->start_controls_section(
            'dagination_style_section',
            [
                'label' => __('Dagination', 'daily-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_dagination' => 'yes',
                ],
            ]

        );
    
        $this->add_control(
            'dagination_color',
            [
                'label' => __('Color', 'daily-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .daily-avatar-dagination' => 'color: {{VALUE}}',
                ],
            ]
        );
    
        // Typography control for subtitle
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'dagination_typography',
                'label' => __('Typography', 'daily-slider'),
                'selector' => '{{WRAPPER}} .daily-avatar-dagination',
                'separator' => 'after',
            ]
        );
    
        // Margin control for subtitle
        $this->add_responsive_control(
            'dagination_margin',
            [
                'label' => __('Margin', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-avatar-dagination' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
    
        $this->end_controls_section(); // Close dagination_style_section

        // Rating Style Section
        $this->start_controls_section(
            'star_rating_section',
            [
                'label' => __('Rating', 'daily-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_star_rating' => 'yes',
                ],
            ]
        );

        // rating color
        $this->add_control(
            'rating_color',
            [
                'label' => __('Color', 'daily-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .daily-review-rating svg' => 'color: {{VALUE}}',
                ],
            ]
        );

       // size control for rating

         $this->add_responsive_control(
            'rating_size',
            [
                'label' => __('Size', 'daily-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .daily-review-rating svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Margin control for rating

        $this->add_responsive_control(
            'rating_margin',
            [
                'label' => __('Margin', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-review-rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // gap control for rating

        $this->add_responsive_control(
            'rating_gap',
            [
                'label' => __('Gap', 'daily-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-review-rating' => 'gap: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );
        

        $this->end_controls_section(); // Close Rating Style Section

        // Decription Style Section
        $this->start_controls_section(
            'description_section',
            [
                'label' => __('Decription', 'daily-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_text' => 'yes',
                ],
            ]
        );

        // description color
        $this->add_control(
            'description_color',
            [
                'label' => __('Color', 'daily-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .daily-text' => 'color: {{VALUE}}',
                ],
            ]
        );

        // Typography control for description
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'label' => __('Typography', 'daily-slider'),
                'selector' => '{{WRAPPER}} .daily-text',
                'separator' => 'after',
            ]
        );

        // Margin control for description
        $this->add_responsive_control(
            'description_margin',
            [
                'label' => __('Margin', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
    
        $this->end_controls_section(); // Close dagination_style_section

        // navigation style
        $this->start_controls_section(
            'navigation_section',
            [
                'label' => __('Navigation', 'daily-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'swiper_navigation' => 'yes',
                ],
            ]
        );

        // tab style
        $this->start_controls_tabs(
            'nav_style_tabs'
        );

        $this->start_controls_tab(
            'nav_style_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'daily-slider' ),
            ]
        );

        // navigation color
        $this->add_control(
            'nav_color',
            [
                'label' => __('Color', 'daily-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .daily-nav-button' => 'color: {{VALUE}}',
                ],
            ]
        );

        // navigation background
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'nav_background',
                'label' => __('Background', 'daily-slider'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .daily-nav-button',
            ]
        );

        // padding control for navigation

        $this->add_responsive_control(
            'nav_padding',
            [
                'label' => __('Padding', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-nav-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // border control for navigation

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'nav_border',
                'label' => __('Border', 'daily-slider'),
                'selector' => '{{WRAPPER}} .daily-nav-button',
            ]

        );

        // box shadow control

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'nav_box_shadow',
                'selector' => '{{WRAPPER}} .daily-nav-button',
            ]
        );

        // border radius control for navigation

        $this->add_responsive_control(
            'nav_border_radius',
            [
                'label' => __('Border Radius', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-nav-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // horizontal space between navigation

        $this->add_responsive_control(
            'nav_spacing',
            [
                'label' => __('Offset', 'daily-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => -10,
                        'max' => 10,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .daily-nav-button' => '--navigation-horizontal-spacing: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );
        

        $this->end_controls_tab();

        $this->start_controls_tab(
            'nav_style_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'daily-slider' ),
            ]
        );

        // navigation color
        $this->add_control(
            'nav_hover_color',
            [
                'label' => __('Color', 'daily-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .daily-nav-button:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        // navigation background
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'nav_hover_background',
                'label' => __('Background', 'daily-slider'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .daily-nav-button:hover:before',
            ]
        );

        // border hover color

        $this->add_control(
            'nav_hover_border_color',
            [
                'label' => __('Border Color', 'daily-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .daily-nav-button:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        // box shadow control

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'nav_hover_box_shadow',
                'selector' => '{{WRAPPER}} .daily-nav-button:hover',
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section(); // Close dagination_style_section

        // pagination style
        $this->start_controls_section(
            'pagination_section',
            [
                'label' => __('Pagination', 'daily-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'swiper_pagination' => 'yes',
                ],
            ]
        );

        // tab style
        $this->start_controls_tabs(
            'pagination_style_tabs'
        );

        $this->start_controls_tab(
            'pagination_style_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'daily-slider' ),
            ]
        );

        // width control for pagination

        $this->add_responsive_control(
            'pagination_width',
            [
                'label' => __('Width', 'daily-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-pagination .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // height control for pagination

        $this->add_responsive_control(
            'pagination_height',
            [
                'label' => __('Height', 'daily-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-pagination .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );


        // pagination background

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'pagination_background',
                'label' => __('Background', 'daily-slider'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .daily-pagination .swiper-pagination-bullet',
            ]
        );

        // border 

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'pagination_border',
                'label' => __('Border', 'daily-slider'),
                'selector' => '{{WRAPPER}} .daily-pagination .swiper-pagination-bullet',
            ]

        );

        // border radius control for pagination

        $this->add_responsive_control(
            'pagination_border_radius',
            [
                'label' => __('Border Radius', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-pagination .swiper-pagination-bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // margin control for pagination

        $this->add_responsive_control(
            'pagination_margin',
            [
                'label' => __('Margin', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-pagination' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // gap control for pagination

        $this->add_responsive_control(
            'pagination_gap',
            [
                'label' => __('Gap', 'daily-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-pagination' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        
        $this->add_responsive_control(
            'pagination_offset',
            [
                'label' => __('Offset', 'daily-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => -10,
                        'max' => 10,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .daily-pagination' => '--pagination-vertical-spacing: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'pagination_style_active_tab',
            [
                'label' => esc_html__( 'Active', 'daily-slider' ),
            ]
        );

        // width control for pagination

        $this->add_responsive_control(
            'pagination_active_width',
            [
                'label' => __('Width', 'daily-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-pagination .swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // height control for pagination

        $this->add_responsive_control(
            'pagination_active_height',
            [
                'label' => __('Height', 'daily-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-pagination .swiper-pagination-bullet-active' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // pagination background

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'pagination_active_background',
                'label' => __('Background', 'daily-slider'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .daily-pagination .swiper-pagination-bullet-active',
            ]
        );

        // border

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'pagination_active_border',
                'label' => __('Border', 'daily-slider'),
                'selector' => '{{WRAPPER}} .daily-pagination .swiper-pagination-bullet-active',
            ]

        );

        // border radius control for pagination

        $this->add_responsive_control(
            'pagination_active_border_radius',
            [
                'label' => __('Border Radius', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-pagination .swiper-pagination-bullet-active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();
        
    }
    

    protected function render() {
        $settings = $this->get_settings_for_display();
        $id = 'ds-' . $this->get_id();

        // ── Determine slide data source ──────────────────────────────────────────
        $data_source = $settings['data_source'] ?? 'static';

        if ( 'google' === $data_source ) {
            $api_key    = trim( $settings['google_api_key'] ?? '' );
            $place_id   = trim( $settings['google_place_id'] ?? '' );
            $limit      = (int) ( $settings['google_limit'] ?? 5 );
            $min_rating = (int) ( $settings['google_min_rating'] ?? 4 );

            if ( empty( $api_key ) || empty( $place_id ) ) {
                if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                    echo '<div style="padding:20px;background:#fff3cd;border:1px solid #ffc107;border-radius:6px;font-size:13px;">'.
                         '<strong>⚠️ Google Reviews:</strong> Please enter your <em>API Key</em> and <em>Place ID or CID</em> in the widget settings to load reviews.</div>';
                }
                return;
            }

            $slides_data = \DailySlider_Google_Reviews_Bridge::get_reviews(
                $place_id,
                $api_key,
                $limit,
                $min_rating
            );

            if ( empty( $slides_data ) ) {
                if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                    echo '<div style="padding:20px;background:#f8d7da;border:1px solid #f5c6cb;border-radius:6px;font-size:13px;">'.
                         '<strong>⚠️ Google Reviews:</strong> No reviews found matching your filters (min rating: ' . (int) $min_rating . '★). '.
                         'Check your API Key, Place ID or CID, and make sure the Places API is enabled.</div>';
                }
                return;
            }
        } else {
            // Static repeater mode — original behaviour.
            $slides_data = $settings['swiper_slides'] ?? [];

            if ( empty( $slides_data ) ) {
                return;
            }
        }
        // ── End data source resolution ───────────────────────────────────────────

        // ── Branch: marquee or carousel ──────────────────────────────────────────
        $display_mode = $settings['display_mode'] ?? 'carousel';
        if ( 'marquee' === $display_mode ) {
            $this->render_marquee( $settings, $slides_data, $id );
            return;
        }
        // ── (continues as Swiper carousel below) ────────────────────────────────

        $elementor_vp_lg = get_option( 'elementor_viewport_lg' );
		$elementor_vp_md = get_option( 'elementor_viewport_md' );
		$viewport_lg     = ! empty( $elementor_vp_lg ) ? $elementor_vp_lg - 1 : 1023;
		$viewport_md     = ! empty( $elementor_vp_md ) ? $elementor_vp_md - 1 : 767;

        $this->add_render_attribute(
			[
				'carousel' => [
                    'class' => 'ds-review-carousel',
                    'id' => $id,
					'data-settings' => [
						wp_json_encode( array_filter( [
							'effect' => esc_attr($settings['swiper_effect'] ?? 'slide'),
                            'loop' => !empty($settings['swiper_loop']) && $settings['swiper_loop'] === 'yes',
                            'autoplay' => !empty($settings['swiper_autoplay']) && $settings['swiper_autoplay'] === 'yes' ? [
                                'delay' => absint($settings['swiper_autoplay_delay'] ?? 5000),
                            ] : false,
                            'speed' => absint($settings['swiper_speed'] ?? 800),
                            'parallax' => true,
                            'centeredSlides' => !empty($settings['swiper_center_slide']) && $settings['swiper_center_slide'] === 'yes',
                            'cubeEffect' => [
                                'shadow' => !empty($settings['swiper_cube_shadow']) && $settings['swiper_cube_shadow'] === 'yes',
                                'slideShadows' => !empty($settings['swiper_slide_shadows']) && $settings['swiper_slide_shadows'] === 'yes',
                                'shadowOffset' => absint($settings['swiper_shadow_offset'] ?? 20),
                                'shadowScale' => floatval($settings['swiper_shadow_scale'] ?? 0.94),
                            ],
                            "slidesPerView"         => isset($settings["columns_mobile"]) ? (int)$settings["columns_mobile"] : 1,
                            // "slidesPerGroup"        => isset($settings["slides_to_scroll_mobile"]) ? (int)$settings["slides_to_scroll_mobile"] : 1,
							"spaceBetween"          => !empty($settings["item_gap_mobile"]["size"]) ? (int)$settings["item_gap_mobile"]["size"] : 20,
                            "breakpoints"           => [
								(int) $viewport_md => [
									"slidesPerView"  => isset($settings["columns_tablet"]) ? (int)$settings["columns_tablet"] : 2,
									"spaceBetween"   => !empty($settings["item_gap_tablet"]["size"]) ? (int)$settings["item_gap_tablet"]["size"] : 20,
									// "slidesPerGroup" => isset($settings["slides_to_scroll_tablet"]) ? (int)$settings["slides_to_scroll_tablet"] : 1,
								],
								(int) $viewport_lg => [
									"slidesPerView"  => isset($settings["columns"]) ? (int)$settings["columns"] : 3,
									"spaceBetween"   => !empty($settings["item_gap"]["size"]) ? (int)$settings["item_gap"]["size"] : 20,
									// "slidesPerGroup" => isset($settings["slides_to_scroll"]) ? (int)$settings["slides_to_scroll"] : 1,
								]
							],

                            "navigation"            => [
								"nextEl" => "#" . $id . " .daily-button-next",
								"prevEl" => "#" . $id . " .daily-button-prev",
							],
							"pagination"            => [
								"el"             => "#" . $id . " .swiper-pagination",
								"type"           => 'bullets',
								"clickable"      => "true",
								'dynamicBullets' => false,
							],
						
						] ) )
					]
				]
			]
		);

        ?>
        <div <?php $this->print_render_attribute_string('carousel'); ?>>
        <div 
    class="swiper mySwiper daily-review-carousel-wrap 
           <?php echo esc_attr($settings['content_align'] . ' ' . $settings['item_direction']); ?>">

                <div class="swiper-wrapper">
                    <?php if ( ! empty( $slides_data ) && is_array( $slides_data ) ) : ?>
                        <?php foreach ( $slides_data as $slide ) : ?>
                            <div class="swiper-slide daily-slide-item">
                                <?php $this->render_card( $settings, $slide ); ?>
                            </div><!-- /.daily-slide-item -->
                        <?php endforeach; ?>
                    <?php endif; // end slides_data loop ?>
                </div>
            </div>

            <?php if ($settings['swiper_navigation'] == 'yes') : ?>
                    <div class="daily-nav-button-wrap">
                        <div class="daily-nav-button daily-button-prev">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-left">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 12l14 0" />
                                <path d="M5 12l6 6" />
                                <path d="M5 12l6 -6" />
                            </svg>
                        </div>
                        <div class="daily-nav-button daily-button-next">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-right">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 12l14 0" />
                                <path d="M13 18l6 -6" />
                                <path d="M13 6l6 6" />
                            </svg>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($settings['swiper_pagination'] == 'yes') : ?>
                    <div class="daily-pagination swiper-pagination"></div>
                <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render the marquee (infinite-scroll) display mode.
     *
     * @param array  $settings   Widget settings array from get_settings_for_display().
     * @param array  $slides_data Normalised slides array (same shape as swiper_slides repeater).
     * @param string $id         Unique widget DOM ID.
     */
    private function render_marquee( $settings, $slides_data, $id ) {
        // Repeat slides if they are too few, to ensure we fill the screen width and prevent loop gaps.
        $count = count( $slides_data );
        if ( $count > 0 && $count < 8 ) {
            $repeated_slides = [];
            while ( count( $repeated_slides ) < 8 ) {
                $repeated_slides = array_merge( $repeated_slides, $slides_data );
            }
            $slides_data = $repeated_slides;
        }

        $num_rows    = max( 1, min( 4, (int) ( $settings['marquee_rows'] ?? 2 ) ) );
        $speed       = max( 5, (int) ( $settings['marquee_speed'] ?? 35 ) );
        $direction   = $settings['marquee_direction'] ?? 'alternate';
        $pause_hover = ! empty( $settings['marquee_pause_hover'] ) && 'yes' === $settings['marquee_pause_hover'];
        $card_w      = (int) ( $settings['marquee_card_width']['size'] ?? 320 );
        $gap         = (int) ( $settings['marquee_gap']['size'] ?? 24 );
        $row_gap     = (int) ( $settings['marquee_row_gap']['size'] ?? 18 );
        $fade_color  = esc_attr( $settings['marquee_fade_color'] ?? 'rgba(0,0,0,0)' );

        $wrapper_classes = 'ds-review-carousel ds-marquee-mode';
        if ( $pause_hover ) {
            $wrapper_classes .= ' ds-pause-on-hover';
        }

        $inline_style = sprintf(
            '--marquee-speed:%ds; --marquee-gap:%dpx; --marquee-card-w:%dpx; --marquee-row-gap:%dpx; --ds-fade-color:%s;',
            $speed, $gap, $card_w, $row_gap, $fade_color
        );
        ?>
        <div
            class="<?php echo esc_attr( $wrapper_classes ); ?>"
            id="<?php echo esc_attr( $id ); ?>"
            style="<?php echo $inline_style; ?>"
        >
            <?php for ( $row = 0; $row < $num_rows; $row++ ) :
                // Determine scroll direction for this row
                if ( 'alternate' === $direction ) {
                    $row_dir = ( 0 === $row % 2 ) ? 'left' : 'right';
                } elseif ( 'right' === $direction ) {
                    $row_dir = 'right';
                } else {
                    $row_dir = 'left';
                }
            ?>
            <div class="ds-marquee-row" data-direction="<?php echo esc_attr( $row_dir ); ?>">
                <div class="ds-marquee-track">
                    <?php
                    // Output 4 copies of all slides.
                    // The CSS animation moves -50% so 2 copies slide off while
                    // 2 remain in view — seamless at any viewport width.
                    for ( $copy = 0; $copy < 4; $copy++ ) :
                        $aria_hidden = ( $copy > 0 ) ? ' aria-hidden="true"' : '';
                    ?>
                        <div class="ds-marquee-copy"<?php echo $aria_hidden; ?>>
                            <?php foreach ( $slides_data as $slide ) : ?>
                                <div class="daily-slide-item">
                                    <?php $this->render_card( $settings, $slide ); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endfor; ?>
                </div><!-- /.ds-marquee-track -->
            </div><!-- /.ds-marquee-row -->
            <?php endfor; ?>
        </div><!-- /.ds-review-carousel.ds-marquee-mode -->
        <?php
    }

    /**
     * Render the inner content of a single review card.
     * Used by both the Swiper and Marquee render paths — keeps card markup DRY.
     *
     * @param array $settings  Widget settings.
     * @param array $slide     Single slide data array.
     */
    private function render_card( $settings, $slide ) { ?>

        <!-- ── Top row: Stars (left) + Google G logo (right) ── -->
        <div class="daily-card-top">

            <?php if ( ! empty( $settings['show_star_rating'] ) ) : ?>
                <div class="daily-review-rating" aria-label="<?php echo (int) ( $slide['rating']['size'] ?? 5 ); ?> out of 5 stars">
                    <?php
                    $rating    = (int) ( $slide['rating']['size'] ?? 5 );
                    for ( $i = 0; $i < 5; $i++ ) :
                        if ( $i < $rating ) : ?>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        <?php else : ?>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" style="opacity:.30">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        <?php endif;
                    endfor; ?>
                </div>
            <?php endif; ?>

            <!-- Google multicolour "G" logo -->
            <div class="daily-google-badge" aria-label="Google Review">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
            </div>

        </div><!-- /.daily-card-top -->

        <!-- ── Bold headline: first 5 words of review ── -->
        <?php if ( ! empty( $settings['show_headline'] ) && 'yes' === $settings['show_headline'] && ! empty( $slide['text'] ) ) :
            $words    = preg_split( '/\s+/', trim( $slide['text'] ) );
            $headline = count( $words ) <= 5 ? $slide['text'] : implode( ' ', array_slice( $words, 0, 5 ) ) . '…';
        ?>
            <h3 class="daily-review-headline"><?php echo esc_html( $headline ); ?></h3>
        <?php endif; ?>

        <!-- ── Full review body text ── -->
        <?php if ( ! empty( $settings['show_text'] ) && ! empty( $slide['text'] ) ) : ?>
            <div class="daily-review-content">
                <p class="daily-text"><?php echo esc_html( $slide['text'] ); ?></p>
            </div>
        <?php endif; ?>

        <!-- ── Footer: avatar + name + date ── -->
        <div class="daily-card-footer">

            <?php if ( ! empty( $settings['show_avatar_image'] ) ) : ?>
                <div class="daily-image-wrap">
                    <?php if ( ! empty( $slide['avatar_image']['id'] ) ) : ?>
                        <?php echo wp_get_attachment_image(
                            $slide['avatar_image']['id'], 'thumbnail', false,
                            [ 'class' => 'daily-avatar', 'alt' => esc_attr( $slide['name'] ?? '' ) ]
                        ); ?>
                    <?php elseif ( ! empty( $slide['avatar_image']['url'] ) ) : ?>
                        <img class="daily-avatar" src="<?php echo esc_url( $slide['avatar_image']['url'] ); ?>"
                             alt="<?php echo esc_attr( $slide['name'] ?? '' ); ?>" loading="lazy" />
                    <?php else : ?>
                        <div class="daily-avatar-initials" aria-hidden="true"><?php
                            $n = trim( $slide['name'] ?? '' );
                            if ( $n ) {
                                $p = explode( ' ', $n );
                                echo esc_html( strtoupper( substr( $p[0], 0, 1 ) ) . ( isset( $p[1] ) ? strtoupper( substr( $p[1], 0, 1 ) ) : '' ) );
                            } else { echo '?'; }
                        ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="daily-avatar-info">
                <?php if ( ! empty( $settings['show_name'] ) && ! empty( $slide['name'] ) ) : ?>
                    <strong class="daily-avatar-name"><?php echo esc_html( $slide['name'] ); ?></strong>
                <?php endif; ?>
                <?php if ( ! empty( $settings['show_dagination'] ) && ! empty( $slide['dagination'] ) ) : ?>
                    <span class="daily-avatar-dagination"><?php echo esc_html( $slide['dagination'] ); ?></span>
                <?php endif; ?>
            </div>

        </div><!-- /.daily-card-footer -->
    <?php
    }

}

