<?php

add_action( 'admin_init', 'wcsm_settings' );
function wcsm_settings()
{
    register_setting( 'wcsm_settings', 'wcsm_parent_country' );
    register_setting( 'wcsm_settings', 'cities_data' );
    register_setting( 'wcsm_settings', 'checkout_cities_label' );
    register_setting( 'wcsm_settings', 'wcsm_disabled_shipping' );
    register_setting( 'wcsm_settings', 'wcsm_disabled_billing' );
    register_setting( 'wcsm_settings', 'wcsm_cities_status' );
}

add_action( 'admin_menu', 'wcsm_menu_page' );
function wcsm_menu_page()
{
    add_menu_page(
        'WC Shipping Manager',
        'WC Shipping',
        'manage_options',
        'wcsm-settings',
        'wcsm_options',
        'dashicons-car',
        999
    );
    add_submenu_page(
        'wcsm-settings',
        'Cities Shipping Rates',
        'Cities & Rates',
        'manage_options',
        'wcsm-cities',
        'wcsm_cities'
    );
}

function wcsm_cities()
{
    ?>
    <h1 ><?php 
    esc_html_e( 'Shipping Manager For WooCommerce', 'shipping-manager-for-woocommerce' );
    ?></h1>
    <?php 
    
    if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        ?>
         <div class="wrap fs-section">
  <h2 class="nav-tab-wrapper">
  <a href="<?php 
        echo  esc_url( admin_url( 'admin.php?page=wcsm-settings' ) ) ;
        ?>" class="nav-tab fs-tab settings">Settings</a>
    <a href="<?php 
        echo  esc_url( admin_url( 'admin.php?page=wcsm-cities' ) ) ;
        ?>" class="nav-tab fs-tab nav-tab-active settings-cities">Cities & Rates</a>

        <a href="<?php 
        echo  esc_url( admin_url( 'admin.php?page=wcsm-settings-account' ) ) ;
        ?>" class="nav-tab fs-tab shipping-manager-for-wooCommerce account">Account</a>
    <a href="<?php 
        echo  esc_url( admin_url( 'admin.php?page=wcsm-settings-contact' ) ) ;
        ?>" class="nav-tab fs-tab contact ">Contact</a>
    <a href="<?php 
        echo  esc_url( 'https://wordpress.org/support/plugin/shipping-manager-for-wooCommerce' ) ;
        ?>" class="nav-tab fs-tab support">Support Forum</a>
    <a href="<?php 
        echo  esc_url( admin_url( 'admin.php?page=wcsm-settings-pricing' ) ) ;
        ?>" class="nav-tab fs-tab Upgrade">Upgrade </a>
    
  </h2>
  <form method="post" action="<?php 
        echo  esc_url( admin_url( 'admin.php?page=wcsm-cities' ) ) ;
        ?>"> 

<?php 
        global  $woocommerce ;
        $countries_data = new WC_Countries();
        $countries = $countries_data->__get( 'countries' );
        echo  '<div id="parent_country"><h2>' . esc_html__( 'Parent Country', 'shipping-manager-for-woocommerce' ) . '</h2>' ;
        echo  '<select name="wcsm_parent_country" class="wcsm-parent-country">' ;
        // if(!empty(get_option('wcsm_parent_country'))){
        // echo '<option selected=selected>'.get_option('wcsm_parent_country').'</option>';
        // }
        foreach ( $countries as $countryCode => $countryName ) {
            
            if ( $countryCode == get_option( 'wcsm_parent_country' ) ) {
                echo  '<option value="' . $countryCode . '" selected=selected>' . $countryName . '</option>' ;
            } else {
                echo  '<option value="' . $countryCode . '">' . $countryName . '</option>' ;
            }
        
        }
        echo  '</select>' ;
        echo  '</div>' ;
        ?>
<hr style="width:50%;text-align:left;margin-left:0">
<h3><?php 
        esc_html_e( 'Cities', 'shipping-manager-for-woocommerce' );
        ?></h3>
<?php 
        
        if ( get_option( 'wcsm_cities_status' ) == 'enabled' ) {
            ?>
<input id="cities_status" type="checkbox" name="wcsm_cities_status" value="enabled" checked>
<label for="cities_status"><strong>(Enable/Disable) Custom Cities Dropdown</strong></label><br>

<?php 
        } else {
            ?>
    <input id="cities_status" type="checkbox" name="wcsm_cities_status" value="enabled" >
    <label for="cities_status"><strong>(Enable/Disable) Custom Cities Dropdown</strong></label><br>
    
    <?php 
        }
        
        ?>

<div id="cities_data" style="<?php 
        if ( get_option( 'wcsm_cities_status' ) != 'enabled' ) {
            echo  'display:none;' ;
        }
        ?>">
<a class="wcsm-toggler" href="javascript:void(0);">
<h3 ><span class="dashicons dashicons-arrow-down-alt2"></span><?php 
        echo  esc_html( WC()->countries->countries[get_option( 'wcsm_parent_country' )] ) ;
        _e( ' Cities', 'shipping-manager-for-woocommerce' );
        ?> </h3>
</a>
<div class="city_wrapper">
    <div class="city-header"> 
        <div><h4><?php 
        esc_html_e( 'City Name', 'shipping-manager-for-woocommerce' );
        ?></h4><hr></div> 
        <div><h4><?php 
        esc_html_e( 'Shipping Cost', 'shipping-manager-for-woocommerce' );
        ?></h4><hr></div>
    </div>
   
<?php 
        $cities = json_decode( get_option( 'cities_data' ), true );
        $cities_count;
        if ( is_array( $cities ) && !empty(get_option( 'cities_data' )) ) {
            
            if ( count( $cities ) > 1 ) {
                $cities_count = count( $cities );
            } else {
                $cities_count = 1;
            }
        
        }
        wp_localize_script( 'wcsm_back', 'backData', [
            'countCities' => $cities_count,
            'pluginPath'  => plugins_url( '../assets/img/remove-icon.png', __FILE__ ),
        ] );
        $i = 0;
        if ( is_array( $cities ) ) {
            foreach ( $cities as $city ) {
                $i++;
                ?>
    <div class="city-settings">
    <input type="text" name="cities_data[<?php 
                echo  $i ;
                ?>][city]" value="<?php 
                echo  esc_html( $city['city'] ) ;
                ?>"/>
    <input type="text" name="cities_data[<?php 
                echo  $i ;
                ?>][rate]" value="<?php 
                echo  esc_html( $city['rate'] ) ;
                ?>"/>
    <a href="javascript:void(0);" class="remove_button" title="Remove city"><img src="<?php 
                echo  plugins_url( '../assets/img/remove-icon.png', __FILE__ ) ;
                ?>"/></a>
    </div>
<?php 
            }
        }
        ?>
            <a href="javascript:void(0);" class="add_button" title="Add City"><img src="<?php 
        echo  plugins_url( '../assets/img/add-icon.png', __FILE__ ) ;
        ?>"/></a>
            </div>
