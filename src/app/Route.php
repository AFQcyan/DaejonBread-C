<?php

namespace src\App;

class Route{
    private static $actions = array();

    public static function init()
    {
        $path = explode("?", $_SERVER['REQUEST_URI']);

        if(count($path) > 1)
        {
            header("location:" . $path[0]);
        }

        $path = $path[0];

        foreach(self::$actions as $request){
            $url = preg_replace('/\//','\\/', $request[0]);
            $url = preg_replace('/\{([^{}]+)\}/', '([^\/]+)', $url);
            $url = '/^' . $url . '$/';

            if(preg_match($url, $path, $result)){
                unset($result[0]);
                $action = explode("@", $request[1]);
                $cont = "src\\Controller\\" . $action[0];
                $cont = new $cont();
                $cont->{$action[1]}(...$result);

                return;
            }
        }

        // if(user()){}else{
        //     redirect('/', '비로그인 사용자의 접근은 제한됩니다.');
        // }
    }

    public static function __callStatic($method, $args){
        $req = strtolower($_SERVER["REQUEST_METHOD"]);

        if($req == $method){
            array_push(self::$actions , $args);
        }
    }
}