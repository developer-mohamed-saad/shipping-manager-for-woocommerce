<?php

if ( !class_exists( 'wcsm' ) ) {
    class wcsm
    {
        function __construct()
        {
            $this->init();
        }
        
        function init()
        {
            add_filter( 'woocommerce_default_address_fields', [ $this, 'wcsm_change_city_label' ] );
            add_filter( 'woocommerce_checkout_fields', [ $this, 'wcsm_change_city_to_dropdown' ] );
            
            if ( get_option( 'wcsm_cities_status' ) == 'enabled' ) {
                add_action( 'woocommerce_checkout_update_order_review', [ $this, 'wcsm_checkout_update' ] );
                add_filter( 'woocommerce_package_rates', [ $this, 'wcsm_adjust_shipping_rate' ], 50 );
            } elseif ( get_option( 'wcsm_cities_status' ) == 'enabled' && ($_POST['wcsm_cities_status'] = 'enabled') ) {
                function wcsm_checkout_default_rate( $post_data )
                {
                    WC()->session->set( 'shipping_city_cost', $rate->cost );
                    foreach ( WC()->cart->get_shipping_packages() as $package_key => $package ) {
                        // this is needed for us to remove the session set for the shipping cost. Without this, we can't set it on the checkout page.
                        WC()->session->set( 'shipping_for_package_' . $package_key, false );
                    }
                }
                
                function wcsm_adjust_shipping_default( $rates )
                {
                    foreach ( $rates as $rate ) {
                        // $default_cost = $rate->cost;
                        $shipping_cost = WC()->session->get( 'shipping_city_cost' );
                        if ( 'flat_rate' === $rate->method_id ) {
                            if ( $shipping_cost ) {
                                $rate->cost = WC()->session->get( 'shipping_city_cost' );
                            }
                        }
                    }
                    return $rates;
                }
                
                add_action( 'woocommerce_checkout_update_order_review', 'wcsm_checkout_default_rate' );
                add_filter( 'woocommerce_package_rates', 'wcsm_adjust_shipping_default', 50 );
            }
        
        }
        
        public function wcsm_change_city_label( $address_fields )
        {
            $default = esc_html__( 'Town / City', 'woocommerce' );
            $city_label = get_option( 'checkout_cities_label' );
            
            if ( !empty($city_label) ) {
                $address_fields['city']['label'] = $city_label;
            } else {
                $address_fields['city']['label'] = $default;
            }
            
            return $address_fields;
        }
        
        public function wcsm_cities_data()
        {
            global  $cities_titles ;
            global  $city_rate ;
            $parent_country = get_option( 'wcsm_parent_country' );
            wp_localize_script( 'wcsm_front', 'countryParent', [
                'parent' => $parent_country,
            ] );
            $cities_data = json_decode( get_option( 'cities_data' ), true );
            $cities_titles = [];
            $city_rate = [];
            foreach ( $cities_data as $city => $values ) {
                $cities_titles[ucfirst( $values['city'] )] = ucfirst( $values['city'] );
                $city_rate[ucfirst( $values['city'] )] = $values['rate'];
            }
            return [ $cities_titles, $city_rate ];
        }
        
        public function wcsm_change_city_to_dropdown( $fields )
        {
            $this->wcsm_cities_data();
            global  $cities_titles ;
            
            if ( get_option( 'wcsm_cities_status' ) == 'enabled' && get_option( 'wcsm_cities_text' ) != 'enabled' && get_option( 'wcsm_cities_zones' ) != 'enabled' ) {
                $shipping_city = wp_parse_args( array(
                    'type'    => 'select',
                    'options' => $cities_titles,
                    'class'   => [ 'update_totals_on_change' ],
                ), $fields['shipping']['shipping_city'] );
                $billing_city = wp_parse_args( array(
                    'type'    => 'select',
                    'options' => $cities_titles,
                    'class'   => [ 'update_totals_on_change' ],
                ), $fields['billing']['billing_city'] );
                $fields['shipping']['shipping_city'] = $shipping_city;
                $fields['billing']['billing_city'] = $billing_city;
            }
            
            $shipping_fields_options = get_option( 'wcsm_disabled_shipping' );
            $billing_fields_options = get_option( 'wcsm_disabled_billing' );
            if ( !empty($shipping_fields_options) ) {
                foreach ( $shipping_fields_options as $field ) {
                    unset( $fields['shipping'][$field] );
                }
            }
            if ( !empty($billing_fields_options) ) {
                foreach ( $billing_fields_options as $field ) {
                    unset( $fields['billing'][$field] );
                }
            }
            return $fields;
        }
        
        public function wcsm_checkout_update( $post_data )
        {
            $this->wcsm_cities_data();
            global  $cities_titles ;
            global  $city_rate ;
            $data = array();
            $vars = explode( '&', $post_data );
            foreach ( $vars as $k => $value ) {
                $v = explode( '=', urldecode( $value ) );
                $data[$v[0]] = $v[1];
            }
            $wcsm_base_field = '';
            $wcsm_base_field = ucfirst( $data['billing_city'] );
            $shipping_cost = $this->wcsm_get_shipping_cost_by_city( $wcsm_base_field, $cities_titles, $city_rate );
            WC()->session->set( 'shipping_city_cost', $shipping_cost );
            foreach ( WC()->cart->get_shipping_packages() as $package_key => $package ) {
                // this is needed for us to remove the session set for the shipping cost. Without this, we can't set it on the checkout page.
                WC()->session->set( 'shipping_for_package_' . $package_key, false );
            }
        }
        
        public function wcsm_adjust_shipping_rate( $rates )
        {
            foreach ( $rates as $rate ) {
                $default_cost = $rate->cost;
                $shipping_cost = WC()->session->get( 'shipping_city_cost' );
                if ( 'flat_rate' === $rate->method_id && get_option( 'wcsm_cities_zones' ) != 'enabled' ) {
                    if ( $shipping_cost ) {
                        $rate->cost = WC()->session->get( 'shipping_city_cost' );
                    }
                }
            }
            return $rates;
        }
        
        public function wcsm_get_shipping_cost_by_city( $city, $cities_titles, $city_rate )
        {
            
            if ( in_array( $city, $cities_titles ) ) {
                return $city_rate[$city];
            } else {
                return;
            }
        
        }
        
        public function wcsm_cities_zones( $states )
        {
            return $states;
        }
    
    }
    /*Instantiate wcsm class */
    $woocommerce_shipping_manager = new wcsm();
}
