<?php

namespace bushcraftcn\phoneverify;

class ext extends \phpbb\extension\base
{
    public function is_enableable()
    {
        $config = $this->container->get('config');
        return version_compare($config['version'], '3.3.0', '>=');
    }

    public function enable_step($old_state)
    {
        if ($old_state === false)
        {
            $this->container->get('request')->enable_super_globals();
        }

        return parent::enable_step($old_state);
    }

    public function purge_data()
    {
        global $phpbb_container;

        $config = $this->container->get('config');
        $db = $phpbb_container->get('dbal.conn');
        
        // 1. 删除配置项
        $config->delete('phoneverify_version');
        $config->delete('phoneverify_aliyun_access_key_id');
        $config->delete('phoneverify_aliyun_access_key_secret');
        $config->delete('phoneverify_aliyun_sign_name');
        $config->delete('phoneverify_aliyun_template_code');

        // 2. 删除数据表
        $db->sql_query('DROP TABLE IF EXISTS ' . PHONE_VERIFY_TABLE);

        // 3. 删除模块
        $sql = 'DELETE FROM ' . MODULES_TABLE . "
                WHERE module_langname = 'ACP_PHONE_VERIFY'";
        $db->sql_query($sql);

        // 4. 清理缓存
        $cache = $phpbb_container->get('cache');
        $cache->purge();

        return parent::purge_data();
    }
} 