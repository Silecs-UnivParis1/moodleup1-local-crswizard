<?php

/**
 * @package    local_crswizard
 * @copyright  2012-2021 Silecs {@link http://www.silecs.info/societe}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once($CFG->libdir . '/formslib.php');
require_once('lib_wizard.php');

class course_wizard_step_confirm extends moodleform {

    function definition() {
        global $USER, $DB, $SESSION, $CFG;

        $myconfig = new my_elements_config();

        $mform = $this->_form;
        $mgConf1 = get_string('bockhelpE7p1', 'local_crswizard');
        $mform->addElement('html', html_writer::tag('div',
            $mgConf1, array('class' => 'fitem', 'id' => 'bockhelpE7')));

        $mform->addElement('header', 'resume', get_string('summaryof', 'local_crswizard'));
        $user_name = fullname($USER);
        $mform->addElement('text', 'user_name', get_string('username', 'local_crswizard'), 'size="30" class="crswizard-form-align"');
        $mform->setType('user_name', PARAM_TEXT);
        $mform->setConstant('user_name', $user_name);
        $mform->addElement('date_selector', 'requestdate', get_string('courserequestdate', 'local_crswizard'), null, ['class' => 'crswizard-form-align']);
        $mform->setDefault('requestdate', time());

        $displaylist = array();
        $displaylist = core_course_category::make_categories_list();
        if (isset($SESSION->wizard['form_step2']['rattachement1']) ) {
            $idratt1 = $SESSION->wizard['form_step2']['rattachement1'];
            $mform->addElement('text', 'category',  get_string('categoryblockE3', 'local_crswizard') . ' : ', 'size="100" class="crswizard-form-align"');
            $mform->setType('category', PARAM_TEXT);
            $mform->setConstant('category' , $displaylist[$idratt1] . ' / ' . $SESSION->wizard['form_step2']['fullname']);
        } else {
            $mform->addElement('select', 'category', get_string('categoryblockE3', 'local_crswizard') . ' : ', $displaylist, ['class' => 'crswizard-form-align']);
        }

        if (!empty($SESSION->wizard['form_step3']['rattachements'])) {
            $paths = wizard_get_myComposantelist($SESSION->wizard['form_step2']['category'], true);
            $nbr = 1;
            $rattachement3 = '';
            foreach ($SESSION->wizard['form_step3']['rattachements'] as $pathid) {
                if ($pathid != '') {
                    $rattachement3 .= $paths[$pathid] ."\n";
                    ++$nbr;
                }
            }
            $mform->addElement('textarea', 'rattachement3', get_string('labelE7ratt2', 'local_crswizard'), ['class' => 'crswizard-form-align', 'rows' => $nbr]);
            $mform->setType('rattachement3', PARAM_RAW);
            $mform->setConstant('rattachement3', $rattachement3);
        }

        // ajout métadonnée supp. indexation pour cas3
        if ($SESSION->wizard['wizardcase'] == 3) {
            $metadonnees = get_array_metadonees();
            foreach ($metadonnees as $key => $label) {
                if (!empty($SESSION->wizard['form_step3'][$key])) {
                    $donnees = '';
                    foreach ($SESSION->wizard['form_step3'][$key] as $elem) {
                        $donnees = $donnees . $elem . ';';
                    }
                    $donnees = substr($donnees, 0, -1);
                    if ($donnees != '') {
                        $mform->addElement('text', $key, $label, 'size="30" class="crswizard-form-align"');
                        $mform->setType($key, PARAM_TEXT);
                        $mform->setConstant($key , $donnees);
                    }
                }
            }
        }

        // rattachement secondaire - cas 2 + hybride
        if (isset($SESSION->wizard['form_step2']['rattachement2'])) {
            $rof2 = $SESSION->wizard['form_step2']['rattachement2'];
            if(count($rof2)) {
                $racine = '';
                if ($SESSION->wizard['wizardcase'] == 2) {
                    $racine = $displaylist[$SESSION->wizard['form_step2']['category']];
                } elseif ($SESSION->wizard['wizardcase'] == 3) {
                    $tabcategories = get_list_category($SESSION->wizard['form_step2']['category']);
                    $racine = $tabcategories[0] . ' / ' . $tabcategories[1];
                }
                $rattachement2 = '';
                foreach ($rof2 as $chemin) {
                    $rattachement2 .= $racine . ' / ' . $chemin . "\n";
                }
                $nbl = count($rof2) + 1;
                $mform->addElement('textarea', 'rattachement2', get_string('labelE7ratt2', 'local_crswizard'), ['class' => 'crswizard-form-align', 'rows' => $nbl]);
                $mform->setType('rattachement2', PARAM_RAW);
                $mform->setConstant('rattachement2', $rattachement2);
            }
        }

        $mform->addElement('text', 'fullname', get_string('fullnamecourse', 'local_crswizard'), 'size="60" class="crswizard-form-align"');
        $mform->setType('fullname', PARAM_TEXT);

        $mform->addElement('text', 'shortname', get_string('shortnamecourse', 'local_crswizard'), 'size="40" class="crswizard-form-align"');
        $mform->setType('shortname', PARAM_TEXT);

        $editoroptions = ['maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes' => $CFG->maxbytes, 'trusttext' => false, 'noclean' => true];
        $mform->addElement('editor', 'summary_editor', get_string('coursesummary', 'local_crswizard'), ['class' => 'crswizard-form-align'], $editoroptions);
        $mform->setType('summary_editor', PARAM_RAW);
        $mform->setConstant('summary_editor', $SESSION->wizard['form_step2']['summary_editor']);

        $imagecours = wizard_get_course_overviewfiles_filemanager_image($SESSION->wizard['form_step2']['overviewfiles_filemanager']);
        $mform->addElement('static', 'imagecours', get_string('courseoverviewfiles', 'local_crswizard'), $imagecours);

        $mform->addElement('date_selector', 'startdate', get_string('coursestartdate', 'local_crswizard'), null, ['class' => 'crswizard-form-align']);

        $mform->addElement('date_selector', 'enddate', get_string('courseenddate', 'local_crswizard'), null, ['class' => 'crswizard-form-align']);

        $mform->addElement('text', 'langue', get_string('courselanguage', 'local_crswizard') . ' : ', 'size="40" class="crswizard-form-align"');
        $mform->setType('langue', PARAM_TEXT);

        if (!empty($SESSION->wizard['form_step1']['coursedmodelid']) && $SESSION->wizard['form_step1']['coursedmodelid'] != '0') {
            $mform->addElement('text', 'coursemodel', get_string('coursemodel', 'local_crswizard'), 'size="60" class="crswizard-form-align"');
            $mform->setType('coursemodel', PARAM_TEXT);
            $mform->setConstant('coursemodel' , '[' . $SESSION->wizard['form_step1']['coursemodelshortname']
                . ']' . $SESSION->wizard['form_step1']['coursemodelfullname']
            );
        }

        $mform->addElement('text', 'profile_field_up1generateur', "Mode de création :", 'size="40" class="crswizard-form-align"');
        $mform->setType('profile_field_up1generateur', PARAM_TEXT);

        if (isset($SESSION->wizard['form_step2']['urlok']) && $SESSION->wizard['form_step2']['urlok'] == 1) {
            $mform->addElement('text', 'urlfixetotal', "URL pérenne :", 'size="60" class="crswizard-form-align"');
            $mform->setType('urlfixetotal', PARAM_TEXT);
            $urltotal = $SESSION->wizard['urlpfixe'];
            if (isset($SESSION->wizard['form_step2']['urlmodel']) && $SESSION->wizard['form_step2']['urlmodel'] == 'fixe') {
                     $urltotal .= trim($SESSION->wizard['form_step2']['modelurlfixe']);
            } else {
                $urltotal .= trim($SESSION->wizard['form_step2']['myurl']);
            }
            $mform->setConstant('urlfixetotal' , $urltotal);

            if (isset($SESSION->wizard['form_step2']['urlmodel']) &&  $SESSION->wizard['form_step2']['urlmodel'] == 'fixe') {
                $html = '<div>Attention, l\'url pérenne de l\'EPI modèle sera transférée à ce nouvel EPI.</div>';
                $mform->addElement('html', $html);
            }
        }

        // validateur pour le cas 2
        if (!empty($SESSION->wizard['form_step3']['all-validators'])) {
            $allvalidators = $SESSION->wizard['form_step3']['all-validators'];
            $mform->addElement('header', 'validators', get_string('selectedvalidator', 'local_crswizard'));
            foreach ($allvalidators as $id => $validator) {
                $mform->addElement('text', 'validator', '', 'class="crswizard-form-align"');
                $mform->setType('validator', PARAM_TEXT);
                $mform->setConstant('validator' , fullname($validator));
            }
        } elseif (!empty($SESSION->wizard['form_step3']['autovalidation'])) {
            $mform->addElement('header', 'validators', get_string('selectedvalidator', 'local_crswizard'));
            $mform->addElement('text', 'validator', 'Autovalidation', 'size="60" class="crswizard-form-align"');
            $mform->setType('validator', PARAM_TEXT);
            $mform->setConstant('validator' , 'Je suis responsable de cet enseignement');
        }

        if (isset($SESSION->wizard['form_step4']['all-users']) && is_array($SESSION->wizard['form_step4']['all-users'])) {
            $allusers = $SESSION->wizard['form_step4']['all-users'];
            $mform->addElement('header', 'teachers', get_string('teachers', 'local_crswizard'));
            $labels = $myconfig->role_teachers;
            foreach ($allusers as $role => $users) {
                $label = $role;
                if (isset($labels[$role])) {
                    $label = get_string($labels[$role], 'local_crswizard');
                }
                $htmllabel = html_writer::label($label, '', true, ['class' => 'd-inline word-break ']);
                $htmldivlabel = html_writer::div($htmllabel, 'col-md-3 col-form-label d-flex pb-0 pe-md-0', ['style' => 'padding-top: 0px ']);
                $teachers = [];
                foreach ($users as $id => $user) {
                    $teachers[] = fullname($user);
                }
                $htmldivcontenu = html_writer::div(implode(", <br/>", $teachers), 'col-md-9 d-flex flex-wrap align-items-start felement');
                $mform->addElement('html', html_writer::div($htmldivlabel . $htmldivcontenu, 'mb-3 row  fitem   crswizard-form-align'));
            }
        }

        if (isset($SESSION->wizard['form_step4']['use_syllabus_step']) && $SESSION->wizard['form_step4']['use_syllabus_step'] == 1) {
            $mform->addElement('header', 'syllabus', 'Syllabus');
            $mform->addElement('text', 'profile_field_syl_elpcode', get_string('code_apogee', 'local_crswizard'), 'size="20" class="crswizard-form-align"');
            $mform->setType('profile_field_syl_elpcode', PARAM_TEXT);
            $mform->addElement('text', 'profile_field_syl_elpintitule', get_string('intitulematiere', 'local_crswizard'), 'size="50" class="crswizard-form-align"');
            $mform->setType('profile_field_syl_elpintitule', PARAM_TEXT);
            $mform->addElement('advcheckbox', 'profile_field_syl_obligatoire', get_string('required_label', 'local_crswizard'),
                get_string('required', 'local_crswizard'), ['class' => 'crswizard-form-align']);
            $mform->addElement('text', 'profile_field_syl_ects', get_string('numbects', 'local_crswizard'), 'size="50" class="crswizard-form-align"');
            $mform->setType('profile_field_syl_ects', PARAM_TEXT);
            $mform->addElement('text', 'profile_field_syl_volume', get_string('duration', 'local_crswizard'), 'size="50" class="crswizard-form-align"');
            $mform->setType('profile_field_syl_volume', PARAM_TEXT);
            $mform->addElement('advcheckbox', 'profile_field_syl_reference', get_string('referencesyllabus_label', 'local_crswizard'),
                get_string('referencesyllabus', 'local_crswizard'), ['class' => 'crswizard-form-align']);

            $mform->addElement('editor', 'profile_field_syl_objectifs', get_string('outcomes_pedagogic', 'local_crswizard'), ['class' => 'crswizard-form-align'], $editoroptions);
            $mform->setType('profile_field_syl_objectifs', PARAM_RAW);
            $mform->setConstant('profile_field_syl_objectifs', $SESSION->wizard['form_step45']['syl_objectifs']);

            $mform->addElement('editor', 'profile_field_syl_plan', get_string('courseplan', 'local_crswizard'), ['class' => 'crswizard-form-align']);
            $mform->setType('profile_field_syl_plan', PARAM_RAW);
            $mform->setConstant('profile_field_syl_plan', $SESSION->wizard['form_step45']['syl_plan']);

            $mform->addElement('editor', 'profile_field_syl_prerequis', get_string('requirement', 'local_crswizard'), ['class' => 'crswizard-form-align']);
            $mform->setType('profile_field_syl_prerequis', PARAM_RAW);
            $mform->setConstant('profile_field_syl_prerequis', $SESSION->wizard['form_step45']['syl_prerequis']);

            $mform->addElement('editor', 'profile_field_syl_evaluation', get_string('assessmentsettings', 'local_crswizard'), ['class' => 'crswizard-form-align']);
            $mform->setType('profile_field_syl_evaluation', PARAM_RAW);
            $mform->setConstant('profile_field_syl_evaluation', $SESSION->wizard['form_step45']['syl_evaluation']);

            $mform->addElement('editor', 'profile_field_syl_bibliographie', get_string('bibliography', 'local_crswizard'), ['class' => 'crswizard-form-align']);
            $mform->setType('profile_field_syl_bibliographie', PARAM_RAW);
            $mform->setConstant('profile_field_syl_bibliographie', $SESSION->wizard['form_step45']['syl_bibliographie']);

            $mform->addElement('textarea', 'profile_field_syl_contacts', get_string('contact_responsable_epi', 'local_crswizard'), ['class' => 'crswizard-form-align', 'rows' => 8]);
            $mform->setType('profile_field_syl_contacts', PARAM_RAW);

            if (isset($SESSION->wizard['form_step45']['all-responsables'])) {
                $allresponsables = $SESSION->wizard['form_step45']['all-responsables'];
                $nbresp = is_array($allresponsables) ? count($allresponsables) : 0;
                if ($nbresp > 1) {
                    $mform->addElement('textarea', 'reponsable_dipl', get_string('responsable_diplome', 'local_crswizard'), ['class' => 'crswizard-form-align', 'rows' => $nbresp]);
                    $mform->setType('reponsable_dipl', PARAM_RAW);
                    $responsables = '';
                    foreach ($allresponsables as $resp) {
                        $responsables .= fullname($resp) . ' : ' . $resp->email . "\n";
                        $mform->setConstant('reponsable_dipl', $responsables);
                    }
                } else {
                    $mform->addElement('text', 'reponsable_dipl', 'Responsable(s) du ou des diplômes concernés', 'size="40" class="crswizard-form-align"');
                    $mform->setType('reponsable_dipl', PARAM_TEXT);
                    $responsable = 'Aucun';
                    if ($nbresp == 1) {
                        $resp = current($allresponsables);
                        $responsable = fullname($resp) . ' : ' . $resp->email;
                    }
                    $mform->setConstant('reponsable_dipl', $responsable);
                }
            }
        }

        if (!empty($SESSION->wizard['form_step5']['all-cohorts'])) {
            $groupsbyrole = $SESSION->wizard['form_step5']['all-cohorts'];
            $mform->addElement('header', 'groups', get_string('cohorts', 'local_crswizard'));
            $labels = $myconfig->role_cohort;
            foreach ($groupsbyrole as $role => $groups) {
                $label = $role;
                if (isset($labels[$role])) {
                    $label = get_string($labels[$role], 'local_crswizard');
                }
                $first = true;
                foreach ($groups as $id => $group) {
                    $mform->addElement('text', 'cohort' . $id, ($first ? $label . ' : ' : ''), 'size="100" class="crswizard-form-align"');
                    $mform->setType('cohort' . $id, PARAM_TEXT);
                    $mform->setConstant('cohort' . $id, $group->name . ' — ' . "{$group->size} inscrits");
                    $first = false;
                }
            }
        }

        /** @todo Do not set the values here, share the code that parses the forms data */
        if (isset($SESSION->wizard['form_step6'])) {
            $form6 = $SESSION->wizard['form_step6'];
            $clefs = wizard_list_clef($form6);
            if (count($clefs)) {
                $mform->addElement('header', 'clefs', get_string('enrolkey', 'local_crswizard'));
                foreach ($clefs as $type => $clef) {
                    $mform->addElement('html', html_writer::tag('h4', $type . ' : '));
                    $c = $clef['code'];
                    if ($clef['password'] == '') {
                        // accès libre
                        $html = '<div class="fitem"><div class="fitemtitle">'
                            . '<div class="fstaticlabel crswizard-mylabel"><label>'
                            . 'Accès libre</label></div></div>'
                            . '<div class="felement fstatic"></div></div>';
                        $mform->addElement('html', $html);
                    } else {
                        $mform->addElement('text', 'valeur' . $c, get_string('enrolkey', 'local_crswizard') . ' : ', 'class="crswizard-form-align"');
                        $mform->setType('valeur' . $c, PARAM_TEXT);
                        $mform->setConstant('valeur' . $c, $clef['password']);
                    }
                }
            }
        }

        //--------------------------------------------------------------------------------
        // normalement, ce code ne sert à rien
        if (isset($SESSION->wizard['idcourse'])) {
            $idcourse = (int) $SESSION->wizard['idcourse'];
            $fieldstab = $DB->get_records_menu('customfield_field', [], '', 'id, shortname');
			$handler = \core_customfield\handler::get_handler('core_course', 'course');    
			$datas = $handler->get_instance_data($idcourse);
			$cinfos = [];
			foreach ($datas as $data) {
				$cinfos[$data->get_field()->get('shortname')] = $data->get_value();
			}
            foreach ($cinfos as $label => $info) {
                $htmlinfo = '<div class="fitemtitle"><div class="fstaticlabel"><label>'
                        . $label . '</label></div></div>'
                        . '<div class="felement fstatic">' . $info . '</div>';
                $mform->addElement('html', html_writer::tag('div', $htmlinfo, array('class' => 'fitem')));
            }
        }
