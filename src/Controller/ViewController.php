<?php 
    namespace src\Controller;

    use src\App\DB;

    class ViewController{
        public function index(){
            $datas['title'] = "메인 ";
            view('index', $datas);
        }
        public function login(){
            $datas['title'] = "로그인 ";
            view('login', $datas);
        }
        public function myPage(){
            $datas['title'] = "마이";
            view('mypage', $datas);
        }
    }
?>