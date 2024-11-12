<?php

/**
 * Edit course settings
 *
 * @package    local_crswizard
 * @copyright  2012-2021 Silecs {@link http://www.silecs.info/societe}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or laters
 */

require_once('../../../config.php');
require_once('../../../course/lib.php');
require_once('../lib_wizard.php');
require_once('../libaccess.php');
require_once('../../../admin/tool/up1_batchprocess/locallib.php');
require_once('../../../admin/tool/up1_batchprocess/batch_lib.php');
require_once('../../../admin/tool/up1_batchprocess/batch_libactions.php');

require_once('archive_form.php');


global $CFG, $PAGE, $OUTPUT, $USER;

require_login();

$id = required_param('id', PARAM_INT);

if (empty($id)) {
    print_error('invalidcourseid');
}

$pageparams = array('id'=>$id);

$PAGE->set_url('/local/crswizard/archive/index.php', $pageparams);
$PAGE->requires->css(new moodle_url('/local/crswizard/css/crswizard.css'));

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

if (! wizard_has_edit_course($course->id, $USER->id) ) {
    throw new moodle_exception('Vous n\'avez pas la permission d\'accéder à cette page.');
}

require_login($course);
$coursecontext = context_course::instance($course->id);
$PAGE->set_context($coursecontext);

$courseshortname = $course->shortname;
$archivecourse = get_string('archivingcourse', 'local_crswizard', $courseshortname);
$PAGE->navbar->add($archivecourse);

$site = get_site();
$PAGE->set_title("$site->shortname: $archivecourse");
$PAGE->set_heading($site->fullname);

$title = html_writer::tag('h2', "Archiver l'espace de cours $courseshortname");

$msg = '';
$prefix = default_prefix();
$isodate = isoDate();

$defaultRolesTeacher = ['from' => 'editingteacher', 'to' => 'ens_epi_archive'];
$rolesTeacher = [];
foreach ($defaultRolesTeacher as $key => $role) {
    if ($DB->record_exists('role', ['shortname' => $role])) {
        $rolesTeacher[$key] = $DB->get_field('role', 'id', ['shortname' => $role]);
    }
}
$substRole = "Substituer les rôles \"enseignant éditeur\" par \"enseignant EPI archivé\". "
    . "C'est-à-dire que vous pourrez toujours accéder à cet EPI et le dupliquer, mais vous ne pourrez plus le modifier.";
if (count($rolesTeacher) < 2) {
    $substRole = '<strike>' . $substRole . '</strike>';
    if (isset($rolesTeacher['to']) == FALSE) {
        $substRole .= '<br /><b>Le rôle "enseignant EPI archivé" n\'exite pas.</b>';
    }
    if (isset($rolesTeacher['from']) == FALSE) {
        $substRole .=  '<br /><b>Le rôle "enseignant éditeur" n\'exite pas.</b>';
    }
}

$defaultRolesStudent = ['from' => 'student', 'to' => 'student-epi-archived'];
$rolesStudent = [];
foreach ($defaultRolesStudent as $key => $role) {
    if ($DB->record_exists('role', ['shortname' => $role])) {
        $rolesStudent[$key] = $DB->get_field('role', 'id', ['shortname' => $role]);
    }
}
$substRoleStudent = 'Substituer les rôles "étudiant" par "étudiant EPI archivé".';
if (count($rolesStudent) < 2) {
    $substRoleStudent = '<strike>' . $substRoleStudent . '</strike>';
    if (isset($rolesStudent['to']) == FALSE) {
        $substRoleStudent .= '<br /><b>Le rôle "étudiant EPI archivé" n\'exite pas.</b>';
    }
    if (isset($rolesStudent['from']) == FALSE) {
        $substRoleStudent .=  '<br /><b>Le rôle "étudiant" n\'exite pas.</b>';
    }
}

$msgEffect = html_writer::tag('div', "Cette action n'est pas réversible. Elle conduira à :", array('class' => 'fitem'));
$msgEffect .= html_writer::start_tag('ul', array('class'=>'list'));
$msgEffect .= html_writer::tag('li', "Fermer \"$courseshortname\", donc les etudiant.e.s n'y auront plus accès.");
$msgEffect .= html_writer::tag('li', "Préfixer le nom de l'EPI avec \"$prefix\".");
$msgEffect .= html_writer::tag('li', $substRole);
$msgEffect .= html_writer::tag('li', "Indiquer la date du jour comme date d'archivage.");
$msgEffect .= html_writer::tag('li', $substRoleStudent);
$msgEffect .= html_writer::end_tag('ul');

$msgEffect .= html_writer::tag('div', " (Toutes ces opérations seront appliquées courant juillet à l'ensemble des EPI, "
    . "sauf ceux qui sont déjà archivés et ceux dont le calendrier ne correspond pas à l'année universitaire.)");


$mform = new wizard_archive_form(null, array('shortname' => $courseshortname));
$newformdata = array('id'=>$id);
$mform->set_data($newformdata);
$formdata = $mform->get_data();

$header = $OUTPUT->header();

if ($formdata) {

    $urlcourse = $CFG->wwwroot . '/course/view.php?id='.$course->id;
    $linkcourse = html_writer::tag('a', 'Retour au cours', array('href'=> $urlcourse));

    if (strtolower(trim($formdata->confirmation)) == 'oui') {
        $categorycontext = context_coursecat::instance($course->category);
        $PAGE->set_context($categorycontext);

        $myCourse = [$course];
        //fermer
        $msg .= batchaction_visibility($myCourse, 0, false) . "<br />\n";
        //prefixe archive...
        $prefix = $prefix . ' ';
        $msg .= batchaction_prefix($myCourse, $prefix, false) . "<br />\n";
        //substituer le role enseignant à "enseignant EPI archivé"
        if (count($rolesTeacher) == 2) {
            $msg .= batchaction_substitute($myCourse, $rolesTeacher['from'], $rolesTeacher['to'], false) . "<br />\n";
        } else {
            $msg .= 'Attention, la substitution "enseignant éditeur" par "enseignant EPI archivé" n\'a pas eu lieu' . "<br />\n";
        }
        //archiver à la date
        $tsdate = isoDateToTs($isodate);
        $msg .= batchaction_archdate($myCourse, $tsdate, false) . "<br />\n";
        //substituer le role étudiant à "étudiant archivé"
        if (count($rolesStudent) == 2) {
            $msg .= batchaction_substitute($myCourse, $rolesStudent['from'], $rolesStudent['to'], false) . "<br />\n";
        } else {
            $msg .= 'Attention, la substitution "étudiant" par "étudiant archivé" n\'a pas eu lieu' . "<br />\n";
        }

        echo $header;
        echo $title;
        echo html_writer::tag('div', $msg, array('class' => 'fitem'));
        echo html_writer::tag('div', $linkcourse, array('class'=>'list'));

    } else {
        echo $header;
        echo $title;
        echo html_writer::tag('div', 'Le cours n\'a pas été archivé.', array('class'=>'list'));
        echo html_writer::tag('div', $linkcourse, array('class'=>'list'));
    }
} else {
    echo $header;
    echo $title;
    echo $msgEffect;
    $mform->display();
}

echo $OUTPUT->footer();