//--------------------------------------------------------------------------------
        $mform->addElement('header', 'confirmation', get_string('confirmation', 'local_crswizard'));
        $mform->addElement('textarea', 'remarques', null, array('rows' => 15, 'cols' => 80, 'class' => 'crswizard-form-align',
            'placeholder' => get_string('consigneremarque', 'local_crswizard')));
        $mform->setType('remarques', PARAM_TEXT);

        $mform->addElement('hidden', 'stepin', null);
        $mform->setType('stepin', PARAM_INT);
        $mform->setConstant('stepin', 7);

        $buttonarray = array();
        if (!isset($SESSION->wizard['form_step1']['fastcopy'])) {
            $buttonarray[] = $mform->createElement(
                'link', 'previousstage', null,
                new moodle_url($SESSION->wizard['wizardurl'], array('stepin' => 6)),
                get_string('previousstage', 'local_crswizard'), array('class' => 'previousstage'));
        }
        $buttonarray[] = $mform->createElement('submit', 'stepgo_8', get_string('finish', 'local_crswizard'));
        $mform->addGroup($buttonarray, 'buttonar', '', null, false);
        $mform->closeHeaderBefore('buttonar');

        $mform->hardFreezeAllVisibleExcept(array('remarques', 'buttonar'));
    }

}
