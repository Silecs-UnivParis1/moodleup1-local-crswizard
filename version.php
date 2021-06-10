<?php
/**
 * @package    local_crswizard
 * @copyright  2012-2020 Silecs {@link http://www.silecs.info/societe}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


$plugin->version   = 2021060600;        // The current plugin version (Date: YYYYMMDDXX)
$plugin->requires  = 2020060900;        // Requires this Moodle version
$plugin->component = 'local_crswizard';       // Full name of the plugin (used for diagnostics)

$plugin->dependencies = [
	'tool_up1_batchprocess' => 2020100300,
    'local_roftools' => 2020100300,
    'local_up1_metadata' => 2020100300,
    'local_jquery' => 2015010900,
    'local_rof_browser' => 2020100300,
    'local_widget_teachersel' => 2016071900,
    'local_widget_groupsel' => 2016071900,
    'local_cohortsyncup1' => 2020103000,
];
