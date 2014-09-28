<?php

require_once dirname(__FILE__) . '/SQSBaseService.php';

/**
 * 消息服务API，主要是发短信和发邮件
 */
class MessageService extends SQSBaseService {

    const QUEUE_NAME_SMS = 'message_sms';
    const QUEUE_NAME_EMAIL = 'message_email';

    /**
     * 发送短信
     * 
     * @param mix $mobiles 要发送的短信的手机号码，当要发送多个的时候，使用数组做为参数
     * @param string $content 发送的内容
     * @param string $send_type 发送消息的类型
     * @param int $max 单个手机号码最多每天发送的次数
     */
    public static function sendSms($mobiles, $content, $send_type, $max = 12) {
        $sqsClient = self::getSQSConnection();

        return $sqsClient->put(self::QUEUE_NAME_SMS, json_encode(array(
                'mobiles' => $mobiles,
                'content' => $content,
                'send_type' => $send_type,
                'max' => $max
        )));
    }

    /**
     * 发送邮件
     * 
     * @param string $mail_title 邮件标题
     * @param string $mail_content 邮件内容
     * @param string $mail_to 接收人
     */
    public static function sendEmail($mail_title, $mail_content, $mail_to) {
        $sqsClient = self::getSQSConnection();

        return $sqsClient->put(self::QUEUE_NAME_EMAIL, json_encode(array(
                'mail_title' => $mail_title,
                'mail_content' => $mail_content,
                'mail_to' => $mail_to
        )));
    }

}
