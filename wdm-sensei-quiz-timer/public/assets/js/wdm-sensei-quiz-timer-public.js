var timer_status;
var interval = '';
jQuery(document).ready(function () {
    if(quiz_meta.logged_in == 'on'){
        $('#timer').countdowntimer({ hours : quiz_meta.hour , minutes : quiz_meta.minutes ,seconds : quiz_meta.seconds , size : "lg"});
        //$('#timer').countdowntimer({ hours : 3 , minutes : 20 ,seconds : 10 , size : "lg"});
        // $('#timer').countdown({until: new Date(quiz_meta.quiz_time), format: 'HMS',compact: true,onExpiry:complete_quiz,timezone: quiz_meta.time_zone});
        // $('#timer').countdown('pause');
        $('#timer').addClass('disable_timer');
        timer_status = 'inactive';
    }
    else{
        $('#timer').countdowntimer({ hours : quiz_meta.hour , minutes : quiz_meta.minutes ,seconds : quiz_meta.seconds , size : "lg", pauseButton : "pauseBtnhms", stopButton : "stopBtnhms"});
        timer_status = 'active';
    }
//$('#timer').countdowntimer({ hours : 3 , minutes : 20 ,seconds : 10 , size : "lg"});
    if(jQuery('#timer').length > 0){
        interval = setInterval(function(){update_quiz_timer('false');}, 60000);
    }

    if (jQuery("input[name=quiz_complete]").length){
        var ce_interval = setInterval(function(){
            if(jQuery('#timer').text() == "00:00:00"){
                console.log("complete_quiz");
                clearInterval(ce_interval);
                jQuery("input[name = 'quiz_complete']").trigger('click');
            }
        }, 1000);
    }

function complete_quiz(){
    clearInterval(interval);
    if(jQuery("input[name = 'quiz_complete']").length>0){
        jQuery("input[name = 'quiz_complete']").trigger('click');
        jQuery("input[name = 'quiz_complete']").attr('disabled','disabled');
    }else{
        jQuery('.quiz form').trigger('submit');
    }
}

jQuery(window).on('beforeunload', function(){
    update_quiz_timer('true');
    jQuery('.quiz-submit.save').trigger("click");
});



function update_quiz_timer(reload){
    time = jQuery('#timer').text();
    jQuery.ajax({
            type:'post',
            url : quiz_meta.ajax_url,
            async:false,
            data:{
            'action' : 'save_time_remaining',
            'quiz_id' : quiz_meta.quiz_id,
            'user_id' : quiz_meta.user_id,
            'time' : time ,
            'reload' : reload,
            'timer_status':timer_status,
        },
        success:function(response){
            if(response){
                timer_status = 'active';

                $('#timer').removeClass('disable_timer');


               // var res = response.split(":");
                //$('#timer').countdown({until: new Date(response), format: 'HMS',compact: true,onExpiry:complete_quiz,timezone: quiz_meta.time_zone});
                //$('#timer').countdowntimer({ hours : response[0] , minutes : response[1] ,seconds : response[2] , size : "lg", pauseButton : "pauseBtnhms", stopButton : "stopBtnhms"});
                //console.log('Ajax Response');
            }
        }
        });
}
});
