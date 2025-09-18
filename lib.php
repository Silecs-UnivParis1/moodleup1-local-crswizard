<?php
/**
 * @package    local_crswizard
 * @copyright  2012-2021 Silecs {@link http://www.silecs.info/societe}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/libaccess.php');

// Depuis Moodle 3.9, on n'utilise plus le menu Navigation de Moodle
// À la place, on utilise le bloc "Création de cours"

function local_crswizard_extend_navigation_course($navigation, $course, $context) {
    global $PAGE, $USER;
    if (get_capability_info('local/up1_capabilities:course_updatesettings')) {
        if (!has_capability('local/up1_capabilities:course_updatesettings', $context, $USER, true)) {
            $children = $navigation->get_children_key_list();
            if (count($children) && in_array('editsettings', $children)) {
                $editsettings = $navigation->find('editsettings', navigation_node::TYPE_SETTING);
                if ($editsettings) {
                    $editsettings->hide();
                }
            }
            $selecteur = '#page div.main-inner div.secondary-navigation ul[role="menubar"]';
            if ($PAGE->theme->name == 'adaptable') {
                $selecteur = '#region-main div.secondary-navigation ul[role="menubar"]';
            }
            $selenc = json_encode($selecteur);
            $PAGE->requires->js_init_code(<<<EOJS
                console.log($selenc);
                let navcouse = document.querySelectorAll($selenc + ' li[data-key="coursehome"]');
                let navparam = document.querySelectorAll($selenc + ' li[data-key="editsettings"]');
                if (navcouse.length == 1 && navparam.length == 1) {
                    navparam[0].remove();
                }
EOJS
        , false);
        }
    }
}
