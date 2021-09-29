<?php
/**
 * @package    local_crswizard
 * @copyright  2012-2021 Silecs {@link http://www.silecs.info/societe}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* @var $ADMIN admin_root */

defined('MOODLE_INTERNAL') || die;

if (has_capability('moodle/site:config', context_system::instance())) {

    require_once(dirname(__FILE__).'/lib_wizard.php');

    $settings = new admin_settingpage('local_crswizard', 'Assistant de création de cours');
    $ADMIN->add('localplugins', $settings);

    $cohorts_cap_creator = new admin_setting_configtext(
            'cohorts_cap_creator',
            'Cohortes autorisées en création',
            'Liste des cohortes autorisées à utiliser l\'assistant : identifiants séparés par des virgules.',
            '',
            PARAM_NOTAGS);
    $cohorts_cap_creator->plugin = 'local_crswizard';
    $settings->add($cohorts_cap_creator);

    $cohorts_cap_validator = new admin_setting_configtext(
            'cohorts_cap_validator',
            'Cohortes autorisées en approbation',
            'Liste des cohortes autorisées à approuver les cours soumis via l\'assistant : identifiants séparés par des vigules.',
            '',
            PARAM_NOTAGS);
    $cohorts_cap_validator->plugin = 'local_crswizard';
    $settings->add($cohorts_cap_validator);

    $helpdesk_user = new admin_setting_configtext(
            'helpdesk_user',
            'Utilisateur support',
            'Nom (username) de l\'utilisateur support. Il recevra les demandes d\'aide.',
            '',
            PARAM_NOTAGS);
    $helpdesk_user->plugin = 'local_crswizard';
    $settings->add($helpdesk_user);

    $categories_list = wizard_make_categories_model_list();
    $category_model = new admin_setting_configselect(
        'category_model',
            'Sélection de la catégorie modèle',
            'Valeur de la catégorie des cours modèles',
            0,
            $categories_list);
    $category_model->plugin = 'local_crswizard';
    $settings->add($category_model);

    $settings->add(new admin_setting_heading('wizardcas2defaults', 'Valeurs par défaut des réglages (cas 2)', ''));

    $etab = wizard_get_catlevel2();
    $cas2_default_etablissement = new admin_setting_configselect(
            'cas2_default_etablissement',
            'Valeur par défaut de l\'établissement',
            'Valeur par défaut de l\'établissement pour le cas 2',
            0,
            $etab);
    $cas2_default_etablissement->plugin = 'local_crswizard';
    $settings->add($cas2_default_etablissement);

    //autovalidation permise
    $autovalidation = new admin_setting_configcheckbox(
        'course_autovalidation',
        'Autovalidation possible',
        'Permet à l\'utilisateur de valider son cours',
        1);
    $autovalidation->plugin = 'local_crswizard';
    $settings->add($autovalidation);
}
