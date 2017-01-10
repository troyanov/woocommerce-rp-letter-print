<?php
/**
 * Plugin Name:       WooCommerce Russian Post Letter Print
 * Description:       Печать конвертов для отправки писем через Почту России
 * Plugin URI:        http://github.com/troyanov/woocommerce-rp-letter-print
 * Version:           1.0.0
 *
 * @package WooCommerce_Russian_Post_Letter_Print
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main WooCommerce_Russian_Post_Letter_Print Class
 *
 * @class WooCommerce_Russian_Post_Letter_Print
 * @version	1.0.0
 * @since 1.0.0
 * @package	WooCommerce_Russian_Post_Letter_Print
 */
final class WooCommerce_Russian_Post_Letter_Print {

	/**
	 * Set up the plugin
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'WooCommerce_Russian_Post_Letter_Print_setup' ), -1 );
		require_once( 'custom/functions.php' );
	}

	/**
	 * Setup all the things
	 */
	public function WooCommerce_Russian_Post_Letter_Print_setup() {
		add_action( 'wp_enqueue_scripts', array( $this, 'WooCommerce_Russian_Post_Letter_Print_css' ), 999 );
		add_action( 'wp_enqueue_scripts', array( $this, 'WooCommerce_Russian_Post_Letter_Print_js' ) );
		add_filter( 'template_include',   array( $this, 'WooCommerce_Russian_Post_Letter_Print_template' ), 11 );
		add_filter( 'wc_get_template',    array( $this, 'WooCommerce_Russian_Post_Letter_Print_wc_get_template' ), 11, 5 );
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab'), 50 );
		add_action( 'woocommerce_settings_tabs_settings_tab_rp_letter_print', array( $this, 'settings_tab') );
		add_action( 'woocommerce_update_options_settings_tab_rp_letter_print', array( $this, 'update_settings') );

		add_action('admin_footer-edit.php', array( $this, 'custom_bulk_admin_footer') );
		add_action('load-edit.php', array( $this, 'custom_bulk_action') );
	}

	/**
	 * Enqueue the CSS
	 *
	 * @return void
	 */
	public function WooCommerce_Russian_Post_Letter_Print_css() {
		wp_enqueue_style( 'custom-css', plugins_url( '/custom/style.css', __FILE__ ) );
	}

	/**
	 * Enqueue the Javascript
	 *
	 * @return void
	 */
	public function WooCommerce_Russian_Post_Letter_Print_js() {
		wp_enqueue_script( 'custom-js', plugins_url( '/custom/custom.js', __FILE__ ), array( 'jquery' ) );
	}

