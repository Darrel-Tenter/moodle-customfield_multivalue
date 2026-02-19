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
 * Data controller for customfield_multiselect.
 *
 * STORAGE CONTRACT:
 *   Selected values are stored as a comma-separated string of option text values
 *   in mdl_customfield_data.value - e.g., 'SE 49,SE 51'.
 *   - No spaces around commas.
 *   - Each token matches an option value exactly (trimmed).
 *   - Empty selection is stored as '' (empty string), never NULL.
 *   - intvalue is set to 0 (not used for this type).
 *   - Enables: FIND_IN_SET('SE 49', cf.value) > 0
 *
 * @package   customfield_multiselect
 * @copyright 2026 Direct Support Learning <support@directsupportlearning.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customfield_multiselect;

/**
 * Data controller class for the multiselect custom field type.
 */
class data_controller extends \core_customfield\data_controller {

    /**
     * Return the database column used to store this field's data.
     * Using 'value' (TEXT, unlimited) not 'charvalue' (255 chars).
     *
     * @return string
     */
    public function datafield(): string {
        return 'value';
    }

    /**
     * Add the multiselect autocomplete element to the instance edit form.
     *
     * @param \MoodleQuickForm $mform
     */
    public function instance_form_definition(\MoodleQuickForm $mform): void {
        $elementname = $this->get_form_element_name();
        $options     = $this->get_options_for_form();
        $displaysize = (int)($this->get_field()->get_configdata_property('displaysize') ?? 5);

        $attrs = [
            'multiple' => true,
            'size'     => max(2, min($displaysize, 20)),
        ];

        $mform->addElement('autocomplete', $elementname, $this->get_field()->get('name'), $options, $attrs);
        $mform->setType($elementname, PARAM_TEXT);

        if ($this->get_field()->get_configdata_property('required') == 1) {
            $mform->addRule($elementname, null, 'required', null, 'client');
        }
    }

    /**
     * Prepare stored value for display in the instance edit form.
     *
     * Sets $instance->elementname as an array so that $form->set_data($instance)
     * correctly pre-selects options in the autocomplete element.
     * This is the correct pattern for Moodle autocomplete pre-population.
     *
     * @param \stdClass $instance Record being edited (e.g., course object).
     */
    public function instance_form_before_set_data(\stdClass $instance): void {
        $elementname = $this->get_form_element_name();
        $stored      = $this->get_stored_value();

        if ($stored !== '') {
            $instance->$elementname = $this->string_to_array($stored);
        } else {
            $defaultraw = trim($this->get_field()->get_configdata_property('defaultvalue') ?? '');
            $instance->$elementname = $defaultraw !== '' ? $this->string_to_array($defaultraw) : [];
        }
    }

    /**
     * Save custom field data from the submitted instance edit form.
     *
     * Autocomplete with multiple=true submits as an array via $form->get_data().
     * Joins to comma-separated string and saves to value column.
     *
     * @param \stdClass $datanew Submitted form data.
     */
    public function instance_form_save(\stdClass $datanew): void {
        $elementname = $this->get_form_element_name();

        if (!property_exists($datanew, $elementname)) {
            return;
        }

        $submitted = $datanew->$elementname;

        if (is_array($submitted)) {
            $tokens = array_values(array_filter(
                array_map('trim', $submitted),
                static function(string $v): bool {
                    return $v !== '';
                }
            ));
            $value = implode(',', $tokens);
        } else {
            $value = trim((string)$submitted);
        }

        $this->set('value', $value);
        $this->set('intvalue', 0);
        $this->save();
    }

    /**
     * Returns selected values in human-readable format for display.
     * Returns array of selected option text values, or null if empty.
     *
     * @return string[]|null
     */
    public function export_value() {
        $stored = $this->get_stored_value();
        if ($stored === '') {
            return null;
        }
        $values = $this->string_to_array($stored);
        return empty($values) ? null : $values;
    }

    /**
     * Check whether the value is considered empty.
     *
     * @param  mixed $value
     * @return bool
     */
    public function is_empty($value): bool {
        if (is_array($value)) {
            $filtered = array_filter($value, static function($v): bool {
                return trim((string)$v) !== '';
            });
            return empty($filtered);
        }
        return $value === null || trim((string)$value) === '';
    }

    /**
     * Return the raw stored string value from the database.
     * Always returns a string; empty string when no data record exists.
     *
     * @return string
     */
    private function get_stored_value(): string {
        if (!$this->get('id')) {
            return '';
        }
        $raw = $this->get($this->datafield());
        return $raw !== null ? (string)$raw : '';
    }

    /**
     * Parse a comma-separated string into a clean array of trimmed, non-empty values.
     *
     * @param  string   $csv Comma-separated values, e.g. 'SE 49,SE 51'
     * @return string[]      Ordered array of option text values.
     */
    private function string_to_array(string $csv): array {
        return array_values(array_filter(
            array_map('trim', explode(',', $csv)),
            static function(string $v): bool {
                return $v !== '';
            }
        ));
    }

    /**
     * Build key => label options array for the autocomplete form element.
     * Key and label are both the option text value (e.g., 'SE 49').
     * This means FIND_IN_SET() works directly on the stored string.
     *
     * @return array<string, string>
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
