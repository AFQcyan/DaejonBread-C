<?php
require_once('./resources/php/lib.php');

// breads의 id과 할인률을 받습니다.
$id = $_POST['id']; // 아이디
$number = $_POST['number']; // 할인률

// 서버 시간을 서울로 바꿉니다.
date_default_timezone_set("Asia/Seoul");

// 상품의 할인률을 변경합니다.
DB::fetch("UPDATE breads SET sale='{$number}' WHERE id='{$id}'");

back("변경되었습니다.");