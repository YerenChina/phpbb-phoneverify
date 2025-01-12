<?php

namespace bushcraftcn\phoneverify\migrations\v10x;

class release_1_0_0 extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return isset($this->config['phoneverify_version']) && version_compare($this->config['phoneverify_version'], '1.0.0', '>=');
    }

    public static function depends_on()
    {
        return ['\phpbb\db\migration\data\v330\v330'];
    }

    public function update_schema()
    {
        return [
            'add_tables' => [
                $this->table_prefix . 'user_phone' => [
                    'COLUMNS' => [
                        'user_id'      => ['VCHAR:255', '', 'utf8mb4'],
                        'phone_number' => ['VCHAR:20', '', 'utf8mb4'],
                        'verified'     => ['BOOL', 1],
                        'verify_time'  => ['TIMESTAMP', 0],
                    ],
                    'PRIMARY_KEY' => 'user_id',
                    'KEYS' => [
                        'phone_number' => ['UNIQUE', ['phone_number']],
                    ],
                ],
                $this->table_prefix . 'phone_verify' => [
                    'COLUMNS' => [
                        'verify_id'      => ['UINT', null, 'auto_increment'],
                        'user_id'        => ['UINT', 0],
                        'phone_number'   => ['VCHAR:20', '', 'utf8mb4'],
                        'verify_code'    => ['VCHAR:6', '', 'utf8mb4'],
                        'created_time'   => ['TIMESTAMP', 0],
                        'expire_time'    => ['TIMESTAMP', 0],
                        'verified'       => ['BOOL', 0],
                    ],
                    'PRIMARY_KEY' => 'verify_id',
                    'KEYS' => [
                        'user_id'      => ['INDEX', 'user_id'],
                        'phone_number' => ['INDEX', 'phone_number'],
                    ],
                ],
            ],
        ];
    }

    public function revert_schema()
    {
        return [
            'drop_tables' => [
                $this->table_prefix . 'phone_verify',
                $this->table_prefix . 'user_phone',
            ],
        ];
    }

    public function update_data()
    {
        return [
            ['config.add', ['phoneverify_version', '1.0.0']],
            ['config.add', ['phoneverify_aliyun_access_key_id', '']],
            ['config.add', ['phoneverify_aliyun_access_key_secret', '']],
            ['config.add', ['phoneverify_aliyun_sign_name', '']],
            ['config.add', ['phoneverify_aliyun_template_code', '']],

            ['module.add', [
                'acp',
                'ACP_CAT_DOT_MODS',
                'ACP_PHONE_VERIFY'
            ]],
            ['module.add', [
                'acp',
                'ACP_PHONE_VERIFY',
                [
                    'module_basename' => '\bushcraftcn\phoneverify\acp\main_module',
                    'modes' => ['settings'],
                ],
            ]],
        ];
    }
} 