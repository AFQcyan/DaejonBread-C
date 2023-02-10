<?php
require_once('./resources/php/lib.php');
// deliveries의 id 컬럼을 받습니다.
$id = $_POST['id'];

// 서버 시간을 서울로 바꿉니다.
date_default_timezone_set("Asia/Seoul");

// 같은 아이디인 주문의 상태를 accept로 바꿉니다.
DB::fetch("UPDATE deliveries SET state='accept' WHERE id='{$id}'");

header('location: ./mypage.php');