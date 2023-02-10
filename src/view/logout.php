<?php
require_once("./resources/php/lib.php");
// 세션을 unset 시킴으로써 로그아웃 시킵니다.
unset($_SESSION['user']);

back('로그아웃 되었습니다.');