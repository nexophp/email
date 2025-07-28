

## 发送邮件

调用事例

~~~
//发送邮箱验证码
$code = rand(100000, 999999); 
add_mail_template('bind_account', "绑定邮箱", '绑定邮箱验证', '您正在绑定邮箱，您的验证码是<b>{code}</b>,5分钟有效，如非本人请求请忽略。');
cache("bind_account_{$email}", $code, 300);

send_mail('bind_account', $email, [
    'code' =>  $code,
]); 
~~~

> 模板发送邮件

~~~
send_mail($template_code, $address, $replace = [])
~~~

> 添加模板

~~~
add_mail_template($name,$code,$title,$content)
~~~

> 测试模板

~~~
/email/site/code?email=你的邮件地址
~~~




> 原始发送邮件，一般建议使用上面的模板形式发送

~~~
use modules\email\lib\Mail;

Mail::send('to@example.com', 'subject', 'body');
~~~


