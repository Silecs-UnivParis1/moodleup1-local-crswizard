<?php
/**
 * @package    local_crswizard
 * @copyright  2012-2021 Silecs {@link http://www.silecs.info/societe}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 

require_once('../../../config.php');
require_once('../lib_wizard.php');
require_once('../libaccess.php');


require_login();
$direct = false;
if (isset($SESSION->wizard['form_step4']['redirect'])) {
	$direct = true;
} 
if (isset($SESSION->wizard['idcourse'])) {
    $idcourse = $SESSION->wizard['idcourse'];
    wizard_require_update_permission($idcourse, $USER->id);
    $course = $DB->get_record('course', array('id'=>$idcourse), '*', MUST_EXIST);
    require_login($course);
    $coursecontext = context_course::instance($course->id);
    $PAGE->set_context($coursecontext);
    $pageparams = array('id'=>$idcourse);
    $PAGE->set_url('/local/crswizard/update/index.php', $pageparams);
    $streditcoursesettings = get_string("editcoursesettings");
    $PAGE->navbar->add($streditcoursesettings);
} else {
	$systemcontext   = context_system::instance();
    $PAGE->set_context($systemcontext);
    wizard_require_permission('creator', $USER->id);
    $PAGE->set_url('/local/crswizard/index.php');
}


$PAGE->set_title($SESSION->wizard['form_step2']['fullname'] . ': ' . get_string('teacher', 'local_crswizard'));
$PAGE->requires->js(new moodle_url('/local/jquery/jquery.js'), true);
$PAGE->requires->js(new moodle_url('/local/jquery/jquery-ui.js'), true);
$PAGE->requires->js(new moodle_url('/local/widget_teachersel/teachersel.js'), true);
$PAGE->requires->css(new moodle_url('/local/crswizard/css/crswizard.css'));

echo $OUTPUT->header();
if (isset($SESSION->wizard['idcourse'])) {
    echo $OUTPUT->box(get_string('upwizardcourse', 'local_crswizard'), 'titlecrswizard');
} else {
    echo $OUTPUT->box(get_string('wizardcourse', 'local_crswizard'), 'titlecrswizard');
}

echo $OUTPUT->box(get_string('enrolteachers', 'local_crswizard'), 'titlecrswizard');
echo $OUTPUT->box(get_string('bockhelpE4', 'local_crswizard'), '');

$myconfig = new my_elements_config();
$labels = $myconfig->role_teachers;
$roles = wizard_role($labels);

if (isset($SESSION->wizard['form_step4']['users-inactif']) && count($SESSION->wizard['form_step4']['users-inactif'])) {
    echo '<p>' . get_string('labelteachersuspended', 'local_crswizard') . '</p>';
    echo '<ul>';
    foreach ($SESSION->wizard['form_step4']['users-inactif'] as $role => $users) {
        $labelrole = isset($roles[$role]['name']) ? $roles[$role]['name'] : $role;
        foreach ($users as $user) {
            echo '<li>' . fullname($user) .  ' — ' . $user->username . ' (' . $labelrole . ')  </li>';
        }
    }
    echo '</ul>';
}

echo '<form action="' . $CFG->wwwroot . $SESSION->wizard['wizardurl'] . '" method="post">';
?>

<br/>
<div id="user-select">
    <div class="widgetselect-panel-left">

<div class="role">
<h3>Choisir un rôle</h3>
	<select name="role" size="1" id="roleteacher" class="custom-select">
        <?php
        foreach ($roles as $r) {
            $label = $r['name'];
            echo '<option value="' . s($r['shortname']) . '">' . format_string($label) . '</option>';
        }
        ?>
	</select>
	<input type='hidden' id="selectedrolelabel"/>
    <input type='hidden' id="selectedrolename"/>
</div>

        <h3><?php echo get_string('findteacher', 'local_crswizard'); ?></h3>
        <input type="text" class="user-selector" name="something" data-inputname="teacher" size="50"
               placeholder="<?php echo s(get_string('teachername', 'local_crswizard')); ?>" />

	  <div>Recherchez l'utilisateur auquel vous souhaitez attribuer ce rôle<br>
				puis cliquez sur + pour l'ajouter aux Enseignants sélectionnés </div>
    </div>
    <div class="widgetselect-panel-right">
        <h3><?php echo get_string('selectedteacher', 'local_crswizard'); ?></h3>
        <div class="users-selected"></div>
    </div>
</div>

<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function () {
    $('#user-select').autocompleteUser({
        urlUsers: '../../mwsgroups/service-users.php',
        wsParams: { affiliation: 1, maxRows: 50 },
        preSelected: <?php echo wizard_preselected_users($direct) ?>
    });

    $('#roleteacher').on('change', function() {
        var sel = $(this).val();
        var sellabel = $('#roleteacher > option:selected').text();
        
        $('#selectedrolelabel').attr('value', sellabel);
        $('#selectedrolename').attr('value', 'user[' + sel + ']');
        if ($('#user-select').data('autocompleteUser') !== undefined) {
			$('#user-select').data('autocompleteUser').settings.fieldName = 'user[' + sel + ']';
			$('#user-select').data('autocompleteUser').settings.labelDetails = sellabel;
		}
    });
    $('#roleteacher').change();
});
//]]>
</script>

<?php
if (isset($SESSION->wizard['idcourse'])) {
	
	echo '<input type="hidden" name="stepin" value="9"/>';
    echo '<div style="margin:50px; clear:both; text-align: center;">'
		. '<div class="buttons">'
		. '<button type="submit" id="etapes" name="enregistrer" value="enregistrer">' . get_string('save') . '</button>'	
		. '</div>'
		. '</div>';
} else {
    require __DIR__ . '/footer.php';
}
