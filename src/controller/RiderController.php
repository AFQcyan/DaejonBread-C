<?php

namespace src\Controller;

use src\App\DB;

class RiderController{
    function reflect(){
        extract($_POST);
        $id = $_POST['id'];

        // 전달받은 id와 같은 id를 가진 주문을 거절합니다.
        DB::fetch("UPDATE deliveries SET state='reject' WHERE id='{$id}'");

        back("거절되었습니다.");
    }
    function order(){
        echo "<pre>";
        var_dump($_POST);
        echo "</pre>";
            
        $list = explode(' ', $_POST['id-list']);
        var_dump($list);
            
        // 먼저 deliveries 테이블에 방금 받은 정보를 추가합니다.
        DB::fetch("
        INSERT INTO deliveries(store_id, orderer_id, state, order_at)
        VALUES (?,?,'order', NOW());
        ", [$_POST['store'], $_SESSION['user']['id']]);
            
        // 방금 넣은 정보가 들어간 deliveries의 id를 구합니다.
        $storeId = DB::fetch("
        SELECT MAX(deliveries.id) as id FROM deliveries;
        ")->id;
            
        echo "<pre>";
        foreach($list as $id){
            // bread_id가 없다면 추가하지 않습니다.
            if($id == ''){
                continue;
            } else{
                echo $id . " ";
            
                // count와 price 정보를 불러옵니다.
                $count = $_POST['c' . $id] . " ";
                $price = $_POST['p' . $id] . " ";
            
                if($count == 0 || $count = ''){
                    // count가 없거나 0이라면 추가하지 않습니다.
                    continue;
                } else{
                    var_dump([$storeId, $id, $price, $count]);
                    // delivery_items 테이블에 주문한 정보를 넣습니다.
                    DB::fetch("
                    INSERT INTO delivery_items(delivery_id, bread_id, price, cnt)
                    VALUES (?, ?, ?, ?)
                    ", [$storeId, $id, $price, $count]);
                }
            }
        }
        echo "</pre>";
        
        // 주문페이지로 돌아갑니다
        back('주문되었습니다.');
    }
    function infoInit(){
        extract($_POST);

        $id = $_POST['id']; // 유저 아이디
        $transportation = $_POST['transportation']; // 교통수단
        $location = $_POST['location']; // 위치 id

        DB::fetch("UPDATE users SET transportation='{$transportation}', location_id='{$location}' WHERE id='{$id}'");
        back("성공적으로 수정되었습니다.");
    }
    function taking(){
        extract($_POST);
        
        $id = $_POST['id'];
        $user_id = user()->id;

        DB::fetch("UPDATE deliveries SET driver_id = '{$user_id}', state = 'taking' WHERE id = '{$id}'");
        back('성공적으로 수락되었습니다. 안전한 배달 되십시오.');
    }
    function complete(){
        extract($_POST);

        $id = $_POST['id'];
        $user_id = user()->id;

        DB::fetch("UPDATE deliveries SET state = 'complete' WHERE id = '{$id}'");
        back('배달이 완료되었습니다.');
    }
}