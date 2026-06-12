<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Skyboot_Portfolio_Elementor_Widget extends Widget_Base {

	public function get_name() {
		return 'skyboot-portfolio-gallery';
	}

	public function get_title() {
		return __( 'Skyboot: Portfolio', 'skyboot-pg' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return array( 'DailySlider-category' );
	}

	public function get_style_depends() {
		return array(
			'skb-framework-css',
			'skb-venobox',
			'skyboot-portfolio-style',
			'skb-portfolio-responsive',
		);
	}

	public function get_script_depends() {
		return array(
			'skb-isotope',
			'skb-hoverdir',
			'skb-venobox',
		);
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_gallery_controls();
		$this->register_general_controls();
		$this->register_popup_controls();
		$this->register_style_controls();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'content_section_1',
			array(
				'label' => esc_html__( 'Section Heading', 'skyboot-pg' ),
			)
		);

		$this->add_control(
			'enable_sec_heading',
			array(
				'label'        => esc_html__( 'Show/Hide Section Heading', 'skyboot-pg' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'skb_section_heading',
			array(
				'label'       => __( 'Heading', 'skyboot-pg' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Photo Gallery',
				'label_block' => true,
				'condition'   => array(
					'enable_sec_heading' => 'yes',
				),
			)
		);

		$this->add_control(
			'skb_sub_heading',
			array(
				'label'       => __( 'Sub Heading', 'skyboot-pg' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => 'Build the gallery directly in Elementor with mixed media items, filters and layout controls.',
				'label_block' => true,
				'condition'   => array(
					'enable_sec_heading' => 'yes',
				),
			)
		);

		$this->add_control(
			'enable_sec_separator',
			array(
				'label'        => esc_html__( 'Show/Hide Section Separator', 'skyboot-pg' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'enable_sec_heading' => 'yes',
				),
			)
		);

		$this->add_control(
			'separator_color1',
			array(
				'label'     => __( 'Color 1', 'skyboot-pg' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#dbd9da',
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .skb-section-title-separator::before' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'separator_color2',
			array(
				'label'     => __( 'Color 2', 'skyboot-pg' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FF5500',
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .skb-section-title-separator::after' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_gallery_controls() {
		$repeater = new Repeater();

		$repeater->add_control(
			'item_type',
			array(
				'label'   => __( 'Item Type', 'skyboot-pg' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => array(
					'image' => __( 'Image', 'skyboot-pg' ),
					'video' => __( 'Video', 'skyboot-pg' ),
					'link'  => __( 'External Link', 'skyboot-pg' ),
				),
			)
		);

		$repeater->add_control(
			'item_image',
			array(
				'label'       => __( 'Thumbnail / Cover Image', 'skyboot-pg' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => __( 'Used as the main image or cover thumbnail for videos and custom links.', 'skyboot-pg' ),
				'default'     => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater->add_control(
			'item_title',
			array(
				'label'       => __( 'Title', 'skyboot-pg' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Project Title', 'skyboot-pg' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'item_sub_title',
			array(
				'label'       => __( 'Description', 'skyboot-pg' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'Short project description goes here.', 'skyboot-pg' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'item_categories',
			array(
				'label'       => __( 'Filter Categories', 'skyboot-pg' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Branding', 'skyboot-pg' ),
				'description' => __( 'Use commas to assign multiple filter labels, for example: Branding, Web Design', 'skyboot-pg' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'item_video_url',
			array(
				'label'         => __( 'Video URL', 'skyboot-pg' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => 'https://www.youtube.com/watch?v=...',
				'label_block'   => true,
				'show_external' => false,
				'dynamic'       => array(
					'active' => true,
				),
				'condition'     => array(
					'item_type' => 'video',
				),
			)
		);

		$repeater->add_control(
			'item_link',
			array(
				'label'         => __( 'Link URL', 'skyboot-pg' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => 'https://example.com',
				'label_block'   => true,
				'show_external' => true,
				'dynamic'       => array(
					'active' => true,
				),
				'condition'     => array(
					'item_type' => 'link',
				),
			)
		);

		$this->start_controls_section(
			'gallery_items_section',
			array(
				'label' => esc_html__( 'Gallery Items', 'skyboot-pg' ),
			)
		);

		$this->add_control(
			'bulk_gallery_items',
			array(
				'label'       => esc_html__( 'Bulk Image Gallery', 'skyboot-pg' ),
				'type'        => Controls_Manager::GALLERY,
				'description' => esc_html__( 'Quickly select multiple images at once. These are great for straight image galleries.', 'skyboot-pg' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'bulk_gallery_categories',
			array(
				'label'       => esc_html__( 'Bulk Gallery Categories', 'skyboot-pg' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => esc_html__( 'Optional categories applied to all bulk-selected images. Separate multiple values with commas.', 'skyboot-pg' ),
				'label_block' => true,
				'condition'   => array(
					'bulk_gallery_items!' => array( '' ),
				),
			)
		);

		$this->add_control(
			'bulk_gallery_text_source',
			array(
				'label'     => esc_html__( 'Bulk Image Text Source', 'skyboot-pg' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'caption',
				'options'   => array(
					'none'        => esc_html__( 'None', 'skyboot-pg' ),
					'title'       => esc_html__( 'Attachment Title', 'skyboot-pg' ),
					'caption'     => esc_html__( 'Attachment Caption', 'skyboot-pg' ),
					'description' => esc_html__( 'Attachment Description', 'skyboot-pg' ),
					'alt'         => esc_html__( 'Image Alt Text', 'skyboot-pg' ),
				),
				'condition' => array(
					'bulk_gallery_items!' => array( '' ),
				),
			)
		);

		$this->add_control(
			'bulk_gallery_separator',
			array(
				'type'  => Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'gallery_items',
			array(
				'label'       => esc_html__( 'Gallery Items', 'skyboot-pg' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ item_title || "Gallery Item" }}}',
				'default'     => array(
					array(
						'item_type'       => 'image',
						'item_title'      => __( 'Brand Identity', 'skyboot-pg' ),
						'item_sub_title'  => __( 'Logo, packaging and social assets.', 'skyboot-pg' ),
						'item_categories' => __( 'Branding', 'skyboot-pg' ),
					),
					array(
						'item_type'       => 'video',
						'item_title'      => __( 'Studio Reel', 'skyboot-pg' ),
						'item_sub_title'  => __( 'Use this for YouTube, Vimeo or direct MP4 links.', 'skyboot-pg' ),
						'item_categories' => __( 'Video', 'skyboot-pg' ),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_general_controls() {
		$this->start_controls_section(
			'genarel_settings_section',
			array(
				'label' => esc_html__( 'General Settings', 'skyboot-pg' ),
			)
		);

		$this->add_control(
			'randomize_items',
			array(
				'label'        => esc_html__( 'Randomize Items', 'skyboot-pg' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
				'description'  => esc_html__( 'Shuffle your gallery items on page load.', 'skyboot-pg' ),
			)
		);

		$this->add_control(
			'enable_filter_menu',
			array(
				'label'        => esc_html__( 'Show/Hide Filter Menu', 'skyboot-pg' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'gallery_layout',
			array(
				'label'   => esc_html__( 'Gallery Layout', 'skyboot-pg' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'masonry',
				'options' => array(
					'masonry' => esc_html__( 'Masonry', 'skyboot-pg' ),
					'fitRows' => esc_html__( 'Grid', 'skyboot-pg' ),
				),
			)
		);

		$this->add_control(
			'grid_image_height',
			array(
				'label'      => esc_html__( 'Grid Image Height', 'skyboot-pg' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array(
						'min' => 100,
						'max' => 600,
					),
				),
				'default'    => array(
					'size' => 250,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .skb-layout-fitRows .skb-gallery-image img' => 'height: {{SIZE}}{{UNIT}}; object-fit: cover; width: 100%;',
				),
				'condition'  => array(
					'gallery_layout' => 'fitRows',
				),
			)
		);

		$this->add_control(
			'column_count',
			array(
				'label'   => esc_html__( 'Column Count', 'skyboot-pg' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '4',
				'options' => array(
					'6' => esc_html__( '2', 'skyboot-pg' ),
					'4' => esc_html__( '3', 'skyboot-pg' ),
					'3' => esc_html__( '4', 'skyboot-pg' ),
					'2' => esc_html__( '6', 'skyboot-pg' ),
				),
			)
		);

		$this->add_control(
			'image_size',
			array(
				'label'   => esc_html__( 'Preview Image Size', 'skyboot-pg' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'large',
				'options' => array(
					'thumbnail'    => esc_html__( 'Thumbnail', 'skyboot-pg' ),
					'medium'       => esc_html__( 'Medium', 'skyboot-pg' ),
					'medium_large' => esc_html__( 'Medium Large', 'skyboot-pg' ),
					'large'        => esc_html__( 'Large', 'skyboot-pg' ),
					'full'         => esc_html__( 'Full', 'skyboot-pg' ),
				),
			)
		);

		$this->add_control(
			'set_icon',
			array(
				'label'       => esc_html__( 'Choose Icon', 'skyboot-pg' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => array(
					'value'   => 'fas fa-camera',
					'library' => 'fa-solid',
				),
				'recommended' => array(
					'fa-solid'  => array(
						'camera',
						'camera-retro',
						'link',
						'arrows',
						'arrows-alt',
						'eye',
						'eye-slash',
						'film',
						'folder-open',
						'play',
						'search',
					),
					'fa-regular' => array(
						'eye',
					),
				),
			)
		);

		$this->add_control(
			'enable_title',
			array(
				'label'        => esc_html__( 'Show/Hide Title', 'skyboot-pg' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'enable_sub_title',
			array(
				'label'        => esc_html__( 'Show/Hide Sub Title', 'skyboot-pg' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'enable_overlay',
			array(
				'label'        => esc_html__( 'Enable Overlay', 'skyboot-pg' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'overlay_type',
			array(
				'label'     => esc_html__( 'Overlay Type', 'skyboot-pg' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'direction_hover',
				'options'   => array(
					'direction_hover' => esc_html__( 'Direction Hover', 'skyboot-pg' ),
					'normal_effect'   => esc_html__( 'Normal Effect', 'skyboot-pg' ),
				),
				'condition' => array(
					'enable_overlay' => 'yes',
				),
			)
		);

		$this->add_control(
			'enable_mouse_hover_image_zoom',
			array(
				'label'        => esc_html__( 'Enable Mouse Hover Image Zoom', 'skyboot-pg' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		$this->end_controls_section();
	}

	protected function register_popup_controls() {
		$this->start_controls_section(
			'popup_settings_section',
			array(
				'label' => esc_html__( 'Popup Settings', 'skyboot-pg' ),
			)
		);

		$this->add_control(
			'enable_popup',
			array(
				'label'        => esc_html__( 'Show/Hide Popup', 'skyboot-pg' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'enable_popup_content',
			array(
				'label'        => esc_html__( 'Show/Hide Popup Content', 'skyboot-pg' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'popup_overlay_bg',
			array(
				'label'   => __( 'Overlay BG', 'skyboot-pg' ),
				'type'    => Controls_Manager::COLOR,
				'default' => 'rgba(0,0,0,0.8)',
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->start_controls_section(
			'style_section',
			array(
				'label' => esc_html__( 'Style', 'skyboot-pg' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'section_title_typography',
				'label'    => __( 'Section Title Typography', 'skyboot-pg' ),
				'selector' => '{{WRAPPER}} .skb-section-title h2',
			)
		);

		$this->add_control(
			'section_title_color',
			array(
				'label'     => __( 'Section Title Color', 'skyboot-pg' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .skb-section-title h2' => 'color: {{VALUE}};',
				),
				'default'   => '#FF5500',
				'separator' => 'after',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'section_sub_title_typography',
				'label'    => __( 'Section Sub Title Typography', 'skyboot-pg' ),
				'selector' => '{{WRAPPER}} .skb-section-title p',
			)
		);

		$this->add_control(
			'section_sub_title_color',
			array(
				'label'     => __( 'Section Sub Title Color', 'skyboot-pg' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .skb-section-title p' => 'color: {{VALUE}};',
				),
				'default'   => '#7a7a7a',
				'separator' => 'after',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'filter_menu_typography',
				'label'    => __( 'Filter Typography', 'skyboot-pg' ),
				'selector' => '{{WRAPPER}} .skb-button-group button',
			)
		);

		$this->add_control(
			'filter_menu_color',
			array(
				'label'     => __( 'Filter Color', 'skyboot-pg' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .skb-button-group button' => 'color: {{VALUE}};',
				),
				'default'   => '#39434a',
			)
		);

		$this->add_control(
			'filter_menu_active_color',
			array(
				'label'     => __( 'Filter Active Color', 'skyboot-pg' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .skb-button-group button.is-checked' => 'color: {{VALUE}};',
					'{{WRAPPER}} .skb-button-group button:hover'      => 'color: {{VALUE}};',
				),
				'default'   => '#FF5500',
				'separator' => 'after',
			)
		);

		$this->add_responsive_control(
			'gallery_item_gap',
			array(
				'label'      => __( 'Item Gap', 'skyboot-pg' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'default'    => array(
					'size' => 30,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .skb-grid' => 'margin-left: calc(-{{SIZE}}{{UNIT}} / 2); margin-right: calc(-{{SIZE}}{{UNIT}} / 2);',
					'{{WRAPPER}} .skb-grid-item' => 'padding-left: calc({{SIZE}}{{UNIT}} / 2); padding-right: calc({{SIZE}}{{UNIT}} / 2);',
					'{{WRAPPER}} .skb-gallery-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hover_overlay_color',
			array(
				'label'     => __( 'Hover Overlay Color', 'skyboot-pg' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,85,0,0.7)',
				'selectors' => array(
					'{{WRAPPER}} .hover-effect-bg' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_icon_color',
			array(
				'label'     => __( 'Item Icon Color', 'skyboot-pg' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .skb-gallery-icon svg' => 'fill: {{VALUE}};',
				),
				'default'   => '#ffffff',
				'separator' => 'after',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_title_typography',
				'label'    => __( 'Item Title Typography', 'skyboot-pg' ),
				'selector' => '{{WRAPPER}} .skb-gallery-inner-content h4',
			)
		);

		$this->add_control(
			'item_title_color',
			array(
				'label'     => __( 'Item Title Color', 'skyboot-pg' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .skb-gallery-inner-content h4' => 'color: {{VALUE}};',
				),
				'default'   => '#ffffff',
				'separator' => 'after',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_content_typography',
				'label'    => __( 'Item Content Typography', 'skyboot-pg' ),
				'selector' => '{{WRAPPER}} .skb-gallery-inner-content span',
			)
		);

		$this->add_control(
			'item_content_color',
			array(
				'label'     => __( 'Item Content Color', 'skyboot-pg' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .skb-gallery-inner-content span' => 'color: {{VALUE}};',
				),
				'default'   => '#ffffff',
			)
		);

		$this->end_controls_section();
	}

	protected function parse_item_filters( $raw_value ) {
		$filters = array();
		$labels  = preg_split( '/[\r\n,]+/', (string) $raw_value );

		if ( empty( $labels ) ) {
			return $filters;
		}

		foreach ( $labels as $label ) {
			$label = trim( wp_strip_all_tags( $label ) );

			if ( '' === $label ) {
				continue;
			}

			$slug = sanitize_title( $label );

			if ( '' === $slug ) {
				continue;
			}

			$filters[ $slug ] = $label;
		}

		return $filters;
	}

	protected function get_media_urls( $media, $preview_size ) {
		$preview_url = '';
		$full_url    = '';
		$alt_text    = '';
		$media_id    = ! empty( $media['id'] ) ? absint( $media['id'] ) : 0;

		if ( $media_id ) {
			$preview_url = wp_get_attachment_image_url( $media_id, $preview_size );
			$full_url    = wp_get_attachment_image_url( $media_id, 'full' );
			$alt_text    = get_post_meta( $media_id, '_wp_attachment_image_alt', true );
		}

		if ( empty( $preview_url ) && ! empty( $media['url'] ) ) {
			$preview_url = $media['url'];
		}

		if ( empty( $full_url ) ) {
			$full_url = $preview_url;
		}

		return array(
			'id'          => $media_id,
			'preview_url' => $preview_url,
			'full_url'    => $full_url,
			'alt_text'    => $alt_text,
		);
	}

	protected function get_auto_thumbnail( $url ) {
		if ( preg_match( '/(youtube\.com|youtu\.be)\/(watch\?v=|v\/|u\/|embed\/)?([\w-]{11})/i', $url, $matches ) ) {
			return 'https://img.youtube.com/vi/' . end( $matches ) . '/hqdefault.jpg';
		} elseif ( preg_match( '/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/', $url, $matches ) ) {
			$video_id = end( $matches );
			$transient_key = 'skb_vim_thumb_' . $video_id;
			$thumb = get_transient( $transient_key );
			if ( false === $thumb ) {
				$response = wp_remote_get( 'https://vimeo.com/api/v2/video/' . $video_id . '.json' );
				if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
					$body = wp_remote_retrieve_body( $response );
					$data = json_decode( $body );
					if ( ! empty( $data[0]->thumbnail_large ) ) {
						$thumb = $data[0]->thumbnail_large;
						set_transient( $transient_key, $thumb, DAY_IN_SECONDS * 7 );
					}
				}
			}
			return $thumb ? $thumb : '';
		}
		return '';
	}

	protected function get_attachment_text( $attachment_id, $source ) {
		if ( ! $attachment_id || 'none' === $source ) {
			return '';
		}

		switch ( $source ) {
			case 'title':
				return get_the_title( $attachment_id );
			case 'caption':
				return wp_get_attachment_caption( $attachment_id );
			case 'description':
				$post = get_post( $attachment_id );
				return $post ? $post->post_content : '';
			case 'alt':
				return (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
			default:
				return '';
		}
	}

	protected function normalize_gallery_items( $settings ) {
		$items        = array();
		$preview_size = ! empty( $settings['image_size'] ) ? $settings['image_size'] : 'large';

		// ── Bulk gallery images ──────────────────────────────────────────────
		$bulk_images   = ! empty( $settings['bulk_gallery_items'] ) && is_array( $settings['bulk_gallery_items'] ) ? $settings['bulk_gallery_items'] : array();
		$bulk_cats     = isset( $settings['bulk_gallery_categories'] ) ? $settings['bulk_gallery_categories'] : '';
		$bulk_source   = ! empty( $settings['bulk_gallery_text_source'] ) ? $settings['bulk_gallery_text_source'] : 'caption';
		$bulk_filters  = $this->parse_item_filters( $bulk_cats );

		foreach ( $bulk_images as $bulk_image ) {
			$media_data  = $this->get_media_urls( $bulk_image, $preview_size );
			$media_id    = ! empty( $bulk_image['id'] ) ? absint( $bulk_image['id'] ) : 0;
			$item_text   = $this->get_attachment_text( $media_id, $bulk_source );
			$item_title  = get_the_title( $media_id );
			$action_url  = ! empty( $media_data['full_url'] ) ? $media_data['full_url'] : '';

			if ( empty( $media_data['preview_url'] ) || $media_data['preview_url'] === Utils::get_placeholder_image_src() ) {
				if ( ! empty( $action_url ) ) {
					$auto_thumb = $this->get_auto_thumbnail( $action_url );
					if ( ! empty( $auto_thumb ) ) {
						$media_data['preview_url'] = $auto_thumb;
						$media_data['full_url']    = $auto_thumb;
					}
				}
			}

			if ( empty( $media_data['preview_url'] ) ) {
				$media_data['preview_url'] = Utils::get_placeholder_image_src();
			}

			if ( empty( $media_data['alt_text'] ) ) {
				$media_data['alt_text'] = $item_title;
			}

			$items[] = array(
				'type'         => 'image',
				'title'        => $item_title,
				'description'  => $item_text,
				'filters'      => $bulk_filters,
				'preview_url'  => $media_data['preview_url'],
				'full_url'     => $media_data['full_url'],
				'alt_text'     => $media_data['alt_text'],
				'action_url'   => $action_url,
				'action_kind'  => ! empty( $action_url ) ? 'popup_image' : 'none',
				'target_blank' => false,
				'nofollow'     => false,
			);
		}

		// ── Custom / repeater items ──────────────────────────────────────────
		$custom_items = ! empty( $settings['gallery_items'] ) && is_array( $settings['gallery_items'] ) ? $settings['gallery_items'] : array();

		foreach ( $custom_items as $custom_item ) {
			$item_type   = ! empty( $custom_item['item_type'] ) ? $custom_item['item_type'] : 'image';
			$media_data  = $this->get_media_urls( isset( $custom_item['item_image'] ) ? $custom_item['item_image'] : array(), $preview_size );
			$item_title  = isset( $custom_item['item_title'] ) ? $custom_item['item_title'] : '';
			$item_text   = isset( $custom_item['item_sub_title'] ) ? $custom_item['item_sub_title'] : '';
			$item_filters = $this->parse_item_filters( isset( $custom_item['item_categories'] ) ? $custom_item['item_categories'] : '' );
			$action_url  = '';
			$action_kind = 'none';
			$target_blank = false;
			$nofollow     = false;

			if ( 'video' === $item_type && ! empty( $custom_item['item_video_url']['url'] ) ) {
				$action_kind = 'popup_video';
				$action_url  = $custom_item['item_video_url']['url'];
			} elseif ( 'link' === $item_type && ! empty( $custom_item['item_link']['url'] ) ) {
				$action_url   = $custom_item['item_link']['url'];
				$action_kind  = 'popup_iframe';
				$target_blank = ! empty( $custom_item['item_link']['is_external'] );
				$nofollow     = ! empty( $custom_item['item_link']['nofollow'] );
			} elseif ( ! empty( $media_data['full_url'] ) ) {
				$action_url  = $media_data['full_url'];
				$action_kind = 'popup_image';
			}

			if ( empty( $media_data['preview_url'] ) || $media_data['preview_url'] === Utils::get_placeholder_image_src() ) {
				if ( in_array( $item_type, array( 'video', 'link' ) ) && ! empty( $action_url ) ) {
					$auto_thumb = $this->get_auto_thumbnail( $action_url );
					if ( ! empty( $auto_thumb ) ) {
						$media_data['preview_url'] = $auto_thumb;
					}
				}
			}

			if ( empty( $media_data['preview_url'] ) ) {
				$media_data['preview_url'] = Utils::get_placeholder_image_src();
			}

			if ( empty( $media_data['alt_text'] ) ) {
				$media_data['alt_text'] = $item_title;
			}

			$items[] = array(
				'type'         => $item_type,
				'title'        => $item_title,
				'description'  => $item_text,
				'filters'      => $item_filters,
				'preview_url'  => $media_data['preview_url'],
				'full_url'     => $media_data['full_url'],
				'alt_text'     => $media_data['alt_text'],
				'action_url'   => $action_url,
				'action_kind'  => $action_kind,
				'target_blank' => $target_blank,
				'nofollow'     => $nofollow,
			);
		}

		if ( 'yes' === ( isset( $settings['randomize_items'] ) ? $settings['randomize_items'] : 'no' ) ) {
			shuffle( $items );
		}

		return $items;
	}

	protected function render_gallery_item_action( $item, $settings, $widget_id_class ) {
		$enable_popup         = $this->get_settings_for_display( 'enable_popup' );
		$enable_popup_content = $this->get_settings_for_display( 'enable_popup_content' );
		$item_title           = ! empty( $item['title'] ) ? $item['title'] : __( 'Gallery item', 'skyboot-pg' );
		$item_description     = ! empty( $item['description'] ) ? wp_strip_all_tags( $item['description'] ) : '';
		$action_url           = ! empty( $item['action_url'] ) ? $item['action_url'] : '';

		if ( empty( $action_url ) ) {
			return;
		}

		if ( 'external_link' === $item['action_kind'] ) {
			// Fallback if link was meant to be truly external
			$rel_parts = array();

			if ( $item['target_blank'] ) {
				$rel_parts[] = 'noopener';
				$rel_parts[] = 'noreferrer';
			}

			if ( $item['nofollow'] ) {
				$rel_parts[] = 'nofollow';
			}

			$rel = implode( ' ', array_unique( $rel_parts ) );
			?>
			<div class="skb-gallery-icon">
				<a href="<?php echo esc_url( $action_url ); ?>"
					aria-label="<?php echo esc_attr( sprintf( __( 'Open %s', 'skyboot-pg' ), $item_title ) ); ?>"
					<?php if ( $item['target_blank'] ) : ?>
						target="_blank"
					<?php endif; ?>
					<?php if ( '' !== $rel ) : ?>
						rel="<?php echo esc_attr( $rel ); ?>"
					<?php endif; ?>>
					<?php
					Icons_Manager::render_icon( array( 'value' => 'fas fa-external-link-alt', 'library' => 'fa-solid' ), array( 'aria-hidden' => 'true' ) );
					?>
				</a>
			</div>
			<?php
			return;
		}

		if ( 'yes' !== $enable_popup ) {
			return;
		}
		?>
		<div class="skb-gallery-icon">
			<a class="skb-popup vbox-item"
				data-gall="gall-<?php echo esc_attr( $widget_id_class ); ?>"
				<?php if ( 'yes' === $enable_popup_content && '' !== $item_description ) : ?>
					data-title="<?php echo esc_attr( $item_description ); ?>"
				<?php endif; ?>
				<?php if ( 'popup_video' === $item['action_kind'] ) : ?>
					<?php if ( strpos( $action_url, 'youtu' ) !== false ) : ?>
						data-vbtype="youtube"
					<?php elseif ( strpos( $action_url, 'vimeo' ) !== false ) : ?>
						data-vbtype="vimeo"
					<?php else : ?>
						data-vbtype="video"
					<?php endif; ?>
					data-autoplay="true"
				<?php endif; ?>
				<?php if ( 'popup_iframe' === $item['action_kind'] ) : ?>
					<?php if ( strpos( $action_url, 'youtu' ) !== false ) : ?>
						data-vbtype="youtube"
					<?php elseif ( strpos( $action_url, 'vimeo' ) !== false ) : ?>
						data-vbtype="vimeo"
					<?php else : ?>
						data-vbtype="iframe"
					<?php endif; ?>
				<?php endif; ?>
				href="<?php echo esc_url( $action_url ); ?>"
				aria-label="<?php echo esc_attr( sprintf( __( 'Open %s', 'skyboot-pg' ), $item_title ) ); ?>">
				<?php
				if ( 'video' === $item['type'] ) {
					Icons_Manager::render_icon( array( 'value' => 'fas fa-play', 'library' => 'fa-solid' ), array( 'aria-hidden' => 'true' ) );
				} elseif ( 'link' === $item['type'] ) {
					Icons_Manager::render_icon( array( 'value' => 'fas fa-link', 'library' => 'fa-solid' ), array( 'aria-hidden' => 'true' ) );
				} elseif ( ! empty( $settings['set_icon'] ) ) {
					Icons_Manager::render_icon( $settings['set_icon'], array( 'aria-hidden' => 'true' ) );
				}
				?>
			</a>
		</div>
		<?php
	}

	protected function render( $instance = array() ) {
		$settings                       = $this->get_settings_for_display();
		$gallery_items                  = $this->normalize_gallery_items( $settings );
		$enable_sec_heading             = $this->get_settings_for_display( 'enable_sec_heading' );
		$enable_sec_separator           = $this->get_settings_for_display( 'enable_sec_separator' );
		$skb_section_heading            = $this->get_settings_for_display( 'skb_section_heading' );
		$skb_sub_heading                = $this->get_settings_for_display( 'skb_sub_heading' );
		$enable_filter_menu             = $this->get_settings_for_display( 'enable_filter_menu' );
		$enable_title                   = $this->get_settings_for_display( 'enable_title' );
		$enable_sub_title               = $this->get_settings_for_display( 'enable_sub_title' );
		$enable_overlay                 = $this->get_settings_for_display( 'enable_overlay' );
		$overlay_type                   = $this->get_settings_for_display( 'overlay_type' );
		$enable_mouse_hover_image_zoom  = $this->get_settings_for_display( 'enable_mouse_hover_image_zoom' );
		$popup_overlay_bg               = $this->get_settings_for_display( 'popup_overlay_bg' );
		$gallery_layout                 = $this->get_settings_for_display( 'gallery_layout' );
		$col                            = isset( $settings['column_count'] ) ? absint( $settings['column_count'] ) : 4;
		$col                            = $col > 0 ? $col : 4;
		$widget_id_class                = 'skb-id-' . sanitize_html_class( $this->get_id() );
		$available_filters              = array();

		foreach ( $gallery_items as $gallery_item ) {
			if ( empty( $gallery_item['filters'] ) || ! is_array( $gallery_item['filters'] ) ) {
				continue;
			}

			$available_filters += $gallery_item['filters'];
		}

		$gallery_layout = in_array( $gallery_layout, array( 'masonry', 'fitRows' ), true ) ? $gallery_layout : 'masonry';
		?>

		<div class="skb-gellery-area <?php echo esc_attr( $widget_id_class ); ?> skb-layout-<?php echo esc_attr( $gallery_layout ); ?>">
			<div class="skb-row">
				<div class="skb-col-xs-12">
					<?php if ( 'yes' === $enable_sec_heading ) : ?>
						<div class="skb-section-title">
							<?php if ( 'yes' === $enable_sec_separator ) : ?>
								<span class="skb-section-title-separator"></span>
							<?php endif; ?>

							<?php if ( ! empty( $skb_section_heading ) ) : ?>
								<h2><?php echo esc_html( $skb_section_heading ); ?></h2>
							<?php endif; ?>

							<?php if ( ! empty( $skb_sub_heading ) ) : ?>
								<p><?php echo esc_html( $skb_sub_heading ); ?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( 'yes' === $enable_filter_menu && ! empty( $available_filters ) ) : ?>
						<div class="skb-button-group text-center">
							<button class="button is-checked" data-filter="*" aria-label="<?php esc_attr_e( 'Show all items', 'skyboot-pg' ); ?>"><?php esc_html_e( 'All', 'skyboot-pg' ); ?></button>
							<?php foreach ( $available_filters as $filter_slug => $filter_label ) : ?>
								<button class="button" data-filter=".<?php echo esc_attr( $filter_slug ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Filter by %s', 'skyboot-pg' ), $filter_label ) ); ?>">
									<?php echo esc_html( $filter_label ); ?>
								</button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<div class="skb-row">
				<div class="skb-grid">
					<?php if ( ! empty( $gallery_items ) ) : ?>
						<?php foreach ( $gallery_items as $gallery_item ) : ?>
							<?php
							$item_filter_css = ! empty( $gallery_item['filters'] ) ? implode( ' ', array_keys( $gallery_item['filters'] ) ) : '';
							$item_classes    = array(
								'skb-col-sm-' . $col,
								'skb-col-xs-12',
								'skb-grid-item',
								$item_filter_css,
								'skb-item-type-' . sanitize_html_class( $gallery_item['type'] ),
							);
							?>
							<div class="<?php echo esc_attr( trim( implode( ' ', $item_classes ) ) ); ?>">
								<div class="skb-gallery-item <?php echo ( 'yes' === $enable_mouse_hover_image_zoom ) ? 'image-mouse-hover' : ''; ?>">
									<?php if ( 'yes' === $enable_overlay ) : ?>
										<div class="<?php echo ( 'direction_hover' === $overlay_type ) ? 'skb-direction-hover-effect' : 'normal-effect'; ?> hover-effect-bg"></div>
									<?php endif; ?>

									<?php if ( ! empty( $gallery_item['preview_url'] ) ) : ?>
										<div class="skb-gallery-image">
											<img src="<?php echo esc_url( $gallery_item['preview_url'] ); ?>" alt="<?php echo esc_attr( $gallery_item['alt_text'] ); ?>">
										</div>
									<?php else : ?>
										<div class="skb-gallery-image skb-gallery-image-placeholder"></div>
									<?php endif; ?>

									<div class="gallery-text text-center">
										<?php $this->render_gallery_item_action( $gallery_item, $settings, $widget_id_class ); ?>

										<div class="skb-gallery-inner-content">
											<?php if ( 'yes' === $enable_title && '' !== trim( $gallery_item['title'] ) ) : ?>
												<h4><?php echo esc_html( $gallery_item['title'] ); ?></h4>
											<?php endif; ?>

											<?php if ( 'yes' === $enable_sub_title && '' !== trim( $gallery_item['description'] ) ) : ?>
												<span><?php echo esc_html( wp_strip_all_tags( $gallery_item['description'] ) ); ?></span>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					<?php else : ?>
						<div class="skb-col-xs-12">
							<p class="skb-portfolio-empty"><?php esc_html_e( 'Add images, videos or links in the Elementor widget to display your gallery.', 'skyboot-pg' ); ?></p>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<script>
			jQuery(document).ready(function($) {
				var $skbwrap = $('.skb-gellery-area.<?php echo esc_js( $widget_id_class ); ?>');
				var $portfolio = $skbwrap.find('.skb-grid');

				$portfolio.imagesLoaded(function() {
					if ($.fn.isotope) {
						var isotopeOptions = {
							itemSelector: '.skb-grid-item',
							filter: '*',
							resizesContainer: true,
							layoutMode: '<?php echo esc_js( $gallery_layout ); ?>',
							transitionDuration: '0.8s'
						};

						if ('<?php echo esc_js( $gallery_layout ); ?>' === 'masonry') {
							isotopeOptions.masonry = {
								columnWidth: '.skb-grid-item'
							};
						}

						$portfolio.isotope(isotopeOptions);

						$skbwrap.find('.skb-button-group button').on('click', function() {
							$skbwrap.find('.skb-button-group button').removeClass('is-checked');
							$(this).addClass('is-checked');
							$portfolio.isotope({
								filter: $(this).attr('data-filter')
							});
						});
					}
				});

				if ($.fn.hoverdir) {
					$skbwrap.find('.skb-gallery-item').each(function() {
						$(this).hoverdir();
					});
				}

				if ($.fn.venobox) {
					$skbwrap.find('.skb-popup').venobox({
						border: '0',
						numeratio: false,
						infinigall: true,
						overlayColor: '<?php echo esc_js( ! empty( $popup_overlay_bg ) ? $popup_overlay_bg : 'rgba(0,0,0,0.8)' ); ?>',
						bgcolor: 'transparent',
						arrowsColor: '#ffffff',
						closeColor: '#ffffff',
						titleattr: 'data-title',
						titlePosition: 'bottom',
						titleBackground: '#000000',
						titleColor: '#fff',
						spinColor: '#ffffff',
						spinner: 'cube-grid'
					});
				}
			});
		</script>
		<?php
	}

	protected function content_template() {}
}

Plugin::instance()->widgets_manager->register( new \Elementor\Skyboot_Portfolio_Elementor_Widget() );
