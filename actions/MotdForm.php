<?php declare(strict_types = 0);
/*
** Copyright (C) 2001-2024 initMAX s.r.o.
** Copyright (C) 2024 Monzphere - Fork mantido por Monzphere
**
** This program is free software: you can redistribute it and/or modify it under the terms of
** the GNU Affero General Public License as published by the Free Software Foundation, version 3.
**
** This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
** without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
** See the GNU Affero General Public License for more details.
**
** You should have received a copy of the GNU Affero General Public License along with this program.
** If not, see <https://www.gnu.org/licenses/>.
**/


namespace Modules\Motd\Actions;

use CCsrfTokenHelper;
use CControllerResponseData;
use Modules\Motd\Service\Message;

class MotdForm extends BaseAction
{
    public function init()
    {
        $this->disableCsrfValidation();
    }

    protected function checkInput()
    {
        return $this->validateInput(['id' => 'id']);
    }

    protected function doAction()
    {
        $data = [
            'id' => 0,
            'status' => Message::STATUS_ENABLED,
            'name' => '',
            'show_since' => '',
            'show_since_color' => '#1f65f4',
            'active_since' => strtotime('now'),
            'active_since_color' => '#1f65f4',
            'active_till' => strtotime('now + 1 day'),
            'active_till_color' => '#1f65f4',
            'repeat' => Message::REPEAT_DISABLED,
            'repeat_interval' => Message::REPEAT_DAY,
            'repeat_frequency' => 1,
            'repeat_end_type' => Message::REPEAT_END_NEVER,
            'repeat_end_date' => '',
            'repeat_end_count' => 1,
            'message' => '',
            'message_color' => '#1f65f4',
            'allow_html' => 1,
            'submit_action' => 'motd.form.submit',
            'csrf_token' => [
                'motd.form.submit' => [CCsrfTokenHelper::CSRF_TOKEN_NAME, $this->getActionCsrfToken('motd.form.submit')],
                'motd.form.delete' => [CCsrfTokenHelper::CSRF_TOKEN_NAME, $this->getActionCsrfToken('motd.form.delete')]
            ],
            'user' => [
                'debug_mode' => $this->getDebugMode()
            ]
        ];

        if ($this->hasInput('id')) {
            $messages = $this->module->storage->get(['ids' => [$this->getInput('id')]]);

            if ($messages) {
                $data = array_merge($data, reset($messages));
            }
        }

        $response = new CControllerResponseData($data);
        $this->setResponse($response);
    }
}
