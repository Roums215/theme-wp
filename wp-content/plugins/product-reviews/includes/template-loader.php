<?php // phpcs:ignore
namespace SKTPREVIEW;

// if direct access than exit the fi le.
defined( 'ABSPATH' ) || exit;

use WP_Error;

/**
 * Template Loader Class
 *
 * Handles the loading of templates for the plugin.
 */
final class Template_Loader {

	private static $plugin_template_path     = ''; // phpcs:ignore
	private static $pro_plugin_template_path = ''; // phpcs:ignore
	private static $theme_path               = ''; // phpcs:ignore
	private static $child_theme_path         = ''; // phpcs:ignore

	/**
	 * Constructor.
	 */
	public function __construct() {

		self::$plugin_template_path = SKTPR_PLUGIN_DIR . 'templates/';

		add_action( 'init', array( $this, 'set_theme_template_path' ) );
	}

	/**
	 * Sets the theme template path.
	 */
	public function set_theme_template_path() {

		$dir = apply_filters( 'sktpr_templates_folder', 'product-reviews' );

		if ( $dir ) {
			$dir              = '/' . trailingslashit( ltrim( $dir, '/\\' ) );
			self::$theme_path = get_template_directory() . $dir;

			if ( is_child_theme() ) {
				self::$child_theme_path = get_stylesheet_directory() . $dir;
			}
		}
	}

	/**
	 * Locates the template file.
	 *
	 * @param string $template_file The template file to locate.
	 * @return string|WP_Error The path to the template file or WP_Error if not found.
	 */
	public static function locate_template( $template_file ) {

		// Default path.
		$path = self::$plugin_template_path;

		// Check if requested file exist in plugin.
		if ( ! empty( self::$pro_plugin_template_path ) && file_exists( self::$pro_plugin_template_path . $template_file ) ) {
			$path = self::$pro_plugin_template_path;
		} elseif ( ! file_exists( $path . $template_file ) ) {
				return new WP_Error( 'sktpr_plugin_template_not_found', __( 'Template file not found - GS Plugins', 'product-reviews' ) );
		}

		// Override default template if exist from theme.
		if ( file_exists( self::$theme_path . $template_file ) ) {
			$path = self::$theme_path;
		}

		if ( is_child_theme() ) {
			// Override default template if exist from child theme.
			if ( file_exists( self::$child_theme_path . $template_file ) ) {
				$path = self::$child_theme_path;
			}
		}

		// Return template path, it can be default or overridden by theme.
		return $path . $template_file;
	}
}
