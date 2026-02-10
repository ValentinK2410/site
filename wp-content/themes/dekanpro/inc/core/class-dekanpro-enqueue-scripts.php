<?php
/**
 * Enqueue scripts & styles.
 *
 * @package     Dekanpro
 * @author      DekanPro
 * @since       1.0.0
 */

/**
 * Enqueue and register scripts and styles.
 *
 * @since 1.0.0
 */
class Dekanpro_Enqueue_Scripts {

	/**
	 * Check if debug is on
	 *
	 * @var boolean
	 */
	private $is_debug;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->is_debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
		add_action( 'wp_enqueue_scripts', array( $this, 'dekanpro_enqueues' ) );
		add_action( 'wp_print_footer_scripts', array( $this, 'dekanpro_skip_link_focus_fix' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'dekanpro_block_editor_assets' ) );
	}

	/**
	 * Enqueue styles and scripts.
	 *
	 * @since 1.0.0
	 */
	public function dekanpro_enqueues() {
		// Script debug.
		$dekanpro_dir    = $this->is_debug ? 'dev/' : '';
		$dekanpro_suffix = $this->is_debug ? '' : '.min';

		wp_enqueue_style( 'swiper', DEKANPRO_THEME_URI . '/assets/css/swiper-bundle' . $dekanpro_suffix . '.css' );

		wp_enqueue_script( 'swiper', DEKANPRO_THEME_URI . '/assets/js/' . $dekanpro_dir . 'vendors/swiper-bundle' . $dekanpro_suffix . '.js', array(), false, true );

		// fontawesome enqueue.
		wp_enqueue_style(
			'FontAwesome',
			DEKANPRO_THEME_URI . '/assets/css/all' . $dekanpro_suffix . '.css',
			false,
			'5.15.4',
			'all'
		);
		// Enqueue theme stylesheet.
		wp_enqueue_style(
			'dekanpro-styles',
			DEKANPRO_THEME_URI . '/assets/css/style' . $dekanpro_suffix . '.css',
			false,
			DEKANPRO_THEME_VERSION,
			'all'
		);

		// Enqueue custom modern styles.
		wp_enqueue_style(
			'dekanpro-custom-modern',
			DEKANPRO_THEME_URI . '/assets/css/custom-modern.css',
			array( 'dekanpro-styles' ),
			DEKANPRO_THEME_VERSION,
			'all'
		);

		// Register Dekanpro slider.
		wp_register_script(
			'dekanpro-slider',
			DEKANPRO_THEME_URI . '/assets/js/' . $dekanpro_dir . 'dekanpro-slider' . $dekanpro_suffix . '.js',
			array( 'imagesloaded' ),
			DEKANPRO_THEME_VERSION,
			true
		);

		wp_register_script(
			'dekanpro-marquee',
			DEKANPRO_THEME_URI . '/assets/js/' . $dekanpro_dir . 'vendors/vanilla-marquee' . $dekanpro_suffix . '.js',
			array( 'imagesloaded' ),
			DEKANPRO_THEME_VERSION,
			true
		);

        if ( wp_script_is("wc-cart-fragments", "registered") && !is_cart() && !is_checkout() && !wp_script_is("wc-cart-fragments", "enqueued") ) {
            wp_enqueue_script("wc-cart-fragments");
        }

		if ( dekanpro()->options->get( 'dekanpro_blog_masonry' ) ) {
			wp_enqueue_script( 'masonry' );
		}

		// Load comment reply script if comments are open.
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// Enqueue main theme script.
		wp_enqueue_script(
			'dekanpro',
			DEKANPRO_THEME_URI . '/assets/js/' . $dekanpro_dir . 'dekanpro' . $dekanpro_suffix . '.js',
			array( 'jquery', 'imagesloaded' ),
			DEKANPRO_THEME_VERSION,
			true
		);

		// Enqueue Tubes Cursor effect (WebGL background).
		wp_enqueue_script(
			'dekanpro-tubes-cursor',
			DEKANPRO_THEME_URI . '/assets/js/tubes-cursor.js',
			array(),
			DEKANPRO_THEME_VERSION,
			true
		);

		// Enqueue Smart Sticky Header.
		wp_enqueue_script(
			'dekanpro-sticky-header',
			DEKANPRO_THEME_URI . '/assets/js/sticky-header.js',
			array(),
			DEKANPRO_THEME_VERSION,
			true
		);

		// Enqueue Prism.js for syntax highlighting.
		wp_enqueue_style(
			'prism-theme',
			'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css',
			array(),
			'1.29.0'
		);
		wp_enqueue_script(
			'prism-core',
			'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js',
			array(),
			'1.29.0',
			true
		);
		wp_enqueue_script(
			'prism-autoloader',
			'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js',
			array('prism-core'),
			'1.29.0',
			true
		);

		// Comment count used in localized strings.
		$comment_count = get_comments_number();

		// Localized variables so they can be used for translatable strings.
		$localized = array(
			'ajaxurl'               	=> esc_url( admin_url( 'admin-ajax.php' ) ),
			'nonce'                 	=> wp_create_nonce( 'dekanpro-nonce' ),
			'live-search-nonce'     	=> wp_create_nonce( 'dekanpro-live-search-nonce' ),
			'post-like-nonce'       	=> wp_create_nonce( 'dekanpro-post-like-nonce' ),
			'close'                 	=> esc_html__( 'Close', 'dekanpro' ),
			'no_results'            	=> esc_html__( 'No results found', 'dekanpro' ),
			'more_results'          	=> esc_html__( 'More results', 'dekanpro' ),
			'responsive-breakpoint' 	=> intval( dekanpro_option( 'main_nav_mobile_breakpoint' ) ),
			'dark_mode' 				=> (bool) dekanpro_option( 'dark_mode' ),
			'sticky-header'         	=> array(
				'enabled' => dekanpro_option( 'sticky_header' ),
				'hide_on' => dekanpro_option( 'sticky_header_hide_on' ),
			),
			'strings'               => array(
				/* translators: %s Comment count */
				'comments_toggle_show' => $comment_count > 0 ? esc_html( sprintf( _n( 'Show %s Comment', 'Show %s Comments', $comment_count, 'dekanpro' ), $comment_count ) ) : esc_html__( 'Leave a Comment', 'dekanpro' ),
				'comments_toggle_hide' => esc_html__( 'Hide Comments', 'dekanpro' ),
			),
		);

		wp_localize_script(
			'dekanpro',
			'dekanpro_vars',
			apply_filters( 'dekanpro_localized', $localized )
		);

		// Enqueue google fonts.
		dekanpro()->fonts->enqueue_google_fonts();

		// Add additional theme styles.
		do_action( 'dekanpro_enqueue_scripts' );
	}

	/**
	 * Skip link focus fix for IE11.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function dekanpro_skip_link_focus_fix() {
		?>
		<script>
			! function() {
				var e = -1 < navigator.userAgent.toLowerCase().indexOf("webkit"),
					t = -1 < navigator.userAgent.toLowerCase().indexOf("opera"),
					n = -1 < navigator.userAgent.toLowerCase().indexOf("msie");
				(e || t || n) && document.getElementById && window.addEventListener && window.addEventListener("hashchange", function() {
					var e, t = location.hash.substring(1);
					/^[A-z0-9_-]+$/.test(t) && (e = document.getElementById(t)) && (/^(?:a|select|input|button|textarea)$/i.test(e.tagName) || (e.tabIndex = -1), e.focus())
				}, !1)
			}();
		</script>
		<?php
	}

	/**
	 * Enqueue assets for the Block Editor.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function dekanpro_block_editor_assets() {

		// RTL version.
		$rtl = is_rtl() ? '-rtl' : '';

		// Minified version.
		$min = $this->is_debug ? '' : '.min';
		// Enqueue block editor styles.
		wp_enqueue_style(
			'dekanpro-block-editor-styles',
			DEKANPRO_THEME_URI . '/inc/admin/assets/css/dekanpro-block-editor-styles' . $rtl . $min . '.css',
			false,
			DEKANPRO_THEME_VERSION,
			'all'
		);

		// Enqueue google fonts.
		dekanpro()->fonts->enqueue_google_fonts();

		// Add dynamic CSS as inline style.
		wp_add_inline_style(
			'dekanpro-block-editor-styles',
			apply_filters( 'dekanpro_block_editor_dynamic_css', dekanpro_dynamic_styles()->get_block_editor_css() )
		);
	}
}
