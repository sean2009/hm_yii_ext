<?php

namespace {

    use yii_ext_lib\extensions\yiioracledb\EOracleLink;

if (!class_exists('PDO', false)) {

        class PDO {

            const FETCH_ASSOC = 1;
            const FETCH_BOTH = 3;

        }

    }

    class EOracleDB extends CDbConnection {

        /**
         *
         * @var yii_ext_lib\extensions\yiioracledb\EOracleLink
         */
        private $_link;
        private $_active = false;
        private $_schema;
        private $_transaction;
        public $persistent = false;
        public $driverMap = array(
            'pgsql' => 'CPgsqlSchema', // PostgreSQL
            'mysqli' => 'CMysqlSchema', // MySQL
            'mysql' => 'CMysqlSchema', // MySQL
            'sqlite' => 'CSqliteSchema', // sqlite 3
            'sqlite2' => 'CSqliteSchema', // sqlite 2
            'mssql' => 'CMssqlSchema', // Mssql driver on windows hosts
            'dblib' => 'CMssqlSchema', // dblib drivers on linux (and maybe others os) hosts
            'sqlsrv' => 'CMssqlSchema', // Mssql
            'oci8' => 'yii_ext_lib\extensions\yiioracledb\COciSchema', // Oracle driver
        );
        public $charset = 'utf8';
        
        public function setActive($value) {
            if ($value != $this->_active) {
                if ($value) {
                    $this->open();
                } else {
                    $this->close();
                }
            }
        }

        public function beginTransaction() {
            Yii::trace('Starting transaction', 'system.db.CDbConnection');
            $this->setActive(true);
            $this->_link->beginTransaction();
            $this->_transaction = new CDbTransaction($this);

            return $this->_transaction;
        }

        public function getActive() {
            return $this->_active;
        }

        public function getDriverName() {
            return 'oci8';
        }

        public function open() {
            $monitor = MonitoringService::getInstance();
            if (!$this->_link) {
                try {
                    $t1 = MonitoringService::getMillisecond();
                    if ($this->persistent) {
                        $link = oci_pconnect($this->username, $this->password, $this->connectionString, $this->charset);
                    } else {
                        $link = oci_connect($this->username, $this->password, $this->connectionString, $this->charset);
                    }
                    $this->_link = new EOracleLink($link);
                    $this->_active = true;
                    
                    $monitor->log(MonitoringService::LOG_TYPE_DB, array(
                        'et' => MonitoringService::diffMillisecond(MonitoringService::getMillisecond(), $t1),
                        'type' => 'CONNECT',
                        'content' => $this->connectionString
                    ));
                } catch (CDbException $e) {
                    if (YII_DEBUG) {
                        throw new CDbException('EOracleConnection failed to open the DB connection: ' .
                        $e->getMessage(), (int) $e->getCode(), $e->errorInfo);
                    } else {
                        throw new CDbException('EOracleConnection failed to open the DB connection: ', (int) $e->getCode(), $e->errorInfo);
                    }
                }
            }
        }

        public function close() {
            $this->_link = null;
            $this->_active = false;
        }
        
        public function getSchema() {
            if ($this->_schema) {
                return $this->_schema;
            } else {
                $this->_schema = Yii::createComponent($this->driverMap[$this->getDriverName()], $this);
                return $this->_schema;
            }
        }

        public function getPdoInstance() {
            return $this->_link;
        }

        public function getPdoType($type) {
            static $map = array
                (
                'boolean' => SQLT_INT,
                'integer' => SQLT_INT,
                'string' => SQLT_CHR,
                'resource' => SQLT_BLOB,
                'NULL' => SQLT_CHR
            );
            return isset($map[$type]) ? $map[$type] : SQLT_CHR;
        }

    }

}

namespace yii_ext_lib\extensions\yiioracledb {

    class EOracleLink {

        private $_link;
        private $_transaction = false;

        public function __construct($link) {
            $this->_link = $link;
        }

        public function prepare($sql) {
            return new EOracleStatement($this, $sql);
        }

        public function beginTransaction() {
            $this->_transaction = true;
        }

        public function isTransaction() {
            return $this->_transaction;
        }

        public function getOciConnection() {
            return $this->_link;
        }

        public function commit() {
            oci_commit($this->_link);
            $this->_transaction = false;
        }

        public function rollback() {
            oci_rollback($this->_link);
            $this->_transaction = false;
        }

    }

    class EOracleStatement {

        private $_state;
        private $_fetchModel;
        private $_ociType;
        private $startTime;
        private $_monitor;
        private $_sql;
        private $_paramLog;
        /**
         *
         * @var EOracleLink 
         */
        private $_db;

