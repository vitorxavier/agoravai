<?php
/**
 * MedZone_Lite Theme Hooks
 *
 * @package MedZone_Lite
 * @since   1.0
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class MedZone_Lite_Hooks
 */
class MedZone_Lite_Hooks {
	/**
	 * MedZone_Lite_Hooks constructor.
	 */
	public function __construct() {
		/**
		 * Custom body classes
		 */
		add_filter( 'body_class', array( $this, 'body_classes' ) );
		/**
		 * Flush the category transient on category edit or post save
		 */
		add_action( 'edit_category', array( $this, 'category_transient_flusher' ) );
		add_action( 'save_post', array( $this, 'category_transient_flusher' ) );
		/**
		 * Add a <span> html tag to the category item
		 */
		add_filter( 'wp_list_categories', array( $this, 'add_span_to_count' ) );
		add_filter( 'get_archives_link', array( $this, 'add_span_to_count' ) );
		/**
		 * Fix responsive videos
		 */
		add_filter( 'embed_oembed_html', array( $this, 'fix_responsive_videos' ), 10, 3 );
		add_filter( 'video_embed_html', array( $this, 'fix_responsive_videos' ) );
		/**
		 * Custom Image Sizes available in JS
		 */
		add_filter( 'image_size_names_choose', array( $this, 'custom_image_sizes' ) );
		/**
		 * Override text domain for the framework
		 */
		add_filter( 'override_load_textdomain', array( $this, 'override_load_textdomain' ), 10, 2 );
		/**
		 * Allow SVG Uploads
		 */
		add_filter( 'upload_mimes', array( $this, 'allow_svg_uploads' ) );
	}

	/**
	 * Allow multiple textdomains in a theme
	 * https://gist.github.com/justintadlock/7a605c29ae26c80878d0
	 *
	 * @param string $override Text domain used as override.
	 * @param string $domain   Current domain override.
	 *
	 * @return string
	 */
	public function override_load_textdomain( $override, $domain ) {
		if ( 'epsilon-framework' === $domain ) {
			global $l10n;
			if ( isset( $l10n['medzone-lite'] ) ) {
				$l10n[ $domain ] = $l10n['medzone-lite'];
			}

			$override = true;
		}

		return $override;
	}

	/**
	 * Filter the categories widget to add a <span> element before the count
	 *
	 * @param string $links link html string.
	 *
	 * @return mixed
	 */
	public function add_span_to_count( $links = '' ) {
		$links = str_replace( '</a>&nbsp;(', '</a> <span class="medzone-count">', $links );
		$links = str_replace( ')', '</span>', $links );

		return $links;
	}

	/**
	 * Flush out the transients used in categorized blog.
	 */
	public function category_transient_flusher() {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		// Like, beat it. Dig?
		delete_transient( 'medzone_lite_categories' );
	}

	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @param array $classes Classes for the body element.
	 *
	 * @return array
	 */
	public function body_classes( $classes ) {
		// Adds a class of group-blog to blogs with more than 1 published author.
		if ( is_multi_author() ) {
			$classes[] = 'group-blog';
		}

		// Adds a class of hfeed to non-singular pages.
		if ( ! is_singular() ) {
			$classes[] = 'hfeed';
		}

		return $classes;
	}

	/**
	 * Adds the responsive container to the video embeds
	 *
	 * @param string $html html string.
	 *
	 * @return string
	 */
	public function fix_responsive_videos( $html ) {
		return '<div class="responsive-video-container">' . $html . '</div>';
	}

	/**
	 * Return more image sizes
	 *
	 * @param array $sizes Array of sizes.
	 *
	 * @return array
	 */
	public function custom_image_sizes( $sizes ) {
		$custom_sizes = array(
			'medzone-blog-image'           => esc_html__( 'Blog Image', 'medzone-lite' ),
			'medzone-hospital-slider'      => esc_html__( 'Hospital Slider Image', 'medzone-lite' ),
			'medzone-doctor-portrait'      => esc_html__( 'Doctor Portrait', 'medzone-lite' ),
			'medzone-testimonial-portrait' => esc_html__( 'Testimonial Portrait', 'medzone-lite' ),
		);

		return array_merge( $sizes, $custom_sizes );
	}

	/**
	 * @param $mimes
	 *
	 * @return mixed
	 */
	public function allow_svg_uploads( $mimes ) {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}
}