<?php 
        ?>

</div>
<?php 
        submit_button( 'Save' );
        ?>

</form>
<?php 
    }

}

function wcsm_options()
{
    ?>
    <h1 ><?php 
    esc_html_e( 'Shipping Manager For WooCommerce', 'shipping-manager-for-woocommerce' );
    ?></h1>
    <?php 
    
    if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        ?>
         <div class="wrap fs-section">
  <h2 class="nav-tab-wrapper">
  <a href="<?php 
        echo  esc_url( admin_url( 'admin.php?page=wcsm-settings' ) ) ;
        ?>" class="nav-tab fs-tab nav-tab-active settings">Settings</a>
    <a href="<?php 
        echo  esc_url( admin_url( 'admin.php?page=wcsm-cities' ) ) ;
        ?>" class="nav-tab fs-tab settings-cities">Cities & Rates</a>
  </h2>
  
        <form method="post" action="<?php 
        echo  esc_url( admin_url( 'admin.php?page=wcsm-settings' ) ) ;
        ?>"> 
<h3><?php 
        esc_html_e( 'Settings', 'shipping-manager-for-woocommerce' );
        ?></h3>
<div style="margin-bottom: 2%;">
<label><strong><?php 
        esc_html_e( 'City Label:', 'shipping-manager-for-woocommerce' );
        ?></strong></label> 
<input type="text" name="checkout_cities_label" value="<?php 
        echo  esc_html( get_option( 'checkout_cities_label' ) ) ;
        ?>"/>
</div>

<?php 
        ?>

<div class="disable-fields" style="margin-bottom: 2%;">
<h3><strong><?php 
        esc_html_e( 'Disable Fields:', 'shipping-manager-for-woocommerce' );
        ?></strong></h3>
<table>
<tr style="margin-bottom: 2%;">
<th><?php 
        esc_html_e( 'Shipping Fields:', 'shipping-manager-for-woocommerce' );
        ?></th>
<td style="padding-bottom: 10px;">
<?php 
        $wcsm_shipping_fields = [
            'shipping_company'   => esc_html__( 'Company name', 'shipping-manager-for-woocommerce' ),
            'shipping_address_1' => esc_html__( 'Street address', 'wcsm' ),
            'shipping_address_2' => esc_html__( 'Apartment, suite, unit, etc.', 'shipping-manager-for-woocommerce' ),
            'shipping_state'     => esc_html__( 'State / County', 'shipping-manager-for-woocommerce' ),
            'shipping_postcode'  => esc_html__( 'Postcode / ZIP', 'shipping-manager-for-woocommerce' ),
        ];
        $shipping_fields_options = get_option( 'wcsm_disabled_shipping' );
        
        if ( !empty($shipping_fields_options) ) {
            $shipping_fields_options = get_option( 'wcsm_disabled_shipping' );
        } else {
            $shipping_fields_options = [];
        }
        
        // var_dump($shipping_fields_options);
        foreach ( $wcsm_shipping_fields as $key => $label ) {
            
            if ( in_array( $key, $shipping_fields_options ) ) {
                ?>
        <input id="<?php 
                echo  $key ;
                ?>" type="checkbox" name="wcsm_disabled_shipping[]" value="<?php 
                echo  esc_attr( esc_html( $key ) ) ;
                ?>" checked>
        <label for="<?php 
                echo  $key ;
                ?>"> <?php 
                echo  $label ;
                ?></label><br>

            <?php 
            } else {
                ?>
        <input id="<?php 
                echo  $key ;
                ?>" type="checkbox" name="wcsm_disabled_shipping[]" value="<?php 
                echo  esc_attr( esc_html( $key ) ) ;
                ?>">
        <label for="<?php 
                echo  $key ;
                ?>"> <?php 
                echo  $label ;
                ?></label><br>
        <?php 
            }
        
        }
        ?>

</td>
</tr>
<tr>
<th>Billing Fields:</th>
<td style="padding-bottom: 10px;">
<?php 
        $wcsm_billing_fields = [
            'billing_company'   => esc_html__( 'Company name', 'shipping-manager-for-woocommerce' ),
            'billing_address_1' => esc_html__( 'Street address', 'shipping-manager-for-woocommerce' ),
            'billing_address_2' => esc_html__( 'Apartment, suite, unit, etc.', 'shipping-manager-for-woocommerce' ),
            'billing_state'     => esc_html__( 'State / County', 'shipping-manager-for-woocommerce' ),
            'billing_postcode'  => esc_html__( 'Postcode / ZIP', 'shipping-manager-for-woocommerce' ),
        ];
        $billing_fields_options = get_option( 'wcsm_disabled_billing' );
        
        if ( !empty($billing_fields_options) ) {
            $billing_fields_options = get_option( 'wcsm_disabled_billing' );
        } else {
            $billing_fields_options = [];
        }
        
        foreach ( $wcsm_billing_fields as $key => $label ) {
            
            if ( in_array( $key, $billing_fields_options ) ) {
                ?>
            <input id="<?php 
                echo  $key ;
                ?>" type="checkbox" name="wcsm_disabled_billing[]" value="<?php 
                echo  esc_attr( esc_html( $key ) ) ;
                ?>" checked>
            <label for="<?php 
                echo  $key ;
                ?>"> <?php 
                echo  $label ;
                ?></label><br>
            <?php 
            } else {
                ?>
                <input id="<?php 
                echo  $key ;
                ?>" type="checkbox" name="wcsm_disabled_billing[]" value="<?php 
                echo  esc_attr( esc_html( $key ) ) ;
                ?>">
                <label for="<?php 
                echo  $key ;
                ?>"> <?php 
                echo  $label ;
                ?></label><br>
                <?php 
            }
        
        }
        ?>

</td>
</tr>

</table>

</div>



<?php 
        submit_button( 'Save' );
        ?>

</form>
</div>
        <?php 
    } else {
        ?>
                     <div class="wrap fs-section">
  <h2 class="nav-tab-wrapper">
    <a href="<?php 
        echo  bloginfo( 'url' ) ;
        ?>/admin.php?page=wcsm-settings" class="nav-tab fs-tab nav-tab-active home">Settings</a>
  </h2>
  <div class="wcsm-error">
            <h3>WooCommerce plugin is not active</h3>
            <h2>Shipping Manager For WooCommerce will not work</h2>
            </div>
</div>
            <?php 
    }

}


