<?php 
class BaseMemCache extends CMemCache{
    
    public function init() {
        $t1 = MonitoringService::getMillisecond();
        parent::init();
        
        $servers = $this->getServers();
        $host = array_reduce($servers, function($res, $item) {
            return $res . '|' . $item->host . ':' . $item->port;
        });
        
        MonitoringService::getInstance()->log(MonitoringService::LOG_TYPE_MEMCACHE, array(
            'et' => MonitoringService::diffMillisecond(MonitoringService::getMillisecond(), $t1),
            'type' => 'CONNECT',
            'content' => $host
        ));
    }
    
    public function get($id) {
        $t1 = MonitoringService::getMillisecond();
        $return = parent::get($id);
        MonitoringService::getInstance()->log(MonitoringService::LOG_TYPE_MEMCACHE, array(
            'et' => MonitoringService::diffMillisecond(MonitoringService::getMillisecond(), $t1),
            'type' => __METHOD__,
            'content' => $id
        ));
        return $return;
    }
    
    public function set($id, $value, $expire = 0, $dependency = null) {
        $t1 = MonitoringService::getMillisecond();
        $ret = parent::set($id, $value, $expire, $dependency);
        
        MonitoringService::getInstance()->log(MonitoringService::LOG_TYPE_MEMCACHE, array(
            'et' => MonitoringService::diffMillisecond(MonitoringService::getMillisecond(), $t1),
            'type' => __METHOD__,
            'content' => $id,
            'success' => $ret
        ));
        
        return $ret;
    }
    
    public function add($id, $value, $expire = 0, $dependency = null) {
        $return = parent::add($id, $value, $expire, $dependency);
        return $return;
    }
    
    public function delete($id) {
        if (YII_IS_MONITORING) {$t1 = MonitoringService::getMillisecond();}
        $return = parent::delete($id);
        return $return;
    }
}