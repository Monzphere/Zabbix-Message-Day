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

use CWebUser;
use CCsrfTokenHelper;
use CController as Action;

abstract class BaseAction extends Action
{

    const GET = 'get';
    const POST = 'post';

    protected const TYPE_FORM_URLENCODED = 0;
    protected const TYPE_JSON = 1;

    /** @property \Modules\Motd\Module $module */
    public $module;

    /** @property int $post_content_type  Type of content expected by action checkInput method. */
    protected $post_content_type = self::TYPE_FORM_URLENCODED;

    protected $request_method = self::GET;

    public function init()
    {
        $this->request_method = strtolower($_SERVER['REQUEST_METHOD']);

        if ($this->request_method === self::GET) {
            $this->disableSIDvalidation();
        }

        if (version_compare(ZABBIX_VERSION, '6.0', '<')) {
            if ($this->post_content_type == self::TYPE_JSON) {
                $_REQUEST = array_merge($_REQUEST, json_decode(file_get_contents('php://input'), true));
            }
        }
        else {
            $this->setPostContentType($this->post_content_type);
        }
    }

    protected function checkPermissions()
    {
        return CWebUser::getType() == USER_TYPE_SUPER_ADMIN;
    }

    protected function getActionCsrfToken(string $action): string {
        if (version_compare(ZABBIX_VERSION, '6.4.13', '<')) {
            $action = 'motd';
        }

        if (version_compare(ZABBIX_VERSION, '7.0.0alpha1', '>') && version_compare(ZABBIX_VERSION, '7.0.0beta2', '<')) {
            $action = 'motd';
        }

        return CCsrfTokenHelper::get($action);
    }

    public function disableSIDvalidation()
    {
        if (version_compare(ZABBIX_VERSION, '6.4.0', '<')) {
            return parent::disableSIDvalidation();
        }

        return parent::disableCsrfValidation();
    }
}
