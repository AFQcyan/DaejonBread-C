<?php

    namespace src\Controller;

    use src\App\DB;

    class OrderController{
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
            ", [$_POST['store'], user()->id]);
                
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
        function accept(){
            extract($_POST);
            $id = $_POST['id'];
            // 같은 아이디인 주문의 상태를 accept로 바꿉니다.
            DB::fetch("UPDATE deliveries SET state='accept' WHERE id='{$id}'");
            back("주문이 성공적으로 수락되었습니다.");
        }
        function deny(){
            extract($_POST);
            $id = $_POST['id'];
            // 같은 아이디인 주문의 상태를 accept로 바꿉니다.
            DB::fetch("UPDATE deliveries SET state='reject' WHERE id='{$id}'");
            back("주문이 성공적으로 거절되었습니다.");
        }
        function build(){
            $data = $_POST;
            $id = isset($data['id']) ? $data['id'] : '';
            $page =  isset($data['page']) ? $data['page'] : '';


            $list = DB::fetchAll("
            SELECT reviews.*, users.name,
            (SELECT COUNT(likes.id) FROM likes WHERE likes.review_id = reviews.id) as count,
            replies.contents replies

            FROM stores, users, reviews LEFT JOIN replies ON replies.review_id = reviews.id

            WHERE stores.id = reviews.store_id
            AND users.id = reviews.user_id
            AND store_id = ?

            GROUP BY reviews.id
            ORDER BY (SELECT COUNT(likes.id) FROM likes WHERE likes.review_id = reviews.id) DESC;
            ", [$id]);

            echo json_encode(array('result' => $list));
            exit;        
        }
    }

?>