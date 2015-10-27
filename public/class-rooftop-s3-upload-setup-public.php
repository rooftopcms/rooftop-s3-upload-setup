<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/rooftopcms
 * @since      1.0.0
 *
 * @package    Rooftop_S3_Offload_Setup
 * @subpackage Rooftop_S3_Offload_Setup/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rooftop_S3_Offload_Setup
 * @subpackage Rooftop_S3_Offload_Setup/public
 * @author     Error <hello@rooftopcms.com>
 */
class Rooftop_S3_Offload_Setup_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rooftop_S3_Offload_Setup_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rooftop_S3_Offload_Setup_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rooftop-s3-upload-setup-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rooftop_S3_Offload_Setup_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rooftop_S3_Offload_Setup_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rooftop-s3-upload-setup-public.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * get the current site's S3 access key and secret key. if they've been
     * set, define the globals that are picked up by wp-amazon-s3-and-cloudfront uploader
     */
    public function define_s3_keys() {
        $blog_id = get_current_blog_id();

        if($blog_id) {
            $access_key_id = get_blog_option($blog_id, 'access_key_id');
            $secret_access_key = get_blog_option($blog_id, 'secret_access_key');

            $updating_s3_credentials = 'POST' === $_SERVER['REQUEST_METHOD'] && '/wp-admin/admin.php?page=rooftop-s3-upload-setup-overview' === $_SERVER['REQUEST_URI'];
            if( !$updating_s3_credentials && ($access_key_id && $secret_access_key) ) {
                define('AWS_ACCESS_KEY_ID', $access_key_id);
                define('AWS_SECRET_ACCESS_KEY', $secret_access_key);
            }
        }
    }

    /**
     * implement wp-upload-s3's as3cf_setting_bucket hook to return our own
     */
    public function get_s3_bucket_name($bucket) {
        if("rooftop.main" === $bucket) {
            return $bucket;
        }

        $blog_id = get_current_blog_id();
        $options = get_blog_details($blog_id, 'domain', false);
        $domain = $options->domain;
        $sub_domain = explode(".", $domain)[0];

        $sub_domain = preg_replace("/[^a-zA-Z0-9]/", "", $sub_domain);
        $bucket = $sub_domain.".media.rooftop.io";

        return $bucket;
    }
}
