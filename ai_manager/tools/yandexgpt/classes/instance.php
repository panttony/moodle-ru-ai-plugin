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

namespace aitool_yandexgpt;

use local_ai_manager\base_instance;
use local_ai_manager\local\aitool_option_temperature;
use stdClass;

/**
 * Instance class for the connector instance of aitool_yandexgpt.
 *
 * @package    aitool_yandexgpt
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class instance extends base_instance {

    /** @var string Constant for API Key authentication. */
    const AUTH_TYPE_APIKEY = 'apikey';

    /** @var string Constant for IAM Token authentication. */
    const AUTH_TYPE_IAM = 'iam';

    #[\Override]
    protected function extend_form_definition(\MoodleQuickForm $mform): void {
        $mform->addElement('select', 'authtype', get_string('authtype', 'aitool_yandexgpt'),
                [
                        self::AUTH_TYPE_APIKEY => get_string('authtypeapikey', 'aitool_yandexgpt'),
                        self::AUTH_TYPE_IAM => get_string('authtypeiam', 'aitool_yandexgpt'),
                ]);

        $mform->addElement('text', 'catalogid', get_string('catalogid', 'aitool_yandexgpt'));
        $mform->setType('catalogid', PARAM_TEXT);
        $mform->addRule('catalogid', get_string('required'), 'required', null, 'client');

        $mform->hideIf('apikey', 'authtype', 'eq', self::AUTH_TYPE_IAM);

        aitool_option_temperature::extend_form_definition($mform);
    }

    #[\Override]
    protected function get_extended_formdata(): stdClass {
        $data = new stdClass();
        $data->authtype = $this->get_customfield2() ?: self::AUTH_TYPE_APIKEY;
        $data->catalogid = $this->get_customfield3() ?: '';
        
        $temperature = $this->get_customfield1();
        $temperaturedata = aitool_option_temperature::add_temperature_to_form_data($temperature);
        foreach ($temperaturedata as $key => $value) {
            $data->{$key} = $value;
        }
        return $data;
    }

    #[\Override]
    protected function extend_store_formdata(stdClass $data): void {
        $temperature = aitool_option_temperature::extract_temperature_to_store($data);
        $this->set_customfield1($temperature);
        $this->set_customfield2($data->authtype);
        $this->set_customfield3($data->catalogid);

        // Set endpoint based on model
        $modeluri = 'gpt://' . $data->catalogid . '/' . $this->get_model() . '/latest';
        $this->set_endpoint('https://llm.api.cloud.yandex.net/foundationModels/v1/completion');
    }

    #[\Override]
    protected function extend_validation(array $data, array $files): array {
        $errors = [];
        
        if (empty($data['catalogid'])) {
            $errors['catalogid'] = get_string('required');
        }
        
        $errors = array_merge($errors, aitool_option_temperature::validate_temperature($data));
        return $errors;
    }

    /**
     * Return the current temperature value as float.
     *
     * @return float the current temperature value
     */
    public function get_temperature(): float {
        return floatval($this->get_customfield1());
    }

    /**
     * Get catalog ID.
     *
     * @return string
     */
    public function get_catalog_id(): string {
        return $this->get_customfield3();
    }

    /**
     * Get authentication type.
     *
     * @return string
     */
    public function get_auth_type(): string {
        return $this->get_customfield2() ?: self::AUTH_TYPE_APIKEY;
    }
}