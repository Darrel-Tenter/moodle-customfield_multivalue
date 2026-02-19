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
 * Version information for customfield_multiselect.
 *
 * Adds a multi-select custom field type to Moodle's customfield API.
 * Selected values are stored as a comma-separated string of option text values
 * in mdl_customfield_data.value. Compatible with FIND_IN_SET queries.
 *
 * @package   customfield_multiselect
 * @copyright 2026 Direct Support Learning <support@directsupportlearning.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'customfield_multiselect';
$plugin->version   = 2026021900;
$plugin->requires  = 2024042200; // Moodle 5.0 minimum.
$plugin->maturity  = MATURITY_STABLE;
$plugin->release   = '1.0.0';