if ( isset( $_POST['submit'] ) && $_GET['page'] == 'wcsm-settings' ) {
    if ( isset( $_POST['checkout_cities_label'] ) ) {
        update_option( 'checkout_cities_label', sanitize_text_field( $_POST['checkout_cities_label'] ) );
    }
    
    if ( isset( $_POST['wcsm_disabled_shipping'] ) ) {
        // var_dump($_POST['wcsm_disabled_shipping']);
        //      $shipping_disabled_fields=[];
        // $billing_disabled_fields=[];
        //sanitize disabled shipping fields options
        
        if ( !empty($_POST['wcsm_disabled_shipping']) ) {
            foreach ( $_POST['wcsm_disabled_shipping'] as $option ) {
                $shipping_disabled_fields[] = sanitize_text_field( $option );
            }
            update_option( 'wcsm_disabled_shipping', $shipping_disabled_fields );
        }
    
    } else {
        delete_option( 'wcsm_disabled_shipping' );
    }
    
    //sanitize disabled billing fields options
    
    if ( isset( $_POST['wcsm_disabled_billing'] ) ) {
        
        if ( !empty($_POST['wcsm_disabled_billing']) ) {
            foreach ( $_POST['wcsm_disabled_billing'] as $option ) {
                $billing_disabled_fields[] = sanitize_text_field( $option );
            }
            update_option( 'wcsm_disabled_billing', $billing_disabled_fields );
        }
    
    } else {
        delete_option( 'wcsm_disabled_billing' );
    }

}


