jQuery(document).ready(function(){
     jQuery('#wdm_save_questions').click(function(event){
        var total=0;
        jQuery('.wdm_no_of_questions').each(function(){
            if(jQuery(this).val().length){
            total= total + parseInt(jQuery(this).val());
        }
            //console.log(jQuery(this).val());
        });
        //console.log(total);
        if(total != 150){
            alert('The addition is must be 150');
         event.preventDefault();
        }
    //     category_id = jQuery('#wdm_question_categories').val();
    //     number_of_questions = jQuery('#wdm_no_of_question').val();
    //     jQuery.ajax({
    //      type : "post",
    //      // dataType : "json",
    //      url : state_exam_admin.ajaxurl,
    //      data : {action: "save_questions",'category_id': category_id,'number_of_questions': number_of_questions},
    //      success: function(response) {
    //         console.log(response);
    //         //alert("Your vote could not be added");

    //      }
    //   })
     });

    // jQuery('#wdm_question_categories').change(function(){
    //     //console.log(jQuery(this).val());
    //     jQuery('#wdm_save_questions').before('<input type="text" id="wdm_no_of_question">');
    // });

});
