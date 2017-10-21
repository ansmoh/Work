jQuery(document).ready(function () {
    jQuery('#input_8_1').change(function(){
       $location = jQuery(this).val();
        jQuery.ajax({
            type:'post',
            url : location_meta.ajax_url,
            data:{
            'action' : 'save_location_value',
            'location' : $location,
        },
        });
    });
});
