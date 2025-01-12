<?php

namespace bushcraftcn\phoneverify\acp;

class main_module
{
    public $u_action;
    public $tpl_name;
    public $page_title;

    public function main($id, $mode)
    {
        global $phpbb_container;

        $config = $phpbb_container->get('config');
        $request = $phpbb_container->get('request');
        $template = $phpbb_container->get('template');
        $user = $phpbb_container->get('user');

        $user->add_lang_ext('bushcraftcn/phoneverify', 'info_acp_phone_verify');
        $this->tpl_name = 'phone_verify_acp';
        $this->page_title = $user->lang('ACP_PHONE_VERIFY_SETTINGS');

        add_form_key('phone_verify');

        if ($request->is_set_post('submit'))
        {
            if (!check_form_key('phone_verify'))
            {
                trigger_error('FORM_INVALID', E_USER_WARNING);
            }

            $config->set('phoneverify_aliyun_access_key_id', $request->variable('phoneverify_aliyun_access_key_id', '', true));
            $config->set('phoneverify_aliyun_access_key_secret', $request->variable('phoneverify_aliyun_access_key_secret', '', true));
            $config->set('phoneverify_aliyun_sign_name', htmlspecialchars_decode($request->variable('phoneverify_aliyun_sign_name', '', true), ENT_QUOTES));
            $config->set('phoneverify_aliyun_template_code', $request->variable('phoneverify_aliyun_template_code', '', true));
            $config->set('phoneverify_daily_limit', $request->variable('phoneverify_daily_limit', 5));
            $config->set('phoneverify_ip_daily_limit', $request->variable('phoneverify_ip_daily_limit', 10));
            $config->set('phoneverify_interval', $request->variable('phoneverify_interval', 60));

            trigger_error($user->lang('SETTINGS_SAVED') . adm_back_link($this->u_action));
        }

        $template->assign_vars([
            'U_ACTION'                           => $this->u_action,
            'ALIYUN_KEY_ID'                     => $config['phoneverify_aliyun_access_key_id'],
            'ALIYUN_KEY_SECRET'                 => $config['phoneverify_aliyun_access_key_secret'],
            'ALIYUN_SIGN_NAME'                  => htmlspecialchars($config['phoneverify_aliyun_sign_name'], ENT_QUOTES, 'UTF-8'),
            'ALIYUN_TEMPLATE_CODE'              => $config['phoneverify_aliyun_template_code'],
            'PHONE_VERIFY_DAILY_LIMIT'          => $config['phoneverify_daily_limit'],
            'PHONE_VERIFY_IP_DAILY_LIMIT'       => $config['phoneverify_ip_daily_limit'],
            'PHONE_VERIFY_INTERVAL'             => $config['phoneverify_interval'],
        ]);
    }
}