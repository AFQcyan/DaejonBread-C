<?php
    namespace src\Controller;

    use src\App\DB;

    class BreadListController{
        function search(){
            extract($_POST);
            $type = isset($_POST['type']) ? $_POST['type'] : 'name';
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';

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

            $where = "WHERE b.store_id = c.id AND c.id = d.store_id AND c.id = e.store_id AND f.id = c.id ";


            if(isset($type) && isset($keyword) && $keyword != ''){
                $key = $_POST['keyword'];
                if($_POST['type'] == 'name'){
                    // 검색어를 포함하는 가게 이름이 있는 컬럼을 검색한다
                    $where .= "AND c.name LIKE '%{$key}%'";
                }
                if($_POST['type'] == 'menu'){
                    // group_concat으로 소보로빵|케이크|바게트 이런 형식으로 문자열을 만든 다음 
                    // 해당 빵집의 메뉴 중 key와 같은 값이 있는지 확인하며 검색한다 
                    $where .= "AND '{$key}' REGEXP (SELECT GROUP_CONCAT(b.name separator '|'))";
                }
                if($_POST['type'] == 'location'){
                    // concat으로 구와 동을 합쳐 지역 이름을 만든 다음 검색한다
                    $where .= "AND CONCAT(f.borough, ' ', f.name) like '%{$key}%'";
                }
            }
            $sql = $sql . "
            {$where}
            GROUP BY b.store_id
            ORDER BY rank ASC;
            ";
            $list = DB::fetchAll($sql);

            echo '<pre>';
            var_dump($list);
            echo '</pre>';

            
            $datas['title'] = '대전 빵집 ';
            $datas['list'] = $list;
            $datas['type'] = $type;
            $datas['keyword'] = $keyword;
            view('/sub', $datas);
            exit;
        }
    }
?>