        public function __construct($conn, $sql) {
            $this->startTime = \MonitoringService::getMillisecond();
            $this->_monitor = \MonitoringService::getInstance();
            $this->_sql = $sql;
            
            $this->_db = $conn;
            $this->_state = oci_parse($this->_db->getOciConnection(), $this->_sql);
            $this->_ociType = oci_statement_type($this->_state);
        }

        public function getPdoType($type) {
            static $map = array
            (
                'boolean' => SQLT_INT,
                'integer' => SQLT_INT,
                'string' => SQLT_CHR,
                'resource' => SQLT_BLOB,
                'NULL' => SQLT_CHR
            );
            return isset($map[$type]) ? $map[$type] : SQLT_CHR;
        }

        public function execute($params = array()) {
            if($params){
                foreach($params as $name => $v){
                    $this->bindValue($name, $v, $this->getPdoType(gettype($v)));
                }
            }
            oci_execute($this->_state, $this->_db->isTransaction() ? OCI_DEFAULT : OCI_COMMIT_ON_SUCCESS);
            
            $this->_monitor->log(\MonitoringService::LOG_TYPE_DB, array(
                'et' => \MonitoringService::diffMillisecond(\MonitoringService::getMillisecond(), $this->startTime),
                'type' => $this->_ociType,
                'content' => $this->_sql.'. Bound with ' .var_export($this->_paramLog,true)
            ));
        }

        public function setFetchMode($model) {
            $this->_fetchModel = $model;
        }

        public function fetchAll() {
            $data = array();
            while ($row = oci_fetch_assoc($this->_state)) {
                $data[] = array_change_key_case($row, CASE_LOWER);
                foreach ($data as &$val) {
                    $val = $this->lob($val);
                }
            }
            
            return $data;
        }

        public function fetch() {
            $data = oci_fetch_assoc($this->_state);
            if (is_array($data)) {
                $data = array_change_key_case($data, CASE_LOWER);
                foreach ($data as &$val) {
                    $val = $this->lob($val);
                }
            }

            return $data;
        }

        /**
         * lob对象内容的获取
         * 
         * @param lob $content
         * @return string
         */
        private function lob($content) {
            return (is_object($content)) ? $content->load() : $content;
        }

        public function fetchColumn() {
            $row = oci_fetch_row($this->_state);
            return array_shift($row);
        }

        public function closeCursor() {
            oci_free_cursor($this->_state);
        }

        public function bindValue($name, $value, $type, $length = -1) {
            $this->_paramLog[$name] = $value;
            oci_bind_by_name($this->_state, $name, $value, $length, $type);
        }

        public function bindParam($name, &$value, $type, $length) {
            $this->_paramLog[$name] = &$value;
            oci_bind_by_name($this->_state, $name, $value, $length, $type);
        }

        public function rowCount() {
            return oci_num_rows($this->_state);
        }

    }

    class COciCommandBuilder extends \COciCommandBuilder {

        public function createInsertCommand($table, $data) {
            if(is_string($table->primaryKey)){
                $data[$table->primaryKey] = new \CDbExpression("{$table->sequenceName}.nextval");
            } elseif (is_array($table->primaryKey)) {
                throw new \CDbException("your primary key has more column");
            }
            
            $this->ensureTable($table);
            $fields = array();
            $values = array();
            $placeholders = array();
            $i = 0;
            foreach ($data as $name => $value) {
                if (($column = $table->getColumn($name)) !== null && ($value !== null || $column->allowNull)) {
                    $fields[] = $column->rawName;
                    if ($value instanceof \CDbExpression) {
                        $placeholders[] = $value->expression;
                        foreach ($value->params as $n => $v)
                            $values[$n] = $v;
                    } else {
                        $placeholders[] = self::PARAM_PREFIX . $i;
                        $values[self::PARAM_PREFIX . $i] = $column->typecast($value);
                        $i++;
                    }
                }
            }

            $sql = "INSERT INTO {$table->rawName} (" . implode(', ', $fields) . ') VALUES (' . implode(', ', $placeholders) . ')';

            if (is_string($table->primaryKey) && ($column = $table->getColumn($table->primaryKey)) !== null && $column->type !== 'string') {
                $sql.=' RETURNING ' . $column->rawName . ' INTO :RETURN_ID';
                $command = $this->getDbConnection()->createCommand($sql);
                $command->bindParam(':RETURN_ID', $this->returnID, SQLT_INT, 11);
            } else
                $command = $this->getDbConnection()->createCommand($sql);

            foreach ($values as $name => $value)
                $command->bindValue($name, $value);

            return $command;
        }

    }

    class COciSchema extends \COciSchema {

