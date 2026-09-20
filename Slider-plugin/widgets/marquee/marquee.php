<?php
namespace DailySlider\Widgets;

if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;

class Marquee_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'daily-slider-marquee';
    }


    public function get_title() {
        return esc_html__('Marquee Slider', 'daily-slider');
    }

    public function get_icon() {
        return 'eicon-slider-push';
    }

    public function get_categories() {
        return ['DailySlider-category'];
    }

    public function get_keywords() {
        return ['marquee', 'marquee text', 'marquee-list', 'ticker'];
    }

    public function get_script_depends() {
        return ['DailySlider-marquee-scripts'];
    }

    public function get_style_depends() {
        return ['DailySlider-marquee-styles'];
    }

    public function has_widget_inner_wrapper(): bool {
        return ! \Elementor\Plugin::$instance->experiments->is_feature_active('e_optimized_markup');
    }

	protected function register_controls() {

		$this->start_controls_section(
			'ms_section_brands',
			[
				'label' => __('Marquee Items', 'daily-slider'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_media',
			[
				'label' => __('Show Media', 'daily-slider'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'daily-slider'),
				'label_off' => __('Hide', 'daily-slider'),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'marquee_item_direction',
			[
				'label' => __('Media Direction', 'daily-slider'),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'row',
				'options' => [
					'row' => [
						'title' => __('Left to Right', 'daily-slider'),
						'icon' => 'eicon-h-align-left',
					],
					'row-reverse' => [
						'title' => __('Right to Left', 'daily-slider'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .daily-slider-marquee-slider .daily-slider-marquee-slider__item-box' => 'flex-direction: {{VALUE}};',
				],
				'condition' => [
					'show_media' => 'yes',
				],

			]
		);

		$this->add_control(
			'divider',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'media_type',
			[
				'label' => __('Media Type', 'daily-slider'),
				'type' => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => [
					'none' => __('None', 'daily-slider'),
					'image' => __('Image', 'daily-slider'),
					'icon' => __('Icon', 'daily-slider'),
				],
			]
		);

		$repeater->add_control(
			'image',
			[
				'label' => __('Image', 'daily-slider'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'condition' => [
					'media_type' => 'image',
				],
			]
		);

		$repeater->add_control(
			'selected_icon',
			[
				'label' => __('Icon', 'daily-slider'),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'condition' => [
					'media_type' => 'icon',
				],
			]
		);

		$repeater->add_control(
			'title',
			[
				'label' => __('Title', 'daily-slider'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Marquee Item', 'daily-slider'),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => __('Link', 'daily-slider'),
				'type' => Controls_Manager::URL,
				'placeholder' => __('https://your-link.com', 'daily-slider'),
				'show_external' => true,
				'default' => [
					'url' => '',
				],
				'label_block' => true,
			]
		);

		$this->add_control(
			'marquee_items',
			[
				'label' => __('Marquee Items', 'daily-slider'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'media_type' => 'image',
						'image' => ['url' => Utils::get_placeholder_image_src()],
						'title' => __('Marquee Item 1', 'daily-slider'),
					],
					[
						'media_type' => 'image',
						'image' => ['url' => Utils::get_placeholder_image_src()],
						'title' => __('Marquee Item 2', 'daily-slider'),
					],
					[
						'media_type' => 'image',
						'image' => ['url' => Utils::get_placeholder_image_src()],
						'title' => __('Marquee Item 3', 'daily-slider'),
					],
					[
						'media_type' => 'image',
						'image' => ['url' => Utils::get_placeholder_image_src()],
						'title' => __('Marquee Item 4', 'daily-slider'),
					],
					[
						'media_type' => 'image',
						'image' => ['url' => Utils::get_placeholder_image_src()],
						'title' => __('Marquee Item 5', 'daily-slider'),
					],
					[
						'media_type' => 'image',
						'image' => ['url' => Utils::get_placeholder_image_src()],
						'title' => __('Marquee Item 6', 'daily-slider'),
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();

		// Marquee Settings Section
		$this->start_controls_section(
			'section_marquee_settings',
			[
				'label' => __('Marquee Settings', 'daily-slider'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);


		$this->add_control(
			'pause_on_hover',
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
			'marquee_direction',
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
			]
		);

		$this->add_control(
			'marquee_speed',
			[
				'label' => __('Speed', 'daily-slider'),
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
			]
		);

		$this->add_responsive_control(
			'items_gap',
			[
				'label' => esc_html__('Column Gap', 'daily-slider'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .daily-slider-marquee-slider__content' => 'gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .daily-slider-marquee-slider__track' => 'gap: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_mask',
			[
				'label' => __('Show Edge Mask', 'daily-slider'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'daily-slider'),
				'label_off' => __('Hide', 'daily-slider'),
				'return_value' => 'yes',
				'default' => 'yes',
				'separator' => 'before',
				'prefix_class' => 'daily-slider-marquee-mask-',
			]
		);

		$this->add_control(
			'mask_size',
			[
				'label' => __('Mask Size', 'daily-slider'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 30,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}}.daily-slider-marquee-mask-yes .daily-slider-marquee-slider' => 'mask-image: linear-gradient(to right, transparent, transparent {{SIZE}}%, #000 calc({{SIZE}}% + var(--mask-smoothness)), #000 calc(100% - {{SIZE}}% - var(--mask-smoothness)), transparent calc(100% - {{SIZE}}%), transparent);-webkit-mask-image: linear-gradient(to right, transparent, transparent {{SIZE}}%, #000 calc({{SIZE}}% + var(--mask-smoothness)), #000 calc(100% - {{SIZE}}% - var(--mask-smoothness)), transparent calc(100% - {{SIZE}}%), transparent);',
				],
				'condition' => [
					'show_mask' => 'yes',
				],
			]
		);

		$this->add_control(
			'mask_smoothness',
			[
				'label' => __('Mask Smoothness', 'daily-slider'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 2,
				],
				'selectors' => [
					'{{WRAPPER}}.daily-slider-marquee-mask-yes .daily-slider-marquee-slider' => '--mask-smoothness: {{SIZE}}%;',
				],
				'condition' => [
					'show_mask' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_style_items',
			[
				'label' => __('Items', 'daily-slider'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_item_style');

		$this->start_controls_tab(
			'tab_item_normal',
			[
				'label' => esc_html__('Normal', 'daily-slider'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'item_background',
				'selector' => '{{WRAPPER}} .daily-slider-marquee-slider .daily-slider-marquee-slider__item-box',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'item_border',
				'label' => esc_html__('Border', 'daily-slider'),
				'selector' => '{{WRAPPER}} .daily-slider-marquee-slider .daily-slider-marquee-slider__item-box',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label' => esc_html__('Border Radius', 'daily-slider'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .daily-slider-marquee-slider .daily-slider-marquee-slider__item-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label' => esc_html__('Padding', 'daily-slider'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .daily-slider-marquee-slider .daily-slider-marquee-slider__item-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .daily-slider-marquee-slider .daily-slider-marquee-slider__item-box',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_hover',
			[
				'label' => esc_html__('Hover', 'daily-slider'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'item_hover_background',
				'selector' => '{{WRAPPER}} .daily-slider-marquee-slider .daily-slider-marquee-slider__item-box:hover',
			]
		);

		$this->add_control(
			'item_hover_border_color',
			[
				'label' => esc_html__('Border Color', 'daily-slider'),
				'type' => Controls_Manager::COLOR,
				'default' => '#2B2D42',
				'condition' => [
					'item_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .daily-slider-marquee-slider .daily-slider-marquee-slider__item-box:hover' => 'border-color: {{VALUE}};',
				],
				'separator' => 'before'
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'item_hover_box_shadow',
				'selector' => '{{WRAPPER}} .daily-slider-marquee-slider .daily-slider-marquee-slider__item-box:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label' => __('Title', 'daily-slider'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __('Color', 'daily-slider'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .daily-slider-marquee-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .daily-slider-marquee-title',
			]
		);

		$this->add_control(
			'title_hover_heading',
			[
				'label' => __('Hover', 'daily-slider'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label' => __('Color', 'daily-slider'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .daily-slider-marquee-slider__item-box:hover .daily-slider-marquee-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_media',
			[
				'label' => __('Media', 'daily-slider'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_media' => 'yes',
				],
			]
		);


		$this->start_controls_tabs('media_styles_tabs');

		$this->start_controls_tab(
			'media_styles_wrapper',
			[
				'label' => __('Wrapper', 'daily-slider'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'media_background',
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .daily-slider-marquee-media',
			]
		);

		$this->add_responsive_control(
			'media_padding',
			[
				'label' => esc_html__('Padding', 'daily-slider'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .daily-slider-marquee-slider__item img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .daily-slider-marquee-media svg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'media_border',
				'selector' => '{{WRAPPER}} .daily-slider-marquee-media',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'media_border_radius',
			[
				'label' => esc_html__('Border Radius', 'daily-slider'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .daily-slider-marquee-media' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'media_box_shadow',
				'selector' => '{{WRAPPER}} .daily-slider-marquee-media',
			]
		);

		$this->add_responsive_control(
			'media_spacing',
			[
				'label' => esc_html__('Spacing', 'daily-slider'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 10,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .daily-slider-marquee-slider .daily-slider-marquee-slider__item-box' => 'gap: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'media_heading',
			[
				'label' => esc_html__('Hover', 'daily-slider'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'media_hover_background',
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .daily-slider-marquee-slider__item-box:hover .daily-slider-marquee-media',
			]
		);

		$this->add_control(
			'media_hover_border_color',
			[
				'label' => esc_html__('Border Color', 'daily-slider'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'media_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .daily-slider-marquee-slider__item-box:hover .daily-slider-marquee-media' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'media_hover_box_shadow',
				'selector' => '{{WRAPPER}} .daily-slider-marquee-slider__item-box:hover .daily-slider-marquee-media',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'media_styles_image',
			[
				'label' => __('Image', 'daily-slider'),
			]
		);

		$this->add_responsive_control(
			'marquee_image_size',
			[
				'label' => __('Size', 'daily-slider'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .daily-slider-marquee-slider img' => '--daily-slider-marquee-img-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'marquee_image_opacity',
			[
				'label' => __('Opacity', 'daily-slider'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .daily-slider-marquee-slider__item img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'marquee_image_css_filters',
				'selector' => '{{WRAPPER}} .daily-slider-marquee-slider__item img',
			]
		);


		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'default' => 'medium',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'marquee_image_heading',
			[
				'label' => esc_html__('Hover', 'daily-slider'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);


		$this->add_control(
			'marquee_image_opacity_hover',
			[
				'label' => __('Opacity', 'daily-slider'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .daily-slider-marquee-slider__item-box:hover img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'marquee_image_css_filters_hover',
				'selector' => '{{WRAPPER}} .daily-slider-marquee-slider__item-box:hover img',
			]
		);


		$this->end_controls_tab();

		$this->start_controls_tab(
			'media_styles_icon',
			[
				'label' => __('Icon', 'daily-slider'),
			]
		);

		$this->add_control(
			'marquee_icon_color',
			[
				'label' => __('Color', 'daily-slider'),
				'type' => Controls_Manager::COLOR,
				'default' => '#334155',
				'selectors' => [
					'{{WRAPPER}} .daily-slider-marquee-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'marquee_icon_size',
			[
				'label' => __('Size', 'daily-slider'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', 'rem'],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
					'em' => [
						'min' => 0.1,
						'max' => 20,
					],
					'rem' => [
						'min' => 0.1,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .daily-slider-marquee-icon svg' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'marquee_icon_heading',
			[
				'label' => __('Hover', 'daily-slider'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'marquee_icon_color_hover',
			[
				'label' => __('Color', 'daily-slider'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .daily-slider-marquee-slider__item-box:hover .daily-slider-marquee-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render marquee image
	 */
	private function render_image($image, $title, $settings)
	{
		if (empty($image['id']) && empty($image['url'])) {
			return;
		}

		$thumb_url = Group_Control_Image_Size::get_attachment_image_src($image['id'], 'thumbnail', $settings);
		if (!$thumb_url) {
			$thumb_url = $image['url'];
		}

		if (empty($thumb_url)) {
			return;
		}
		?>
		<div class="daily-slider-marquee-media daily-slider-marquee-image">
			<img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($title); ?>" />
		</div>
		<?php
	}

	/**
	 * Render marquee icon
	 */
	private function render_icon($icon)
	{
		?>
		<div class="daily-slider-marquee-media daily-slider-marquee-icon">
			<?php Icons_Manager::render_icon($icon, ['aria-hidden' => 'true']); ?>
		</div>
		<?php
	}

	/**
	 * Render marquee title
	 */
	private function render_title($title)
	{
		if (!empty($title)): ?>
			<span class="daily-slider-marquee-title"><?php echo esc_html($title); ?></span>
		<?php endif;
	}

	/**
	 * Render marquee item content
	 */
	private function render_item_content($item, $settings)
	{
		if ($settings['show_media'] === 'yes' && !empty($item['media_type']) && $item['media_type'] !== 'none') {
			if ($item['media_type'] === 'image' && (!empty($item['image']['id']) || !empty($item['image']['url']))) {
				$this->render_image($item['image'], $item['title'], $settings);
			} elseif ($item['media_type'] === 'icon' && !empty($item['selected_icon']['value'])) {
				$this->render_icon($item['selected_icon']);
			}
		}
		$this->render_title($item['title']);
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		// Check if we have marquee items, if not show simple text
		if (empty($settings['marquee_items'])) {
			// Fallback to simple marquee text
			$marquee_text = __( 'This is a sample marquee slider text', 'daily-slider' );
			$marquee_direction = $settings['marquee_direction'] ?? 'left';
			$marquee_speed = $settings['marquee_speed'] ?? ['size' => 30];
			
			echo '<marquee class="daily-slider-marquee" direction="' . esc_attr($marquee_direction) . '" scrollamount="3">' . esc_html($marquee_text) . '</marquee>';
			return;
		}

		$this->add_render_attribute('marquee-slider', [
			'class' => 'daily-slider-marquee-slider',
			'data-settings' => wp_json_encode([
				'direction' => $settings['marquee_direction'],
				'speed' => absint($settings['marquee_speed']['size']),
				'pauseOnHover' => $settings['pause_on_hover'] === 'yes'
			])
		]);

		?>
		<div <?php $this->print_render_attribute_string('marquee-slider'); ?>>
			<div class="daily-slider-marquee-slider__track">
				<div class="daily-slider-marquee-slider__content" style="display: inline-flex; white-space: nowrap;">
					<?php foreach ($settings['marquee_items'] as $index => $item):
						$item_key = 'marquee_item_' . $index;
						$this->add_render_attribute($item_key, 'class', [
							'daily-slider-marquee-slider__item',
							'elementor-repeater-item-' . $item['_id']
						]);

						$link_key = 'link_' . $index;

						if (!empty($item['link']['url'])) {
							$this->add_link_attributes($link_key, $item['link']);
						}
						?>
						<div <?php $this->print_render_attribute_string($item_key); ?>>
							<?php if (!empty($item['link']['url'])): ?>
								<a class="daily-slider-marquee-slider__item-box" <?php $this->print_render_attribute_string($link_key); ?>>
									<?php $this->render_item_content($item, $settings); ?>
								</a>
							<?php else: ?>
								<div class="daily-slider-marquee-slider__item-box">
									<?php $this->render_item_content($item, $settings); ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}

}
