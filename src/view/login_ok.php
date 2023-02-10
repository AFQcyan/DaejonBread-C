<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/resources/php/lib.php");

// 아이디, 비밀번호가 전달되지 않았다면 로그인하지 않습니다.
if(!isset($_POST['id'])){
    back('아이디 혹은 비밀번호를 다시 확인해주세요.');
    exit;
}
if(!isset($_POST['pw'])){
    back('아이디 혹은 비밀번호를 다시 확인해주세요.');
    exit;
}

$id = $_POST['id'];
$pw = $_POST['pw'];

// 유저 정보를 불러 옵니다
$info = DB::fetch("SELECT * FROM users WHERE id = '{$id}'");

// 없는 아이디 이거나 비밀번호가 틀리다면 뒤로 갑니다.
if(!$info){ 
    back('아이디 혹은 비밀번호를 다시 확인해주세요.');
    exit;
}
if($info->pw != $pw){
    back('아이디 혹은 비밀번호를 다시 확인해주세요.');
    exit;
}

// 모두 맞다면 세션을 설정해주고 돌아갑니다.
$_SESSION['user'] = ['id'=>$id, 'pw'=>$pw, 'type'=>$info->type, 'trans'=>$info->transportation, 'name'=>$info->name, 'location'=>$info->location_id];
back('로그인 되었습니다.');