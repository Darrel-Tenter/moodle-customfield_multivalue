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
 * Privacy API implementation for customfield_multiselect.
 *
 * This plugin does not store or process personal data itself.
 * All custom field data (including the selected values) is stored and managed
 * by the core customfield subsystem in mdl_customfield_data. The core system
 * handles all privacy requests (export, deletion) for that table.
 *
 * @package   customfield_multiselect
 * @copyright 2026 Direct Support Learning <support@directsupportlearning.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customfield_multiselect\privacy;

/**
 * Privacy provider — this plugin stores no personal data beyond what the
 * core customfield subsystem already manages.
 */
class provider implements \core_privacy\local\metadata\null_provider {

    /**
     * Returns the reason why this plugin stores no personal data.
     *
     * @return string Language string identifier explaining the null provider status.
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }
}
