<?php
/**
 * @package    local_crswizard
 * @copyright  2012-2021 Silecs {@link http://www.silecs.info/societe}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once('lib_wizard.php');
require_once('libaccess.php');

require_login();

$systemcontext   = context_system::instance();

wizard_require_permission('creator', $USER->id);

$autovalidation = get_config('local_crswizard','course_autovalidation');

$PAGE->set_context($systemcontext);
$PAGE->set_url('/local/crswizard/index.php');
$PAGE->set_title('Select validator');
$PAGE->requires->js(new moodle_url('/local/jquery/jquery.js'), true);
$PAGE->requires->js(new moodle_url('/local/jquery/jquery-ui.js'), true);
$PAGE->requires->js(new moodle_url('/local/widget_teachersel/teachersel.js'), true);
$PAGE->requires->js_init_code(file_get_contents(__DIR__ . '/js/include-for-validator.js'));
$PAGE->requires->css(new moodle_url('/local/crswizard/css/crswizard.css'));

echo $OUTPUT->header();
echo $OUTPUT->box(get_string('wizardcourse', 'local_crswizard'), 'titlecrswizard');
echo $OUTPUT->box(get_string('selectvalidator', 'local_crswizard'), 'titlecrswizard');
if ($autovalidation == 1) {
    echo $OUTPUT->box(get_string('bockhelpE3autovalidator', 'local_crswizard'), '');
} else {
    echo $OUTPUT->box(get_string('bockhelpE3validator', 'local_crswizard'), '');
}


echo '<form action="' . $CFG->wwwroot . '/local/crswizard/index.php" method="post" class="mform">';
?>

<div class="fitem">
<fieldset class="clearfix" id="categoryheader">
    <div class="fcontainer clearfix">
        <br/>
        <div id="user-select">
            <div class="widgetselect-panel-left">
    <?php
        if ($autovalidation == 1) {
            $inputchecked = ' checked="checked" ';
            if (!empty($SESSION->wizard['form_step3']) && empty($SESSION->wizard['form_step3']['autovalidation'])) {
                $inputchecked = '';
            }
            echo '<div class="fitem fitem_fcheckbox"><div class="fitemtitle">'
                . '<span for="id_autovalidation">Je suis responsable de cet enseignement</span></div>'
                . '<div class="felement fcheckbox"><span>'
                . '<input type="checkbox" style="margin-top: 9px;" name="autovalidation" id="id_autovalidation" '
                . $inputchecked . '/></span></div></div>';
            echo '<h3>' . get_string('findvalidator', 'local_crswizard') . '</h3>';
            echo '<div class="fcontainer clearfix">Si vous créez cet EPI pour quelqu\'un d\'autre ou si vous êtes chargé de TD'
                . ', veuillez rechercher le responsable de l\'enseignement puis ajoutez-le en cliquant sur le symbole +. Veuillez également décocher la case ci-dessus.</div>';
        } else {
            echo '<h3>' . get_string('findvalidator', 'local_crswizard') . '</h3>';
        }
    ?>
                <input type="text" class="user-selector" name="something" data-inputname="teacher" size="50"
                    placeholder="<?php echo s(get_string('validatorname', 'local_crswizard')); ?>" />
            </div>
            <div class="widgetselect-panel-right">
                <h3><?php echo get_string('selectedvalidator', 'local_crswizard'); ?></h3>
                <div class="users-selected"></div>
            </div>
        </div>
    </div>
</fieldset>
</div>

<?php
$tabinfo = array();
$tabinfo['username'] = fullname($USER);
$tabinfo['userlogin'] = $USER->username;
$tabinfo['courserequestdate'] = date('d-m-Y');
?>

<div class="fitem">
<fieldset class="clearfix" id="categoryheader">
    <legend class="ftoggler" ><?php echo get_string('managecourseblock', 'local_crswizard');?></legend>
    <div class="fcontainer clearfix">
        <?php
            foreach ($tabinfo as $key => $value) {
                $label = get_string($key, 'local_crswizard');
                $htmllabel = html_writer::label($label, '', true, ['class' => 'd-inline word-break ']);
                $htmldivlabel = html_writer::div($htmllabel, 'col-md-3 col-form-label d-flex pb-0 pe-md-0', ['style' => 'padding-top: 0px ']);
                $htmldivcontenu = html_writer::div($value, 'col-md-9 d-flex flex-wrap align-items-start felement');
                echo html_writer::div($htmldivlabel . $htmldivcontenu, 'mb-3 row  fitem   crswizard-form-align');
            }
        ?>
    </div>
</fieldset>
</div>

<?php
$listCohort = trim(get_config('local_crswizard', 'cohorts_cap_validator'));
$listCohort = preg_replace('/\s+,\s+/', ',', $listCohort);
?>

<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function () {
    $('#user-select').autocompleteUser({
        urlUsers: '../mwsgroups/service-users.php',
        labelDetails: 'approbateur',
        maxSelected: 1,
        wsParams: { exclude: '<?php echo $USER->username;?>',
            cohorts: '<?php echo $listCohort;?>',
            affiliation: 1,
            maxRows: 50 },
        preSelected: <?php echo wizard_preselected_validators();?>
    });
});
//]]>
</script>

<?php
require __DIR__ . '/enrol/footer.php';
