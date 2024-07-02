<?php
/**
 * Edit course settings
 *
 * @package    local_crswizard
 * @copyright  2012-2021 Silecs {@link http://www.silecs.info/societe}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or laters
 */

/**
 * cette classe utilise le compte "administrateur principal" (renvoyé par
 * get_admin()) pour créer le backup et effectuer la restauration. Besoin des
 * permissions moodle/course:create, moodle/restore:restorecourse et
 * moodle/backup:backupcourse au niveau de la plateforme
 */
class wizard_modele_duplicate {

    protected $adminuser;
    public $courseid;
    public $newcoursedata;
    public $backupid;
    public $backupbasepath;
    public $file;
    public $backupsettings = array();

    public $backupdefaults = array(
        'activities' => 1,
        'blocks' => 1,
        'filters' => 1,
        'users' => 0,
        'role_assignments' => 1,
        'comments' => 0,
        'userscompletion' => 0,
        'logs' => 0,
        'grade_histories' => 0,
        'badges' => 1,
    );

    public function __construct($courseid, $mydata, $options) {
        $this->courseid = $courseid;
        $this->mydata = $mydata;
        $this->options = $options;
        $this->adminuser = get_admin();
    }

    public function create_backup() {
        global $CFG;
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
        // Check for backup and restore options.
        if (!empty($this->options)) {
            foreach ($this->options as $option) {

                // Strict check for a correct value (allways 1 or 0, true or false).
                $value = clean_param($option['value'], PARAM_INT);

                if ($value !== 0 and $value !== 1) {
                    throw new moodle_exception('invalidextparam', 'webservice', '', $option['name']);
                }

                if (!isset($this->backupdefaults[$option['name']])) {
                    throw new moodle_exception('invalidextparam', 'webservice', '', $option['name']);
                }

                $this->backupsettings[$option['name']] = $value;
            }
        }

        $bc = new backup_controller(backup::TYPE_1COURSE, $this->courseid, backup::FORMAT_MOODLE,
        backup::INTERACTIVE_NO, backup::MODE_SAMESITE, $this->adminuser->id);

        foreach ($this->backupsettings as $name => $value) {
            $bc->get_plan()->get_setting($name)->set_value($value);
        }

        $this->backupid       = $bc->get_backupid();
        $this->backupbasepath = $bc->get_plan()->get_basepath();
        
        $bc->execute_plan();
        $results = $bc->get_results();
        $this->file = $results['backup_destination'];
        $bc->destroy();
    }


    public function retore_backup() {
        global $CFG,$DB,$USER;
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
        // Check if we need to unzip the file because the backup temp dir does not contains backup files.
        if (!file_exists($this->backupbasepath . "/moodle_backup.xml")) {
            $this->file->extract_to_pathname(get_file_packer('application/x-gzip'), $this->backupbasepath);
        }

         // Create new course.
        $newcourseid = restore_dbops::create_new_course($this->mydata->fullname . ' en attente',
            $this->mydata->shortname . ' en attente', $this->mydata->category);
            
        $rc = new restore_controller($this->backupid, $newcourseid,
                backup::INTERACTIVE_NO, backup::MODE_SAMESITE, $this->adminuser->id, backup::TARGET_NEW_COURSE);
        
        //bug panopto name
        $fullnamesetting = $rc->get_plan()->get_setting('course_fullname');
        if ($fullnamesetting->get_status() == backup_setting::NOT_LOCKED) {
            $fullnamesetting->set_value($this->mydata->fullname);
        }
        $fullnamesetting = $rc->get_plan()->get_setting('course_shortname');
        if ($fullnamesetting->get_status() == backup_setting::NOT_LOCKED) {
            $fullnamesetting->set_value($this->mydata->shortname);
        }
        
        foreach ($this->backupsettings as $name => $value) {
            $setting = $rc->get_plan()->get_setting($name);
            if ($setting->get_status() == backup_setting::NOT_LOCKED) {
                $setting->set_value($value);
            }
        }

        if (!$rc->execute_precheck()) {
            $precheckresults = $rc->get_precheck_results();
            if (is_array($precheckresults) && !empty($precheckresults['errors'])) {
                if (empty($CFG->keeptempdirectoriesonbackup)) {
                    fulldelete($backupbasepath);
                }
                $errorinfo = '';
                foreach ($precheckresults['errors'] as $error) {
                    $errorinfo .= $error;
                }
                if (array_key_exists('warnings', $precheckresults)) {
                    foreach ($precheckresults['warnings'] as $warning) {
                        $errorinfo .= $warning;
                    }
                }
                throw new moodle_exception('backupprecheckerrors', 'webservice', '', $errorinfo);
            }
        }
        
        //hack pour donner à $USER le droit moodle/calendar:manageentries (role manager dans le nouveau cours)
        require_once("$CFG->dirroot/lib/enrollib.php");
        $enrol = new stdClass();
        $enrol->enrol = "manual";
        $enrol->status = 0;
        $enrol->courseid = $newcourseid;
        $enrol->timemodified = time();
        $enrol->timecreated = $enrol->timemodified;
        $enrol->sortorder = $DB->get_field('enrol', 'COALESCE(MAX(sortorder), -1) + 1', ['courseid' => $newcourseid]);
        $enrol->roleid = $DB->get_field('role', 'id', ['shortname' => 'student']);
        $enrol->expirythreshold = 86400;
        $enrol->id = $DB->insert_record('enrol', $enrol);
        $role = $DB->get_record('role', ['shortname' => 'manager']);
        if ($role) {
            enrol_try_internal_enrol($newcourseid, $USER->id, $role->id);
        }

        $rc->execute_plan();
        $rc->destroy();
        
        //suppression rôle manager de USER dans le nouveau cours
        $plugin = enrol_get_plugin('manual');
        $plugin->unenrol_user($enrol, $USER->id);

        $courseconfig = get_config('moodlecourse');
        $course = $DB->get_record('course', array('id' => $newcourseid), '*', MUST_EXIST);

        $course->fullname = $this->mydata->fullname;
        $course->shortname = $this->mydata->shortname;
        if (isset($this->mydata->idnumber)) {
            $course->idnumber = $this->mydata->idnumber;
        }
        $course->visible = $this->mydata->visible;
        $course->startdate = $this->mydata->startdate;
        $course->enddate   = $this->mydata->enddate;
        $course->format = $this->mydata->format ?? $courseconfig->format;
        $course->summary       = $this->mydata->summary_editor['text'];
        $course->summaryformat = $this->mydata->summary_editor['format'];
        $course->timecreated = time();
        $course->timemodified = $course->timecreated;
        $course->newsitems = $this->mydata->newsitems ?? $courseconfig->newsitems;
        
        // Set shortname and fullname back.
        $DB->update_record('course', $course);
        
        if (empty($CFG->keeptempdirectoriesonbackup)) {
            fulldelete($this->backupbasepath);
        }

        // Delete the course backup file created by this WebService. Originally located in the course backups area.
        $this->file->delete();

        return $course;
    }
}
