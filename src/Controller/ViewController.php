<?php 
    namespace src\Controller;

    use src\App\DB;

    class ViewController{
        public function index(){
            $datas['title'] = "메인 ";
            view('index', $datas);
        }
        // 대전 빵집 페이지
        public function sub(){
            $datas['title'] = '대전 빵집 ';
            view('sub', $datas);
        }
        public function login(){
            $datas['title'] = "로그인 ";
            view('login', $datas);
        }
        public function myPage(){
            $datas['title'] = "마이";
            view('mypage', $datas);
        }
        public function order(){
            $datas['title'] = '주문';
            view('order', $datas);
        }
        public function discount(){
            $datas['title'] = '할인 이벤트 ';
            view('discount', $datas);
        }
    }
?>