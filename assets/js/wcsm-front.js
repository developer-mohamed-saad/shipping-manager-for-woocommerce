jQuery(function($){
if(!$('.woocommerce-order-received')[0]){
    var parentCountry = countryParent.parent;
}
    var billingInputText='<input type="text" class="input-text " name="billing_city" id="billing_city" placeholder="" autocomplete="address-level2">';
    var billingdDropdown ;
    var billingSelect2Field;
    $('#billing_city_field select').select2();
    $( '#billing_country' ).on( 'select2:select', function() {
    var selectedCountry = $( '#billing_country' ).data( 'select2' ).val();
        if(parentCountry == selectedCountry ){
            if(billingdDropdown){
                billingInputText = $('input#billing_city').detach();
                $('#billing_city_field .woocommerce-input-wrapper').append(billingdDropdown);
                $('#billing_city_field .woocommerce-input-wrapper').append(billingSelect2Field);
            }
        }else{
            billingdDropdown = $('select#billing_city').detach();
            billingSelect2Field = $('#billing_city_field span.select2').detach();
            $('#billing_city_field .woocommerce-input-wrapper').append(billingInputText);
        }
    })
    
    if(typeof billingdDropdown === "undefined" & parentCountry != $('#billing_country option:selected').val()){
            billingdDropdown = $('select#billing_city').detach();
            billingSelect2Field = $('#billing_city_field span.select2').detach();
            $('#billing_city_field .woocommerce-input-wrapper').append(billingInputText);
    }

    var shippingInputText='<input type="text" class="input-text " name="shipping_city" id="shipping_city" placeholder="" autocomplete="address-level2">';
var shippingdDropdown ;
var shippingSelect2Field;
$('#shipping_city_field select').select2();
$( '#shipping_country' ).on( 'select2:select', function() {
var selectedCountry = $( '#shipping_country' ).data( 'select2' ).val();
    if(parentCountry == selectedCountry ){
        if(shippingdDropdown){
            shippingInputText = $('input#shipping_city').detach();
            $('#shipping_city_field .woocommerce-input-wrapper').append(shippingdDropdown);
            $('#shipping_city_field .woocommerce-input-wrapper').append(shippingSelect2Field);
        }
    }else{
        shippingdDropdown = $('select#shipping_city').detach();
        shippingSelect2Field = $('#shipping_city_field span.select2').detach();
        $('#shipping_city_field .woocommerce-input-wrapper').append(shippingInputText);
    }
})

if(typeof shippingdDropdown === "undefined" & parentCountry != $('#shipping_country option:selected').val()){
        shippingdDropdown = $('select#shipping_city').detach();
        shippingSelect2Field = $('#shipping_city_field span.select2').detach();
        $('#shipping_city_field .woocommerce-input-wrapper').append(shippingInputText);
}

    
});
