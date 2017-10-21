jQuery(document).ready(function(){
	jQuery(window).scroll(function() {
    var scroll = jQuery(window).scrollTop();
    var width = jQuery(window).width();
    if ( width >= 790) {
        if (scroll >= 300) {
        	if(jQuery('#wdm_auto_complete_quiz').length && !jQuery('.wdm_timer').length){
        		jQuery('#wdm_auto_complete_quiz').parent().addClass('wdm_timer');
        	}
        }
        else if(jQuery('.wdm_timer').length){
        	jQuery('#wdm_auto_complete_quiz').parent().removeClass('wdm_timer');
        }
    }
});

    jQuery('a.answers-button').click(function(event){
        event.preventDefault();
        jQuery('.wdm_right').toggleClass('right_answer');
        jQuery('div.sensei-message.info.info-special').toggleClass('wdm_hide');
        text=jQuery(this).text();
        if(text == 'Reveal Answers'){
        jQuery(this).parent().removeClass('wdm_reveal_answer');
        jQuery(this).parent().addClass('wdm_hide_answer');
        jQuery(this).text('Hide Answers');
    }
    else{
        jQuery(this).parent().removeClass('wdm_hide_answer');
        jQuery(this).parent().addClass('wdm_reveal_answer');
        jQuery(this).text('Reveal Answers');
    }
    });
});

jQuery(window).load(function(){
    if(jQuery('#wdm_state_exam').length){
        jQuery("input[name='quiz_save']").addClass('wdm_hide');
        jQuery('.sensei-breadcrumb').remove();
    }
});

