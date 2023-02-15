<?php

namespace src\Controller;

use src\App\DB;
class ReviewController{
    function grade(){
        extract($_POST);
                
        $score = $_POST['score']; // 입력한 점수
        $id = $_POST['id']; // 빵집의 아이디
        $user = user()->id; // 유저 야이디
        DB::fetch("INSERT INTO `grades`(`user_id`, `store_id`, `score`) VALUES ('{$user}','{$id}',{$score})");

        back('등록되었습니다.');
    }
    function insert(){
        //리뷰의 필수항목 title, content, id를 받습니다.

        $title = $_POST['title'];
        $content = $_POST['content'];
        $id = $_POST['id'];

        //리뷰에 필수항목이 아닌 img가 있다면 이미지가 저장된 주소를 이 변수에 저장합니다
        $imageName = '';

        if($_FILES['img']['name'] === null){
            var_dump($_FILES['img']);
        
            //이미지를 복사해와서 저장한 뒤 주소를 저장합니다.
        
            $tmp_name = explode('.', $_FILES['img']['tmp_name']);
            $tmp_name = explode('\\', $tmp_name[0]);
            $tmp_name = $tmp_name[3];
        
            var_dump($tmp_name);
        
            if(explode('/', $_FILES['img']['type'])[0] == 'image'){
                // ./resources/img/image/bread/이미지이름.확장자로 저장합니다.
                // 기본 제공 파일에 있던 sql의 이미지 경로가 /image 으로 시작했기 떄문에 똑같이 저장합니다.
                $imageName = '/image/bread/' . $tmp_name . '.' . explode('/', $_FILES['img']['type'])[1];
                // 임시파일을 복사해서 저장할 경로에 붙여넣습니다.
                copy($_FILES['img']['tmp_name'], './resources/img/image/bread/' . $tmp_name . '.' . explode('/', $_FILES['img']['type'])[1]);
            }
        }

        // 받은 정보들을 이용해 INSERT합니다.
        $user = user()->id;
        DB::fetch("INSERT INTO `reviews`(`title`, `contents`, `image`, `write_at`, `store_id`, `user_id`) VALUES ('{$title}','{$content}','{$imageName}', NOW(), '{$id}' ,'{$user}')");

        back('등록 되었습니다');
    }
    function like(){
        extract($_POST);
        if(is_null($_POST['id'])){
            back('잘못된 경로 입니다.');
            exit;
        }
        DB::fetch("INSERT INTO likes(user_id, review_id) VALUES (?,?)", [user()->id, $_POST['id']]);
        back('성공적으로 공감하였습니다.');
    }
}