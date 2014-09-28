<?php
/*
	mysql 使用
	添加了应用监控相关代码
*/
namespace {

    /**
     * 有监控的数据库连接
     */
    class EDbConnection extends CDbConnection {
        
        public $pdoClass = 'yii_ext_lib\extensions\yiipdo\PDO';
        
        public function init() {
            parent::init();
        }

        public function open() {
            $t1 = MonitoringService::getMillisecond();
            $monitor = MonitoringService::getInstance();
            
            parent::open();
            
            $monitor->log(MonitoringService::LOG_TYPE_DB, array(
                'et' => MonitoringService::diffMillisecond(MonitoringService::getMillisecond(), $t1),
                'type' => 'CONNECT',
                'content' => $this->connectionString
            ));
        }
        
    }

}

namespace yii_ext_lib\extensions\yiipdo {
        
    class PDO extends \PDO {
        
        public function prepare($statement, $driver_options = array()) {
            $stat = new PDOStatement(parent::prepare($statement, $driver_options));
            
            return $stat;
        }
        
    }
    
    class PDOStatement {
        
        /**
         *
         * @var \PDO
         */
        private $_state;
        private $_sqlType;
        private $startTime;
        private $_sql;

        public function __construct($state = '') {
            $this->startTime = \MonitoringService::getMillisecond();
            $this->_state = $state;
            
            $this->_sql = trim($this->_state->queryString);
            $this->_sqlType = strtoupper(substr($this->_sql, 0, strpos($this->_sql, ' ')));
        }
        
        public function __call($name, $arguments) {
            $ret = call_user_func_array(array($this->_state, $name), $arguments);
            
            if ($name == 'execute') {
                \MonitoringService::getInstance()->log(\MonitoringService::LOG_TYPE_DB, array(
                    'et' => \MonitoringService::diffMillisecond(\MonitoringService::getMillisecond(), $this->startTime),
                    'type' => $this->_sqlType,
                    'content' => $this->_sql
                ));    
            }
            
            return $ret;
        }
        
        public function __get($name) {
            return $this->_state->$name;
        }
        
    }

}

