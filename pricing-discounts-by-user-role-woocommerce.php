<?php

/*
  Plugin Name: WooCommerce Catalog Mode, Wholesale & Role Based Pricing (BASIC)
  Plugin URI: https://www.xadapter.com/product/prices-by-user-role-for-woocommerce/
  Description:  One click Catalog mode. Hide add to cart for guest, specific user. Hide price for guest, specific user for simple products. Create user role specific product price. Enforce markup/discount on price for selected user roles.
  Version: 2.1.8
  WC requires at least: 2.6.0
  WC tested up to: 3.4
  Author: AdaptXY
  Author URI: https://adaptxy.com/
 Text Domain: eh-woocommerce-pricing-discount
 */

// to check wether accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// for Required functions
if (!function_exists('eh_is_woocommerce_active')) {
    require_once ('eh-includes/eh-functions.php');
}

// to check woocommerce is active
if (!(eh_is_woocommerce_active())) {
    add_action( 'admin_notices', 'xa_basic_prices_woocommerce_inactive_notice' );
    return;
}

function xa_basic_prices_woocommerce_inactive_notice() {
    ?>
<div id="message" class="error">
    <p>
	<?php	print_r(__( '<b>WooCommerce</b> plugin must be active for <b>WooCommerce Catalog Mode, Wholesale & Role Based Pricing (BASIC)</b> to work. ', 'eh-woocommerce-pricing-discount' ) ); ?>
    </p>
</div>
<?php
}

if (!defined('EH_PRICING_DISCOUNT_MAIN_URL_PATH')) {
    define('EH_PRICING_DISCOUNT_MAIN_URL_PATH', plugin_dir_url(__FILE__));
}

/**
 * Plugin activation check
 */
function xa_prices_by_user_role_plugin_pre_activation_check() {
    //check if premium version is there
    if (is_plugin_active('pricing-discounts-by-user-role-woocommerce/pricing-discounts-by-user-role-woocommerce.php')) {
        deactivate_plugins(basename(__FILE__));
        wp_die(__("Is everything fine? You already have the Premium version installed in your website. For any issues, kindly raise a ticket via <a target='_blank' href='//support.xadapter.com/'>support.xadapter.com</a>"), "", array('back_link' => 1));
    }
    set_transient('xa_prices_by_user_role_welcome_screen_activation_redirect', true, 30);
}

register_activation_hook(__FILE__, 'xa_prices_by_user_role_plugin_pre_activation_check');

add_action( 'init', 'xa_prices_role_lang_loader' );
   function xa_prices_role_lang_loader() {
       load_plugin_textdomain( 'eh-woocommerce-pricing-discount', false, dirname( plugin_basename( __FILE__ ) ) . '/lang');
   }

if (!class_exists('Pricing_discounts_By_User_Role_WooCommerce')) {

    class Pricing_discounts_By_User_Role_WooCommerce {

        // initializing the class
        public function __construct() {
            add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'eh_pricing_discount_action_links')); //to add settings, doc, etc options to plugins base
            add_action('init', array($this, 'eh_pricing_discount_admin_menu')); //to add pricing discount settings options on woocommerce shop
            add_action('admin_menu', array($this, 'eh_pricing_discount_admin_menu_option')); //to add pricing discount settings menu to main menu of woocommerce
        }

        // function to add settings link to plugin view
        public function eh_pricing_discount_action_links($links) {
            $plugin_links = array(
                '<a href="' . admin_url('admin.php?page=wc-settings&tab=eh_pricing_discount') . '">' . __('Settings', 'eh-woocommerce-pricing-discount') . '</a>',
                '<a href="https://www.xadapter.com/category/product/prices-by-user-role-for-woocommerce/" target="_blank">' . __('Documentation', 'eh-woocommerce-pricing-discount') . '</a>',
                '<a href="http://www.xadapter.com/product/prices-by-user-role-for-woocommerce/" target="_blank">' . __('Premium Upgrade', 'eh-woocommerce-pricing-discount') . '</a>',
                '<a href="https://wordpress.org/support/plugin/prices-by-user-role" target="_blank">' . __('Support', 'eh-woocommerce-pricing-discount') . '</a>'
            );
            return array_merge($plugin_links, $links);
        }

        // function to add menu in woocommerce
        public function eh_pricing_discount_admin_menu() {
            require_once('includes/class-eh-price-discount-admin.php');
            require_once('includes/class-eh-price-discount-settings.php');
        }

        public function eh_pricing_discount_admin_menu_option() {
            global $pricing_discount_settings_page;
            $pricing_discount_settings_page = add_submenu_page('woocommerce', __('Pricing & Discount', 'eh-woocommerce-pricing-discount'), __('Pricing & Discount', 'eh-woocommerce-pricing-discount'), 'manage_woocommerce', 'admin.php?page=wc-settings&tab=eh_pricing_discount');
        }

    }

    new Pricing_discounts_By_User_Role_WooCommerce();
}
