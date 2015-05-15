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
 * niazpardaz configuration page
 *
 * @package    message_niazpardaz
 * @copyright  2011 Lancaster University Network Services Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
  $settings->add(new admin_setting_configtext('niazpardaznumber', get_string('niazpardaznumber', 'message_niazpardaz'), get_string('configniazpardaznumber', 'message_niazpardaz'), '', PARAM_RAW));
  $settings->add(new admin_setting_configtext('niazpardazusername', get_string('niazpardazusername', 'message_niazpardaz'), get_string('configniazpardazusername', 'message_niazpardaz'), '', PARAM_RAW));
  $settings->add(new admin_setting_configpasswordunmask('niazpardazpassword', get_string('niazpardazpassword', 'message_niazpardaz'), get_string('configniazpardazpassword', 'message_niazpardaz'), ''));
}
