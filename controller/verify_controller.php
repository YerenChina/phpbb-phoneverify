<?php

namespace bushcraftcn\phoneverify\controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class verify_controller
{
    /** @var \phpbb\config\config */
    protected $config;

    /** @var \phpbb\template\template */
    protected $template;

    /** @var \phpbb\user */
    protected $user;

    /** @var \phpbb\request\request */
    protected $request;

    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

    /** @var \bushcraftcn\phoneverify\service\sms_sender */
    protected $sms_sender;

    /** @var \phpbb\controller\helper */
    protected $helper;

    /** @var string */
    protected $php_ext;

    /** @var string */
    protected $root_path;

    /** @var string */
    protected $verify_table;

    /** @var string */
    protected $user_phone_table;

    public function __construct(
        \phpbb\config\config $config,
        \phpbb\template\template $template,
        \phpbb\user $user,
        \phpbb\request\request $request,
        \phpbb\db\driver\driver_interface $db,
        \bushcraftcn\phoneverify\service\sms_sender $sms_sender,
        \phpbb\controller\helper $helper,
        $php_ext,
        $root_path,
        $verify_table,
        $user_phone_table
    ) {
        $this->config = $config;
        $this->template = $template;
        $this->user = $user;
        $this->request = $request;
        $this->db = $db;
        $this->sms_sender = $sms_sender;
        $this->helper = $helper;
        $this->php_ext = $php_ext;
        $this->root_path = $root_path;
        $this->verify_table = $verify_table;
        $this->user_phone_table = $user_phone_table;
    }

    public function send_code()
    {
        // 检查是否是 AJAX 请求
        if (!$this->request->is_ajax()) {
            throw new \phpbb\exception\http_exception(403, 'NOT_AUTHORISED');
        }

        // 获取手机号
        $phone_number = $this->request->variable('phone_number', '');

        // 验证手机号格式
        if (!preg_match('/^1[3-9]\d{9}$/', $phone_number)) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $this->user->lang['PHONE_NUMBER_INVALID']
            ]);
        }

        // 检查手机号是否已被注册
        $sql = 'SELECT user_id FROM ' . $this->user_phone_table . '
                WHERE phone_number = \'' . $this->db->sql_escape($phone_number) . '\'';
        $result = $this->db->sql_query($sql);
        $exists = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        if ($exists) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $this->user->lang['PHONE_NUMBER_ALREADY_USED']
            ]);
        }

        // 检查是否频繁发送
        $sql = 'SELECT COUNT(*) as count FROM ' . $this->verify_table . '
                WHERE phone_number = "' . $this->db->sql_escape($phone_number) . '"
                AND created_time > ' . (time() - 60);
        $result = $this->db->sql_query($sql);
        $count = (int) $this->db->sql_fetchfield('count');
        $this->db->sql_freeresult($result);

        if ($count > 0) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $this->user->lang['SMS_SEND_TOO_FREQUENT']
            ]);
        }

        // 生成验证码
        $verify_code = sprintf('%06d', mt_rand(0, 999999));

        // 发送短信
        $result = $this->sms_sender->send_verify_code($phone_number, $verify_code);

        if (!$result) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $this->user->lang['SMS_SEND_FAILED']
            ]);
        }

        // 保存验证码
        $sql_ary = [
            'phone_number'   => $phone_number,
            'verify_code'    => $verify_code,
            'created_time'   => time(),
            'expire_time'    => time() + 300, // 5分钟有效期
            'verified'       => 0,
        ];

        $sql = 'INSERT INTO ' . $this->verify_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary);
        $this->db->sql_query($sql);

        return new JsonResponse([
            'status' => 'success',
            'message' => $this->user->lang['VERIFY_CODE_SENT']
        ]);
    }

    public function handle()
    {
        // 处理验证请求的方法
        // ...
    }
}