<?php

/*

Plugin Name: Zipcode To Address For Woocommerce Japanese
Plugin URI: https://wordpress.org/
Description: Automatic Address registration from zipcode at checkout form for woocommerce Japanese
Version: 1.0
Author: kelvin lee
License: GPLv2
Text Domain: zip2addr-jp-wc
 
*/

/**
 * * This plugin is inspired by woocommerce for japan
 * * and the main logic flow and implementation is from the plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'plugins_loaded', 'zip2addr_jp_init_class' );

/**
 * Init the class Zip2addr_Jp_Wc
 */
function zip2addr_jp_init_class() {

    /**
     * Zip2addr_Jp_Wc class
     * 
     * @class Zip2addr_Jp_Wc
     */    
    class Zip2addr_Jp_Wc {
        
        /**
         * * construct
         */
        public function __construct() {
            add_action( 'woocommerce_after_checkout_billing_form', array( $this, 'auto_zip2address_billing'), 10 );
            add_action( 'woocommerce_after_checkout_shipping_form', array( $this, 'auto_zip2address_shipping'), 10 );
            add_action( 'woocommerce_after_edit_address_form_billing', array( $this, 'auto_zip2address_billing'), 10 );
            add_action( 'woocommerce_after_edit_address_form_shipping', array( $this, 'auto_zip2address_shipping'), 10 );

            /**
             * 
             */
            add_action( 'wp_enqueue_scripts', array( $this, 'custom_design_of_checkout' ) );
            // wp_enqueue_script( 'custom_design_of_checkout_forms', plugin_dir_url(__FILE__) . 'js/custom_design.js' );
        }

        /**
         * * Automatic input from postal code to Address for billing
         */
        public function auto_zip2address_billing(){
            $this->auto_zip2address( 'billing' );
        }

        /**
         * * Automatic input from postal code to Address for shipping
         */
        public function auto_zip2address_shipping(){
            $this->auto_zip2address( 'shipping' );
        }

        /**
         * Display JavaScript code for automatic registration of address by zip code.
         *
         * @param string $method 'billing' or 'shipping'
         */
        public function auto_zip2address($method) {
            if(version_compare( WC_VERSION, '3.6', '>=' )){
                $jp4wc_countries = new WC_Countries;
                $states = $jp4wc_countries->get_states();
            }else{
                global $states;
            }
            if(get_option( 'wc4jp-yahoo-app-id' )){
                $yahoo_app_id = get_option( 'wc4jp-yahoo-app-id' );
            } else {
                $yahoo_app_id = 'dj0zaiZpPWZ3VWp4elJ2MXRYUSZzPWNvbnN1bWVyc2VjcmV0Jng9MmY-';
            }
            $state_id = 'select2-'.$method.'_state-container';
            if (get_option( 'wc4jp-zip2address' )){
                wp_enqueue_script( 'yahoo-app','https://map.yahooapis.jp/js/V1/jsapi?appid='.$yahoo_app_id,array('jquery'),JP4WC_VERSION);

                $states_filtered = array();
                foreach( (array)$states['JP'] as $key => $value ) {
                    $key = substr($key, 2);

                    if ($key == '14' || $key == "30" || $key == "46") {
                        $states_filtered[$key] = mb_substr($value, 0, 3);
                    } else {
                        $states_filtered[$key] = $value;
                    }
                }

                $param_array = array(
                    'method' => $method,
                    'hyphen_text' => "郵便番号を入れる時はハイフン [ - ] を入力してください。",
                    'yahoo_app_id' => $yahoo_app_id,
                    'states_jp' => $states['JP'],
                    'states_filtered' => $states_filtered,
                    'state_element_id' => $state_id,
                );

                $this->enqueue_script($param_array);
            }
        }

        /**
         * * 
         */
        public function enqueue_script($params) {
            wp_enqueue_script( 'wc_address_search_zipcode' . $params['method'], plugin_dir_url(__FILE__) . 'js/zipcode_search_' . $params['method'] . '.js' );
            wp_localize_script( 'wc_address_search_zipcode' . $params['method'], 'params', $params );
        }

        /**
         * * Custom design of checkout form
         * * change label text of create account
         */
        public function custom_design_of_checkout() {
            wp_enqueue_script( 'custom_design_checkout_form', plugin_dir_url(__FILE__) . 'js/custom_design.js' );
        }
    }

    new Zip2addr_Jp_Wc();
}
