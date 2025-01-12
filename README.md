# 野人手机实名认证

这是一个专门为中国大陆用户开发的 phpBB 手机号实名认证扩展。用户在注册时需要验证手机号，并确保每个手机号只能注册一个账号防止恶意注册。

## 功能特点

- 用户注册时必须验证手机号
- 一个手机号只能注册一个账号
- 使用阿里云短信服务发送验证码
- 验证码有效期为5分钟
- 支持中文界面

## 系统要求

- phpBB >= 3.3.0, < 4.0.0@dev
- PHP >= 7.2.0
- PHP CURL 扩展
- 阿里云短信服务账号

## 安装步骤

1. 下载扩展文件
2. 解压到 phpBB 根目录的 `ext/bushcraftcn/phoneverify/` 目录
3. 在管理员控制面板中启用扩展
4. 配置阿里云短信服务参数

## 配置说明

### 阿里云短信服务配置

1. 登录阿里云控制台
2. 开通阿里云短信服务
3. 创建短信签名和模板
4. 获取以下信息：
   - AccessKey ID
   - AccessKey Secret
   - 短信签名
   - 短信模板 CODE

### 扩展配置

1. 进入管理员控制面板
2. 找到"扩展"标签
3. 点击"野人手机实名认证"
4. 填入阿里云短信服务配置信息：
   - 阿里云 AccessKey ID
   - 阿里云 AccessKey Secret
   - 短信签名名称
   - 短信模板 CODE

## 注意事项

1. **安全性**
   - 请妥善保管阿里云 AccessKey 信息
   - 建议定期更换 AccessKey
   - 建议设置短信发送频率限制

2. **短信模板**
   - 模板中必须包含 `${code}` 变量
   - 模板示例：`您的验证码为：${code}，5分钟内有效，请勿泄露给他人。`

3. **费用说明**
   - 短信服务为付费服务
   - 请关注阿里云短信服务的计费规则
   - 建议设置短信用量告警

4. **故障排查**
   - 检查阿里云配置是否正确
   - 查看 PHP 错误日志
   - 确保 CURL 扩展已启用

## 常见问题

1. **验证码发送失败**
   - 检查阿里云配置是否正确
   - 确认短信签名和模板是否审核通过
   - 查看阿里云短信服务控制台的发送记录

2. **无法保存配置**
   - 确保 PHP 有写入权限
   - 检查 phpBB 缓存是否可写

## 技术支持

- 问题反馈：[GitHub Issues](https://github.com/YerenChina/phpbb-phoneverify/issues)
- 作者邮箱：521133238@qq.com
- 项目主页：https://bushcraftcn.com

## 许可证

本扩展遵循 GPL-2.0-only 许可证发布。

## 更新日志

### 1.0.0 (2025-01-12)
- 首次发布
- 支持手机号验证
- 支持阿里云短信服务
- 支持中文界面 