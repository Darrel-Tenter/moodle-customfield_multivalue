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
 * Restore plugin for customfield_multivalue.
 *
 * Restores the comma-separated value stored in mdl_customfield_data.value
 * for each multivalue field instance. No additional tables are involved.
 *
 * @package   customfield_multivalue
 * @copyright 2026 Direct Support Learning <support@directsupportlearning.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Restore plugin class for customfield_multivalue.
 *
 * The customfield restore framework calls define_plugin_structure() to
 * declare the restore path elements matching the backup structure. Since
 * this plugin produces an empty backup element, the restore element is
 * also empty — no extra processing is required on restore.
 */
class restore_customfield_multivalue_plugin extends restore_customfield_plugin {

    /**
     * Define the plugin restore structure.
     *
     * Returns an empty array of path elements. All data for this field type
     * lives in the core mdl_customfield_data.value column, which the
     * customfield restore framework already handles.
     *
     * @return restore_path_element[] Empty array.
     */
    protected function define_plugin_structure() {
        return [];
    }
}
