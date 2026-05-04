<?php
$stepback = $SESSION->wizard['wizardcase'] == 3 ? 3 : 2;
$urlback = new moodle_url('/local/crswizard/update/index.php', array('stepin' => $stepback));
$stepin = $SESSION->wizard['navigation']['stepin'];
$stepnext = $SESSION->wizard['navigation']['suite'];

$PAGE->requires->js_init_code('
var globalFormLock = true;
$("form").submit(function(e) {
    if (globalFormLock) { e.preventDefault(); } else { return true };
});
$(":submit").on("click", function(e) {
    // mouse || focus on submit button
    if (e.clientX > 0 || e.clientY > 0 | $(e.currentTarget).is(":submit:focus")) {
        globalFormLock = false;
    }
});
');
?>
<div style="margin:50px; clear:both; text-align: center;">
    <input type="hidden" name="stepin" value="<?php echo $stepin; ?>"/>
    <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>"/>
    <input type="hidden" name="step" value=""/>
    <div class="buttons">
        <span class="previousstage">
            <?php
            if (isset($SESSION->wizard['init_course']['profile_field_syl_elpcode']) && $SESSION->wizard['init_course']['profile_field_syl_elpcode']) {
                $urlback = new moodle_url('/local/crswizard/syllabus/step_syllabus.php');
            }
            echo $OUTPUT->action_link($urlback, get_string('previousstage', 'local_crswizard'));
            ?>
        </span>
        <button type="submit" id="etapes" name="step" value="">
            <?php echo get_string('nextstage', 'local_crswizard'); ?>
        </button>
        <?php 
        if (isset($SESSION->wizard['acces'])) {
			echo '<button type="submit" id="etapes" name="enregistrer" value="enregistrer">'
            . get_string('save')
			. '</button>';			
		}
       ?>
    </div>
</div>

</form>
<?php
echo $OUTPUT->footer();
