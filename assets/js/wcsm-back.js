jQuery(document).ready(function($){

    $('.wcsm-parent-country').select2({
        width: 'resolve'
    });
    // console.log(backData.pluginPath);
    var maxField = 100; //Input fields increment limitation
    var addButton = $('#cities_data:not(.city-only) .add_button'); //Add button selector
    var addCity = $('.city-only .add_button'); //Add button selector

    var wrapper = $('.city_wrapper'); //Input field wrapper
    var x = backData.countCities; //Initial field counter is 1

    //Once add button is clicked
    $(addButton).click(function(){
        if(x < maxField){ 
            x++; //Increment field counter
        }

        if($(this).parent().parent().hasClass('city-only')){
            var fieldHTML = '<div class="city-settings" ><input type="text" name="cities_data['+x+'][city]" /><input type="text" name="cities_data['+x+'][rate]" style="display: none;" /><a href="javascript:void(0);" class="remove_button"><img src="'+backData.pluginPath+'"/></a></div>'; //New input field html 
        }else{
            var fieldHTML = '<div class="city-settings" ><input type="text" name="cities_data['+x+'][city]" /><input type="text" name="cities_data['+x+'][rate]" /><a href="javascript:void(0);" class="remove_button"><img src="'+backData.pluginPath+'"/></a></div>'; //New input field html 
        }

        //Check maximum number of input fields
        if(x < maxField){ 
            var removedButton = $(this).detach();
            $(wrapper).append(fieldHTML); //Add field html
            $(wrapper).append(removedButton);
        }
    });
    $(addCity).click(function(){
        //Check maximum number of input fields
        if(x < maxField){ 
            x++; //Increment field counter
            var fieldHTML = '<div class="city-settings" ><input type="text" name="cities_data['+x+'][city]" /><a href="javascript:void(0);" class="remove_button"><img src="'+backData.pluginPath+'"/></a></div>'; //New input field html 
            var removedButton = $(this).detach();
            $(wrapper).append(fieldHTML); //Add field html
            $(wrapper).append(removedButton);
        }
    });


    $(wrapper).on('click', '.remove_button', function(e){
        e.preventDefault();
        $(this).parent('div').remove(); //Remove field html
        x--; //Decrement field counter
    });

    $('#cities_status').change(function(){
        if(this.checked)
            $('#cities_data').fadeIn('slow');
        else
            $('#cities_data').fadeOut('slow');

    });
    $('.city_wrapper').slideUp();
    $('.add_button').slideUp();
    $('.wcsm-toggler').click(function(){
        $(this).toggleClass('country-collapsed');
        $('.wcsm-toggler span').toggleClass('dashicons-arrow-up-alt2');

        $('.city_wrapper').slideToggle('1000');
        $('.add_button').slideToggle('1000');
       
    })


});
