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
 * Language strings for customfield_multiselect.
 *
 * @package   customfield_multiselect
 * @copyright 2026 Direct Support Learning <support@directsupportlearning.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['defaultvalue']            = 'Default selection';
$string['defaultvalue_help']       = 'Optionally enter one or more option values to pre-select by default, separated by commas with no spaces around the commas. Each token must exactly match an option in the list above. Leave blank for no default selection.';
$string['displaysize']             = 'Display size (rows)';
$string['displaysize_help']        = 'Number of visible rows in the selection control. Minimum 2, maximum 20.';
$string['errordefaultnotanoption'] = 'The default value "{$a}" is not in the options list. Each default token must match an option exactly.';
$string['errordisplaysize']        = 'Display size must be a positive integer.';
$string['erroroptionsrequired']    = 'You must enter at least one option.';
$string['options']                 = 'Options';
$string['options_help']            = 'Enter each selectable option on its own line. The exact text you enter here is stored in the database when the option is selected, so do not change option text after data has been saved — existing records will no longer match.';
$string['pluginname']              = 'Multi-select';
$string['privacy:metadata']        = 'The multi-select custom field type plugin does not store any personal data. All custom field data is stored and managed by the core customfield subsystem.';
$string['specificsettings']        = 'Multi-select field settings';
