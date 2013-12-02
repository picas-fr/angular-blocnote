<?php

// REST api request/response
class RESTapi
{

    protected static $_instance;
    public $response    = array();
    protected $errors   = array();
    protected $warnings = array();
    protected $infos    = array();
    private $dbg        = true;

    public static function getInstance($dbg = REST_DEBUG)
    {
        if (empty(self::$_instance)) {
            $cls = __CLASS__;
            self::$_instance = new $cls($dbg);
        }
        return self::$_instance;
    }

    protected function __construct($dbg = REST_DEBUG) 
    {
        $this->dbg = $dbg;
        $this->_init();
    }

    protected function _init()
    {
        ini_set("always_populate_raw_post_data", "1");
    }

    public function getResponse() 
    {
        if (!empty($this->errors)) {
            $this->response['status'] = 'error';
            $this->response['messages'] = $this->errors;
        } elseif (!empty($this->warnings)) {
            $this->response['status'] = 'warning';
            $this->response['messages'] = $this->warnings;
        } else {
            $this->response['status'] = 'ok';
            $this->response['messages'] = $this->infos;
        }
        return $this->response;
    }
    
    public function getRequest() 
    {
        $args = array(
            'method' => strtolower($_SERVER['REQUEST_METHOD']),
            'action' => isset($_GET['action']) ? $_GET['action'] : 'readAll',
            'model' => isset($_GET['model']) ? $_GET['model'] : null,
            'id' => null,
            'data' => null,
        );
        if (!empty($args['method'])) {
            switch ($args['method']) {
                default: case 'get':
                    $args['data'] = $_GET;
                    $args['id'] = isset($_GET['id']) ? $_GET['id'] : null;
                    if (!empty($_GET['id'])) { $args['action'] = 'read'; }
                    break;
                case 'post':
                    $args['action'] = 'create'; 
                    $put_data = @file_get_contents('php://input');
                    if (strpos($_SERVER['CONTENT_TYPE'], 'json')>0 && !empty($put_data)) {
                        $args['data'] = json_decode($put_data, true);
                    } else {
                        $args['data'] = $_POST;
                    }
                    $args['id'] = isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? $_GET['id'] : null);
                    break;
                case 'put':
                    $args['action'] = 'update';
                    $put_data = @file_get_contents('php://input');
                    if (!empty($put_data)) {
                        $put_vars = json_decode($put_data, true);
                    }
                    $args['data'] = $put_vars;
                    $args['id'] = isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? $_GET['id'] : null);
                    break;
                case 'delete':
                    $args['action'] = 'delete';
                    $args['id'] = isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? $_GET['id'] : null);
                    break;
            }
        }
        return $args;
    }
    
    public function dispatch() 
    {
        if ($this->dbg) {
            $this->response['request'] = "Handling request: "
                ." method:".str_replace("\n", ' ', var_export($_SERVER['REQUEST_METHOD'],1))
                ." query:".str_replace("\n", ' ', var_export($_GET,1))
                ." data:".str_replace("\n", ' ', var_export($_POST,1))
                ." put:".str_replace("\n", ' ', var_export($put_vars,1))
                ." request:".str_replace("\n", ' ', var_export($_REQUEST,1))
                ." content-type:".$_SERVER['CONTENT_TYPE']
                ;
        }
        $args = $this->getRequest();
        if (!empty($args['model']) && class_exists($args['model'])) {
            $model_cls = $args['model'];
            $model = new $model_cls;
        } elseif (!empty($args['model'])) {
            throw new Exception("Unknown model '{$args['model']}'!");
        } else {
            throw new Exception("Model not defined!");
        }
        if (!empty($args['action']) && method_exists($model, $args['action'])) {
            $reflect = new \ReflectionMethod($model, $args['action']);
            if ($this->dbg) {
                $this->response['args'] = "Organized arguments are: "
                    .str_replace("\n", ' ', var_export($args,1));
            }
            $args_def = array();
            foreach ($reflect->getParameters() as $_param) {
                $arg_index = $_param->getName();
                $arg_pos = $_param->getPosition();
                if (isset($args[$arg_index])) {
                    $args_def[$arg_pos] = $args[$arg_index];
                }
            }
            if ($this->dbg) {
                $this->response['debug'] = "Calling action '{$args['action']}' with arguments: "
                    .str_replace("\n", ' ', var_export($args_def,1));
            }
            try {
                $response = call_user_func_array(array($model, $args['action']), $args_def);
                if (is_array($response)) {
                    $this->response['data'] = $response;
                } elseif (is_string($response)) {
                    $this->info($response);
                } elseif (is_bool($response) && $response!==true) {
                    $this->warning('Unknown error');
                }
            } catch (Exception $e) {
                throw $e;
            }
        } else {
            throw new Exception("Unknown action '{$args['action']}' in model '{$args['model']}'!");
        }
        return $this;
    }

    public function error($str)
    {
        $this->errors[] = (is_object($str) && ($str instanceof Exception)) ? $str->getMessage() : $str;
        return $this;
    }

    public function warning($str)
    {
        $this->warnings[] = (is_object($str) && ($str instanceof Exception)) ? $str->getMessage() : $str;
        return $this;
    }

    public function info($str)
    {
        $this->infos[] = $str;
        return $this;
    }

}
