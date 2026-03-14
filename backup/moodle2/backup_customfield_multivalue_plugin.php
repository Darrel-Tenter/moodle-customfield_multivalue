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
 * Backup plugin for customfield_multivalue.
 *
 * Backs up the comma-separated value stored in mdl_customfield_data.value
 * for each multivalue field instance. No additional tables are involved.
 *
 * @package   customfield_multivalue
 * @copyright 2026 Direct Support Learning <support@directsupportlearning.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Backup plugin class for customfield_multivalue.
 *
 * The customfield backup framework calls define_plugin_structure() to
 * declare what extra data (beyond the core customfield_data record) this
 * field type needs to back up. This plugin stores everything in the core
 * value column, so no extra structure is needed — we return an empty
 * plugin element.
 */
class backup_customfield_multivalue_plugin extends backup_customfield_plugin {

    /**
     * Define the plugin backup structure.
     *
     * Returns an empty plugin element. All data for this field type lives in
     * the core mdl_customfield_data.value column, which the customfield
     * backup framework already handles.
     *
     * @return backup_plugin_element The plugin element (empty).
     */
    protected function define_plugin_structure() {
        return $this->get_plugin_element();
    }
}
