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

        // 4. 清理缓存
        $cache = $phpbb_container->get('cache');
        $cache->purge();

        return parent::purge_data();
    }
} 