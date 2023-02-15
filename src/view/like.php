<?php
require_once('./resources/php/lib.php');

if(is_null($_POST['id'])){
    back('잘못된 경로 입니다.');
    exit;
}

// 서버 시간을 서울로 바꿉니다.
date_default_timezone_set("Asia/Seoul");

// 공감버튼을 누른 리뷰와 유저의 정보를 넣습니다.
DB::fetch("INSERT INTO likes(user_id, review_id) VALUES (?,?)", [$_SESSION['user']['id'], $_GET['id']]);

back('공감되었습니다.');