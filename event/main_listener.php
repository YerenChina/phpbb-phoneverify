<?php

namespace bushcraftcn\phoneverify\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class main_listener implements EventSubscriberInterface
{
    /** @var \phpbb\config\config */
    protected $config;

    /** @var \phpbb\request\request */
    protected $request;

    /** @var \phpbb\template\template */
    protected $template;

    /** @var \phpbb\user */
    protected $user;

    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

    /** @var \phpbb\controller\helper */
    protected $helper;

    /** @var string */
    protected $verify_table;

    /** @var string */
    protected $user_phone_table;

    public function __construct(
        \phpbb\config\config $config,
        \phpbb\request\request $request,
        \phpbb\template\template $template,
        \phpbb\user $user,
        \phpbb\db\driver\driver_interface $db,
        \phpbb\controller\helper $helper,
        $verify_table,
        $user_phone_table
    ) {
        $this->config = $config;
        $this->request = $request;
        $this->template = $template;
        $this->user = $user;
        $this->db = $db;
        $this->helper = $helper;
        $this->verify_table = $verify_table;
        $this->user_phone_table = $user_phone_table;
    }

    public static function getSubscribedEvents()
    {
        return [
            'core.user_setup'                => 'load_language_on_setup',
            'core.ucp_register_data_before'  => 'add_phone_verify_field',
            'core.ucp_register_data_after'   => 'verify_phone_number',
            'core.user_add_modify_data'      => 'save_phone_number',
            'core.page_header'               => 'add_page_header_link',
        ];
    }

    public function load_language_on_setup($event)
    {
        $lang_set_ext = $event['lang_set_ext'];
        $lang_set_ext[] = [
            'ext_name' => 'bushcraftcn/phoneverify',
            'lang_set' => 'phone_verify',
        ];
        $event['lang_set_ext'] = $lang_set_ext;
    }

    public function add_phone_verify_field($event)
    {
        $event['data'] = array_merge($event['data'], [
            'phone_number'    => $this->request->variable('phone_number', ''),
            'verify_code'     => $this->request->variable('verify_code', ''),
        ]);

        $data = $event['data'];
        $this->template->assign_vars([
            'S_PHONE_VERIFY'      => true,
            'U_SEND_CODE'         => $this->helper->route('bushcraftcn_phoneverify_send_code', [], true, false, false),
            'PHONE_NUMBER'        => $data['phone_number'],
        ]);
    }

    public function verify_phone_number($event)
    {
        $error = $event['error'];
        $data = $event['data'];

        error_log('Verifying phone number - Data: ' . print_r($data, true));

        if (!empty($data['phone_number']) && !empty($data['verify_code'])) {
            $sql = 'SELECT * FROM ' . $this->verify_table . '
                    WHERE phone_number = \'' . $this->db->sql_escape($data['phone_number']) . '\'
                    AND verify_code = \'' . $this->db->sql_escape($data['verify_code']) . '\'
                    AND verified = 0
                    AND expire_time > ' . time() . '
                    ORDER BY verify_id DESC';
            $result = $this->db->sql_query_limit($sql, 1);
            $row = $this->db->sql_fetchrow($result);
            $this->db->sql_freeresult($result);

            if (!$row) {
                $error[] = $this->user->lang['VERIFY_CODE_INVALID'];
            } else {
                // 标记验证码已使用
                $sql = 'UPDATE ' . $this->verify_table . '
                        SET verified = 1
                        WHERE verify_id = ' . (int)$row['verify_id'];
                $this->db->sql_query($sql);

                // 确保手机号被传递到后续步骤
                if (isset($event['sql_ary'])) {
                    $event['sql_ary']['phone_number'] = $data['phone_number'];
                }
                // 保存到 session 以备后用
                $this->user->data['phone_number'] = $data['phone_number'];
            }
        } else {
            $error[] = $this->user->lang['PHONE_VERIFY_REQUIRED'];
        }

        $event['error'] = $error;
    }

    public function save_phone_number($event)
    {
        $phone_number = null;
        
        // 1. 从 event data 获取
        if (!empty($event['data']['phone_number'])) {
            $phone_number = $event['data']['phone_number'];
        }
        // 2. 从 session 获取
        elseif (!empty($this->user->data['phone_number'])) {
            $phone_number = $this->user->data['phone_number'];
        }
        // 3. 从请求中获取
        elseif ($this->request->is_set('phone_number')) {
            $phone_number = $this->request->variable('phone_number', '');
        }

        if (!empty($phone_number)) {
            try {
                // 先检查手机号是否已被使用
                $sql = 'SELECT user_id FROM ' . $this->user_phone_table . '
                        WHERE phone_number = \'' . $this->db->sql_escape($phone_number) . '\'';
                $result = $this->db->sql_query($sql);
                $exists = $this->db->sql_fetchrow($result);
                $this->db->sql_freeresult($result);

                if ($exists) {
                    // 如果手机号已存在，添加错误信息
                    $error = $event['error'] ?? [];
                    $error[] = $this->user->lang['PHONE_NUMBER_ALREADY_USED'];
                    $event['error'] = $error;
                    error_log('Phone number already in use, adding error message');
                } else {
                    // 手机号不存在，保存到数据库
                    $sql = 'INSERT INTO ' . $this->user_phone_table . ' ' . 
                           $this->db->sql_build_array('INSERT', [
                               'user_id'      => $event['user_row']['username'],
                               'phone_number' => $phone_number,
                               'verified'     => 1,
                               'verify_time'  => time(),
                           ]);
                    
                    error_log('Save phone number - SQL: ' . $sql);
                    $this->db->sql_query($sql);
                    error_log('Phone number saved successfully');
                }
            } catch (\Exception $e) {
                error_log('Failed to save phone number: ' . $e->getMessage());
            }
        } else {
            error_log('No phone number found in registration data');
            // 如果没有手机号，添加错误信息
            $error = $event['error'] ?? [];
            $error[] = $this->user->lang['PHONE_VERIFY_REQUIRED'];
            $event['error'] = $error;
        }
    }

    public function add_page_header_link($event)
    {
        $this->template->assign_vars([
            'U_SEND_CODE' => $this->helper->route('bushcraftcn_phoneverify_send_code'),
        ]);
    }
} 