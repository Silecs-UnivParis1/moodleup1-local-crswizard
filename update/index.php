<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Edit course settings
 *
 * @package    local
 * @subpackage crswizard
 * @copyright  2012-2013 Silecs {@link http://www.silecs.info/societe}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or laters
 */

require_once('../../../config.php');
require_once('../../../course/lib.php');
require_once('../../../lib/coursecatlib.php');
require_once(__DIR__ . '/../lib_wizard.php');
require_once(__DIR__ . '/../wizard_modele_duplicate.class.php');
require_once(__DIR__ . '/../wizard_core.class.php');
require_once(__DIR__ . '/../libaccess.php');
require_once(__DIR__ . '/../step2_form.php');
require_once(__DIR__ . '/../step2_rof_form.php');
require_once(__DIR__ . '/../step3_form.php');
require_once(__DIR__ . '/../step_cle.php');
require_once(__DIR__ . '/confirm.php');


require_once(__DIR__ . '/lib_update_wizard.php');

global $CFG, $PAGE, $OUTPUT, $SESSION, $USER;

require_login();

$id = optional_param('id', 0, PARAM_INT);
if (empty($id)) {
    if (isset($SESSION->wizard['init_course']['id'])) {
        $id = $SESSION->wizard['init_course']['id'];
    } else {
        print_error('invalidcourseid');
    }
}
$pageparams = array('id'=>$id);
$PAGE->set_url('/local/crswizard/update/index.php', $pageparams);
$PAGE->requires->css(new moodle_url('../local/crswizard/css/crswizard.css'));

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);
require_login($course);
$coursecontext = context_course::instance($course->id);
$PAGE->set_context($coursecontext);

$stepin = optional_param('stepin', 0, PARAM_INT);

if (!$stepin) {
    $stepin = 2;
    $stepgo = 2;
    if (isset($SESSION->wizard)) {
        unset($SESSION->wizard);
    }
    // recupérer les données du cours
    wizard_get_course($id);
    wizard_require_update_permission($id, $USER->id);

    $SESSION->wizard['wizardurl'] = '/local/crswizard/update/index.php';
    $SESSION->wizard['idcourse'] = $id;
    $SESSION->wizard['urlpfixe'] = $CFG->wwwroot . '/fixe/';

} else {
    $stepgo = $stepin + 1;
}

wizard_navigation($stepin);
$wizardcase = $SESSION->wizard['wizardcase'];
// si $wizardcase == 0 faire quelque chose

