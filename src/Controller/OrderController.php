<?php

    namespace src\Controller;

    use src\App\DB;

    class OrderController{
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
    }

?>