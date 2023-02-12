<?php

use src\App\DB;
use src\App\Session;

function view($file_name,$datas = []){
    extract($datas);
    require_once __VIEWS . __DS . 'header.php';
    require_once __VIEWS . __DS . $file_name . '.php';
    require_once __VIEWS . __DS . 'footer.php';
}
// 이스케이프 처리를 하는 함수입니다.
function print_text($str)
{
    echo htmlspecialchars($str);
}
function alert($msg){
    echo "
    <script>alert('{$msg}');</script>
    ";
}
// 알림창을 띄우고 뒤로가는 함수입니다.
function back($str)
{
    alert($str);
    echo"
    <script>window.history.back();</script>";
    exit;
}
function session()
{
	return new Session();
}
function user()
{
    return !empty(session()->get('user',true)[0])?session()->get('user', true)[0]:'';
}
function taking()
{
    extract($_POST);
    // $id = $_POST['id']; // 주문의 id
    // $userId = $_SESSION['user']['id']; // 유저의 id
    
    
    //받은 주문 아이디와 같은 주문의 driver_id를 유저 아이디, taking_at을 NOW()로 변경합니다.
    DB::fetch("UPDATE deliveries SET driver_id='{$userId}', state='taking', taking_at=NOW() WHERE id='{$id}'");
    
    back("수락되었습니다.");
}
function redirect($url, $msg)
{
    alert($msg);
    echo $url;        
    header("location: $url");
}

// function back($msg, $type = 'error')
// {
// 	session()->set($type, $msg);

// 	echo "<script>history.back()</script>";

// 	exit;
// }

