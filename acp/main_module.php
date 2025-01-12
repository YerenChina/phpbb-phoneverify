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

        if ($request->is_set_post('submit'))
        {
            if (!check_form_key('phone_verify'))
            {
                trigger_error('FORM_INVALID', E_USER_WARNING);
            }

            // 保存设置前进行 UTF-8 编码
            $config->set('phoneverify_aliyun_access_key_id', $request->variable('phoneverify_aliyun_access_key_id', '', true));
            $config->set('phoneverify_aliyun_access_key_secret', $request->variable('phoneverify_aliyun_access_key_secret', '', true));
            $config->set('phoneverify_aliyun_sign_name', htmlspecialchars_decode($request->variable('phoneverify_aliyun_sign_name', '', true), ENT_QUOTES));
            $config->set('phoneverify_aliyun_template_code', $request->variable('phoneverify_aliyun_template_code', '', true));

            trigger_error($user->lang('SETTINGS_SAVED') . adm_back_link($this->u_action));
        }

        // 显示设置时进行解码
        $template->assign_vars([
            'U_ACTION'           => $this->u_action,
            'ALIYUN_KEY_ID'      => $config['phoneverify_aliyun_access_key_id'],
            'ALIYUN_KEY_SECRET'  => $config['phoneverify_aliyun_access_key_secret'],
            'ALIYUN_SIGN_NAME'   => htmlspecialchars($config['phoneverify_aliyun_sign_name'], ENT_QUOTES, 'UTF-8'),
            'ALIYUN_TEMPLATE_CODE'=> $config['phoneverify_aliyun_template_code'],
        ]);

        add_form_key('phone_verify');
    }
}