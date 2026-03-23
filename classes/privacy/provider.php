<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Privacy provider for customfield_multivalue.
 *
 * @package   customfield_multivalue
 * @copyright 2026 Direct Support Learning <support@directsupportlearning.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customfield_multivalue\privacy;

/**
 * Privacy provider — this plugin stores no personal data directly.
 */
class provider implements \core_privacy\local\metadata\null_provider {

    /**
     * Return the reason this plugin stores no personal data.
     *
     * @return string Language string key.
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }
}
