<?php

/**
 * @package wcsm
 * @author Mohamed Saad
 * @link https://wpsaad.com
 * @since 1.0.0
 */
/**
 * Plugin Name: Shipping Manager For WooCommerce Premium
 * plugin URI: https://wpsaad.com/custom-product-type-for-woocommerce/
 * Description: Adds Custom cities and convert checkout city field to dropdown, also adding custom WooCommerce Shipping zone, Plus (Show/Hide) checkout shipping fields.
 * Version: 1.6.0
 * Author: Mohamed Saad
 * Author URI: https://wpsaad.com
 * License: GPLv2 or later
 * Text Domain: shipping-manager-for-woocommerce
 * Domain Path: /languages
 *  
 */
defined( 'ABSPATH' ) or die;

if ( !function_exists( 'wcsm_fs' ) ) {
    // Create a helper function for easy SDK access.
    function wcsm_fs()
    {
        global  $wcsm_fs ;
        
        if ( !isset( $wcsm_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $wcsm_fs = fs_dynamic_init( array(
                'id'             => '7170',
                'slug'           => 'shipping-manager-for-wooCommerce',
                'premium_slug'   => 'woocommerce-shipping-manager-premium',
                'type'           => 'plugin',
                'navigation'     => 'tabs',
                'public_key'     => 'pk_23014ecc3838b686f7179f95c4a6e',
                'is_premium'     => false,
                'premium_suffix' => 'Premium',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'menu'           => array(
                'slug' => 'wcsm-settings',
            ),
                'is_live'        => true,
            ) );
        }
        
        return $wcsm_fs;
    }
    
    // Init Freemius.
    wcsm_fs();
    // Signal that SDK was initiated.
    do_action( 'wcsm_fs_loaded' );
}

require_once plugin_dir_path( __FILE__ ) . 'inc/wcsm-activate.php';
register_activation_hook( __FILE__, array( 'wcsm_activate', 'activate' ) );
//register admin scripts and styles
add_action( 'admin_enqueue_scripts', 'wcsm_admin_scripts' );
function wcsm_admin_scripts()
{
    $screen = get_current_screen();
    //Check if its plugin settings page
    
    if ( $screen->base == 'toplevel_page_wcsm-settings' || $screen->base == 'wc-shipping_page_wcsm-cities' ) {
        wp_enqueue_style(
            'wcsm-admin-style',
            plugins_url( '/assets/css/wcsm-admin-style.css', __FILE__ ),
            [],
            null
        );
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script(
            'wcsm_back',
            plugins_url( '/assets/js/wcsm-back.js', __FILE__ ),
            [],
            null,
            true
        );
        wp_enqueue_style(
            'select2',
            plugins_url( '/assets/css/select2.css', __FILE__ ),
            [],
            null
        );
        wp_enqueue_script(
            'select2',
            plugins_url( '/assets/js/select2.js', __FILE__ ),
            [],
            null,
            true
        );
    }

}

add_action( 'wp_enqueue_scripts', 'wcsm_front_scripts' );
function wcsm_front_scripts()
{
    if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        if ( is_checkout() ) {
            wp_enqueue_script(
                'wcsm_front',
                plugins_url( '/assets/js/wcsm-front.js', __FILE__ ),
                [],
                null,
                true
            );
        }
    }
}

add_action( 'init', 'wcsm_load' );
function wcsm_load()
{
    $current_user = wp_get_current_user();
    if ( user_can( $current_user, 'manage_options' ) ) {
        require_once plugin_dir_path( __FILE__ ) . 'inc/wcsm-admin.php';
    }
    if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        require_once plugin_dir_path( __FILE__ ) . 'inc/wcsm-functions.php';
    }
}

$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_{$plugin}", 'settings_link' );
function settings_link( $links )
{
    $settings_link = '<a href="admin.php?page=wcsm-settings">Settings</a>';
    array_push( $links, $settings_link );
    return $links;
}
