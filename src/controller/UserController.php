<?php

    namespace src\Controller;

    use src\App\DB;

    class UserController{
        public function login(){
            extract($_POST);
            $sameVal = DB::fetchAll("SELECT * FROM `users` WHERE `id` = ? AND `pw` = ?", [$id, $pw]);
            if(!$sameVal){
                back('아이디 혹은 비밀번호를 다시 확인해주세요.');     
            }
            session()->set("user", $sameVal);
            redirect('/', '성공적으로 로그인되었습니다.');
        }
        public function logout(){
            session()->remove('user');
            redirect('/', '로그아웃 되었습니다.');
        }
    }


?>