	/**
	 * Look in this plugin for template files first.
	 * This works for the top level templates (IE single.php, page.php etc). However, it doesn't work for
	 * template parts yet (content.php, header.php etc).
	 *
	 * Relevant trac ticket; https://core.trac.wordpress.org/ticket/13239
	 *
	 * @param  string $template template string.
	 * @return string $template new template string.
	 */
	public function WooCommerce_Russian_Post_Letter_Print_template( $template ) {
		if ( file_exists( untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/custom/templates/' . basename( $template ) ) ) {
			$template = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/custom/templates/' . basename( $template );
		}

		return $template;
	}

	/**
	 * Look in this plugin for WooCommerce template overrides.
	 *
	 * For example, if you want to override woocommerce/templates/cart/cart.php, you
	 * can place the modified template in <plugindir>/custom/templates/woocommerce/cart/cart.php
	 *
	 * @param string $located is the currently located template, if any was found so far.
	 * @param string $template_name is the name of the template (ex: cart/cart.php).
	 * @return string $located is the newly located template if one was found, otherwise
	 *                         it is the previously found template.
	 */
	public function WooCommerce_Russian_Post_Letter_Print_wc_get_template( $located, $template_name, $args, $template_path, $default_path ) {
		$plugin_template_path = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/custom/templates/woocommerce/' . $template_name;

		if ( file_exists( $plugin_template_path ) ) {
			$located = $plugin_template_path;
		}

		return $located;
	}

	public function add_settings_tab( $settings_tabs ) {
    $settings_tabs['settings_tab_rp_letter_print'] = __( 'Печать Конвертов', 'woocommerce-settings-tab-rp_letter_print' );
    return $settings_tabs;
	}

	public function settings_tab() {
		woocommerce_admin_fields( $this->get_settings() );
	}

	public function update_settings() {
		woocommerce_update_options( $this->get_settings() );
	}

	public function get_settings() {
		$rp_letter_print_settings = array(
			'section_title' => array(
				'name'     => __( 'Печать конверта С6', 'woocommerce-settings-tab-rp_letter_print' ),
				'type'     => 'title',
				'desc'     => '',
				'id'       => 'wc_settings_tab_rp_letter_print_section_title'
			),
			'wc_settings_tab_rp_letter_print_sender_name' => array(
				'name' => __( 'От кого:', 'woocommerce-settings-tab-rp_letter_print' ),
				'type' => 'text',
				'desc' => '',
				'id'   => 'wc_settings_tab_rp_letter_print_section_sender_name'
			),      
			'wc_settings_tab_rp_letter_print_sender_address_line1' => array(
				'name' => __( 'Откуда (строка 1):', 'woocommerce-settings-tab-rp_letter_print' ),
				'type' => 'text',
				'desc' => '',
				'id'   => 'wc_settings_tab_rp_letter_print_section_sender_address_line1'
			),
			'wc_settings_tab_rp_letter_print_sender_address_line2' => array(
				'name' => __( 'Откуда (строка 2):', 'woocommerce-settings-tab-rp_letter_print' ),
				'type' => 'text',
				'desc' => '',
				'id'   => 'wc_settings_tab_rp_letter_print_section_sender_address_line2'
			),
			'wc_settings_tab_rp_letter_print_sender_address_line3' => array(
				'name' => __( 'Откуда (строка 3):', 'woocommerce-settings-tab-rp_letter_print' ),
				'type' => 'text',
				'desc' => '',
				'id'   => 'wc_settings_tab_rp_letter_print_section_sender_address_line3'
			),     
			'wc_settings_tab_rp_letter_print_sender_address_zipcode' => array(
				'name' => __( 'Индекс:', 'woocommerce-settings-tab-rp_letter_print' ),
				'type' => 'text',
				'desc' => '',
				'id'   => 'wc_settings_tab_rp_letter_print_section_sender_address_zipcode'
			),        
			'section_end' => array(
				'type' => 'sectionend',
				'id' => 'wc_settings_tab_rp_letter_print_section_end'
			)
		);
		return apply_filters( 'wc_settings_tab_rp_letter_print_settings', $rp_letter_print_settings );
	}

	public function custom_bulk_admin_footer() {
		global $post_type;
			if($post_type == 'shop_order') {
        ?>
					<script type="text/javascript">
						jQuery(document).ready(function() {
						//print
						jQuery('<option disabled="disabled">').val('separator').text('----').appendTo("select[name='action']");                
						jQuery('<option>').val('print_letter').text('<?php _e('Печать конвертов')?>').appendTo("select[name='action']");
						jQuery('<option disabled="disabled">').val('separator').text('----').appendTo("select[name='action2']");
						jQuery('<option>').val('print_letter').text('<?php _e('Печать конвертов')?>').appendTo("select[name='action2']");
						});
					</script>
        <?php
			}
	}

	public function custom_bulk_action() {
		global $typenow;
		$post_type = $typenow;

		if($post_type == 'shop_order') {
			$wp_list_table = _get_list_table('WP_Posts_List_Table');
			$action = $wp_list_table->current_action();
			$allowed_actions = array("print_letter");
			if(!in_array($action, $allowed_actions)) return;
			if(isset($_REQUEST['post'])) {
				$orderids = array_map('intval', $_REQUEST['post']);
			}
			switch($action) {
			case "print_letter":
				require_once dirname(__FILE__).'/custom/mpdf/vendor/autoload.php';
				
				$mpdf = new mPDF('utf-8', array(114,162), 0, '', 0, 0, 0, 0, 0, 0);
				
				foreach( $orderids as $orderid ) {
					$this->print_order($orderid, $mpdf);                
				}           
				$content = $mpdf->Output('', 'S');
				header("Content-type: application/x-msdownload",true,200);
				header("Content-Disposition: attachment; filename=print_letter.pdf");
				header("Pragma: no-cache");
				header("Expires: 0");
				echo $content;
				exit();

				wp_redirect($content);
				break;
				default: return;
			}
		}
	}

	function print_order($orderid, $mpdf) {    
    $order = new WC_Order($orderid);
    $mpdf->AddPage('L');

    $shipping_full_name = $order->get_formatted_shipping_full_name()?: '&nbsp;';
    $shipping_address = $order->get_address('shipping');
    $shipping_address_address1 = $shipping_address['address_1']?: '&nbsp;';

		// wrap by 35, since there is less space for data on the first line
    $shipping_address_data = explode(PHP_EOL, $this->mbWordwrap($shipping_address_address1, 35, "\n", false));
    $shipping_address_line1 = $shipping_address_data[0]?: '&nbsp;';    
    $shipping_address_line2 = $shipping_address_data[1]?: '&nbsp;';
    $shipping_address_line3 = $shipping_address_data[2]?: '&nbsp;';

		// combine two last lines, and wrap by 42
    $shipping_address_data = implode(' ', [$shipping_address_line2, $shipping_address_line3]);
    $shipping_address_data = explode(PHP_EOL, $this->mbWordwrap($shipping_address_data, 42, "\n", false));

    $shipping_address_line2 = $shipping_address_data[0]?: '&nbsp;';
    $shipping_address_line3 = $shipping_address_data[1]?: '&nbsp;';

    $shipping_address_zipcode = $shipping_address['postcode']?: '&nbsp;';

    $shipping_method = $order->get_shipping_method();
    if($shipping_method == 'Письмо заказное'){
      $shipping_method = 'ЗАКАЗНОЕ';
    }
    else{
      $shipping_method = '';      
    }      

    $sender_name = get_option('wc_settings_tab_rp_letter_print_section_sender_name');
    $sender_address_line1 = get_option('wc_settings_tab_rp_letter_print_section_sender_address_line1')?: '&nbsp;';
    $sender_address_line2 = get_option('wc_settings_tab_rp_letter_print_section_sender_address_line2')?: '&nbsp;';
    $sender_address_line3 = get_option('wc_settings_tab_rp_letter_print_section_sender_address_line3')?: '&nbsp;';
    $sender_zipcode = get_option('wc_settings_tab_rp_letter_print_section_sender_address_zipcode')?: '&nbsp;';

		$template = file_get_contents(dirname(__FILE__).'/custom/assets/c6_template.html');
		eval("\$template = \"$template\";");
    $mpdf->WriteHTML($template);
	}
	
	function mbWordwrap($str, $width = 74, $break = "\n", $cut = false) { 
		return preg_replace('#([\S\s]{'. $width .'}'. ($cut ? '' : '\s') .')#u', '$1'. $break , $str); 
	} 
} // End Class

/**
 * The 'main' function
 *
 * @return void
 */
function WooCommerce_Russian_Post_Letter_Print_main() {
	new WooCommerce_Russian_Post_Letter_Print();
}

/**
 * Initialise the plugin
 */
add_action( 'plugins_loaded', 'WooCommerce_Russian_Post_Letter_Print_main' );



