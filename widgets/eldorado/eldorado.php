<?php
namespace DailySlider\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Plugin;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Eldorado_Widget extends Widget_Base {

    public function get_name() {
        return 'daily-slider-eldorado';  // Unique name for Eldorado widget
    }
    
    public function get_title() {
        return __('Eldorado', 'daily-slider');
    }

    public function get_icon() {
        return 'eicon-button';
    }

    public function get_categories() {
        return ['DailySlider-category']; // Custom category defined in the main plugin file
    }

    public function get_script_depends(): array {
        return [ 'DailySlider-eldorado-scripts' ];
    }

    public function get_style_depends(): array {
		return [ 'e-swiper', 'widget-image-carousel', 'DailySlider-common-styles', 'DailySlider-eldorado-styles' ];
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

        $this->add_control(
            'general_info',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => esc_html__( 'Control what appears inside each slide card. These settings affect visibility and alignment for image, title, and subtitle.', 'daily-slider' ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
            ]
        );

        $this->add_control(
            'show_avatar_image',
            [
                'label' => __('Show Slide Image', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'return_value' => 'yes',
                'default' => 'yes',
                'separator' => 'before',
                'description' => __('Turn this off to hide the media area and show text content only.', 'daily-slider'),
            ]
        );

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

        $this->add_control(
            'title_tag',
            [
                'label' => __('Title Tag', 'daily-slider'),
                'type' => Controls_Manager::SELECT,
                'default' => 'h2',
                'options' => [
                    'h1' => __('H1', 'daily-slider'),
                    'h2' => __('H2', 'daily-slider'),
                    'h3' => __('H3', 'daily-slider'),
                    'h4' => __('H4', 'daily-slider'),
                    'h5' => __('H5', 'daily-slider'),
                    'h6' => __('H6', 'daily-slider'),
                ],
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_dagination',
            [
                'label' => __('Show Subtitle', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'return_value' => 'yes',
                'default' => 'yes',
                'description' => __('Shows or hides the secondary text line under the title.', 'daily-slider'),
            ]
        );

        $this->add_control(
            'content_align',
            [
                'label' => __('Content Alignment', 'daily-slider'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'daily-slider' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'daily-slider' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'daily-slider' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'toggle' => true,
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .daily-eldorado-wrap .daily-avatar-content' => 'text-align: {{VALUE}};',
                ],
                'description' => __('Aligns the text block inside the overlay content panel.', 'daily-slider'),
            ]
        );

        $this->add_control(
            'content_reveal_mode',
            [
                'label' => __('Content Panel Visibility', 'daily-slider'),
                'type' => Controls_Manager::SELECT,
                'default' => 'hover',
                'options' => [
                    'hover' => __('Show On Hover', 'daily-slider'),
                    'always' => __('Always Visible', 'daily-slider'),
                    'hide' => __('Always Hidden', 'daily-slider'),
                ],
                'description' => __('Choose how the overlay content panel appears on each slide.', 'daily-slider'),
            ]
        );

        $this->end_controls_section(); // <-- close genarel_section

        // Swiper Slides Section
        $this->start_controls_section(
            'swiper_slides_section',
            [
                'label' => __('Eldorado Items', 'daily-slider'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'title',
            [
                'label' => __('Title', 'daily-slider'),
                'type' => Controls_Manager::TEXT,
                'default' => __('John Doe', 'daily-slider'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'link_url',
            [
                'label' => __('Link URL', 'daily-slider'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'daily-slider'),
                'show_external' => true,
                'default' => [
                    'url' => '',
                ],
                'description' => __('Optional: set a link for this slide card.', 'daily-slider'),
            ]
        );

        $repeater->add_control(
            'dagination',
            [
                'label' => __('Subtitle', 'daily-slider'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Web Developer', 'daily-slider'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'avatar_image',
            [
                'label' => __('Image', 'daily-slider'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'id' => '',
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'swiper_slides',
            [
                'label' => __('Eldorado Item', 'daily-slider'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'title' => __('Dhaka', 'daily-slider'),
                        'link_url' => [ 'url' => '' ],
                        'dagination' => __('Web Developer', 'daily-slider'),
                        'avatar_image' => ['url' => plugin_dir_url(__FILE__) . '../../assets/images/item-1.svg'],
                    ],
                    [
                        'title' => __('Rajshahi', 'daily-slider'),
                        'link_url' => [ 'url' => '' ],
                        'dagination' => __('Business Owner', 'daily-slider'),
                        'avatar_image' => ['url' => plugin_dir_url(__FILE__) . '../../assets/images/item-2.svg'],
                    ],
                    [
                        'title' => __('Sylhet', 'daily-slider'),
                        'link_url' => [ 'url' => '' ],
                        'dagination' => __('Web Designer', 'daily-slider'),
                        'avatar_image' => ['url' => plugin_dir_url(__FILE__) . '../../assets/images/item-3.svg'],
                    ],
                ],
                'title_field' => '{{{ title }}}',
            ]
        );

        $this->end_controls_section(); // close swiper_slides_section

        // Swiper Settings Section
        $this->start_controls_section(
            'swiper_settings_section',
            [
                'label' => __('Carousel Settings', 'daily-slider'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'carousel_settings_info',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => esc_html__( 'Tip: use fewer options first (Effect, Columns, Gap, Autoplay). Then enable Navigation/Pagination only when needed.', 'daily-slider' ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
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
            ]
        );

		$this->add_responsive_control(
				'columns',
				[
					'label'          => __('Visible Columns', 'daily-slider'),
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
					'label'   => __('Space Between Items', 'daily-slider'),
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
            'smooth_scroll',
            [
                'label' => __('Smooth Scroll', 'daily-slider'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'daily-slider'),
                'label_off' => __('No', 'daily-slider'),
                'return_value' => 'yes',
                'default' => 'no',
                'separator' => 'before',
                'description' => __('Creates a continuous marquee-like slider motion.', 'daily-slider'),
            ]
        );

        $this->add_control(
            'smooth_scroll_direction',
            [
                'label' => __('Scroll Direction', 'daily-slider'),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'left',
                'options' => [
                    'left' => [
                        'title' => __('Left', 'daily-slider'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'right' => [
                        'title' => __('Right', 'daily-slider'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'toggle' => false,
                'condition' => [
                    'smooth_scroll' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'smooth_scroll_speed',
            [
                'label' => __('Smooth Speed', 'daily-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['s'],
                'range' => [
                    's' => [
                        'min' => 5,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 's',
                    'size' => 30,
                ],
                'condition' => [
                    'smooth_scroll' => 'yes',
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
                    'smooth_scroll!' => 'yes',
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
                'return_value' => 'yes',
                'default' => 'yes',
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
                'condition' => [
                    'smooth_scroll!' => 'yes',
                ],
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
    
        $this->end_controls_section(); // close swiper_settings_section

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
                'selector' => '{{WRAPPER}} .daily-slide-card',
            ]
        );

        $this->add_responsive_control(
            'item_padding',
            [
                'label' => __('Padding', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-slide-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'item_border',
                'label' => __('Border', 'daily-slider'),
                'selector' => '{{WRAPPER}} .daily-slide-card',
            ]
        );

        $this->add_responsive_control(
            'item_border_radius',
            [
                'label' => __('Border Radius', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-slide-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'item_box_shadow',
                'selector' => '{{WRAPPER}} .daily-slide-card',
            ]
        );

        $this->end_controls_section();

        // Name Style Section
        $this->start_controls_section(
            'avatar_image_style_section',
            [
                'label' => __('Image', 'daily-slider'),
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
                    '{{WRAPPER}} .daily-image-wrap' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'default' => [
                    'size' => 300,
                    'unit' => 'px',
                ],
                
            ]
        );

        $this->add_control(
            'avatar_image_fit',
            [
                'label' => __('Image Fit', 'daily-slider'),
                'type' => Controls_Manager::SELECT,
                'default' => 'cover',
                'options' => [
                    'cover' => __('Cover', 'daily-slider'),
                    'contain' => __('Contain', 'daily-slider'),
                    'fill' => __('Fill', 'daily-slider'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .daily-image-wrap .daily-avatar' => 'object-fit: {{VALUE}};',
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
                    '{{WRAPPER}} .daily-slide-card' => 'gap: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'content_wrap_style_section',
            [
                'label' => __('Content Panel', 'daily-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        ); 

        $this->add_control(
            'content_panel_style_info',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => esc_html__( 'These styles target the overlay panel (.daily-avatar-content) that sits over each slide image.', 'daily-slider' ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
            ]
        );

        $this->start_controls_tabs('style_tabs');

        // Content tab
        $this->start_controls_tab(
            'style_content_tab',
            [
                'label' => esc_html__( 'Panel', 'daily-slider' ),
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'content_background',
                'label' => __('Background', 'daily-slider'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .daily-eldorado-wrap .daily-avatar-content',
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label' => __('Padding', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-eldorado-wrap .daily-avatar-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_margin',
            [
                'label' => __('Margin', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-eldorado-wrap .daily-avatar-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'content_border',
                'label' => __('Border', 'daily-slider'),
                'selector' => '{{WRAPPER}} .daily-eldorado-wrap .daily-avatar-content',
            ]
        );

        $this->add_responsive_control(
            'content_border_radius',
            [
                'label' => __('Border Radius', 'daily-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .daily-eldorado-wrap .daily-avatar-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'content_box_shadow',
                'selector' => '{{WRAPPER}} .daily-eldorado-wrap .daily-avatar-content',
            ]
        );
        
        $this->end_controls_tab();

        // Title tab
        $this->start_controls_tab(
            'style_title_tab',
            [
                'label' => esc_html__( 'Title', 'daily-slider' ),
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'name_color',
            [
                'label' => __('Color', 'daily-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .daily-eldorado-wrap .daily-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'text_stroke',
                'selector' => '{{WRAPPER}} .daily-eldorado-wrap .daily-title',
            ]
        );
        
        // Typography control
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'name_typography',
                'label' => __('Typography', 'daily-slider'),
                'selector' => '{{WRAPPER}} .daily-eldorado-wrap .daily-title',
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
                    '{{WRAPPER}} .daily-eldorado-wrap .daily-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_tab();

        // Dagination tab
        $this->start_controls_tab(
            'style_dagination_tab',
            [
                'label' => esc_html__( 'Subtitle', 'daily-slider' ),
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
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();

        $this->end_controls_section();

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

        $this->add_control(
            'navigation_style_info',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => esc_html__( 'Style the previous/next arrows. Use Offset to move arrows inward or outward.', 'daily-slider' ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
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
                'label' => __('Horizontal Offset', 'daily-slider'),
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

        $this->add_control(
            'pagination_style_info',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => esc_html__( 'Customize bullet size, spacing, and active state to match your brand style.', 'daily-slider' ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
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
                'label' => __('Vertical Offset', 'daily-slider'),
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
        $this->end_controls_tabs();
        $this->end_controls_section();
        
    }
    

    protected function render() {
        $settings = $this->get_settings_for_display();
        $id = 'ds-' . $this->get_id();
        $smooth_scroll_enabled = ! empty( $settings['smooth_scroll'] ) && 'yes' === $settings['smooth_scroll'];
        $content_always_visible = ! empty( $settings['content_reveal_mode'] ) && 'always' === $settings['content_reveal_mode'];
        $content_always_hidden = ! empty( $settings['content_reveal_mode'] ) && 'hide' === $settings['content_reveal_mode'];
        $render_content_panel = ! $content_always_hidden;
        $is_editor_mode = Plugin::$instance->editor->is_edit_mode();
        $show_navigation = ! empty( $settings['swiper_navigation'] ) && 'yes' === $settings['swiper_navigation'];
        $show_pagination = ! empty( $settings['swiper_pagination'] ) && 'yes' === $settings['swiper_pagination'];

        $elementor_vp_lg = get_option( 'elementor_viewport_lg' );
        $elementor_vp_md = get_option( 'elementor_viewport_md' );
        $viewport_lg     = ! empty( $elementor_vp_lg ) ? $elementor_vp_lg - 1 : 1023;
        $viewport_md     = ! empty( $elementor_vp_md ) ? $elementor_vp_md - 1 : 767;

        $swiper_settings = array_filter(
            [
                'effect' => esc_attr( $settings['swiper_effect'] ?? 'slide' ),
                'loop' => ! empty( $settings['swiper_loop'] ) && 'yes' === $settings['swiper_loop'],
                'autoplay' => ! empty( $settings['swiper_autoplay'] ) && 'yes' === $settings['swiper_autoplay']
                    ? [
                        'delay' => absint( $settings['swiper_autoplay_delay'] ?? 5000 ),
                    ]
                    : false,
                'speed' => absint( $settings['swiper_speed'] ?? 800 ),
                'pauseOnHover' => ! empty( $settings['swiper_pause_on_hover'] ) && 'yes' === $settings['swiper_pause_on_hover'],
                'smoothScroll' => $smooth_scroll_enabled,
                'smoothScrollDirection' => esc_attr( $settings['smooth_scroll_direction'] ?? 'left' ),
                'smoothScrollSpeed' => absint( $settings['smooth_scroll_speed']['size'] ?? 30 ),
                'centeredSlides' => ! empty( $settings['swiper_center_slide'] ) && 'yes' === $settings['swiper_center_slide'],
                'slidesPerView' => isset( $settings['columns_mobile'] ) ? (int) $settings['columns_mobile'] : 1,
                'spaceBetween' => ! empty( $settings['item_gap_mobile']['size'] ) ? (int) $settings['item_gap_mobile']['size'] : 20,
                'breakpoints' => [
                    (int) $viewport_md => [
                        'slidesPerView' => isset( $settings['columns_tablet'] ) ? (int) $settings['columns_tablet'] : 2,
                        'spaceBetween' => ! empty( $settings['item_gap_tablet']['size'] ) ? (int) $settings['item_gap_tablet']['size'] : 20,
                    ],
                    (int) $viewport_lg => [
                        'slidesPerView' => isset( $settings['columns'] ) ? (int) $settings['columns'] : 3,
                        'spaceBetween' => ! empty( $settings['item_gap']['size'] ) ? (int) $settings['item_gap']['size'] : 20,
                    ],
                ],
                'navigation' => $show_navigation
                    ? [
                        'nextEl' => '#' . $id . ' .daily-button-next',
                        'prevEl' => '#' . $id . ' .daily-button-prev',
                    ]
                    : false,
                'pagination' => $show_pagination
                    ? [
                        'el' => '#' . $id . ' .swiper-pagination',
                        'type' => 'bullets',
                        'clickable' => 'true',
                        'dynamicBullets' => false,
                    ]
                    : false,
            ]
        );

        $this->add_render_attribute(
            [
                'carousel' => [
                    'class' => [
                        'ds-eldorado',
                        $smooth_scroll_enabled ? 'ds-eldorado-smooth-scroll' : '',
                        $content_always_visible ? 'ds-eldorado-content-always' : '',
                        $content_always_hidden ? 'ds-eldorado-content-hidden' : '',
                        $is_editor_mode ? 'ds-eldorado-editor-mode' : '',
                    ],
                    'id' => $id,
                    'data-settings' => [
                        wp_json_encode( $swiper_settings )
                    ]
                ]
            ]
        );

        ?>
        <div <?php $this->print_render_attribute_string('carousel'); ?>>
        <div class="swiper mySwiper daily-eldorado-wrap">
                <div class="swiper-wrapper">
                    <?php if (!empty($settings['swiper_slides']) && is_array($settings['swiper_slides'])) : ?>
    <?php foreach ($settings['swiper_slides'] as $index => $slide) : ?>
        <?php
            $slide_href = isset( $slide['link_url']['url'] ) ? $slide['link_url']['url'] : '';
            $has_link = ! empty( $slide_href );

            $link_key = 'slide-link-' . $this->get_id() . '-' . $index;

            if ( $has_link ) {
                $this->add_render_attribute( $link_key, 'href', $slide_href );

                if ( ! empty( $slide['link_url']['is_external'] ) ) {
                    $this->add_render_attribute( $link_key, 'target', '_blank' );
                }

                if ( ! empty( $slide['link_url']['nofollow'] ) ) {
                    $this->add_render_attribute( $link_key, 'rel', 'nofollow' );
                }

                $this->add_render_attribute( $link_key, 'class', 'daily-slide-link' );
            }
        ?>
        <div class="swiper-slide daily-slide-item">
            <div class="daily-slide-card">
                <?php if ( $has_link ) : ?>
                    <a <?php $this->print_render_attribute_string( $link_key ); ?>>
                <?php endif; ?>

                <?php if (!empty($settings['show_avatar_image'])) : ?>
                    <div class="daily-image-wrap">
                        <?php if (!empty($slide['avatar_image']['id'])) : ?>
                            <?php
                            echo wp_get_attachment_image(
                                $slide['avatar_image']['id'],
                                'full',
                                false,
                                [
                                    'class' => 'daily-avatar',
                                    'alt'   => esc_attr($slide['title'] ?? __('Avatar', 'daily-slider')),
                                ]
                            );
                            ?>
                        <?php else : ?>
                            <img
                                class="daily-avatar"
                                src="<?php echo esc_url($slide['avatar_image']['url']); ?>"
                                alt="<?php echo esc_attr($slide['title'] ?? __('Avatar', 'daily-slider')); ?>"
                            />
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ( $render_content_panel ) : ?>
                    <div class="daily-avatar-content">
                        <?php if (!empty($settings['show_title']) && !empty($slide['title'])) : ?>
                            <?php $title_tag = !empty($settings['title_tag']) ? $settings['title_tag'] : 'h2'; ?>
                            <<?php echo esc_html($title_tag); ?> class="daily-title">
                                <?php echo wp_kses_post($slide['title']); ?>
                            </<?php echo esc_html($title_tag); ?>>
                        <?php endif; ?>

                        <?php if (!empty($settings['show_dagination']) && !empty($slide['dagination'])) : ?>
                            <span class="daily-avatar-dagination">
                                <?php echo esc_html($slide['dagination']); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ( $has_link ) : ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
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
