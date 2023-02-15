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
            extract($_GET);

            $sql = "SELECT c.id, c.name, c.connect, c.image, e.cmt,".
            // 가게의 아이디, 이름, 연락처, 대표사진, 리뷰 갯수를 가져온다.
            "ROUND(AVG(d.score),1) score, ".
            // 가게의 평균 평점을 구합니다.
            "CONCAT(f.borough, ' ', f.name) AS location, RANK() OVER (ORDER BY SUM(a.cnt) DESC) rank ".
            // 가게의 주소와 주문 갯수의 순위를 구합니다

            "FROM delivery_items a, breads b, stores c, grades d, 
            (select store_id, count(store_id) cmt from reviews group by store_id) e,".
            // 서브쿼리로 가게별 리뷰 갯수 정보를 가지고 온다
            "(select s.id, l.borough, l.name from stores s, locations l, users u where u.type = 'owner' and u.location_id = l.id and s.user_id = u.id) f";
            // 서브쿼리로 가게의 주소 정보를 가지고 온다.

            $where = "WHERE a.bread_id = b.id AND b.store_id = c.id AND c.id = d.store_id AND c.id = e.store_id AND f.id = c.id ";

            $sql = $sql . "
            {$where}
            GROUP BY b.store_id
            ORDER BY rank ASC;
            ";
            $list = DB::fetchAll($sql);

            
            $datas['title'] = '대전 빵집 ';
            $datas['list'] = $list;
            $datas['type'] = '';
            $datas['keyword'] = '';
            view('/sub', $datas);
            exit;
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
            extract($_POST);
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $page =  isset($_POST['page']) ? $_POST['page'] : '';

            if (!(isset($_POST['page']) && isset($_POST['id']))) {
                echo '잘못된 접근 입니다.';
                exit;
            }
            
            $pageId = $_POST['id'];
            
            if (!isset($_SESSION['user'])) {
                header('location: /login');
                exit;
            }
            $datas['title'] = '주문';
            $datas['pageId'] = $pageId;
            $datas['page'] = $page;
            view('order', $datas);
        }
        public function discount(){
            $datas['title'] = '할인 이벤트 ';
            view('discount', $datas);
        }
    }
?>