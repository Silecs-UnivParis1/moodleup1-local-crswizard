<?php
/**
 * @package    local_crswizard
 * @copyright  2012-2026 Silecs {@link http://www.silecs.info/societe}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class course_wizard_step_syllabus_form extends moodleform {
    function definition() {
        global $SESSION, $USER;

        $mform = $this->_form;
        $editoroptions = $this->_customdata['editoroptions'];
        $roffreeze = $this->_customdata['roffreeze'];
        $rofupmsg = $this->_customdata['rofupmsg'];

        $mform->addElement('header', 'etape2', 'Champs déduit de l\'étape identification de l\'espace');

        $mform->addElement('text', 'syl_elpcode', get_string('code_apogee', 'local_crswizard'), 'maxlength="20" size="20" class="crswizard-form-align"');
        $mform->setType('syl_elpcode', PARAM_TEXT);

        $mform->addElement('text', 'syl_elpintitule', get_string('intitulematiere', 'local_crswizard'), 'maxlength="200" size="50" class="crswizard-form-align"');
        $mform->setType('syl_elpintitule', PARAM_TEXT);
        if (array_key_exists('syl_elpintitule', $rofupmsg)) {
            $defaultvalue = $rofupmsg['syl_elpintitule'];
            $html = '<div id="info_syl_elpintitule" class="sylrofinfo" style="padding-left:200px;">Attention, la valeur par défaut est : ' . $defaultvalue . '</div>';
            $mform->addElement('html', $html);
        }

        $mform->addElement('advcheckbox', 'syl_obligatoire', get_string('required_label', 'local_crswizard'),
            get_string('required', 'local_crswizard'), ['class' => 'crswizard-form-align']);
        if (array_key_exists('syl_obligatoire', $rofupmsg)) {
            $defaultvalue = $rofupmsg['syl_obligatoire'];
            $html = '<div id="info_syl_obligatoire" class="sylrofinfo" style="padding-left:200px;">Attention, la valeur par défaut est : ' . $defaultvalue . '</div>';
            $mform->addElement('html', $html);
        }

        $mform->addElement('float', 'syl_ects', get_string('numbects', 'local_crswizard'), 'maxlength="5" size="5" class="crswizard-form-align"');
        $mform->setType('syl_ects', PARAM_TEXT);
        if (array_key_exists('syl_ects', $rofupmsg)) {
            $defaultvalue = $rofupmsg['syl_ects'] ? $rofupmsg['syl_ects'] : 0;
            $html = '<div id="info_syl_ects" class="sylrofinfo" style="padding-left:200px;">Attention, la valeur par défaut est : ' . $defaultvalue . '</div>';
            $mform->addElement('html', $html);
        }

        $mform->addElement('text', 'syl_volumecm', get_string('durationcm', 'local_crswizard'), 'maxlength="5" size="5" class="crswizard-form-align"');
        $mform->setType('syl_volumecm', PARAM_TEXT);
        if (array_key_exists('syl_volumecm', $rofupmsg)) {
            $defaultvalue = $rofupmsg['syl_volumecm'] ? $rofupmsg['syl_volumecm'] : 0;
            $html = '<div id="info_syl_volumecm" class="sylrofinfo" style="padding-left:200px;">Attention, la valeur par défaut est : ' . $defaultvalue . '</div>';
            $mform->addElement('html', $html);
        }

        $mform->addElement('text', 'syl_volumetd', get_string('durationtd', 'local_crswizard'), 'maxlength="5" size="5" class="crswizard-form-align"');
        $mform->setType('syl_volumetd', PARAM_TEXT);
        if (array_key_exists('syl_volumetd', $rofupmsg)) {
            $defaultvalue = $rofupmsg['syl_volumetd'] ? $rofupmsg['syl_volumetd'] : 0;
            $html = '<div id="info_syl_volumetd" class="sylrofinfo" style="padding-left:200px;">Attention, la valeur par défaut est : ' . $defaultvalue . '</div>';
            $mform->addElement('html', $html);
        }

        $mform->addElement('editor', 'summary_editor', get_string('coursesummary', 'local_crswizard'), ['class' => 'crswizard-form-align'], $editoroptions);
        $mform->setType('summary_editor', PARAM_RAW);

        $mform->addElement('header', 'pedagogie', 'Pédagogies');
        $mform->setExpanded('pedagogie');

        $mform->addElement('advcheckbox', 'syl_reference', get_string('referencesyllabus_label', 'local_crswizard'),
            get_string('referencesyllabus', 'local_crswizard'), ['class' => 'crswizard-form-align']);

        $mform->addElement('editor', 'syl_objectifs', get_string('outcomes_pedagogic', 'local_crswizard'), ['class' => 'crswizard-form-align'], $editoroptions);
        $mform->setType('syl_objectifs', PARAM_RAW);

        $mform->addElement('editor', 'syl_plan', get_string('courseplan', 'local_crswizard'), ['class' => 'crswizard-form-align'], $editoroptions);
        $mform->setType('syl_plan', PARAM_RAW);

        $mform->addElement('editor', 'syl_prerequis', get_string('requirement', 'local_crswizard'), ['class' => 'crswizard-form-align'], $editoroptions);
        $mform->setType('syl_prerequis', PARAM_RAW);

        $mform->addElement('editor', 'syl_evaluation', get_string('assessmentsettings', 'local_crswizard'), ['class' => 'crswizard-form-align'], $editoroptions);
        $mform->setType('syl_evaluation', PARAM_RAW);

        $mform->addElement('editor', 'syl_bibliographie', get_string('bibliography', 'local_crswizard'), ['class' => 'crswizard-form-align'], $editoroptions);
        $mform->setType('syl_bibliographie', PARAM_RAW);

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
/**
    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        if (!empty($data['syl_reference'])) {
            // Ce statut doit être unique par code Apogee et par année universitaire 
            //$errors['syl_reference'] = 'Il existe déjà un cours présentant le Syllabus de référence pour cette matière cette annéee';
        }
        return $errors;
    }
    **/
}
