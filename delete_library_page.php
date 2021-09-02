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
 * Responsible for displaying the delete library page
 *
 * @package    mod_hvp
 * @copyright  2021 Loïc Villanné
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once($CFG->libdir.'/adminlib.php');
require_once("locallib.php");

// No guest autologin.
require_login(0, false);

$libraryid = required_param('library_id', PARAM_INT);
$pageurl = new moodle_url('/mod/hvp/delete_library_page.php', array('library_id' => $libraryid));
$PAGE->set_url($pageurl);
admin_externalpage_setup('h5plibraries');
$PAGE->set_title("{$SITE->shortname}: " . get_string('deletelibrary', 'hvp'));

global $DB;
$library = $DB->get_record_sql('SELECT hl.id, hl.title, hl.major_version, hl.minor_version, hl.patch_version
                                   FROM {hvp_libraries} hl
                                  WHERE hl.id = ?', array($libraryid));

$libraryTitle = $library === false ? get_string('deletelibraryunknown', 'hvp') : $library->title . ' (' . \H5PCore::libraryVersion($library) . ')';
$PAGE->set_heading(get_string('deletelibraryheading', 'hvp', $libraryTitle));

// Create delete library form.
$deletelibraryform = new \mod_hvp\delete_library_form(null, [
    'library_id' => $libraryid,
    'token' => \H5PCore::createToken('deletelibrary')
]);
$data = $deletelibraryform->get_data();
echo $OUTPUT->header();

// On form submit.
if ($data && $libraryid === $data->library_id) {
    hvp_delete_library($libraryid);
} else {
    $deletelibraryform->display();
}

print '<br> <a href="' . (new moodle_url('/mod/hvp/library_list.php'))->out(false) . '">' . get_string('deletelibraryreturn', 'hvp') . '</a>';
echo $OUTPUT->footer();
