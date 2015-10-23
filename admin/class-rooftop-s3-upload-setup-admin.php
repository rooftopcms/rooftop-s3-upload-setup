<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/rooftopcms
 * @since      1.0.0
 *
 * @package    Rooftop_S3_Offload_Setup
 * @subpackage Rooftop_S3_Offload_Setup/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rooftop_S3_Offload_Setup
 * @subpackage Rooftop_S3_Offload_Setup/admin
 * @author     Error <hello@rooftopcms.com>
 */
class Rooftop_S3_Offload_Setup_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rooftop-s3-upload-setup-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rooftop-s3-upload-setup-admin.js', array( 'jquery' ), $this->version, false );

	}

    /**
     *
     */

    public function rooftop_add_s3_bucket(&$error=null) {
        $bucket = 'rooftop.site.'.get_current_blog_id();
        $bucket = apply_filters( 'as3cf_setting_bucket', $bucket );

        $amazon_webservices_path = ABSPATH . '../app/mu-plugins/amazon-web-services/amazon-web-services.php';
        $wp_s3_cloudfront_path   = ABSPATH . '../app/mu-plugins/amazon-s3-and-cloudfront/wordpress-s3.php';
        $webservice = new Amazon_Web_Services($amazon_webservices_path);
        $aws = new Amazon_S3_And_CloudFront($wp_s3_cloudfront_path, $webservice);

        try {
            $aws->create_bucket($bucket, 'eu-west-1');
            return true;
        } catch ( Exception $e ) {
            error_log( 'Error creating bucket S3: ' . $e->getMessage() );
            $error = $e;
            return;
        }
    }

    /**
     * create the WP admin UI for setting S3 keys
     *
     */
    public function rooftop_s3_menu_links() {
        $rooftop_webhook_menu_slug = "rooftop-overview";
        add_submenu_page($rooftop_webhook_menu_slug, "S3 Credentials", "S3 Credentials", "manage_options", $this->plugin_name."-overview", array($this, 'rooftop_s3_config_callback'));
    }

    function rooftop_s3_config_callback() {
        $blog_id = get_current_blog_id();
        $verify_nonce = false;

        if($_POST && array_key_exists('method', $_POST)) {
            $method = strtoupper($_POST['method']);
            $verify_nonce = true;
        }elseif($_POST && array_key_exists('id', $_POST)) {
            $method = 'PATCH';
            $verify_nonce = true;
        }else {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        if($verify_nonce) {
            if( ! isset($_POST['rooftop-s3-field-token']) || ! wp_verify_nonce($_POST['rooftop-s3-field-token'], 'rooftop-s3-offset-config') ) {
                print '<div class="wrap"><div class="errors"><p>Form token not verified</p></div></div>';
                exit;
            }
        }

        switch($method) {
            case 'GET':
                break;
            case 'POST':
                update_blog_option($blog_id, 'access_key_id', $_POST['access_key_id']);
                update_blog_option($blog_id, 'secret_access_key', $_POST['secret_access_key']);

                if( ! defined('AWS_ACCESS_KEY_ID') ) {
                    define('AWS_ACCESS_KEY_ID', $_POST['access_key_id']);
                }

                if( ! defined('AWS_SECRET_ACCESS_KEY') ) {
                    define('AWS_SECRET_ACCESS_KEY', $_POST['secret_access_key']);
                }

//                $current_bucket = get_blog_option($blog_id, 'bucket');
//                if(! $current_bucket ) {
                    $bucket = 'rooftop.site.'.get_current_blog_id();
                    $bucket = apply_filters( 'as3cf_setting_bucket', $bucket );

                    $error = null;
                    if(!$this->rooftop_add_s3_bucket($error)) {
                        exit("Couldn't create S3 bucket: $error");
                    }

                    /**
                     * As we remove the upload_files capability by default, we can restore
                     * it if we've been given some valid S3 credentials
                     */
                    global $wp_roles;
                    $can_upload = current_user_can('upload_files');

                    $upload_capabilities_restored = get_blog_option($blog_id, 'upload_capabilities_restored');
                    if(! $upload_capabilities_restored ) {
                        foreach($wp_roles->roles as $role => $role_attributes) {
                            $wp_roles->add_cap($role, 'upload_files');
                        }
                        update_blog_option($blog_id, 'upload_capabilities_restored', true);

                        $current_user = wp_get_current_user();
                        $current_user->add_cap('upload_files');
                    }

                    update_blog_option($blog_id, 'bucket', $bucket);
                    // need to a reload since we're potentially adding the the Media
                    // menu, but the hook for that has already been called at this point
                    echo "<script>window.location.reload()</script>";
//                }

                break;
            case 'DELETE':
                delete_blog_option($blog_id, 'access_key_id');
                delete_blog_option($blog_id, 'secret_access_key');
                break;
        }

        $this->rooftop_s3_form();
    }

    /**
     * render the form
     *
     */
    private function rooftop_s3_form() {
        $blog_id = get_current_blog_id();
        $access_key_id = get_blog_option($blog_id, 'access_key_id');
        $secret_access_key = get_blog_option($blog_id, 'secret_access_key');
        $current_bucket = get_blog_option($blog_id, 'bucket');

        require_once plugin_dir_path( __FILE__ ) . 'partials/rooftop-s3-upload-setup-admin-display.php';
        return;
    }
}
