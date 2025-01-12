<?php

if (!defined('IN_PHPBB'))
{
    exit;
}

if (empty($lang) || !is_array($lang))
{
    $lang = [];
}

$lang = array_merge($lang, [
    'PHONE_VERIFY'           => '手机实名验证',
    'PHONE_VERIFY_REQUIRED'  => '请输入手机号码并完成验证',
    'PHONE_NUMBER'           => '手机号码',
    'VERIFY_CODE'            => '验证码',
    'SEND_CODE'             => '发送验证码',
    'SUBMIT_VERIFY'         => '提交验证',
    'PHONE_NUMBER_INVALID'   => '请输入有效的中国大陆手机号码（11位数字，以1开头）',
    'VERIFY_CODE_INVALID'    => '验证码无效或已过期',
    'VERIFY_SUCCESS'         => '验证成功',
    'VERIFY_FAILED'          => '验证失败',
    'SMS_SEND_FAILED'        => '短信发送失败',
    'ERROR'                  => '错误',
    'SUCCESS'               => '成功',
    'VERIFY_CODE_SENT'     => '验证码已发送，请注意查收',
    'PHONE_VERIFY_EXPLAIN'  => '为了确保账号安全，请完成手机实名验证',
    'REGISTER_PHONE_VERIFY' => '手机实名验证',
    'REGISTER_PHONE_VERIFY_EXPLAIN' => '请输入您的手机号码并完成验证',
    'PHONE_NUMBER_ALREADY_USED' => '该手机号已被注册，请使用其他手机号',
    'SMS_DAILY_LIMIT_REACHED'   => '该手机号今日发送次数已达上限',
    'SMS_IP_LIMIT_REACHED'      => '当前IP地址今日发送次数已达上限',
]);