        public function loadTable($name) {
            $table = parent::loadTable(strtoupper($name));
            $table->sequenceName = "seq_{$name}_" . $table->primaryKey;

            return $table;
        }

        protected function findColumns($table) {
            $tableName = $table->name;

            $sql = <<<EOD
SELECT a.column_name, a.data_type ||
    case
        when data_precision is not null
            then '(' || a.data_precision ||
                    case when a.data_scale > 0 then ',' || a.data_scale else '' end
                || ')'
        when data_type = 'DATE' then ''
        when data_type = 'NUMBER' then ''
        else '(' || to_char(a.data_length) || ')'
    end as data_type,
    a.nullable, a.data_default,
    (   SELECT D.constraint_type
        FROM ALL_CONS_COLUMNS C
        inner join ALL_constraints D on D.OWNER = C.OWNER and D.constraint_name = C.constraint_name
        WHERE C.OWNER = B.OWNER
           and C.table_name = B.object_name
           and C.column_name = A.column_name
           and D.constraint_type = 'P') as Key,
    com.comments as column_comment
FROM ALL_TAB_COLUMNS A
inner join ALL_OBJECTS B ON b.owner = a.owner and ltrim(B.OBJECT_NAME) = ltrim(A.TABLE_NAME)
LEFT JOIN user_col_comments com ON (A.table_name = com.table_name AND A.column_name = com.column_name)
WHERE (b.object_type = 'TABLE' or b.object_type = 'VIEW')
	and b.object_name = '{$tableName}'
ORDER by a.column_id
EOD;
            $command = $this->getDbConnection()->createCommand($sql);

            if (($columns = $command->queryAll()) === array()) {
                return false;
            }

            foreach ($columns as $column) {
                $c = $this->createColumn($column);

                $table->columns[$c->name] = $c;
                if ($c->isPrimaryKey) {
                    if ($table->primaryKey === null)
                        $table->primaryKey = $c->name;
                    elseif (is_string($table->primaryKey))
                        $table->primaryKey = array($table->primaryKey, $c->name);
                    else
                        $table->primaryKey[] = $c->name;
                    $table->sequenceName = '';
                    $c->autoIncrement = true;
                }
            }
            return true;
        }

        public function quoteColumnName($name) {
            return parent::quoteColumnName(strtolower($name));
        }

        public function quoteSimpleTableName($name) {
            return $name;
        }

        public function quoteSimpleColumnName($name) {
            return $name;
        }

        public function createColumn($column) {
            $column = array_change_key_case($column, CASE_UPPER);

            $c=new COciColumnSchema();
            $c->name=$column['COLUMN_NAME'];
            $c->rawName=$this->quoteColumnName($c->name);
            $c->allowNull=$column['NULLABLE']==='Y';
            $c->isPrimaryKey=strpos($column['KEY'],'P')!==false;
            $c->isForeignKey=false;
            $c->init($column['DATA_TYPE'],$column['DATA_DEFAULT']);
            $c->comment=$column['COLUMN_COMMENT']===null ? '' : $column['COLUMN_COMMENT'];
            
            $c->name = strtolower($c->name);

            return $c;
        }

        protected function findConstraints($table) {
            $sql = <<<EOD
		SELECT D.constraint_type, C.COLUMN_NAME, C.position, D.r_constraint_name,
                E.table_name as table_ref, f.column_name as column_ref,
            	C.table_name
        FROM ALL_CONS_COLUMNS C
        inner join ALL_constraints D on D.OWNER = C.OWNER and D.constraint_name = C.constraint_name
        left join ALL_constraints E on E.OWNER = D.r_OWNER and E.constraint_name = D.r_constraint_name
        left join ALL_cons_columns F on F.OWNER = E.OWNER and F.constraint_name = E.constraint_name and F.position = c.position
        WHERE C.table_name = '{$table->name}'
           and D.constraint_type <> 'P'
        order by d.constraint_name, c.position
EOD;
            $command = $this->getDbConnection()->createCommand($sql);
            foreach ($command->queryAll() as $row) {
                if ($row['constraint_type'] === 'R') {   // foreign key
                    $name = $row["column_name"];
                    $table->foreignKeys[$name] = array($row["table_ref"], $row["column_ref"]);
                    if (isset($table->columns[$name]))
                        $table->columns[$name]->isForeignKey = true;
                }
            }
        }
        
        public function createCommandBuilder() {
            return new COciCommandBuilder($this);
        }

    }
    
    class COciColumnSchema extends \COciColumnSchema {
        
        public function typecast($value) {
            $value = parent::typecast($value);
            if ($value != null) {
                switch ($this->dbType) {
                    case 'DATE':
                        $value = new \CDbExpression($value);
                        break;
                }
            }
            
            return $value;
        }
    
    }
}