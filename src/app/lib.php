<?php

namespace src\App;

use src\App\DB;

class Lib{
    // 이스케이프 처리를 하는 함수입니다.
    function print_text($str)
    {
        echo htmlspecialchars($str);
    }
    
    // 알림창을 띄우고 뒤로가는 함수입니다.
    function back($str)
    {
        echo "
            <script>
                alert('{$str}');
                window.history.back();
            </script>
        ";
        exit;
    }

    function taking(){
        extract($_POST);
        // $id = $_POST['id']; // 주문의 id
        // $userId = $_SESSION['user']['id']; // 유저의 id
        
        
        //받은 주문 아이디와 같은 주문의 driver_id를 유저 아이디, taking_at을 NOW()로 변경합니다.
        DB::fetch("UPDATE deliveries SET driver_id='{$userId}', state='taking', taking_at=NOW() WHERE id='{$id}'");
        
        back("수락되었습니다.");
    }
}

