<?php

class BaseActiveRecord extends CActiveRecord {

    /**
     * @var CDbCommand
     */
    private $dbCommand = null;
    protected $t1;

    /**
     * 取得命令组装器
     * @return CDbCommand
     */
    public function getDbCommand() {
        if (!$this->dbCommand instanceof CDbCommand)
            $this->dbCommand = $this->getDbConnection()->createCommand();
        return $this->dbCommand;
    }

    protected function logUrlContent($type, $t1, $func_get_args,$params_type = NULL) {
    }

    public function find($condition = '', $params = array()) {
        if (YII_IS_MONITORING) {$t1 = MonitoringService::getMillisecond();}
        $return = parent::find($condition, $params);
        if (YII_IS_MONITORING) {
            $this->logUrlContent('find',  $t1, func_get_args(),array('condition','params'));
        }
        return $return;
    }

    public function findAll($condition = '', $params = array()) {
        if (YII_IS_MONITORING) {$t1 = MonitoringService::getMillisecond();}
        $return = parent::findAll($condition, $params);
        if (YII_IS_MONITORING) {
            $this->logUrlContent('findAll',  $t1, func_get_args(),array('condition','params'));
        }
        return $return;
    }

    public function findByPk($pk, $condition = '', $params = array()) {
        if (YII_IS_MONITORING) {$t1 = MonitoringService::getMillisecond();}
        $return = parent::findByPk($pk, $condition, $params);
        if (YII_IS_MONITORING) {
            $this->logUrlContent('findByPk',  $t1, func_get_args(),array('pk','condition','params'));
        }
        return $return;
    }

    public function findAllByPk($pk, $condition = '', $params = array()) {
        if (YII_IS_MONITORING) {$t1 = MonitoringService::getMillisecond();}
        $return = parent::findAllByPk($pk, $condition, $params);
        if (YII_IS_MONITORING) {
            $this->logUrlContent('findAllByPk',  $t1, func_get_args(),array('pk','condition','params'));
        }
        return $return;
    }

    public function findByAttributes($attributes, $condition = '', $params = array()) {
        if (YII_IS_MONITORING) {$t1 = MonitoringService::getMillisecond();}
        $return = parent::findByAttributes($attributes, $condition, $params);
        if (YII_IS_MONITORING) {
            $this->logUrlContent('findByAttributes',  $t1, func_get_args(),array('attributes','condition','params'));
        }
        return $return;
    }

    public function findAllByAttributes($attributes, $condition = '', $params = array()) {
        if (YII_IS_MONITORING) {$t1 = MonitoringService::getMillisecond();}
        $return = parent::findAllByAttributes($attributes, $condition, $params);
        if (YII_IS_MONITORING) {
            $this->logUrlContent('findAllByAttributes',  $t1, func_get_args(),array('attributes','condition','params'));
        }
        return $return;
    }

    public function findBySql($sql, $params = array()) {
        if (YII_IS_MONITORING) {$t1 = MonitoringService::getMillisecond();}
        $return = parent::findBySql($sql, $params);
        if (YII_IS_MONITORING) {
            $this->logUrlContent('findBySql',  $t1, func_get_args(),array('sql','params'));
        }
        return $return;
    }

    public function findAllBySql($sql, $params = array()) {
        if (YII_IS_MONITORING) {$t1 = MonitoringService::getMillisecond();}
        $return = parent::findAllBySql($sql, $params);
        if (YII_IS_MONITORING) {
            $this->logUrlContent('findAllBySql',  $t1, func_get_args(),array('sql','params'));
        }
        return $return;
    }

    public function count($condition = '', $params = array()) {
        if (YII_IS_MONITORING) {$t1 = MonitoringService::getMillisecond();}
        $return = parent::count($condition, $params);
        if (YII_IS_MONITORING) {
            $this->logUrlContent('count',  $t1, func_get_args(),array('condition','params'));
        }
        return $return;
    }

    public function countByAttributes($attributes, $condition = '', $params = array()) {
        if (YII_IS_MONITORING) {$t1 = MonitoringService::getMillisecond();}
        $return = parent::countByAttributes($attributes, $condition, $params);
        if (YII_IS_MONITORING) {
            $this->logUrlContent('countByAttributes',  $t1, func_get_args(),array('attributes','condition','params'));
        }
        return $return;
    }

    public function countBySql($sql, $params = array()) {
        if (YII_IS_MONITORING) {$t1 = MonitoringService::getMillisecond();}
        $return = parent::countBySql($sql, $params);
        if (YII_IS_MONITORING) {
            $this->logUrlContent('countBySql',  $t1, func_get_args(),array('sql','params'));
        }
        return $return;
    }

    public function exists($condition = '', $params = array()) {
        if (YII_IS_MONITORING) {$t1 = MonitoringService::getMillisecond();}
        $return = parent::exists($condition, $params);
        if (YII_IS_MONITORING) {
            $this->logUrlContent('exists',  $t1, func_get_args(),array('condition','params'));
        }
        return $return;
    }

    public function updateByPk($pk, $attributes, $condition = '', $params = array()) {
        if (YII_IS_MONITORING) {$t1 = MonitoringService::getMillisecond();}
        $return = parent::updateByPk($pk, $attributes, $condition, $params);
        if (YII_IS_MONITORING) {
            $this->logUrlContent('updateByPk',  $t1, func_get_args(),array('pk','attributes','condition','params'));
        }
        return $return;
    }

    public function update($attributes = null) {
        if (YII_IS_MONITORING) {$t1 = MonitoringService::getMillisecond();}
        $return = parent::update($attributes);
        if (YII_IS_MONITORING) {
            $this->logUrlContent('update',  $t1, func_get_args(),array('attributes'));
        }
        return $return;
    }

    public function insert($attributes = null) {
        if (YII_IS_MONITORING) {$t1 = MonitoringService::getMillisecond();}
        $return = parent::insert($attributes);
        if (YII_IS_MONITORING) {
            $this->logUrlContent('insert',  $t1, func_get_args(),array('attributes'));
        }
        return $return;
    }

//        
//        protected function beforeSave()
//	{
//            if(YII_IS_MONITORING){
//                $this->t1 = MonitoringLogService::getMicrotime();
//            }
//            return true;
//	}
//        
//        protected function afterSave() {
//            if(YII_IS_MONITORING){
//                $save_type = $this->getIsNewRecord() ? 'insert' : 'update';
//                MonitoringLogService::logUrlContent('db','mysql-save',$save_type,$this->t1,$this->getAttributes());
//            }
////            parent::afterSave();
//        }
//        
//        public function beforeDelete() {
//            parent::beforeDelete();
//        }
//
//        public function afterDelete() {
//            parent::afterDelete();
//        }
//        
//        public function beforeFind() {
//            if(YII_IS_MONITORING){
//                $this->t1 = MonitoringLogService::getMicrotime();
//            }
//            return true;
////            return parent::beforeFind();
//            
//        }
//        
//        public function afterFind()
//	{
//                if(YII_IS_MONITORING){
//                    MonitoringLogService::logUrlContent('db','mysql-find','',$this->t1,$this->getAttributes());
//                }
//		return parent::afterFind();
//	}
}
