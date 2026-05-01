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
 * Data controller for customfield_multivalue.
 *
 * STORAGE CONTRACT:
 *   Selected values are stored as a JSON-encoded array of option text values
 *   in mdl_customfield_data.value — e.g., '["SE 49","SE 51"]'.
 *   - Empty selection is stored as '[]' or '' (empty string), never NULL.
 *   - intvalue is set to 0 (not used for this type).
 *   - JSON encoding is used so option values containing commas are preserved.
 *
 * @package   customfield_multivalue
 * @copyright 2026 Direct Support Learning <support@directsupportlearning.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customfield_multivalue;

defined('MOODLE_INTERNAL') || die();

/**
 * Data controller class for the multivalue custom field type.
 */
class data_controller extends \core_customfield\data_controller {

    /**
     * Return the name of the database column used to store this field's data.
     *
     * We use 'value' (TEXT, unlimited length) rather than 'charvalue' (255 chars).
     *
     * @return string The column name in mdl_customfield_data.
     */
    public function datafield(): string {
        return 'value';
    }

    /**
     * Add the multivalue element to the instance edit form (e.g., course settings).
     *
     * Uses Moodle's autocomplete form element with multiple selection enabled.
     *
     * @param \MoodleQuickForm $mform The instance edit form.
     */
    public function instance_form_definition(\MoodleQuickForm $mform): void {
        $elementname = $this->get_form_element_name();
        $options     = $this->get_options_for_form();
        $displaysize = (int)($this->get_field()->get_configdata_property('displaysize') ?? 5);

        $attrs = [
            'multiple' => true,
            'size'     => max(2, min($displaysize, 20)),
        ];

        $mform->addElement(
            'autocomplete',
            $elementname,
            $this->get_field()->get('name'),
            $options,
            $attrs
        );
        $mform->setType($elementname, PARAM_TEXT);

        if ($this->get_field()->get_configdata_property('required') == 1) {
            $mform->addRule($elementname, null, 'required', null, 'client');
        }
    }

    /**
     * Prepare custom field data for set_data() before the form is displayed.
     *
     * @param \stdClass $instance The record being edited (e.g., a course object).
     */
    public function instance_form_before_set_data(\stdClass $instance): void {
        $elementname = $this->get_form_element_name();
        $stored      = $this->get_stored_value();

        if ($stored !== '') {
            $instance->$elementname = json_decode($stored, true) ?? [];
        } else {
            /** @var field_controller $fc */
            $fc = $this->get_field();
            $instance->$elementname = $fc->get_default_values();
        }
    }

    /**
     * Save custom field data from the submitted form.
     *
     * @param \stdClass $datanew The submitted form data object.
     */
    public function instance_form_save(\stdClass $datanew): void {
        $elementname = $this->get_form_element_name();

        if (!property_exists($datanew, $elementname)) {
            return;
        }

        $submitted = $datanew->$elementname;

        if (is_array($submitted)) {
            $tokens = array_values(array_filter(array_map('trim', $submitted), static function($t) {
                return $t !== '';
            }));
            $value = json_encode($tokens);
        } else {
            $trimmed = trim((string)$submitted);
            $value = $trimmed !== '' ? json_encode([$trimmed]) : '[]';
        }

        $this->set('value', $value);
        $this->set('intvalue', 0);
        $this->save();
    }

    /**
     * Returns the value as a comma-separated string for display and report builder compatibility.
     *
     * @return string|null Comma-separated selected values, or null if empty.
     */
    public function export_value(): ?string {
        $stored = $this->get_stored_value();

        if ($stored === '') {
            return null;
        }

        $values = array_filter(json_decode($stored, true) ?? [], static function($v) {
            return trim((string)$v) !== '';
        });

        return empty($values) ? null : implode(', ', array_values($values));
    }

    /**
     * Check whether the stored value is considered empty.
     *
     * @param  mixed $value The value to check.
     * @return bool         True if the value represents no selection.
     */
    public function is_empty($value): bool {
        if (is_array($value)) {
            $filtered = array_filter($value, static function($v) {
                return trim((string)$v) !== '';
            });
            return empty($filtered);
        }
        return $value === null || trim((string)$value) === '';
    }

    /**
     * Return the default value for this field as defined in field configuration.
     *
     * Required by core_customfield\data_controller in Moodle 4.5 as an abstract
     * method. Returns the comma-separated default string from configdata, or
     * empty string if no default is set.
     *
     * @return string The default value, or empty string.
     */
    public function get_default_value(): string {
        return trim($this->get_field()->get_configdata_property('defaultvalue') ?? '');
    }

    /**
     * Return the stored JSON value, or empty string if no record exists yet.
     *
     * Returns empty string when there is no saved record so that callers fall
     * through to their default-value handling rather than receiving a non-JSON
     * string that silently fails json_decode.
     *
     * Named distinctly from get_value() to avoid conflicting with the parent's
     * mixed return type declaration in Moodle 5.x.
     *
     * @return string JSON-encoded value, or empty string if no record exists.
     */
    private function get_stored_value(): string {
        if (!$this->get('id')) {
            return '';
        }
        return (string)$this->get($this->datafield());
    }

    /**
     * Build the key => label options array for the MoodleQuickForm autocomplete element.
     *
     * @return array<string,string> ['SE 49' => 'SE 49', 'SE 50' => 'SE 50', ...]
     */
    private function get_options_for_form(): array {
        /** @var field_controller $fc */
        $fc = $this->get_field();
        $result = [];
        foreach ($fc->get_options() as $option) {
            $result[$option] = $option;
        }
        return $result;
    }
}
