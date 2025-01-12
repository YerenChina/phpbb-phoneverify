<?php

namespace bushcraftcn\phoneverify\service;

class sms_sender
{
    /** @var \phpbb\config\config */
    protected $config;

    public function __construct(\phpbb\config\config $config)
    {
        $this->config = $config;
    }

    public function send_verify_code($phone_number, $verify_code)
    {
        // 检查阿里云配置是否完整
        if (empty($this->config['phoneverify_aliyun_access_key_id']) ||
            empty($this->config['phoneverify_aliyun_access_key_secret']) ||
            empty($this->config['phoneverify_aliyun_sign_name']) ||
            empty($this->config['phoneverify_aliyun_template_code'])) {
            error_log('Aliyun SMS configuration is incomplete');
            return ['error' => '短信服务尚未配置，请联系管理员'];
        }

        try {
            $accessKeyId = $this->config['phoneverify_aliyun_access_key_id'];
            $accessKeySecret = $this->config['phoneverify_aliyun_access_key_secret'];
            $signName = htmlspecialchars_decode($this->config['phoneverify_aliyun_sign_name'], ENT_QUOTES);
            $templateCode = $this->config['phoneverify_aliyun_template_code'];

            // 构建请求参数
            $params = [
                'PhoneNumbers' => $phone_number,
                'SignName' => $signName,
                'TemplateCode' => $templateCode,
                'TemplateParam' => json_encode(['code' => $verify_code]),
            ];

            // 生成签名
            $params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
            $params['SignatureNonce'] = uniqid();
            $params['AccessKeyId'] = $accessKeyId;
            $params['SignatureMethod'] = 'HMAC-SHA1';
            $params['SignatureVersion'] = '1.0';
            $params['Version'] = '2017-05-25';
            $params['Action'] = 'SendSms';
            $params['RegionId'] = 'cn-hangzhou';
            $params['Format'] = 'JSON';

            // 按参数名称排序
            ksort($params);

            // 构建签名字符串
            $canonicalizedQueryString = '';
            foreach ($params as $key => $value) {
                $canonicalizedQueryString .= '&' . $this->percentEncode($key) . '=' . $this->percentEncode($value);
            }
            $stringToSign = 'POST&%2F&' . $this->percentEncode(substr($canonicalizedQueryString, 1));
            
            // 计算签名
            $signature = base64_encode(hash_hmac('sha1', $stringToSign, $accessKeySecret . '&', true));
            $params['Signature'] = $signature;

            // 发送请求
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://dysmsapi.aliyuncs.com/');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);

            // 检查发送结果
            if (isset($result['Code']) && $result['Code'] === 'OK') {
                return true;
            } else {
                error_log('Aliyun SMS error: ' . json_encode($result));
                return false;
            }
        } catch (\Exception $e) {
            error_log('SMS send error: ' . $e->getMessage());
            return false;
        }
    }

    private function percentEncode($string)
    {
        $result = urlencode($string);
        $result = str_replace(['+', '*'], ['%20', '%2A'], $result);
        $result = preg_replace('/%7E/', '~', $result);
        return $result;
    }
}