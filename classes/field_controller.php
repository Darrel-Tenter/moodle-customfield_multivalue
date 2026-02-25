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
 *   in mdl_customfield_data.value — e.g., 'SE 49,SE 51'.
 *   - No spaces around commas.
 *   - Each token matches an option value exactly (trimmed).
 *   - Empty selection is stored as '' (empty string), never NULL.
 *   - intvalue is set to 0 (not used for this type).
 *
 * @package   customfield_multiselect
 * @copyright 2026 Direct Support Learning <support@directsupportlearning.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customfield_multiselect;

defined('MOODLE_INTERNAL') || die();

/**
 * Data controller class for the multiselect custom field type.
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
     * Add the multiselect element to the instance edit form (e.g., course settings).
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
     * Called from instance edit form definition_after_data().
     *
     * Converts the stored comma-separated string back into an array so that
     * MoodleQuickForm can pre-select the correct options.
     *
     * @param \MoodleQuickForm $mform The instance edit form.
     */
    public function instance_form_definition_after_data(\MoodleQuickForm $mform): void {
        $elementname = $this->get_form_element_name();

        if (!$mform->elementExists($elementname)) {
            return;
        }

        $stored = $this->get_stored_value();

        if ($stored !== '') {
            $selected = array_map('trim', explode(',', $stored));
            $mform->getElement($elementname)->setSelected($selected);
        } else {
            $defaultraw = trim($this->get_field()->get_configdata_property('defaultvalue') ?? '');
            if ($defaultraw !== '') {
                $defaults = array_map('trim', explode(',', $defaultraw));
                $mform->getElement($elementname)->setSelected($defaults);
            }
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
            $instance->$elementname = array_map('trim', explode(',', $stored));
        } else {
            $defaultraw = trim($this->get_field()->get_configdata_property('defaultvalue') ?? '');
            if ($defaultraw !== '') {
                $instance->$elementname = array_map('trim', explode(',', $defaultraw));
            } else {
                $instance->$elementname = [];
            }
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
            $tokens = array_map('trim', $submitted);
            $tokens = array_filter($tokens, static function($t) {
                return $t !== '';
            });
            $value = implode(',', array_values($tokens));
        } else {
            $value = trim((string)$submitted);
        }

        $this->set('value', $value);
        $this->set('intvalue', 0);
        $this->save();
    }

    /**
     * Returns the value in a human-readable format for display.
     *
     * @return array|null Array of selected values, or null if empty.
     */
    public function export_value() {
        $stored = $this->get_stored_value();

        if ($stored === '') {
            return null;
        }

        $values = array_map('trim', explode(',', $stored));
        $values = array_filter($values, static function($v) {
            return $v !== '';
        });

        return empty($values) ? null : array_values($values);
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
    public function get_default_value() {
        return trim($this->get_field()->get_configdata_property('defaultvalue') ?? '');
    }

    /**
     * Return the stored comma-separated value, or empty string if no record exists.
     *
     * Named distinctly from get_value() to avoid conflicting with the parent's
     * mixed return type declaration in Moodle 5.x.
     *
     * @return string The stored value, or empty string.
     */
    private function get_stored_value(): string {
        if (!$this->get('id')) {
            $defaultraw = trim($this->get_field()->get_configdata_property('defaultvalue') ?? '');
            return $defaultraw;
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