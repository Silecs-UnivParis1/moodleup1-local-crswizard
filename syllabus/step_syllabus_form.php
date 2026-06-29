<?php
/**
 * @package    local_crswizard
 * @copyright  2012-2026 Silecs {@link http://www.silecs.info/societe}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class course_wizard_step_syllabus_form extends moodleform {
    function definition() {
        global $SESSION, $USER, $OUTPUT;

        $mform = $this->_form;
        $editoroptions = $this->_customdata['editoroptions'];
        $roffreeze = $this->_customdata['roffreeze'];
        $syllabus_ref = $this->_customdata['syllabus_ref'];

        $mform->addElement('header', 'etape2', 'Champs déduit de l\'étape identification de l\'espace');

        $mform->addElement('text', 'syl_elpcode', get_string('code_apogee', 'local_crswizard'), 'maxlength="20" size="20" class="crswizard-form-align"');
        $mform->setType('syl_elpcode', PARAM_TEXT);

        $mform->addElement('text', 'syl_elpintitule', get_string('intitulematiere', 'local_crswizard'), 'maxlength="200" size="50" class="crswizard-form-align"');
        $mform->setType('syl_elpintitule', PARAM_TEXT);

        $mform->addElement('float', 'syl_ects', get_string('numbects', 'local_crswizard'), 'maxlength="5" size="5" class="crswizard-form-align"');
        $mform->setType('syl_ects', PARAM_TEXT);

        $mform->addElement('text', 'syl_volumecm', get_string('durationcm', 'local_crswizard'), 'maxlength="5" size="5" class="crswizard-form-align"');
        $mform->setType('syl_volumecm', PARAM_TEXT);

        $mform->addElement('text', 'syl_volumetd', get_string('durationtd', 'local_crswizard'), 'maxlength="5" size="5" class="crswizard-form-align"');
        $mform->setType('syl_volumetd', PARAM_TEXT);

        $mform->addElement('editor', 'summary_editor', get_string('coursesummary', 'local_crswizard'), ['class' => 'crswizard-form-align'], $editoroptions);
        $mform->setType('summary_editor', PARAM_RAW);

        $mform->addElement('header', 'pedagogie', 'Pédagogies');
        $mform->setExpanded('pedagogie');

        $mform->addElement('advcheckbox', 'syl_reference', get_string('referencesyllabus_label', 'local_crswizard'),
            get_string('referencesyllabus', 'local_crswizard'), ['class' => 'crswizard-form-align']);
        $referencesyllabus_definition = html_writer::div(get_string('referencesyllabus_definition', 'local_crswizard'), 'referencesyllabushelp');
        $mform->addElement('html',  $referencesyllabus_definition);

        if ($syllabus_ref) {
            $html = get_string('referencesyllabus_existe', 'local_crswizard', $syllabus_ref['syllabus_ref_id']);
            if (isset($syllabus_ref['modele_reference'])) {
                $html .= get_string('referencesyllabus_msg_duplication', 'local_crswizard');
            }
            $button = $OUTPUT->action_link($syllabus_ref['url_syllabus'], '<i class="fas fa-s"></i>', null,
                ['title' => 'Afficher le syllabus de référence', 'class' => 'action-icon action-icon-referencesyllabus', 'target' => '_blank']
            );
            $html .= html_writer::span($button, 'syllabus-icon');
            $mform->addElement('html',  html_writer::div($html, 'referencesyllabusinfo'));
        }
        $mform->addElement('editor', 'syl_objectifs', get_string('outcomes_pedagogic', 'local_crswizard'), ['class' => 'crswizard-form-align'], $editoroptions);
        $mform->setType('syl_objectifs', PARAM_RAW);
        if ($syllabus_ref) {
            $mform->addElement('editor', 'syl_objectifs_ref', get_string('outcomes_pedagogic_ref', 'local_crswizard'), ['class' => 'crswizard-form-align'], $editoroptions);
            $mform->setType('syl_objectifs_ref', PARAM_RAW);
            $mform->addElement('advcheckbox', 'syl_objectifs_ref_use', '', get_string('outcomes_pedagogic_ref_use', 'local_crswizard'), ['class' => 'crswizard-form-align']);
            $mform->hideIf('syl_objectifs', 'syl_objectifs_ref_use', 'checked');
            $mform->hideIf('syl_objectifs_ref', 'syl_objectifs_ref_use', 'notchecked');
        }

        $mform->addElement('editor', 'syl_plan', get_string('courseplan', 'local_crswizard'), ['class' => 'crswizard-form-align'], $editoroptions);
        $mform->setType('syl_plan', PARAM_RAW);
        if ($syllabus_ref) {
            $mform->addElement('editor', 'syl_plan_ref', get_string('courseplan_ref', 'local_crswizard'), ['class' => 'crswizard-form-align'], $editoroptions);
            $mform->setType('syl_plan_ref', PARAM_RAW);
            $mform->addElement('advcheckbox', 'syl_plan_ref_use', '', get_string('courseplan_ref_use', 'local_crswizard'), ['class' => 'crswizard-form-align']);
            $mform->hideIf('syl_plan', 'syl_plan_ref_use', 'checked');
            $mform->hideIf('syl_plan_ref', 'syl_plan_ref_use', 'notchecked');
        }

        $mform->addElement('editor', 'syl_prerequis', get_string('requirement', 'local_crswizard'), ['class' => 'crswizard-form-align'], $editoroptions);
        $mform->setType('syl_prerequis', PARAM_RAW);
        if ($syllabus_ref) {
            $mform->addElement('editor', 'syl_prerequis_ref', get_string('requirement_ref', 'local_crswizard'), ['class' => 'crswizard-form-align'], $editoroptions);
            $mform->setType('syl_prerequis_ref', PARAM_RAW);
            $mform->addElement('advcheckbox', 'syl_prerequis_ref_use', '', get_string('requirement_ref_use', 'local_crswizard'), ['class' => 'crswizard-form-align']);
            $mform->hideIf('syl_prerequis', 'syl_prerequis_ref_use', 'checked');
            $mform->hideIf('syl_prerequis_ref', 'syl_prerequis_ref_use', 'notchecked');
        }

        $mform->addElement('editor', 'syl_evaluation', get_string('assessmentsettings', 'local_crswizard'), ['class' => 'crswizard-form-align'], $editoroptions);
        $mform->setType('syl_evaluation', PARAM_RAW);
        if ($syllabus_ref) {
            $mform->addElement('editor', 'syl_evaluation_ref', get_string('assessmentsettings_ref', 'local_crswizard'), ['class' => 'crswizard-form-align'], $editoroptions);
            $mform->setType('syl_evaluation_ref', PARAM_RAW);
            $mform->addElement('advcheckbox', 'syl_evaluation_ref_use', '', get_string('assessmentsettings_ref_use', 'local_crswizard'), ['class' => 'crswizard-form-align']);
            $mform->hideIf('syl_evaluation', 'syl_evaluation_ref_use', 'checked');
            $mform->hideIf('syl_evaluation_ref', 'syl_evaluation_ref_use', 'notchecked');
        }

        $mform->addElement('editor', 'syl_bibliographie', get_string('bibliography', 'local_crswizard'), ['class' => 'crswizard-form-align'], $editoroptions);
        $mform->setType('syl_bibliographie', PARAM_RAW);
        if ($syllabus_ref) {
            $mform->addElement('editor', 'syl_bibliographie_ref', get_string('bibliography_ref', 'local_crswizard'), ['class' => 'crswizard-form-align'], $editoroptions);
            $mform->setType('syl_bibliographie_ref', PARAM_RAW);
            $mform->addElement('advcheckbox', 'syl_bibliographie_ref_use', '', get_string('bibliography_ref_use', 'local_crswizard'), ['class' => 'crswizard-form-align']);
            $mform->hideIf('syl_bibliographie', 'syl_bibliographie_ref_use', 'checked');
            $mform->hideIf('syl_bibliographie_ref', 'syl_bibliographie_ref_use', 'notchecked');
        }

        $mform->addElement('header', 'etape4', 'Champs déduit de l\'étape désignation des contributeurs enseignants');
        $mform->setExpanded('etape4');
        $mform->addElement('textarea', 'syl_contacts', get_string('contact_responsable_epi', 'local_crswizard'), ['class' => 'crswizard-form-align', 'rows' => 8]);
        $mform->setType('syl_contacts', PARAM_RAW);

        $mform->addElement('header', 'responsable', get_string('responsable_diplome', 'local_crswizard'));
        $mform->setExpanded('responsable');
        $htmlSelectedResponsables = '<div class="fcontainer clearfix"><div id="user-select">'
            . '<div class="widgetselect-panel-left">'
                . '<h3>Chercher un Responsable</h3>'
                . '<input type="text" class="user-selector" name="something" data-inputname="teacher" size="50" placeholder="Nom du responsable" />'
            . '</div>'
            . '<div class="widgetselect-panel-right">'
                . '<h3>Responsable(s) sélectionné(s)</h3>'
                . '<div class="users-selected"></div>'
            . '</div>'
        . '</div></div>';
        $mform->addElement('html',  $htmlSelectedResponsables);

        $preselected = wizard_preselected_responsable();
        $codeJ = '<script type="text/javascript">' . "\n"
                    . '//<![CDATA['."\n"
                    . 'jQuery(document).ready(function () {'
                    . '$(\'#user-select\').autocompleteUser({'
                    . "urlUsers: '../../mwsgroups/service-users.php',"
                    . "labelDetails: 'responsable',"
                    . "fieldName: 'syl_responsables',"
                    . 'wsParams: { affiliation: 1, maxRows: 50 },'
                    . 'preSelected: '. $preselected . ','
                    . '});'
                    . '});'
                    . '//]]>' . "\n"
                    . '</script>';
        $mform->addElement('html', $codeJ);
        $tepin = 4;
        if (isset($SESSION->wizard['idcourse'])) {
            $tepin = $SESSION->wizard['wizardcase'] == 3 ? 3 : 2;
        }
        $buttonarray[] = $mform->createElement(
            'link', 'previousstage', null,
            new moodle_url($SESSION->wizard['wizardurl'], array('stepin' => $tepin)),
            get_string('previousstage', 'local_crswizard'), array('class' => 'previousstage'));
        $buttonarray[] = $mform->createElement(
                'submit', 'stepgo_5', get_string('nextstage', 'local_crswizard'));
        $mform->addGroup($buttonarray, 'buttonar', '', null, false);
        $mform->closeHeaderBefore('buttonar');

        $mform->hardFreeze($roffreeze);
    }
}
