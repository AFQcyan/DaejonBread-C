<?php

namespace src\Controller;
use src\App\DB;
class ReserveController{
    function insert(){
        extract($_POST);
        $date = date("Y-m-d h:i:s", strtotime($_POST['date'] . " " . $_POST['time']));

        // 방금 구한 date와 정보들을 이용하여 예약주문을 추가합니다.
        DB::fetch("
        INSERT INTO `reservations`(`store_id`, `user_id`, `request_at`, `reservation_at`, `state`)
        VALUES (?, ?, NOW(), ?, 'order')
        ", [$_POST['store'], user()->id, $date]);

        back("예약 주문 되었습니다");
    }
    function reflect(){
        $id = $_POST['id'];

        // 전달받은 id와 같은 id를 가진 에약주문을 거절합니다.
        DB::fetch("UPDATE reservations SET state='reject' WHERE id='{$id}'");

        back("거절되었습니다.");
    }
}