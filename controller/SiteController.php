<?php
/**
 * 邮件
 * @author sunkangchina <68103403@qq.com>
 * @license MIT <https://mit-license.org/>
 * @date 2025
 */

namespace modules\email\controller;

use \modules\email\lib\Mail;

class SiteController extends \core\AppController
{
    /**
     * 发送测试邮件
     */
    public function actionTest()
    {
        if (!$this->uid || $this->user_info['tag'] != 'admin') {
            json_error(['msg' => lang('无法测试邮件发送功能，如有疑问请联系管理员')]);
        }

        $data = $this->post_data;
        $to = $data['email'] ?? '';
        $subject = $data['subject'] ?? '';
        $content = $data['content'] ?? '';

        if (empty($to) || empty($subject) || empty($content)) {
            json_error(['msg' => lang('请填写完整信息')]);
        }

        try {
            Mail::send($to, $subject, $content);
            json_success(['msg' => lang('测试邮件发送成功')]);
        } catch (\Exception $e) {
            json_error(['msg' => $e->getMessage()]);
        }
    }

    /**
     * 发送模板测试邮件
     */
    public function actionCode()
    {
        if (!$this->uid || $this->user_info['tag'] != 'admin') {
            json_error(['msg' => lang('无法测试邮件发送功能，如有疑问请联系管理员')]);
        }
        $code = 'login';
        $email = g('email');
        if (!$email) {
            json_error(['msg' => lang('请填写邮箱')]);
        }
        add_mail_template('code', "登录", '登录验证', '您的验证码是{code},5分钟有效，如非本人请求请忽略。');
        send_mail($code, $email, [
            'code' =>  mt_rand(1000, 9999),
        ]);
        json_success(['msg' => lang('测试邮件发送成功')]);
    }
}
