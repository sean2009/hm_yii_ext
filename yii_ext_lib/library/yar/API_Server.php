<?php

namespace yii_ext_lib\yar {

    class Server {

        private $obj;

        public function __construct($obj) {
            $this->obj = $obj;
        }

        public function handle() {
            $request = json_decode(file_get_contents('php://input'), true);
            echo json_encode($this->obj->request($request));
        }
    }

}

namespace {

    class API_Server {

        private static $instance = null;
        private $server;

        /**
         * @param config $configfile 配置文件路径
         * @return API_Server
         */
        public static function getInstance($configfile) {
            if (!self::$instance) {
                self::$instance = new self($configfile);
            }
            return self::$instance;
        }

        public function run() {
            if (class_exists('Yar_Server', false)) {
                $this->server = new Yar_Server($this);
            } else {
                $this->server = new yii_ext_lib\yar\Server($this);
            }
            
            $this->server->handle();
        }

        public function request($params = '') {
            // 是否是从有监控的客户端来的请求
            $fromMonitoringClient = isset($_GET['monitoring_request_id']);
            $return = null;
            if (func_num_args() == 1) {
                $params = func_get_arg(0);
            } else {
                $params = func_get_args();
            }
            
            $r = explode('/', $params[0]);
            $controller = ucfirst($r[0]) . 'Controller';
            $action = 'action' . ucfirst($r[1]);
            
            Yii::import("application.controllers.$controller");
            
            $controllerClass = new $controller();
            $ex = null;
            unset($params[0]);
            
            try {
                $return = call_user_func(array($controllerClass, $action), $params[1][0]);
            } catch (Exception $cex) {
                if ($fromMonitoringClient) {
                    $ex = new yii_ext_lib\yar\YarException($cex->getMessage() . "\n" . $cex->getFile() . "\n" . $cex->getLine(), $cex->getCode());
                }
            }
            if ($fromMonitoringClient) {
                $return = array(
                    'exception' => serialize($ex),
                    'return' => $return
                );
            }
             
            return $return;
        }
        
        private function __construct($configfile) {
            $path = dirname(__FILE__);
            define('YII_IS_CLIENT',false);
            include substr($path, 0, strpos($path, 'library')) . 'bootStrap.php';
            require_once dirname(__FILE__) . '/API_Base.php';

            Yii::createWebApplication($configfile);
            header_remove('Content-Encoding');
            
            // 读取接口文档
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html>
                    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                    <style type="text/css">
                    .comment{margin-left: 20px;background: white;}
                    ul li {border: 1px solid #333366;margin: 10px 0;}
                    ul li div.class-comment {margin-left: 0;}
                    ul li.open ol {display:block;}
                    ul li.close ol {display:none;}
                    ul li h3 {background: #333366;color: white;padding:5px 10px;cursor:pointer;margin:0;}
                    ol li {background: #eeeeff;padding: 10px;margin-right:3px;}
                    </style>
                    <script type="text/javascript">
                        function toggleException(obj) {
                            var exception = obj.parentNode;
                            if (exception.className.indexOf("close") != -1) {
                                exception.className = "open";
                            } else {
                                exception.className = "close";
                            }
                        }
                    </script>
                    </head><body><h1>接口描述（点击查看详情）</h1>';
                $ul = '<ul>';
                foreach (glob(Yii::app()->basePath . "/controllers/*.php") as $file) {
                    $pathInfo = pathinfo(realpath($file));
                    include_once  $file;
                    if (class_exists($pathInfo['filename'])) {
                        $reflectionClass = new ReflectionClass($pathInfo['filename']);
                        if (!$reflectionClass->isSubclassOf('YarServiceController')) {
                            continue;
                        }
                        
                        $li = '<li class="close"><h3 onclick="toggleException(this)" class="class-name">' . str_replace('Controller', '', lcfirst($reflectionClass->getName())) .'</h3>' . '<div class="class-comment">' . str_replace(array("\r\n", "\n"), '<br/>', $reflectionClass->getDocComment()) . '</div>';
                        
                        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
                        $pMethods = $reflectionClass->getParentClass()->getMethods(ReflectionMethod::IS_PUBLIC|ReflectionMethod::IS_PROTECTED);
                        $methods = array_diff($methods, $pMethods);
                        
                        $sub = '<ol>';
                        foreach ($methods as $m) {
                            $sub .= '<li><div class="comment">' . str_replace(array("\r\n", "\n"), '<br/>', $m->getDocComment()) . '</div><b>' . lcfirst(substr($m->getName(), 6)) . '</b>' . '</li>';
                        }
                        $sub .= '</ol>';
                        
                        $li  .= "$sub</li>";
                        $ul .= $li;
                    }
                }
                $ul .= '</ul>';
                $html .= "$ul</body></html>";
                exit($html);
            }
        }

        private function __clone() {
            
        }

    }

}