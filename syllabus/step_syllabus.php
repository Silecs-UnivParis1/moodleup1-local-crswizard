<?php
/**
 * @package    local_crswizard
 * @copyright  2012-2026 Silecs {@link http://www.silecs.info/societe}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once('../../../config.php');
require_once('../lib_wizard.php');
require_once('../libaccess.php');

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/completionlib.php');
require_once(__DIR__ . '/step_syllabus_form.php');

require_login();

if (isset($SESSION->wizard['idcourse'])) {
    $idcourse = $SESSION->wizard['idcourse'];
    wizard_require_update_permission($idcourse, $USER->id);
    $course = $DB->get_record('course', ['id' => $idcourse], '*', MUST_EXIST);
    require_login($course);
    $coursecontext = context_course::instance($course->id);
    $PAGE->set_context($coursecontext);
    $pageparams = ['id' => $idcourse];
    $PAGE->set_url('/local/crswizard/update/index.php', $pageparams);
    $streditcoursesettings = get_string("editcoursesettings");
    $PAGE->navbar->add($streditcoursesettings);
} else {
    $systemcontext   = context_system::instance();
    $PAGE->set_context($systemcontext);
    wizard_require_permission('creator', $USER->id);
    $PAGE->set_url('/local/crswizard/syllabus/step_syllabus.php');
}

$editoroptions = ['maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes' => $CFG->maxbytes, 'trusttext' => false, 'noclean' => true];
$editform = new course_wizard_step_syllabus_form(NULL, ['editoroptions' => $editoroptions]);

$champSyllabusEditor = ['syl_objectifs', 'syl_plan', 'syl_prerequis', 'syl_evaluation', 'syl_bibliographie'];

if ($editform_data  = $editform->get_data()) {
    //traitement des données
    $syl_responsables = isset($_POST['syl_responsables']) ? $_POST['syl_responsables'] : '';
    $SESSION->wizard['form_step45']['syl_responsables'] = $syl_responsables;
    $SESSION->wizard['form_step45']['all-responsables'] = wizard_get_responsables($syl_responsables);
    $SESSION->wizard['form_step45']['syl_obligatoire'] = $editform_data->syl_obligatoire; //normalement non-modifiable
    $SESSION->wizard['form_step45']['syl_ects'] = $editform_data->syl_ects; //normalement non-modifiable
    $SESSION->wizard['form_step45']['syl_volume'] = $editform_data->syl_volume; //normalement non-modifiable
    $SESSION->wizard['form_step45']['syl_reference'] = $editform_data->syl_reference;
    $SESSION->wizard['form_step45']['syl_contacts'] = $editform_data->syl_contacts;
    foreach ($champSyllabusEditor as $champ) {
        $SESSION->wizard['form_step45'][$champ] = $editform_data->$champ;
    }
    // enregistrer summary_editor dans form2
    $SESSION->wizard['form_step2']['summary_editor'] =  $editform_data->summary_editor;
    //redirection
    redirect($CFG->wwwroot . '/local/crswizard/index.php?stepin=5');
}
$PAGE->set_title($SESSION->wizard['form_step2']['fullname'] . ': ' . 'Étape Syllabus');
$PAGE->requires->js(new moodle_url('/local/jquery/jquery.js'), true);
$PAGE->requires->js(new moodle_url('/local/jquery/jquery-ui.js'), true);
$PAGE->requires->js(new moodle_url('/local/widget_teachersel/teachersel.js'), true);
$PAGE->requires->css(new moodle_url('/local/crswizard/css/crswizard.css'));
$site = get_site();
$PAGE->set_heading($site->fullname);
echo $OUTPUT->header();
$titlecrswizard = isset($SESSION->wizard['idcourse']) ? get_string('upwizardcourse', 'local_crswizard') : get_string('wizardcourse', 'local_crswizard');
echo $OUTPUT->box($titlecrswizard, 'titlecrswizard');
echo $OUTPUT->box('Étape 4.5 - Étape Syllabus', 'titlecrswizard');

$form_stepx = 'form_step' . $SESSION->wizard['wizardcase'];
$form_step_rof = $SESSION->wizard[$form_stepx];
if (!isset($form_step_rof['rattachement-matiere'])) {
    $form_step_rof['rattachement-matiere'] = wizard_get_rattachement_matiere($form_stepx);
}
$rof = $form_step_rof['all-rof'][$form_step_rof['rattachement-matiere']];
if ($rof) {
    $SESSION->wizard['form_step45']['syl_elpcode'] = $rof['object']->code;
    $SESSION->wizard['form_step45']['syl_elpintitule'] = $rof['object']->name;
}
$SESSION->wizard['form_step45']['summary_editor'] = $SESSION->wizard['form_step2']['summary_editor'];
if (isset($SESSION->wizard['form_step4']) && isset($SESSION->wizard['form_step4']['all-users']) && isset($SESSION->wizard['form_step4']['all-users']['responsable_epi'])) {
    $responsables_epi = $SESSION->wizard['form_step4']['all-users']['responsable_epi'];
    if (count($responsables_epi)) {
        $text = '';
        foreach ($responsables_epi as $resp) {
            $text .= $resp->firstname . ' ' . $resp->lastname . ' : ' . $resp->email. "\n";
        }
        $SESSION->wizard['form_step45']['syl_contacts'] = $text;
    }
}

if (isset($SESSION->wizard['form_step45'])) {
    $editform->set_data($SESSION->wizard['form_step45']);
}

$editform->display();
echo $OUTPUT->footer();
