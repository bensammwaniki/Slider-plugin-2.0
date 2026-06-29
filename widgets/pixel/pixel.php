<?php
namespace DailySlider\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Pixel_Widget extends Widget_Base {

    public function get_name() {
        return 'daily-slider-pixel';  // Unique name for Review Carousel widget
    }
    

    public function get_title() {
        return __( 'Pixel', 'daily-slider' );
    }

    public function get_icon() {
        return 'eicon-button';
    }

    public function get_categories() {
        return [ 'DailySlider-category' ]; // Custom category defined in the main plugin file
    }

    public function get_script_depends(): array {
        return [ 'DailySlider-pixel-scripts' ];
    }

    public function get_style_depends(): array {
		return [ 'e-swiper', 'widget-image-carousel', 'DailySlider-common-styles', 'DailySlider-pixel-styles' ];
	}

    protected function register_controls() {

        // General
        $this->start_controls_section(
            'genarel_section',
            [
                'label' => __('General', 'daily-slider'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        // switcher control
        $this->add_control(
            'show_title',
            [
                'label' => __('Show Title', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        // Add Title Tag Control
        $this->add_control(
            'title_tag',
            [
                'label' => __('Title Tag', 'daily-slider'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'h2',  // Default to h2
                'options' => [
                    'h1' => __('H1', 'daily-slider'),
                    'h2' => __('H2', 'daily-slider'),
                    'h3' => __('H3', 'daily-slider'),
                    'h4' => __('H4', 'daily-slider'),
                    'h5' => __('H5', 'daily-slider'),
                    'h6' => __('H6', 'daily-slider'),
                ],
                'condition' => [
                    'show_title' => 'yes', // Show only when show_title is yes
                ],
            ]
        );
        

        $this->add_control(
            'show_sub_title',
            [
                'label' => __('Show Sub Title', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'return_value' => 'yes',
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        // Add Heading Tag Control
        $this->add_control(
            'sub_title_tag',
            [
                'label' => __('Sub Title Tag', 'daily-slider'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'h4',  // Default to h4
                'options' => [
                    'h1' => __('H1', 'daily-slider'),
                    'h2' => __('H2', 'daily-slider'),
                    'h3' => __('H3', 'daily-slider'),
                    'h4' => __('H4', 'daily-slider'),
                    'h5' => __('H5', 'daily-slider'),
                    'h6' => __('H6', 'daily-slider'),
                ],
                'condition' => [
                    'show_sub_title' => 'yes', // Show only when show_title is yes
                ],
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
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'show_link',
            [
                'label' => __('Show Link', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Swiper Slides Section
        $this->start_controls_section(
            'swiper_slides_section',
            [
                'label' => __('Slider Items', 'daily-slider'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
    
        // Repeater for slides
        $repeater = new Repeater();
    
        $repeater->add_control(
            'title',
            [
                'label' => __('Title', 'daily-slider'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Slide Title Here', 'daily-slider'),
                'label_block' => true,
            ]
        );
    
        $repeater->add_control(
            'sub_title',
            [
                'label' => __('Sub Title', 'daily-slider'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Slide Sub Title Here', 'daily-slider'),
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

        // Add Link URL Field
        $repeater->add_control(
            'link',
            [
                'label' => __('Link', 'daily-slider'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'daily-slider'),
                'options' => [ 'url', 'is_external', 'nofollow' ],
                'default' => [
                    'url' => '#',
                    'is_external' => false,
                    'nofollow' => false,
                ],
            ]
        );
        

        // Add Link Text Field
        $repeater->add_control(
            'link_text',
            [
                'label' => __('Link Text', 'daily-slider'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Learn More', 'daily-slider'),
                'label_block' => true,
            ]
        );
        
    
        $repeater->add_control(
            'slide_media_type',
            [
                'label' => __('Media Type', 'daily-slider'),
                'type' => Controls_Manager::SELECT,
                'default' => 'image',
                'options' => [
                    'image' => __('Image', 'daily-slider'),
                    'video' => __('Video', 'daily-slider'),
                ],
            ]
        );

        $repeater->add_control(
            'slide_image',
            [
                'label' => __('Slide Image', 'daily-slider'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'slide_media_type' => 'image',
                ],
            ]
        );

        $repeater->add_control(
            'slide_video',
            [
                'label' => __('Slide Video', 'daily-slider'),
                'type' => Controls_Manager::MEDIA,
                'media_types' => [ 'video' ],
                'condition' => [
                    'slide_media_type' => 'video',
                ],
            ]
        );

        $repeater->add_control(
            'slide_video_poster',
            [
                'label' => __('Video Display Image', 'daily-slider'),
                'type' => Controls_Manager::MEDIA,
                'condition' => [
                    'slide_media_type' => 'video',
                ],
            ]
        );
    
    
        $this->add_control(
            'swiper_slides',
            [
                'label' => __('Slides', 'daily-slider'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'slide_media_type' => 'image',
                        'title' => __('Dhaka', 'daily-slider'),
                        'sub_title' => __('Capital', 'daily-slider'),
                        'text' => __('Dhaka is the capital and largest city of Bangladesh.', 'daily-slider'),
                        'link' => '#',
                        'link_text' => __('Read More', 'daily-slider'),
                        'slide_image' => ['url' => plugin_dir_url(__FILE__) . '../../assets/images/item-1.svg'],
                    ],
                    [
                        'slide_media_type' => 'image',
                        'title' => __('Rajshahi', 'daily-slider'),
                        'sub_title' => __('District', 'daily-slider'),
                        'text' => __('Rajshahi is a metropolitan city and a major urban, administrative, commercial and educational centre of Bangladesh.', 'daily-slider'),
                        'link' => '#',
                        'link_text' => __('Read More', 'daily-slider'),
                        'slide_image' => ['url' => plugin_dir_url(__FILE__) . '../../assets/images/item-2.svg'],
                    ],
                    [
                        'slide_media_type' => 'image',
                        'title' => __('Sylhet', 'daily-slider'),
                        'sub_title' => __('Division', 'daily-slider'),
                        'text' => __('Sylhet is a metropolitan city located in the northeastern region of Bangladesh.', 'daily-slider'),
                        'link' => '#',
                        'link_text' => __('Read More', 'daily-slider'),
                        'slide_image' => ['url' => plugin_dir_url(__FILE__) . '../../assets/images/item-3.svg'],
                    ],
                ],
                'title_field' => '{{{ title }}}',
            ]
        );
    
        $this->end_controls_section(); // Close swiper_slides_section
    
        // Swiper Settings Section
        $this->start_controls_section(
            'swiper_settings_section',
            [
                'label' => __('Slider Settings', 'daily-slider'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
    
        $this->add_responsive_control(
            'swiper_effect',
            [
                'label' => __('Effect', 'daily-slider'),
                'type' => Controls_Manager::SELECT,
                'default' => 'slide',
                'options' => [
                    'slide' => __('Slide', 'daily-slider'),
                    'fade' => __('Fade', 'daily-slider'),
                    'smooth_fade' => __('Smooth Fade', 'daily-slider'),
                    'cube' => __('Cube', 'daily-slider'),
                    'flip' => __('Flip', 'daily-slider'),
                    'coverflow' => __('Coverflow', 'daily-slider'),
                ],
            ]
        );

        $this->add_control(
            'swiper_fade_cross_fade',
            [
                'label' => __('Cross Fade', 'daily-slider'),
                'description' => __('Blends outgoing and incoming slides for a smoother fade transition.', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'default' => 'yes',
                'condition' => [
                    'swiper_effect' => ['fade', 'smooth_fade'],
                ],
            ]
        );
        
        $this->add_control(
            'swiper_cube_shadow',
            [
                'label' => __('Cube Shadow', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'default' => 'yes',
                'condition' => [
                    'swiper_effect' => 'cube',
                ],
            ]
        );
        
        $this->add_control(
            'swiper_slide_shadows',
            [
                'label' => __('Slide Shadows', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'default' => 'yes',
                'condition' => [
                    'swiper_effect' => 'cube',
                ],
            ]
        );
        
        $this->add_control(
            'swiper_shadow_offset',
            [
                'label' => __('Shadow Offset', 'daily-slider'),
                'type' => Controls_Manager::NUMBER,
                'default' => 20,
                'condition' => [
                    'swiper_effect' => 'cube',
                    'swiper_cube_shadow' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'swiper_shadow_scale',
            [
                'label' => __('Shadow Scale', 'daily-slider'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0.94,
                'condition' => [
                    'swiper_effect' => 'cube',
                    'swiper_cube_shadow' => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'swiper_autoplay',
            [
                'label' => __('Autoplay', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'default' => 'yes',
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
            'swiper_pause_on_hover',
            [
                'label' => __('Pause on Hover', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'default' => 'no',
                'condition' => [
                    'swiper_autoplay' => 'yes',
                ],
            ]
        );
    
        $this->add_responsive_control(
            'swiper_loop',
            [
                'label' => __('Infinite loop', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'default' => 'yes',
            ]
        );
    
        $this->add_responsive_control(
            'swiper_speed',
            [
                'label' => __('Transition Speed (ms)', 'daily-slider'),
                'type' => Controls_Manager::NUMBER,
                'default' => 800,
            ]
        );

        $this->add_control(
            'swiper_transition_timing',
            [
                'label' => __('Transition Timing Function', 'daily-slider'),
                'type' => Controls_Manager::SELECT,
                'default' => 'ease',
                'options' => [
                    'linear' => __('Linear', 'daily-slider'),
                    'ease' => __('Ease', 'daily-slider'),
                    'ease-in' => __('Ease In', 'daily-slider'),
                    'ease-out' => __('Ease Out', 'daily-slider'),
                    'ease-in-out' => __('Ease In Out', 'daily-slider'),
                ],
            ]
        );
    
        $this->add_responsive_control(
            'swiper_navigation',
            [
                'label' => __('Navigation', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'default' => 'yes',
            ]
        );

        $this->add_responsive_control(
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
                'label' => __('Slider Container', 'daily-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // height control

        $this->add_responsive_control(
            'height',
            [
                'label' => __('Slider Height', 'daily-slider'),
                'label_block' => true,
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%', 'vh'],
                'selectors' => [
                    '{{WRAPPER}} .daily-pixel-slider-wrap' => '--daily-pixel-height: {{SIZE}}{{UNIT}}; height: var(--daily-pixel-height);',
                    '{{WRAPPER}} .daily-pixel-slider-wrap .swiper-wrapper, {{WRAPPER}} .daily-pixel-slider-wrap .swiper-slide' => 'height: var(--daily-pixel-height);',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'overlay_section',
            [
                'label' => __('Overlay', 'daily-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'overlay_color',
            [
                'label' => __('Overlay Color', 'daily-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .daily-overlay' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'overlay_gradient',
                'label' => __('Overlay Gradient', 'daily-slider'),
                'types' => ['gradient'],
                'selector' => '{{WRAPPER}} .daily-overlay',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'title_style_section',
            [
                'label' => __('Title', 'daily-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );        
    
        $this->add_control(
            'title_color',
            [
                'label' => __('Title Color', 'daily-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .daily-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'text_stroke',
                'selector' => '{{WRAPPER}} .daily-title',
            ]
        );
        
        
    
        // Typography control
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => __('Typography', 'daily-slider'),
                'selector' => '{{WRAPPER}} .daily-title',
            ]
        );
    
        $this->end_controls_section(); // Close title_style_section

$this->start_controls_section(
    'content_section',
    [
        'label' => __('Content Box', 'daily-slider'),
        'tab' => Controls_Manager::TAB_STYLE,
    ]
);

$this->add_responsive_control(
    'content_max_width',
    [
        'label' => __('Max Width', 'daily-slider'),
        'type' => Controls_Manager::SLIDER,
        'size_units' => ['px', '%'],
        'selectors' => [
            '{{WRAPPER}} .daily-content-inner' => 'max-width: {{SIZE}}{{UNIT}};',
        ],
        'default' => [
            'unit' => 'px',
            'size' => 600,
        ],
    ]
);


$this->add_responsive_control(
    'content_alignment',
    [
        'label' => __('Alignment', 'daily-slider'),
        'type' => \Elementor\Controls_Manager::CHOOSE,
        'options' => [
            'flex-start' => [
                'title' => __('Left', 'daily-slider'),
                'icon' => 'eicon-text-align-left',
            ],
            'center' => [
                'title' => __('Center', 'daily-slider'),
                'icon' => 'eicon-text-align-center',
            ],
            'flex-end' => [
                'title' => __('Right', 'daily-slider'),
                'icon' => 'eicon-text-align-right',
            ],
            'justify' => [
                'title' => __('Justify', 'daily-slider'),
                'icon' => 'eicon-text-align-justify',
            ],
        ],
        'default' => 'flex-start',
        'selectors_dictionary' => [
            'flex-start' => 'justify-content: flex-start; text-align: left;',
            'center' => 'justify-content: center; text-align: center;',
            'flex-end' => 'justify-content: flex-end; text-align: right;',
            'justify' => 'justify-content: flex-start; text-align: justify;',
        ],
        'selectors' => [
            '{{WRAPPER}} .daily-content-wrap, {{WRAPPER}} .daily-content-inner' => '{{VALUE}}',
        ],
    ]
);


$this->add_group_control(
    \Elementor\Group_Control_Background::get_type(),
    [
        'name' => 'content_background',
        'types' => ['classic', 'gradient'],
        'selector' => '{{WRAPPER}} .daily-content-inner',
    ]
);

$this->add_responsive_control(
    'content_padding',
    [
        'label' => __('Padding', 'daily-slider'),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => ['px', '%'],
        'selectors' => [
            '{{WRAPPER}} .daily-content-inner' =>
                'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
    ]
);

$this->add_responsive_control(
    'content_margin',
    [
        'label' => __('Margin', 'daily-slider'),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => ['px', '%'],
        'selectors' => [
            '{{WRAPPER}} .daily-content-inner' =>
                'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
    ]
);

$this->add_group_control(
    \Elementor\Group_Control_Border::get_type(),
    [
        'name' => 'content_border',
        'selector' => '{{WRAPPER}} .daily-content-inner',
    ]
);

$this->add_responsive_control(
    'content_border_radius',
    [
        'label' => __('Border Radius', 'daily-slider'),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => ['px', '%'],
        'selectors' => [
            '{{WRAPPER}} .daily-content-inner' =>
                'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
    ]
);

$this->add_group_control(
    \Elementor\Group_Control_Box_Shadow::get_type(),
    [
        'name' => 'content_box_shadow',
        'selector' => '{{WRAPPER}} .daily-content-inner',
    ]
);

$this->end_controls_section();// Close sub_title_style_section
    
		
		
        // Sub Title Style Section
        $this->start_controls_section(
            'sub_title_style_section',
            [
                'label' => __('Subtitle', 'daily-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_sub_title' => 'yes',
                ],
            ]

        );
    
        $this->add_control(
            'sub_title_color',
            [
                'label' => __('Subtitle Color', 'daily-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .daily-sub-title' => 'color: {{VALUE}}',
                ],
            ]
        );
    
        // Typography control for subtitle
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'sub_title_typography',
                'label' => __('Typography', 'daily-slider'),
                'selector' => '{{WRAPPER}} .daily-sub-title',
                'separator' => 'after',
            ]
        );
    
        // Margin control for subtitle
        $this->add_responsive_control(
            'sub_title_margin',
            [
                'label' => __('Subtitle Margin', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-sub-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
    
        $this->end_controls_section(); // Close sub_title_style_section

        // Sub Title Style Section
        $this->start_controls_section(
            'description_section',
            [
                'label' => __('Description', 'daily-slider'),
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
                'label' => __('Description Color', 'daily-slider'),
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
                'label' => __('Description Margin', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
    
        $this->end_controls_section(); // Close sub_title_style_section

        // link button 
        $this->start_controls_section(
            'link_button_section',
            [
                'label' => __('Link', 'daily-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_link' => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs(
            'style_tabs'
        );
        
        $this->start_controls_tab(
            'style_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'daily-slider' ),
            ]
        );

        
        // link button color
        $this->add_control(
            'link_button_color',
            [
                'label' => __('Link Color', 'daily-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .daily-link' => 'color: {{VALUE}}',
                ],
            ]
        );

        // typography control for link button
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'link_button_typography',
                'label' => __('Typography', 'daily-slider'),
                'selector' => '{{WRAPPER}} .daily-link',
                'separator' => 'after',
            ]
        );

        // Background control for link button
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'link_button_background',
                'label' => __('Background', 'daily-slider'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .daily-link',
            ]
        );

        // padding control for link button

        $this->add_responsive_control(
            'link_button_padding',
            [
                'label' => __('Link Padding', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Margin control for link button
        $this->add_responsive_control(
            'link_button_margin',
            [
                'label' => __('Link Margin', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'after',
            ]
        );

        // border control for link button
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'link_button_border',
                'label' => __('Link Border', 'daily-slider'),
                'selector' => '{{WRAPPER}} .daily-link',
            ]

        );

        // border radius control for link button

        $this->add_responsive_control(
            'link_button_border_radius',
            [
                'label' => __('Link Border Radius', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'style_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'daily-slider' ),
            ]
        );

        // link button color
        $this->add_control(
            'link_button_hover_color',
            [
                'label' => __('Link Hover Color', 'daily-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .daily-link:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        // Background control for link button
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'link_button_hover_background',
                'label' => __('Background', 'daily-slider'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .daily-link:hover',
            ]
        );

        // border hover color

        $this->add_control(
            'link_button_hover_border_color',
            [
                'label' => __('Link Hover Border Color', 'daily-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .daily-link:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section(); // Close sub_title_style_section

        // navigation style
        $this->start_controls_section(
            'navigation_section',
            [
                'label' => __('Navigation Arrows', 'daily-slider'),
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
                'label' => esc_html__( 'Default', 'daily-slider' ),
            ]
        );

        // navigation color
        $this->add_control(
            'nav_color',
            [
                'label' => __('Arrow Color', 'daily-slider'),
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
                'label' => __('Arrow Background', 'daily-slider'),
                'types' => ['classic', 'gradient'],
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .daily-nav-button',
            ]
        );

        // padding control for navigation

        $this->add_responsive_control(
            'nav_padding',
            [
                'label' => __('Arrow Padding', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-nav-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'nav_wrap_vertical_position',
            [
                'label' => __('Arrow Vertical Position', 'daily-slider'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'top' => [
                        'title' => __('Top', 'daily-slider'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'middle' => [
                        'title' => __('Middle', 'daily-slider'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'bottom' => [
                        'title' => __('Bottom', 'daily-slider'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'default' => 'middle',
                'selectors_dictionary' => [
                    'top' => 'top: var(--daily-pixel-spacing); bottom: auto;',
                    'middle' => 'top: 50%; bottom: auto;',
                    'bottom' => 'bottom: var(--daily-pixel-spacing); top: auto;',
                ],
                'selectors' => [
                    '{{WRAPPER}} .daily-nav-button-wrap' => '{{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'nav_wrap_offset',
            [
                'label' => __('Arrow Position Offset', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-nav-button-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'nav_prev_margin',
            [
                'label' => __('Previous Arrow Margin', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-button-prev' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'nav_next_margin',
            [
                'label' => __('Next Arrow Margin', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-button-next' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        

        // border control for navigation

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'nav_border',
                'label' => __('Arrow Border', 'daily-slider'),
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
                'label' => __('Arrow Border Radius', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-nav-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
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
                'label' => __('Arrow Color', 'daily-slider'),
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
                'label' => __('Arrow Background', 'daily-slider'),
                'types' => ['classic', 'gradient'],
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .daily-nav-button:hover',
            ]
        );

        // border hover color

        $this->add_control(
            'nav_hover_border_color',
            [
                'label' => __('Arrow Border Color', 'daily-slider'),
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
        $this->end_controls_section(); // Close sub_title_style_section

        // pagination style
        $this->start_controls_section(
            'pagination_section',
            [
                'label' => __('Pagination Dots', 'daily-slider'),
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
                'label' => esc_html__( 'Inactive', 'daily-slider' ),
            ]
        );

        // width control for pagination

        $this->add_responsive_control(
            'pagination_width',
            [
                'label' => __('Dot Width', 'daily-slider'),
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
                'label' => __('Dot Height', 'daily-slider'),
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
                'label' => __('Dot Background', 'daily-slider'),
                'types' => ['classic', 'gradient'],
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .daily-pagination .swiper-pagination-bullet',
            ]
        );

        // border 

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'pagination_border',
                'label' => __('Dot Border', 'daily-slider'),
                'selector' => '{{WRAPPER}} .daily-pagination .swiper-pagination-bullet',
            ]

        );

        // border radius control for pagination

        $this->add_responsive_control(
            'pagination_border_radius',
            [
                'label' => __('Dot Border Radius', 'daily-slider'),
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
                'label' => __('Pagination Offset', 'daily-slider'),
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
                'label' => __('Dot Gap', 'daily-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-pagination' => 'gap: {{SIZE}}{{UNIT}};',
                ],
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
                'label' => __('Active Width', 'daily-slider'),
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
                'label' => __('Active Height', 'daily-slider'),
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
                'label' => __('Active Background', 'daily-slider'),
                'types' => ['classic', 'gradient'],
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .daily-pagination .swiper-pagination-bullet-active',
            ]
        );

        // border

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'pagination_active_border',
                'label' => __('Active Border', 'daily-slider'),
                'selector' => '{{WRAPPER}} .daily-pagination .swiper-pagination-bullet-active',
            ]

        );

        // border radius control for pagination

        $this->add_responsive_control(
            'pagination_active_border_radius',
            [
                'label' => __('Active Border Radius', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-pagination .swiper-pagination-bullet-active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
        
    }
    

    protected function render() {
        $settings = $this->get_settings_for_display();

        $id = 'ds-' . $this->get_id();

        // Viewport breakpoints: cache per request to avoid repeated get_option (reduces DB load)
        static $viewport_md = null;
        if ($viewport_md === null) {
            $vp_md = get_option('elementor_viewport_md');
            $viewport_md = !empty($vp_md) ? (int) $vp_md - 1 : 767;
        }
        $normalize_effect = static function ($effect) {
            return $effect === 'smooth_fade' ? 'fade' : $effect;
        };

        $desktop_effect_raw = $settings['swiper_effect'] ?? 'slide';
        $desktop_effect = $normalize_effect($desktop_effect_raw);
        $tablet_effect_raw = $settings['swiper_effect_tablet'] ?? '';
        $mobile_effect_raw = $settings['swiper_effect_mobile'] ?? '';

        $has_fade_effect = in_array($desktop_effect_raw, ['fade', 'smooth_fade'], true)
            || in_array($tablet_effect_raw, ['fade', 'smooth_fade'], true)
            || in_array($mobile_effect_raw, ['fade', 'smooth_fade'], true);
        $has_smooth_fade_effect = in_array($desktop_effect_raw, ['smooth_fade'], true)
            || in_array($tablet_effect_raw, ['smooth_fade'], true)
            || in_array($mobile_effect_raw, ['smooth_fade'], true);
        $cross_fade_enabled = !empty($settings['swiper_fade_cross_fade']) && $settings['swiper_fade_cross_fade'] === 'yes';
        $allowed_transition_timing = ['linear', 'ease', 'ease-in', 'ease-out', 'ease-in-out'];
        $transition_timing = isset($settings['swiper_transition_timing']) ? sanitize_text_field($settings['swiper_transition_timing']) : 'ease';
        if (!in_array($transition_timing, $allowed_transition_timing, true)) {
            $transition_timing = 'ease';
        }
        
        // Prepare Swiper settings array with responsive values
        $swiper_settings = [
            'effect' => esc_attr($desktop_effect),
            'loop' => !empty($settings['swiper_loop']) && $settings['swiper_loop'] === 'yes',
            'speed' => absint($settings['swiper_speed'] ?? 800),
            'parallax' => true,
            'pauseOnHover' => !empty($settings['swiper_pause_on_hover']) && $settings['swiper_pause_on_hover'] === 'yes',
            'transitionTimingFunction' => $transition_timing,
            // Keep swipe/drag behavior consistent between Elementor preview and the published page.
            'touchEventsTarget' => 'container',
            'allowTouchMove' => true,
            'simulateTouch' => true,
            'grabCursor' => true,
            'watchOverflow' => true,
            'observer' => true,
            'observeParents' => true,
            'touchStartPreventDefault' => false,
        ];

        if ($has_fade_effect) {
            $swiper_settings['fadeEffect'] = [
                'crossFade' => $has_smooth_fade_effect || $cross_fade_enabled,
            ];
        }
        
        // Add responsive breakpoints
        $breakpoints = [];
        
        // Tablet breakpoint
        if (!empty($settings['swiper_effect_tablet']) || !empty($settings['swiper_loop_tablet']) || !empty($settings['swiper_speed_tablet'])) {
            $breakpoints[$viewport_md] = [];
            if (!empty($settings['swiper_effect_tablet'])) {
                $breakpoints[$viewport_md]['effect'] = esc_attr($normalize_effect($settings['swiper_effect_tablet']));
            }
            if (!empty($settings['swiper_loop_tablet'])) {
                $breakpoints[$viewport_md]['loop'] = $settings['swiper_loop_tablet'] === 'yes';
            }
            if (!empty($settings['swiper_speed_tablet'])) {
                $breakpoints[$viewport_md]['speed'] = absint($settings['swiper_speed_tablet']);
            }
        }
        
        // Mobile breakpoint
        if (!empty($settings['swiper_effect_mobile']) || !empty($settings['swiper_loop_mobile']) || !empty($settings['swiper_speed_mobile'])) {
            $breakpoints[0] = [];
            if (!empty($settings['swiper_effect_mobile'])) {
                $breakpoints[0]['effect'] = esc_attr($normalize_effect($settings['swiper_effect_mobile']));
            }
            if (!empty($settings['swiper_loop_mobile'])) {
                $breakpoints[0]['loop'] = $settings['swiper_loop_mobile'] === 'yes';
            }
            if (!empty($settings['swiper_speed_mobile'])) {
                $breakpoints[0]['speed'] = absint($settings['swiper_speed_mobile']);
            }
        }
        
        if (!empty($breakpoints)) {
            $swiper_settings['breakpoints'] = $breakpoints;
        }
        
        // Add autoplay settings if enabled (desktop default)
        $autoplay_desktop = !empty($settings['swiper_autoplay']) && $settings['swiper_autoplay'] === 'yes';
        if ($autoplay_desktop) {
            $swiper_settings['autoplay'] = [
                'delay' => absint($settings['swiper_autoplay_delay'] ?? 5000),
                'disableOnInteraction' => false,
            ];
        }
        
        // Add cube effect settings if applicable
        $effect = $desktop_effect;
        if ($effect === 'cube') {
            $swiper_settings['cubeEffect'] = [
                'shadow' => !empty($settings['swiper_cube_shadow']) && $settings['swiper_cube_shadow'] === 'yes',
                'slideShadows' => !empty($settings['swiper_slide_shadows']) && $settings['swiper_slide_shadows'] === 'yes',
                'shadowOffset' => absint($settings['swiper_shadow_offset'] ?? 20),
                'shadowScale' => floatval($settings['swiper_shadow_scale'] ?? 0.94),
            ];
        }
        
        // Helper to read responsive switcher values created by add_responsive_control
        // Elementor stores responsive values as: key, key_tablet, key_mobile
        $get_resp_switch = function(string $key, string $device = 'desktop') use ($settings) : ?bool {
            $map = [
                'desktop' => $key,
                'tablet'  => $key . '_tablet',
                'mobile'  => $key . '_mobile',
            ];
            $device_key = $map[$device] ?? $key;

            if (array_key_exists($device_key, $settings)) {
                return !empty($settings[$device_key]) && $settings[$device_key] === 'yes';
            }

            // If device-specific key isn't present, fall back to desktop.
            if ($device !== 'desktop' && array_key_exists($key, $settings)) {
                return !empty($settings[$key]) && $settings[$key] === 'yes';
            }

            return null;
        };

        // Determine navigation/pagination defaults per breakpoint
        $nav_desktop = $get_resp_switch('swiper_navigation', 'desktop');
        $nav_tablet = $get_resp_switch('swiper_navigation', 'tablet');
        $nav_mobile = $get_resp_switch('swiper_navigation', 'mobile');

        $pag_desktop = $get_resp_switch('swiper_pagination', 'desktop');
        $pag_tablet = $get_resp_switch('swiper_pagination', 'tablet');
        $pag_mobile = $get_resp_switch('swiper_pagination', 'mobile');

        // Fallbacks: if a responsive value is not explicitly set (null), use sensible defaults
        // Default behavior: navigation and pagination default to enabled unless explicitly turned off
        if ($nav_desktop === null) $nav_desktop = true;
        if ($nav_tablet === null) $nav_tablet = $nav_desktop;
        if ($nav_mobile === null) $nav_mobile = $nav_tablet;

        if ($pag_desktop === null) $pag_desktop = true;
        if ($pag_tablet === null) $pag_tablet = $pag_desktop;
        if ($pag_mobile === null) $pag_mobile = $pag_tablet;

        // Top-level (desktop) navigation/pagination
        $swiper_settings['navigation'] = $nav_desktop ? [
            'nextEl' => "#" . $id . " .daily-button-next",
            'prevEl' => "#" . $id . " .daily-button-prev",
        ] : false;

        $swiper_settings['pagination'] = $pag_desktop ? [
            'el' => "#" . $id . " .swiper-pagination",
            'type' => 'bullets',
            'clickable' => true,
            'dynamicBullets' => false,
        ] : false;

        // Add responsive breakpoints for nav/pagination/autoplay
        if (!isset($swiper_settings['breakpoints'])) {
            $swiper_settings['breakpoints'] = [];
        }

        // Tablet breakpoint
        $tablet_bp = [];
        if (isset($breakpoints[$viewport_md])) {
            $tablet_bp = $breakpoints[$viewport_md];
        }
        // navigation for tablet
        if (isset($nav_tablet)) {
            $tablet_bp['navigation'] = $nav_tablet ? [
                'nextEl' => "#" . $id . " .daily-button-next",
                'prevEl' => "#" . $id . " .daily-button-prev",
            ] : false;
        }
        // pagination for tablet
        if (isset($pag_tablet)) {
            $tablet_bp['pagination'] = $pag_tablet ? [
                'el' => "#" . $id . " .swiper-pagination",
                'type' => 'bullets',
                'clickable' => true,
                'dynamicBullets' => false,
            ] : false;
        }
        // autoplay for tablet
        $autoplay_tablet = !empty($settings['swiper_autoplay_tablet']) && $settings['swiper_autoplay_tablet'] === 'yes';
        if ($autoplay_tablet) {
            $tablet_bp['autoplay'] = [ 'delay' => absint($settings['swiper_autoplay_delay'] ?? 5000), 'disableOnInteraction' => false ];
        } elseif (array_key_exists('swiper_autoplay_tablet', $settings) && !$autoplay_tablet) {
            $tablet_bp['autoplay'] = false;
        }

        if (!empty($tablet_bp)) {
            $swiper_settings['breakpoints'][$viewport_md] = $tablet_bp;
        }

        // Mobile breakpoint (0)
        $mobile_bp = [];
        if (isset($breakpoints[0])) {
            $mobile_bp = $breakpoints[0];
        }
        // Honor the responsive mobile navigation setting in both editor and live views.
        if (isset($nav_mobile)) {
            $mobile_bp['navigation'] = $nav_mobile ? [
                'nextEl' => "#" . $id . " .daily-button-next",
                'prevEl' => "#" . $id . " .daily-button-prev",
            ] : false;
        }
        // Pagination for mobile
        if (isset($pag_mobile)) {
            $mobile_bp['pagination'] = $pag_mobile ? [
                'el' => "#" . $id . " .swiper-pagination",
                'type' => 'bullets',
                'clickable' => true,
                'dynamicBullets' => false,
            ] : false;
        }
        // autoplay for mobile
        $autoplay_mobile = !empty($settings['swiper_autoplay_mobile']) && $settings['swiper_autoplay_mobile'] === 'yes';
        if ($autoplay_mobile) {
            $mobile_bp['autoplay'] = [ 'delay' => absint($settings['swiper_autoplay_delay'] ?? 5000), 'disableOnInteraction' => false ];
        } elseif (array_key_exists('swiper_autoplay_mobile', $settings) && !$autoplay_mobile) {
            $mobile_bp['autoplay'] = false;
        }
        // Ensure touch is enabled on mobile
        $mobile_bp['touchEventsTarget'] = 'container';
        $mobile_bp['allowTouchMove'] = true;
        $mobile_bp['simulateTouch'] = true;
        $mobile_bp['grabCursor'] = true;
        $mobile_bp['touchStartPreventDefault'] = false;

        if (!empty($mobile_bp)) {
            $swiper_settings['breakpoints'][0] = $mobile_bp;
        }
        
        $this->add_render_attribute(
            [
                'carousel' => [
                    'class' => [
                        'ds-pixel-slider',
                        // navigation visibility classes
                        ($nav_desktop ? '' : 'nav-off-desktop'),
                        ($nav_tablet ? '' : 'nav-off-tablet'),
                        ($nav_mobile ? '' : 'nav-off-mobile'),
                        // pagination visibility classes
                        ($pag_desktop ? '' : 'pag-off-desktop'),
                        ($pag_tablet ? '' : 'pag-off-tablet'),
                        ($pag_mobile ? '' : 'pag-off-mobile'),
                    ],
                    'id' => $id,
                    'data-settings' => wp_json_encode($swiper_settings)
                ]
            ]
        );

        ?>











<div <?php $this->print_render_attribute_string('carousel'); ?>>
    <div class="swiper mySwiper daily-pixel-slider-wrap">

        <div class="swiper-wrapper">
            <?php if (!empty($settings['swiper_slides']) && is_array($settings['swiper_slides'])) : ?>
                <?php foreach ($settings['swiper_slides'] as $slide) : ?>
                    <div class="swiper-slide daily-slide-item">

                        <!-- IMAGE -->
                        <div class="daily-image-wrap">
                            <?php
                            $slide_media_type = $slide['slide_media_type'] ?? 'image';
                            $video_url = '';
                            if (!empty($slide['slide_video']) && is_array($slide['slide_video'])) {
                                if (!empty($slide['slide_video']['id'])) {
                                    $video_url = wp_get_attachment_url((int) $slide['slide_video']['id']);
                                } elseif (!empty($slide['slide_video']['url'])) {
                                    $video_url = $slide['slide_video']['url'];
                                }
                            }
                            $poster_url = '';
                            if (!empty($slide['slide_video_poster']) && is_array($slide['slide_video_poster'])) {
                                if (!empty($slide['slide_video_poster']['id'])) {
                                    $poster_url = wp_get_attachment_url((int) $slide['slide_video_poster']['id']);
                                } elseif (!empty($slide['slide_video_poster']['url'])) {
                                    $poster_url = $slide['slide_video_poster']['url'];
                                }
                            }
                            ?>
                            <?php if ($slide_media_type === 'video' && !empty($video_url)) : ?>
                                <video
                                    class="daily-video"
                                    autoplay
                                    muted
                                    loop
                                    playsinline
                                    preload="metadata"
                                    <?php if (!empty($poster_url)) : ?>
                                        poster="<?php echo esc_url($poster_url); ?>"
                                    <?php endif; ?>
                                >
                                    <source src="<?php echo esc_url($video_url); ?>">
                                </video>
                            <?php else : ?>
                                <?php if (!empty($slide['slide_image']['id'])) : ?>
                                    <?php
                                    echo wp_get_attachment_image(
                                        $slide['slide_image']['id'],
                                        'full',
                                        false,
                                        [
                                            'class' => 'daily-img',
                                            'alt'   => esc_attr($slide['title'] ?? __('Slide', 'daily-slider')),
                                        ]
                                    );
                                    ?>
                                <?php else : ?>
                                    <img
                                        class="daily-img"
                                        src="<?php echo esc_url($slide['slide_image']['url']); ?>"
                                        alt="<?php echo esc_attr($slide['title'] ?? __('Slide', 'daily-slider')); ?>"
                                    />
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <!-- OVERLAY -->
                        <div class="daily-overlay"></div>

                        <!-- CONTENT -->
                        <div class="daily-content-wrap">
                            <div class="daily-content-inner">

                                <?php if (!empty($settings['show_title']) && !empty($slide['title'])) :
                                    $title_tag = !empty($settings['title_tag']) ? $settings['title_tag'] : 'h2';
                                ?>
                                    <<?php echo esc_html($title_tag); ?>
                                        class="daily-title"
                                        data-swiper-parallax="-300">
                                        <?php echo esc_html($slide['title']); ?>
                                    </<?php echo esc_html($title_tag); ?>>
                                <?php endif; ?>

                                <?php if (!empty($settings['show_sub_title']) && !empty($slide['sub_title'])) :
                                    $sub_title_tag = !empty($settings['sub_title_tag']) ? $settings['sub_title_tag'] : 'h4';
                                ?>
                                    <<?php echo esc_html($sub_title_tag); ?>
                                        class="daily-sub-title"
                                        data-swiper-parallax="-250">
                                        <?php echo esc_html($slide['sub_title']); ?>
                                    </<?php echo esc_html($sub_title_tag); ?>>
                                <?php endif; ?>

                                <?php if (!empty($settings['show_text']) && !empty($slide['text'])) : ?>
                                    <p class="daily-text" data-swiper-parallax="-200">
                                        <?php echo esc_html($slide['text']); ?>
                                    </p>
                                <?php endif; ?>

                                <?php
                                $raw_link = $slide['link'] ?? null;
                                $link_url_raw = is_array($raw_link) ? ($raw_link['url'] ?? '') : (is_string($raw_link) ? $raw_link : '');
                                ?>
                                <?php if (!empty($settings['show_link']) && !empty($link_url_raw)) :
                                    $link_url = esc_url($link_url_raw);
                                    $link_target = (is_array($raw_link) && !empty($raw_link['is_external'])) ? ' target="_blank"' : '';
                                    $link_nofollow = (is_array($raw_link) && !empty($raw_link['nofollow'])) ? ' rel="nofollow"' : '';
                                    $link_text = !empty($slide['link_text'])
                                        ? esc_html($slide['link_text'])
                                        : esc_html__('Learn More', 'daily-slider');
                                ?>
                                    <div class="daily-link-wrap" data-swiper-parallax="-150">
                                        <a class="daily-link"
                                           href="<?php echo $link_url; ?>"
                                           <?php echo $link_target . $link_nofollow; ?>>
                                            <?php echo $link_text; ?>
                                        </a>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                       <!-- CONTENT -->

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- NAVIGATION -->
        <div class="daily-nav-button-wrap">
            <div class="daily-nav-button daily-button-prev">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 12l14 0"/>
                    <path d="M5 12l6 6"/>
                    <path d="M5 12l6 -6"/>
                </svg>
            </div>

            <div class="daily-nav-button daily-button-next">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 12l14 0"/>
                    <path d="M13 18l6 -6"/>
                    <path d="M13 6l6 6"/>
                </svg>
            </div>
        </div>

        <!-- PAGINATION -->
        <div class="daily-pagination swiper-pagination"></div>

    </div>
</div>
<?php




















    }
    
    
}
