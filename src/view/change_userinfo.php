<?php
require_once('./resources/php/lib.php');
// 라이더가 자신의 정보를 바꿀 수 있게 합니다.

$id = $_POST['id']; // 유저 아이디
$transportation = $_POST['transportation']; // 교통수단
$location = $_POST['location']; // 위치 id

// 서버 시간을 서울로 바꿉니다.
date_default_timezone_set("Asia/Seoul");

// 유저의 교통수단, 위치를 바꿉니다
DB::fetch("UPDATE users SET transportation='{$transportation}', location_id='{$location}' WHERE id='{$id}'");
back("변경되었습니다.");