if ( isset( $_POST['submit'] ) && $_GET['page'] == 'wcsm-cities' ) {
    if ( isset( $_POST['wcsm_parent_country'] ) ) {
        update_option( 'wcsm_parent_country', sanitize_text_field( $_POST['wcsm_parent_country'] ) );
    }
    
    if ( isset( $_POST['wcsm_cities_status'] ) ) {
        update_option( 'wcsm_cities_status', sanitize_text_field( $_POST['wcsm_cities_status'] ) );
    } else {
        delete_option( 'wcsm_cities_status' );
    }
    
    //cities Data saving
    
    if ( isset( $_POST['cities_data'] ) && is_array( $_POST['cities_data'] ) ) {
        $field_values_array = [];
        // $cities_data=$_POST['cities_data'];
        foreach ( $_POST['cities_data'] as $key => $value ) {
            
            if ( !empty($_POST['cities_data'][$key]['city']) && !empty($_POST['cities_data'][$key]['rate']) ) {
                // $field_values_array[$key] = $value;
                $field_values_array[$key] = [
                    'city' => sanitize_text_field( ucfirst( $value['city'] ) ),
                    'rate' => filter_var( $value['rate'], FILTER_SANITIZE_NUMBER_INT ),
                ];
            } elseif ( !empty($_POST['cities_data'][$key]['city']) ) {
                $field_values_array[$key] = [
                    'city' => sanitize_text_field( $value['city'] ),
                ];
            }
            
            // print_r($key);
            // print_r($value['city']);
        }
        update_option( 'cities_data', json_encode( $field_values_array ) );
    }

}
