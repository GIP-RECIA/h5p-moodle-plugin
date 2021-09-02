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
 * \mod_hvp\delete_library_form class
 *
 * @package    mod_hvp
 * @copyright  2021 Loïc Villanné
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_hvp;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Form to validate library deletion.
 *
 * @package    mod_hvp
 * @copyright  2021 Loïc Villanné
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_library_form extends \moodleform {

    /**
     * Define form elements
     */
    public function definition() {
        // Get form.
        $mform = $this->_form;

        $mform->addElement('hidden', 'library_id', $this->_customdata['library_id']);
        $mform->setType('library_id', PARAM_INT);

        $mform->addElement('hidden', 'token', $this->_customdata['token']);
        $mform->setType('token', PARAM_RAW);

        $mform->addElement('static', 'deletelibrarydescription', '',
            get_string('deletelibrarydescription', 'hvp'));

        $this->add_action_buttons(false, get_string('deletelibrarybuttonlabel', 'hvp'));
    }
}