switch ($stepin) {
    case 1:
        $url = new moodle_url($CFG->wwwroot.'/course/view.php', array('id'=>$id));
        redirect($url);
        break;
    case 2:
        $steptitle = get_string('upcoursedefinition', 'local_crswizard');
        $editoroptions = array(
            'maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes' => $CFG->maxbytes, 'trusttext' => false, 'noclean' => true
        );
        $PAGE->requires->js(new moodle_url('/local/jquery/jquery.js'), true);
        $PAGE->requires->css(new moodle_url('/local/crswizard/css/crswizard.css'));
        if ($wizardcase == 3) {
            $editform = new course_wizard_step2_form(NULL, array('editoroptions' => $editoroptions));
        } elseif ($wizardcase == 2) {
            $PAGE->requires->css(new moodle_url('/local/rof_browser/browser.css'));
            $PAGE->requires->js(new moodle_url('/local/rof_browser/selected.js'), true);
            $PAGE->requires->js_init_code(file_get_contents(__DIR__ . '/../js/include-for-rofform.js'), true);
            $editform = new course_wizard_step2_rof_form(NULL, array('editoroptions' => $editoroptions));
        }
        $PAGE->requires->js_init_code(file_get_contents(__DIR__ . '/../js/include-for-urlfixe.js'), true);

        $data = $editform->get_data();
        if ($data){
            $data->fullname = trim($data->fullname);
            if (isset($data->shortname)) {
                $data->shortname = trim($data->shortname);
            }
            $SESSION->wizard['form_step' . $stepin] = (array) $data;
            if ($wizardcase == 2) {
                 $SESSION->wizard['form_step2']['item'] = wizard_get_array_item($_POST['item']);
                 $SESSION->wizard['form_step2']['all-rof'] = wizard_get_rof();
                 $SESSION->wizard['form_step2']['complement'] = trim($_POST['complement']);
            }
            redirect($CFG->wwwroot . '/local/crswizard/update/index.php?stepin=' . $stepgo);
        } else {
            $PAGE->requires->js(new moodle_url('/local/crswizard/js/select-into-subselects.js'), true);
            $PAGE->requires->js_init_code(file_get_contents(__DIR__ . '/../js/include-for-subselects.js'));
        }
        break;
    case 3:
        if ($wizardcase == 3) {
            $hybridattachment_permission = false;
            $idcourse = 1;
            if (isset($SESSION->wizard['idcourse'])) {
                $idcourse = $SESSION->wizard['idcourse'];
            }
            $hybridattachment_permission = wizard_has_hybridattachment_permission($idcourse, $USER->id);
            get_selected_etablissement_id();

            $editform = new course_wizard_step3_form();

            $data = $editform->get_data();
            if ($data){
                $data->user_name = $SESSION->wizard['form_step3']['user_name'];
                $data->user_login = $SESSION->wizard['form_step3']['user_login'];
                $data->requestdate = $SESSION->wizard['form_step3']['requestdate'];
                $data->idetab = $SESSION->wizard['form_step3']['idetab'];

                if ($hybridattachment_permission === false) {
                    $init_course_form3 = $SESSION->wizard['init_course']['form_step3'];
                    $data->item = (isset($init_course_form3['item']) ? $init_course_form3['item'] : array());
                } else {
                    $data->item = (isset($_POST['item']) ? wizard_get_array_item($_POST['item']) : array());
                }

                $data->rattachements = array_unique(array_filter($data->rattachements));
                $SESSION->wizard['form_step' . $stepin] = (array) $data;
                $SESSION->wizard['form_step3']['all-rof'] = wizard_get_rof('form_step3');
                redirect($CFG->wwwroot . '/local/crswizard/update/index.php?stepin=' . $stepgo);
            }
            $steptitle = get_string('upcoursedescription', 'local_crswizard');
            $PAGE->requires->js(new moodle_url('/local/jquery/jquery.js'), true);
            $PAGE->requires->js(new moodle_url('/local/crswizard/js/select-into-subselects.js'), true);
            $PAGE->requires->js_init_code(file_get_contents(__DIR__ . '/../js/include-for-rattachements.js'));
            if ($hybridattachment_permission) {
                $PAGE->requires->css(new moodle_url('/local/rof_browser/browser.css'));
                $PAGE->requires->js(new moodle_url('/local/rof_browser/selected.js'), true);
            }
        } elseif ($wizardcase == 2) {
            $SESSION->wizard['navigation']['stepin'] = 5;
            $SESSION->wizard['navigation']['suite'] = 6;
            $SESSION->wizard['navigation']['retour'] = 3;
            redirect(new moodle_url('/local/crswizard/enrol/cohort.php'));
        }
        break;
    case 4:
        $SESSION->wizard['navigation']['stepin'] = 5;
        $SESSION->wizard['navigation']['suite'] = 6;
        $SESSION->wizard['navigation']['retour'] = 3;
        redirect(new moodle_url('/local/crswizard/enrol/cohort.php'));
        break;
    case 5:
        if (isset($_POST['step'])) {
            //* @todo Validate cohort list
            $SESSION->wizard['form_step' . $stepin] = $_POST;
            $SESSION->wizard['form_step5']['all-cohorts'] = wizard_get_enrolement_cohorts();
            redirect($CFG->wwwroot . '/local/crswizard/update/index.php?stepin=' . $stepgo);
        }
        redirect(new moodle_url('/local/crswizard/enrol/cohort.php'));
        break;
    case 6:
        $steptitle = get_string('upstepkeycase2', 'local_crswizard');
        if ($wizardcase == 3) {
            $steptitle = get_string('upstepkeycase3', 'local_crswizard');
        }
        $editform = new course_wizard_step_cle();
        $PAGE->requires->css(new moodle_url('/local/crswizard/css/crswizard.css'));
        $PAGE->requires->js(new moodle_url('/local/jquery/jquery.js'), true);
        $PAGE->requires->js_init_code(file_get_contents(__DIR__ . '/../js/include-for-key.js'));

        $data = $editform->get_data();
        if ($data){
            $SESSION->wizard['form_step' . $stepin] = (array) $data;
            redirect($CFG->wwwroot . '/local/crswizard/update/index.php?stepin=' . $stepgo);
        }
        break;
    case 7:
        if ($wizardcase == 2) {
            $steptitle = get_string('updatetitlecase2', 'local_crswizard');
        } else {
            $steptitle = get_string('updatetitlecase3', 'local_crswizard');
        }
        $corewizard = new wizard_core($SESSION->wizard, $USER);
        $formdata = $corewizard->prepare_update_course();
        $editform = new course_wizard_confirm();
        $editform->set_data($formdata);

        $data = $editform->get_data();
        if ($data){
            $SESSION->wizard['form_step' . $stepin] = (array) $data;
            redirect($CFG->wwwroot . '/local/crswizard/update/index.php?stepin=' . $stepgo);
        }
        break;
    case 8:
        $corewizard = new wizard_core($SESSION->wizard, $USER);
        $errorMsg = $corewizard->update_course();
        $urlcourse = new moodle_url('/course/view.php',array('id' => $id));
        unset($SESSION->wizard);
        redirect($urlcourse);
        break;
}

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

$streditcoursesettings = get_string("editcoursesettings");
$PAGE->navbar->add($streditcoursesettings);

$site = get_site();
$PAGE->set_title("$site->shortname: $streditcoursesettings");
$PAGE->set_heading($site->fullname);

echo $OUTPUT->header();
echo $OUTPUT->box(get_string('upwizardcourse', 'local_crswizard'), 'titlecrswizard');
echo $OUTPUT->box($steptitle, 'titlecrswizard');

if (isset($editform)) {
    if (isset($SESSION->wizard['form_step' . $stepin])) {
        $editform->set_data($SESSION->wizard['form_step' . $stepin]);
    }
    $editform->display();
} else {
    echo '<p>Pas de formulaires</p>';
}

echo $OUTPUT->footer();
