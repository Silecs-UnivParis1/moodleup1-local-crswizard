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

        $mform->addElement('header', 'etape2', 'Champs déduit de l\'étape identification de l\'espace');
        
        $mform->addElement('text', 'syl_elpcode', 'Code APOGEE', 'maxlength="20" size="20" class="syllabus-align"');
        $mform->setType('syl_elpcode', PARAM_TEXT);
        $mform->hardFreeze('syl_elpcode');
        
        $mform->addElement('text', 'syl_elpintitule', 'Intitulé matière', 'maxlength="200" size="50" class="syllabus-align"');
        $mform->setType('syl_elpintitule', PARAM_TEXT);
        $mform->hardFreeze('syl_elpintitule');
        
        $mform->addElement('advcheckbox', 'syl_obligatoire', 'Type : obligatoire / optionnel', 'Obligatoire', ['class' => 'syllabus-align']);
        //$mform->hardFreeze('syl_obligatoire');
        
        $mform->addElement('text', 'syl_ects', 'Nombre d\'ECTS', 'maxlength="50" size="50" class="syllabus-align"');
        $mform->setType('syl_ects', PARAM_TEXT);
        
        $mform->addElement('text', 'syl_volume', 'Volume horaire', 'maxlength="50" size="50" class="syllabus-align"');
        $mform->setType('syl_volume', PARAM_TEXT);

        $mform->addElement('editor', 'summary_editor', get_string('coursesummary', 'local_crswizard'), null, $editoroptions);
        $mform->setType('summary_editor', PARAM_RAW);

        $mform->addElement('header', 'pedagogie', 'Pédagogies');
        $mform->setExpanded('pedagogie');
        
        $mform->addElement('advcheckbox', 'syl_reference', 'Syllabus de référence pour cette matière', 'Syllabus de référence', ['class' => 'syllabus-align']);

        $mform->addElement('editor', 'syl_objectifs', 'Objectifs pédagogiques', ['class' => 'syllabus-align'], $editoroptions);
        $mform->setType('syl_objectifs', PARAM_RAW);
        
        $mform->addElement('editor', 'syl_plan', 'Plan du cours', ['class' => 'syllabus-align'], $editoroptions);
        $mform->setType('syl_plan', PARAM_RAW);
        
        $mform->addElement('editor', 'syl_prerequis', 'Prérequis', ['class' => 'syllabus-align'], $editoroptions);
        $mform->setType('syl_prerequis', PARAM_RAW);
        
        $mform->addElement('editor', 'syl_evaluation', 'Modalités d\'évaluation', ['class' => 'syllabus-align'], $editoroptions);
        $mform->setType('syl_evaluation', PARAM_RAW);
        
        $mform->addElement('editor', 'syl_bibliographie', 'Bibliographie', ['class' => 'syllabus-align'], $editoroptions);
        $mform->setType('syl_bibliographie', PARAM_RAW);

        $mform->addElement('header', 'etape4', 'Champs déduit de l\'étape désignation des contributeurs enseignants');
        $mform->setExpanded('etape4');
        $mform->addElement('textarea', 'syl_contacts', 'Contact(s) responsable(s) epi', ['class' => 'syllabus-align', 'rows' => 8]);
        $mform->setType('syl_contacts', PARAM_RAW);
        
        $mform->addElement('header', 'responsable', 'Responsable(s) du ou des diplômes concernés');
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

        $buttonarray[] = $mform->createElement(
            'link', 'previousstage', null,
            new moodle_url($SESSION->wizard['wizardurl'], array('stepin' => 4)),
            get_string('previousstage', 'local_crswizard'), array('class' => 'previousstage'));
        $buttonarray[] = $mform->createElement(
                'submit', 'stepgo_5', get_string('nextstage', 'local_crswizard'));
        $mform->addGroup($buttonarray, 'buttonar', '', null, false);
        $mform->closeHeaderBefore('buttonar');
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
