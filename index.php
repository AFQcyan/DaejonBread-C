<?php
    session_start();

    date_default_timezone_set('Asia/Seoul');

    define("__ROOT", __DIR__);
    define("__DS", "/");
    define("__SRC" , __ROOT . __DS . 'src');
    define("__VIEWS", __SRC . __DS . 'views');
?>