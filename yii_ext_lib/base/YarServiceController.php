<?php

/**
 * 基于Yar的服务器端接口，所有使用Yar实现的服务都使用这个接口
 */
class YarServiceController {
    
    /**
     * 返回信息给调用者
     * 
     * @param int $code 消息的代码
     * @param string $msg 消息
     * @param mix $data 返回的数据
     * @return array
     */
    public function response($code = 0, $msg = '', $data = '') {
        return array(
            'code' => $code,
            'msg' => $msg,
            'response' => $data
        );
    }
    
}