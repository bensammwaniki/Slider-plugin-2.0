<?php
/**
 * Plugin Name: Bensam Slider
 * Description: Enhance Elementor with customizable hero sliders, review carousels, and portfolio showcases, featuring responsive design, animations, and hover effects.
 * Version: 1.7.1
 * Author: Bensam Mwaniki
 * Author URI: https://www.linkedin.com/in/bensam-mwaniki-njoroge/
 * Text Domain: daily-slider
 * Elementor tested up to: 3.26.3
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */


 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class DailySliderPlugin {
    const VERSION = '1.7.1';
    const MINIMUM_ELEMENTOR_VERSION = '3.26.0';
    const MINIMUM_PHP_VERSION = '7.4';

    private static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        add_action( 'plugins_loaded', array( $this, 'init' ) );
    }

    public function init() {
        if ( ! did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', array( $this, 'admin_notice_missing_main_plugin' ) );
            return;
        }

        if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
            add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
            return;
        }

        if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
            add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php_version' ) );
            return;
        }

        add_action( 'elementor/elements/categories_registered', array( $this, 'add_elementor_category' ) );
        add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
        // Register assets for both Elementor editor preview and live frontend.
        add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_assets' ) );
        add_action( 'elementor/frontend/after_register_styles', array( $this, 'register_assets' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ), 5 );
        add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'enqueue_editor_assets' ) );
    }

    public function register_assets() {
        static $assets_registered = false;
        if ( $assets_registered ) {
            return;
        }
        $assets_registered = true;

        $asset_version = static function( $relative_path ) {
            $path = plugin_dir_path( __FILE__ ) . ltrim( $relative_path, '/' );
            return file_exists( $path ) ? (string) filemtime( $path ) : self::VERSION;
        };

        // Styles
        wp_register_style( 'DailySlider-common-styles', plugins_url( 'assets/css/common.css', __FILE__ ), array(), $asset_version( 'assets/css/common.css' ) );
        wp_register_style( 'DailySlider-eldorado-styles', plugins_url( 'assets/css/widgets/eldorado.css', __FILE__ ), array(), $asset_version( 'assets/css/widgets/eldorado.css' ) );
        wp_register_style( 'DailySlider-pixel-styles', plugins_url( 'assets/css/widgets/pixel.css', __FILE__ ), array(), $asset_version( 'assets/css/widgets/pixel.css' ) );
        wp_register_style( 'DailySlider-review-carousel-styles', plugins_url( 'assets/css/widgets/review-carousel.css', __FILE__ ), array(), $asset_version( 'assets/css/widgets/review-carousel.css' ) );
        wp_register_style( 'DailySlider-marquee-styles', plugins_url( 'assets/css/widgets/marquee.css', __FILE__ ), array(), $asset_version( 'assets/css/widgets/marquee.css' ) );
        wp_register_style( 'DailySlider-modal-styles', plugins_url( 'assets/css/widgets/modal.css', __FILE__ ), array(), $asset_version( 'assets/css/widgets/modal.css' ) );

        // Scripts
        wp_register_script( 'DailySlider-eldorado-scripts', plugins_url( 'assets/js/widgets/eldorado.js', __FILE__ ), array( 'jquery', 'elementor-frontend', 'swiper' ), $asset_version( 'assets/js/widgets/eldorado.js' ), true );
        wp_register_script( 'DailySlider-pixel-scripts', plugins_url( 'assets/js/widgets/pixel.js', __FILE__ ), array( 'jquery', 'elementor-frontend', 'swiper' ), $asset_version( 'assets/js/widgets/pixel.js' ), true );
        wp_register_script( 'DailySlider-review-carousel-scripts', plugins_url( 'assets/js/widgets/review-carousel.js', __FILE__ ), array( 'jquery', 'elementor-frontend', 'swiper' ), $asset_version( 'assets/js/widgets/review-carousel.js' ), true );
        wp_register_script( 'DailySlider-marquee-scripts', plugins_url( 'assets/js/widgets/marquee.js', __FILE__ ), array( 'jquery', 'elementor-frontend' ), $asset_version( 'assets/js/widgets/marquee.js' ), true );
        wp_register_script( 'DailySlider-modal-scripts', plugins_url( 'assets/js/widgets/modal.js', __FILE__ ), array( 'jquery' ), $asset_version( 'assets/js/widgets/modal.js' ), true );

        // Skyboot Portfolio widget assets (merged into this plugin package).
        wp_register_style( 'skb-framework-css', plugins_url( 'assets/css/widgets/gallery-framework.css', __FILE__ ), array(), $asset_version( 'assets/css/widgets/gallery-framework.css' ) );
        wp_register_style( 'skb-venobox', plugins_url( 'assets/css/widgets/gallery-venobox.css', __FILE__ ), array(), $asset_version( 'assets/css/widgets/gallery-venobox.css' ) );
        wp_register_style( 'skyboot-portfolio-style', plugins_url( 'assets/css/widgets/gallery.css', __FILE__ ), array(), $asset_version( 'assets/css/widgets/gallery.css' ) );
        wp_register_style( 'skb-portfolio-responsive', plugins_url( 'assets/css/widgets/gallery-responsive.css', __FILE__ ), array(), $asset_version( 'assets/css/widgets/gallery-responsive.css' ) );

        wp_register_script( 'skb-modernizr', plugins_url( 'assets/js/widgets/gallery-modernizr-2.8.3.min.js', __FILE__ ), array( 'jquery' ), '2.8.3', false );
        wp_register_script( 'skb-isotope', plugins_url( 'assets/js/widgets/gallery-isotope.pkgd.min.js', __FILE__ ), array( 'jquery', 'imagesloaded' ), $asset_version( 'assets/js/widgets/gallery-isotope.pkgd.min.js' ), true );
        wp_register_script( 'skb-hoverdir', plugins_url( 'assets/js/widgets/gallery-hoverdir.js', __FILE__ ), array( 'jquery', 'skb-modernizr' ), $asset_version( 'assets/js/widgets/gallery-hoverdir.js' ), true );
        wp_register_script( 'skb-venobox', plugins_url( 'assets/js/widgets/gallery-venobox.js', __FILE__ ), array( 'jquery' ), $asset_version( 'assets/js/widgets/gallery-venobox.js' ), true );
    }

    public function enqueue_editor_assets() {
        $asset_version = file_exists( plugin_dir_path( __FILE__ ) . 'assets/js/widgets/modal-editor.js' ) 
            ? (string) filemtime( plugin_dir_path( __FILE__ ) . 'assets/js/widgets/modal-editor.js' ) 
            : self::VERSION;

        wp_enqueue_script(
            'DailySlider-modal-editor',
            plugins_url( 'assets/js/widgets/modal-editor.js', __FILE__ ),
            [ 'elementor-editor' ],
            $asset_version,
            true
        );
    }


    public function add_elementor_category( $elements_manager ) {
        $elements_manager->add_category(
            'DailySlider-category',
            [
                'title' => esc_html__( 'BensamSlider', 'daily-slider' ),
                'icon' => 'eicon-cog',
            ],
            1
        );
    }

    public function register_widgets( $widgets_manager ) {

        require_once plugin_dir_path( __FILE__ ) . 'widgets/eldorado/eldorado.php';
        $widgets_manager->register( new \DailySlider\Widgets\Eldorado_Widget() );

        require_once plugin_dir_path( __FILE__ ) . 'widgets/pixel/pixel.php';
        $widgets_manager->register( new \DailySlider\Widgets\Pixel_Widget() );

        require_once plugin_dir_path( __FILE__ ) . 'widgets/review-carousel/review-carousel.php';
        $widgets_manager->register( new \DailySlider\Widgets\ReviewCarousel_Widget() );

        require_once plugin_dir_path( __FILE__ ) . 'widgets/marquee/marquee.php';
        $widgets_manager->register( new \DailySlider\Widgets\Marquee_Widget() );

        require_once plugin_dir_path( __FILE__ ) . 'widgets/modal/modal.php';
        $widgets_manager->register( new \DailySlider\Widgets\Modal_Widget() );

        // Load merged Skyboot portfolio widget (self-registers with Elementor).
        $skyboot_widget_file = plugin_dir_path( __FILE__ ) . 'widgets/gallery/gallery.php';
        if ( file_exists( $skyboot_widget_file ) ) {
            require_once $skyboot_widget_file;
        }
    }
    
    
    public function admin_notice_missing_main_plugin() {
        $message = sprintf(
            // translators: %1$s is the plugin name, %2$s is the required plugin name.
            esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'daily-slider' ),
            '<strong>' . esc_html__( 'DailySlider', 'daily-slider' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor', 'daily-slider' ) . '</strong>'
        );
        printf( '<div class="notice notice-warning daily-slider-notice is-dismissible"><p>%s</p></div>', wp_kses_post( $message ) );
    }
    
    public function admin_notice_minimum_php_version() {
        $message = sprintf(
            // translators: %1$s is the plugin name, %2$s is "PHP", %3$s is the required PHP version.
            esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'daily-slider' ),
            '<strong>' . esc_html__( 'DailySlider', 'daily-slider' ) . '</strong>',
            '<strong>' . esc_html__( 'PHP', 'daily-slider' ) . '</strong>',
            esc_html( self::MINIMUM_PHP_VERSION )
        );
        printf( '<div class="notice notice-warning daily-slider-notice is-dismissible"><p>%s</p></div>', wp_kses_post( $message ) );
    }
    
    public function admin_notice_minimum_elementor_version() {
        $message = sprintf(
            // translators: %1$s is the plugin name, %2$s is "Elementor", %3$s is the required Elementor version.
            esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'daily-slider' ),
            '<strong>' . esc_html__( 'DailySlider', 'daily-slider' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor', 'daily-slider' ) . '</strong>',
            esc_html( self::MINIMUM_ELEMENTOR_VERSION )
        );
        printf( '<div class="notice notice-warning daily-slider-notice is-dismissible"><p>%s</p></div>', wp_kses_post( $message ) );
    }
    
    
    
    
}

DailySliderPlugin::instance();

