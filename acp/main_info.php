<?php

namespace bushcraftcn\phoneverify\acp;

class main_info
{
    public function module()
    {
        return [
            'filename'  => '\bushcraftcn\phoneverify\acp\main_module',
            'title'     => 'ACP_PHONE_VERIFY',
            'modes'     => [
                'settings'   => [
                    'title' => 'ACP_PHONE_VERIFY_SETTINGS',
                    'auth'  => 'ext_bushcraftcn/phoneverify && acl_a_board',
                    'cat'   => ['ACP_PHONE_VERIFY']
                ],
            ],
        ];
    }
}