<?php
/**
 * The Template for displaying Multiple Choice Questions.
 */

global $woothemes_sensei, $current_user;
$question_count = count($questions);
foreach ($questions as $question) {
    $question_id = $question->ID;
    $question_right_answer = get_post_meta($question_id, '_question_right_answer', true);
    $question_wrong_answers = get_post_meta($question_id, '_question_wrong_answers', true);
// Question media
    $question_media = get_post_meta($question_id, '_question_media', true);
    $question_media_type = $question_media_thumb = $question_media_link = $question_media_title = $question_media_description = '';
    if (0 < intval($question_media)) {
        $mimetype = get_post_mime_type($question_media);
        if ($mimetype) {
            $mimetype_array = explode('/', $mimetype);
            if (isset($mimetype_array[0]) && $mimetype_array[0]) {
                $question_media_type = $mimetype_array[0];
                $question_media_url = wp_get_attachment_url($question_media);
                $attachment = get_post($question_media);
                $question_media_title = $attachment->post_title;
                $question_media_description = $attachment->post_content;
                switch ($question_media_type) {
                    case 'image':
                        $image_size = apply_filters('sensei_question_image_size', 'medium', $question_id);
                        $attachment_src = wp_get_attachment_image_src($question_media, $image_size);
                        $question_media_link = '<a class="' . esc_attr($question_media_type) . '" title="' . esc_attr($question_media_title) . '" href="' . esc_url($question_media_url) . '" target="_blank"><img src="' . $attachment_src[0] . '" width="' . $attachment_src[1] . '" height="' . $attachment_src[2] . '" /></a>';
                        break;

                    case 'audio':
                        $question_media_link = wp_audio_shortcode(array( 'src' => $question_media_url ));
                        break;

                    case 'video':
                        $question_media_link = wp_video_shortcode(array( 'src' => $question_media_url ));
                        break;

                    default:
                        $question_media_filename = basename($question_media_url);
                        $question_media_link = '<a class="' . esc_attr($question_media_type) . '" title="' . esc_attr($question_media_title) . '" href="' . esc_url($question_media_url) . '" target="_blank">' . $question_media_filename . '</a>';
                        break;
                }
            }
        }
    }
    $answer_type = 'radio';
    if (is_array($question_right_answer)) {
        if (1 < count($question_right_answer)) {
            $answer_type = 'checkbox';
        }
        $question_wrong_answers = array_merge($question_wrong_answers, $question_right_answer);
    } else {
        array_push($question_wrong_answers, $question_right_answer);
    }

    $question_answers =array();
// Setup answer array
    foreach ($question_wrong_answers as $answer) {
        $answer_id = $woothemes_sensei->post_types->lesson->get_answer_id($answer);
        $question_answers[ $answer_id ] = $answer;
    }

    $answers_sorted = array();
    $random_order = get_post_meta($question_id, '_random_order', true);
    if ($random_order && $random_order == 'yes') {
        $answers_sorted = $question_answers;
        shuffle($answers_sorted);
    } else {
        $answer_order = array();
        $answer_order_string = get_post_meta($question_id, '_answer_order', true);
        if ($answer_order_string) {
            $answer_order = array_filter(explode(',', $answer_order_string));
            if (count($answer_order) > 0) {
                foreach ($answer_order as $answer_id) {
                    if (isset($question_answers[ $answer_id ])) {
                        $answers_sorted[ $answer_id ] = $question_answers[ $answer_id ];
                        unset($question_answers[ $answer_id ]);
                    }
                }

                if (count($question_answers) > 0) {
                    foreach ($question_answers as $id => $answer) {
                        $answers_sorted[ $id ] = $answer;
                    }
                }
            } else {
                $answers_sorted = $question_answers;
            }
        } // end if $answer_order_string
    }

    $question_info = get_post_meta($question_id, 'di_question_details', true);

    $question_text = get_the_title($question);
    $question_description = apply_filters('the_content', $question->post_content);

    $answer_message = false;
    $answer_notes = false;

    $answer_notes = get_post_meta($question_id, '_answer_feedback', true);
    if ($answer_notes) {
        $answer_message_class .= ' has_notes';
    }
?>
<div class="di-info"><?php echo $question_info ;?></div>
<li class="multiple-choice">
    <span class="question"><?php echo apply_filters('sensei_question_title', esc_html($question_text)); ?> <?php /* ?><span class="grade">[<?php echo $question_grade; ?>]</span><?php*/ ?></span>
    <?php echo $question_description; ?>
    <?php if ($question_media_link) { ?>
        <div class="question_media_display">
            <?php echo $question_media_link; ?>
            <dl>
                <?php if ($question_media_title) { ?>
                    <dt><?php echo $question_media_title; ?></dt>
                <?php } ?>
                <?php if ($question_media_description) { ?>
                    <?php echo '<dd>' . $question_media_description . '</dd>'; ?>
                <?php } ?>
            </dl>
        </div>
    <?php } ?>
     <input type="hidden" name="<?php echo esc_attr('question_id_' . $question_id); ?>" value="<?php echo esc_attr($question_id); ?>" />
     <ul class="answers">
    <?php
    $count = 0;
    foreach ($answers_sorted as $id => $answer) {
        $checked = '';
        $count++;

        $answer_class = '';
        if (is_array($question_right_answer) && in_array($answer, $question_right_answer)) {
                $answer_class .= ' wdm_right';
        }
        ?>
        <li class="<?php esc_attr_e($answer_class); ?>">
            <input type="<?php echo $answer_type; ?>" id="<?php echo esc_attr('question_' . $question_id) . '-option-' . $count; ?>" name="<?php echo esc_attr('sensei_question[' . $question_id . ']'); ?>[]" value="<?php echo esc_attr($answer); ?>" <?php echo $checked; ?><?php if (!is_user_logged_in()) {
                echo ' disabled';
} ?>>&nbsp;
            <label for="<?php echo esc_attr('question_' . $question_id) . '-option-' . $count; ?>"><?php echo apply_filters('sensei_answer_text', $answer); ?></label>
        </li>
    <?php                                                                                                     } // End For Loop ?>
    </ul>
    <?php if ($answer_notes) { ?>
        <div class="sensei-message info info-special wdm_hide"><?php echo apply_filters('the_content', $answer_notes); ?></div>
    <?php } ?>
</li>
<?php
}//End of foreach of questions array
