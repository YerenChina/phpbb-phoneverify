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
    'ACP_PHONE_VERIFY'          => '野人手机实名认证',
    'ACP_PHONE_VERIFY_SETTINGS' => '阿里云短信设置',
    'ACP_ALIYUN_SETTINGS'       => '阿里云短信服务配置',
    'ACP_ALIYUN_KEY_ID'         => '阿里云 AccessKey ID',
    'ACP_ALIYUN_KEY_ID_EXPLAIN' => '在阿里云控制台获取的 AccessKey ID',
    'ACP_ALIYUN_KEY_SECRET'     => '阿里云 AccessKey Secret',
    'ACP_ALIYUN_KEY_SECRET_EXPLAIN' => '在阿里云控制台获取的 AccessKey Secret',
    'ACP_ALIYUN_SIGN_NAME'      => '短信签名名称',
    'ACP_ALIYUN_SIGN_NAME_EXPLAIN' => '在阿里云短信服务中申请的短信签名',
    'ACP_ALIYUN_TEMPLATE_CODE'  => '短信模板 CODE',
    'ACP_ALIYUN_TEMPLATE_CODE_EXPLAIN' => '在阿里云短信服务中申请的短信模板 CODE',
    'SETTINGS_SAVED'            => '设置已保存',
]);