<?php

namespace bushcraftcn\phoneverify\migrations\v10x;

class release_1_0_1 extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return $this->db_tools->sql_column_exists($this->table_prefix . 'phone_verify', 'ip');
    }

    static public function depends_on()
    {
        return array('\bushcraftcn\phoneverify\migrations\v10x\release_1_0_0');
    }

    public function update_schema()
    {
        return array(
            'add_columns' => array(
                $this->table_prefix . 'phone_verify' => array(
                    'ip' => array('VCHAR:45', ''),
                ),
            ),
        );
    }

    public function update_data()
    {
        return array(
            array('config.add', array('phoneverify_daily_limit', 5)),
            array('config.add', array('phoneverify_ip_daily_limit', 10)),
            array('config.add', array('phoneverify_interval', 60)),
        );
    }

    public function revert_schema()
    {
        return array(
            'drop_columns' => array(
                $this->table_prefix . 'phone_verify' => array(
                    'ip',
                ),
            ),
        );
    }

    public function revert_data()
    {
        return array(
            array('config.remove', array('phoneverify_daily_limit')),
            array('config.remove', array('phoneverify_ip_daily_limit')),
            array('config.remove', array('phoneverify_interval')),
        );
    }
} 