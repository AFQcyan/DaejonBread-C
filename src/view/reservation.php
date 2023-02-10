<?php
require_once('./resources/php/lib.php');
// date와 time을 받아서 date로 변환합니다.
$date = date("Y-m-d h:i:s", strtotime($_POST['date'] . " " . $_POST['time']));

// 방금 구한 date와 정보들을 이용하여 예약주문을 추가합니다.
DB::fetch("
INSERT INTO `reservations`(`store_id`, `user_id`, `request_at`, `reservation_at`, `state`)
VALUES (?, ?, NOW(), ?, 'order')
", [$_POST['store'], $_SESSION['user']['id'], $date]);

back("예약 주문 되었습니다");