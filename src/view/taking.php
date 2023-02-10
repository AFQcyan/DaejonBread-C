<?php
require_once('./resources/php/lib.php');

$id = $_POST['id']; // 주문의 id
$userId = $_SESSION['user']['id']; // 유저의 id

// 서버 시간을 서울로 바꿉니다.
date_default_timezone_set("Asia/Seoul");

//받은 주문 아이디와 같은 주문의 driver_id를 유저 아이디, taking_at을 NOW()로 변경합니다.
DB::fetch("UPDATE deliveries SET driver_id='{$userId}', state='taking', taking_at=NOW() WHERE id='{$id}'");

back("수락되었습니다.");