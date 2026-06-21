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
                'label'       => __( 'Google Place ID', 'daily-slider' ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => 'ChIJN1t_tDeuEmsRUsoyG83frY4',
                'description' => __( 'Find your Place ID at: developers.google.com/maps/documentation/places/web-service/place-id', 'daily-slider' ),
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
                'label' => __('Genarel', 'daily-slider'),
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
    
        // Swiper Settings Section
        $this->start_controls_section(
            'swiper_settings_section',
            [
                'label' => __('Carousel Settings', 'daily-slider'),
                'tab' => Controls_Manager::TAB_CONTENT,
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
                // 'separator' => 'before',
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
                // 'separator' => 'before',
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
            ]
        );
    
        $this->add_control(
            'swiper_autoplay_delay',
            [
                'label' => __('Autoplay Delay (ms)', 'daily-slider'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3000,
                'condition' => [
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
            ]
        );
    
        $this->add_control(
            'swiper_speed',
            [
                'label' => __('Transition Speed (ms)', 'daily-slider'),
                'type' => Controls_Manager::NUMBER,
                'default' => 600,
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
            ]
        );
    
        $this->end_controls_section(); // Close swiper_settings_section
    
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
                         '<strong>⚠️ Google Reviews:</strong> Please enter your <em>API Key</em> and <em>Place ID</em> in the widget settings to load reviews.</div>';
                }
                return;
            }

            $slides_data = DailySlider_Google_Reviews_Bridge::get_reviews(
                $place_id,
                $api_key,
                $limit,
                $min_rating
            );

            if ( empty( $slides_data ) ) {
                if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                    echo '<div style="padding:20px;background:#f8d7da;border:1px solid #f5c6cb;border-radius:6px;font-size:13px;">'.
                         '<strong>⚠️ Google Reviews:</strong> No reviews found matching your filters (min rating: ' . (int) $min_rating . '★). '.
                         'Check your API Key, Place ID, and make sure the Places API is enabled.</div>';
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
                        <?php
                        // Pre-compute emoji pool — cycles through cards
                        $emoji_pool = [ '😍', '❤️', '😊', '🤩', '👏', '🙌', '💯', '⭐' ];
                        $slide_index = 0;
                        ?>
                        <?php foreach ( $slides_data as $slide ) :
                            $emoji = $emoji_pool[ $slide_index % count( $emoji_pool ) ];
                            $slide_index++;
                        ?>
                            <div class="swiper-slide daily-slide-item">

                                <!-- ── Card header: avatar + name (left) | emoji badge (right) ── -->
                                <div class="daily-card-header">
                                    <div class="daily-img-content-wrap">
                                        <?php if ( ! empty( $settings['show_avatar_image'] ) ) : ?>
                                            <div class="daily-image-wrap">
                                                <?php if ( ! empty( $slide['avatar_image']['id'] ) ) : ?>
                                                    <?php echo wp_get_attachment_image(
                                                        $slide['avatar_image']['id'],
                                                        'thumbnail',
                                                        false,
                                                        [
                                                            'class' => 'daily-avatar',
                                                            'alt'   => esc_attr( $slide['name'] ?? __( 'Avatar', 'daily-slider' ) ),
                                                        ]
                                                    ); ?>
                                                <?php elseif ( ! empty( $slide['avatar_image']['url'] ) ) : ?>
                                                    <img
                                                        class="daily-avatar"
                                                        src="<?php echo esc_url( $slide['avatar_image']['url'] ); ?>"
                                                        alt="<?php echo esc_attr( $slide['name'] ?? __( 'Avatar', 'daily-slider' ) ); ?>"
                                                        loading="lazy"
                                                    />
                                                <?php else : ?>
                                                    <!-- Initials fallback when no avatar image is available -->
                                                    <div class="daily-avatar-initials" aria-hidden="true">
                                                        <?php
                                                        $initials = '';
                                                        if ( ! empty( $slide['name'] ) ) {
                                                            $parts = explode( ' ', trim( $slide['name'] ) );
                                                            $initials = strtoupper( substr( $parts[0], 0, 1 ) );
                                                            if ( isset( $parts[1] ) ) {
                                                                $initials .= strtoupper( substr( $parts[1], 0, 1 ) );
                                                            }
                                                        }
                                                        echo esc_html( $initials ?: '?' );
                                                        ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="daily-avatar-content">
                                            <?php if ( ! empty( $settings['show_name'] ) && ! empty( $slide['name'] ) ) : ?>
                                                <h3 class="daily-avatar-name"><?php echo esc_html( $slide['name'] ); ?></h3>
                                            <?php endif; ?>
                                        </div>
                                    </div><!-- /.daily-img-content-wrap -->

                                    <!-- Emoji reaction badge -->
                                    <div class="daily-emoji-badge" aria-hidden="true"><?php echo $emoji; ?></div>
                                </div><!-- /.daily-card-header -->

                                <!-- ── Stars ─────────────────────────────────────────────────── -->
                                <?php if ( ! empty( $settings['show_star_rating'] ) ) : ?>
                                    <div class="daily-review-rating">
                                        <?php
                                        $rating     = (int) ( $slide['rating']['size'] ?? 5 );
                                        $max_stars  = 5;
                                        for ( $i = 0; $i < $max_stars; $i++ ) :
                                            // Filled star for earned rating, outline for remaining
                                            if ( $i < $rating ) :
                                        ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        <?php else : ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        <?php endif; endfor; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- ── Review body text ──────────────────────────────────────── -->
                                <?php if ( ! empty( $settings['show_text'] ) && ! empty( $slide['text'] ) ) : ?>
                                    <div class="daily-review-content">
                                        <p class="daily-text"><?php echo esc_html( $slide['text'] ); ?></p>
                                    </div>
                                <?php endif; ?>

                                <!-- ── Footer: role / company ──────────────────────────────── -->
                                <?php if ( ! empty( $settings['show_dagination'] ) && ! empty( $slide['dagination'] ) ) : ?>
                                    <div class="daily-card-footer">
                                        <span class="daily-avatar-dagination">
                                            <?php echo esc_html( $slide['dagination'] ); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

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
    
}
