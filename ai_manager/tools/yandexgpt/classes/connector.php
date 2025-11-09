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

use local_ai_manager\local\prompt_response;
use local_ai_manager\local\request_response;
use local_ai_manager\local\unit;
use local_ai_manager\local\usage;
use local_ai_manager\request_options;
use Psr\Http\Message\StreamInterface;

class connector extends \local_ai_manager\base_connector {

    #[\Override]
    public function get_models_by_purpose(): array {
        $textmodels = ['yandexgpt', 'yandexgpt-lite', 'summarization'];
        return [
                'chat' => $textmodels,
                'feedback' => $textmodels,
                'singleprompt' => $textmodels,
                'translate' => $textmodels,
                'itt' => $textmodels,
                'questiongeneration' => $textmodels,
        ];
    }

    #[\Override]
    public function get_unit(): unit {
        return unit::TOKEN;
    }

    #[\Override]
    public function execute_prompt_completion(StreamInterface $result, request_options $requestoptions): prompt_response {
        $content = json_decode($result->getContents(), true);

        if (isset($content['error'])) {
            return prompt_response::create_from_error(
                $this->instance->get_model(),
                $content['error']['message'] ?? 'Unknown error'
            );
        }

        $textanswer = $content['result']['alternatives'][0]['message']['text'] ?? '';
        
        $usage = new usage(
            (float) ($content['result']['usage']['totalTokens'] ?? 0),
            (float) ($content['result']['usage']['inputTextTokens'] ?? 0),
            (float) ($content['result']['usage']['completionTokens'] ?? 0)
        );

        return prompt_response::create_from_result(
                $this->instance->get_model(),
                $usage,
                $textanswer,
        );
    }

    #[\Override]
    public function get_prompt_data(string $prompttext, request_options $requestoptions): array {
        $options = $requestoptions->get_options();
        $messages = [];
        
        if (array_key_exists('conversationcontext', $options)) {
            foreach ($options['conversationcontext'] as $message) {
                switch ($message['sender']) {
                    case 'user':
                        $role = 'user';
                        break;
                    case 'ai':
                        $role = 'assistant';
                        break;
                    case 'system':
                        $role = 'system';
                        break;
                    default:
                        throw new \moodle_exception('exception_badmessageformat', 'local_ai_manager');
                }
                $messages[] = [
                        'role' => $role,
                        'text' => $message['message'],
                ];
            }
        }
        
        $messages[] = [
                'role' => 'user',
                'text' => $prompttext,
        ];

        $modeluri = 'gpt://' . $this->instance->get_catalog_id() . '/' . 
                    $this->instance->get_model() . '/latest';

        return [
                'modelUri' => $modeluri,
                'completionOptions' => [
                        'stream' => false,
                        'temperature' => $this->instance->get_temperature(),
                        'maxTokens' => $options['maxTokens'] ?? 1000,
                ],
                'messages' => $messages,
        ];
    }

    #[\Override]
    public function has_customvalue1(): bool {
        return true;
    }

    #[\Override]
    public function has_customvalue2(): bool {
        return true;
    }

    #[\Override]
    public function has_customvalue3(): bool {
        return true;
    }

    #[\Override]
    protected function get_headers(): array {
        $headers = parent::get_headers();
        
        if ($this->instance->get_auth_type() === instance::AUTH_TYPE_APIKEY) {
            $headers['Authorization'] = 'Api-Key ' . $this->get_api_key();
        } else {
            // For IAM token
            $headers['Authorization'] = 'Bearer ' . $this->get_api_key();
        }
        
        $headers['x-folder-id'] = $this->instance->get_catalog_id();
        
        return $headers;
    }

    #[\Override]
    public function allowed_mimetypes(): array {
        return [];
    }
}
