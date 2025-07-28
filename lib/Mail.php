<?php
/**
 * 邮件
 * @author sunkangchina <68103403@qq.com>
 * @license MIT <https://mit-license.org/>
 * @date 2025
 */
namespace modules\email\lib;

class Mail
{
    /**
     * 发送模板邮件
     */
    public static function sendByTemplate($code, $address, $replace = [])
    {
        $template = db_get_one('email_template', "*", [
            'code' => $code,
        ]);
        if (!$template) {
            return false;
        }
        $subject = $template['subject'];
        $body = $template['content'];
        foreach ($replace as $key => $value) {
            $subject = str_replace('{' . $key . '}', $value, $subject);
            $body = str_replace('{' . $key . '}', $value, $body);
        }
        \lib\Mail::send([
            'to'     => $address,
            'subject' => $subject,
            'html'   => $body,
        ]);
        return true;
    }
    /**
     * 发送邮件
     */
    public static function send($to, $subject, $body)
    {
        return \lib\Mail::send([
            'to'     => $to,
            'subject' => $subject,
            'html'   => $body,
        ]);
    }
}
