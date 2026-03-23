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
 * Field controller for customfield_multivalue.
 *
 * @package   customfield_multivalue
 * @copyright 2026 Direct Support Learning <support@directsupportlearning.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customfield_multivalue;

defined('MOODLE_INTERNAL') || die();

/**
 * Field controller class for the multivalue custom field type.
 */
class field_controller extends \core_customfield\field_controller {

    /** @var string Plugin type identifier. Must match the directory name under customfield/field/. */
    const TYPE = 'multivalue';

    /**
     * Add field-type-specific settings to the field configuration form.
     *
     * @param \MoodleQuickForm $mform The field configuration form.
     */
    public function config_form_definition(\MoodleQuickForm $mform): void {
        $mform->addElement(
            'header',
            'header_multivalue_settings',
            get_string('specificsettings', 'customfield_multivalue')
        );
        $mform->setExpanded('header_multivalue_settings', true);

        $mform->addElement(
            'textarea',
            'configdata[options]',
            get_string('options', 'customfield_multivalue'),
            ['rows' => 10, 'cols' => 40]
        );
        $mform->setType('configdata[options]', PARAM_TEXT);
        $mform->addHelpButton('configdata[options]', 'options', 'customfield_multivalue');

        $mform->addElement(
            'text',
            'configdata[defaultvalue]',
            get_string('defaultvalue', 'customfield_multivalue'),
            ['size' => 60]
        );
        $mform->setType('configdata[defaultvalue]', PARAM_TEXT);
        $mform->addHelpButton('configdata[defaultvalue]', 'defaultvalue', 'customfield_multivalue');

        $mform->addElement(
            'text',
            'configdata[displaysize]',
            get_string('displaysize', 'customfield_multivalue'),
            ['size' => 4]
        );
        $mform->setType('configdata[displaysize]', PARAM_INT);
        $mform->setDefault('configdata[displaysize]', 5);
        $mform->addHelpButton('configdata[displaysize]', 'displaysize', 'customfield_multivalue');
    }

    /**
     * Validate the field configuration form data.
     *
     * @param  array $data  Submitted form data.
     * @param  array $files Submitted files (unused).
     * @return array        Associative array of element name => error string.
     */
    public function config_form_validation(array $data, $files = []): array {
        $errors = [];

        $raw = trim($data['configdata']['options'] ?? '');
        if ($raw === '') {
            $errors['configdata[options]'] = get_string('erroroptionsrequired', 'customfield_multivalue');
            return $errors;
        }

        $options = $this->parse_options($raw);

        $displaysize = (int)($data['configdata']['displaysize'] ?? 0);
        if ($displaysize < 1) {
            $errors['configdata[displaysize]'] = get_string('errordisplaysize', 'customfield_multivalue');
        }

        $defaultraw = trim($data['configdata']['defaultvalue'] ?? '');
        if ($defaultraw !== '') {
            $defaults = array_map('trim', explode(',', $defaultraw));
            foreach ($defaults as $token) {
                if ($token !== '' && !in_array($token, $options, true)) {
                    $errors['configdata[defaultvalue]'] = get_string(
                        'errordefaultnotanoption',
                        'customfield_multivalue',
                        s($token)
                    );
                    break;
                }
            }
        }

        return $errors;
    }

    /**
     * Return the parsed options list from configdata.
     *
     * @return string[] Ordered array of option text values.
     */
    public function get_options(): array {
        $raw = $this->get_configdata_property('options') ?? '';
        return $this->parse_options($raw);
    }

    /**
     * Parse a raw newline-delimited options string into a clean array.
     *
     * @param  string   $raw Raw textarea content.
     * @return string[]      Trimmed, non-empty option values.
     */
    private function parse_options(string $raw): array {
        $lines = explode("\n", str_replace("\r\n", "\n", $raw));
        $options = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed !== '') {
                $options[] = $trimmed;
            }
        }
        return $options;
    }
}
