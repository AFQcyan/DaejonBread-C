<?php
    namespace src\Controller;

    use src\App\DB;

    class BreadListController{
        function search(){
            $data = $_POST;
            $type = isset($data['type']) ? $data['type'] : 'name';
            $keyword = isset($data['keyword']) ? $data['keyword'] : '';

            $sql = "SELECT c.id, c.name, c.connect, c.image, e.cmt,
            ROUND(AVG(d.score),1) score, 
            CONCAT(f.borough, ' ', f.name) AS location, RANK() OVER (ORDER BY SUM(a.cnt) DESC) rank 
            FROM delivery_items a, breads b, stores c, grades d, 
            (select store_id, count(store_id) cmt from reviews group by store_id) e,
            (select s.id, l.borough, l.name from stores s, locations l, users u where u.type = 'owner' and u.location_id = l.id and s.user_id = u.id) f";
            // 서브쿼리로 가게의 주소 정보를 가지고 온다.

            $where = "WHERE a.bread_id = b.id AND b.store_id = c.id AND c.id = d.store_id AND c.id = e.store_id AND f.id = c.id ";


            if(isset($type) && isset($keyword) && $keyword != ''){
                $key = $data['keyword'];
                if($data['type'] == 'name'){
                    // 검색어를 포함하는 가게 이름이 있는 컬럼을 검색한다
                    $where .= "AND c.name LIKE '%{$key}%'";
                }
                if($data['type'] == 'menu'){
                    // group_concat으로 소보로빵|케이크|바게트 이런 형식으로 문자열을 만든 다음 
                    // 해당 빵집의 메뉴 중 key와 같은 값이 있는지 확인하며 검색한다 
                    $where .= "AND '{$key}' REGEXP (SELECT GROUP_CONCAT(b.name separator '|'))";
                }
                if($data['type'] == 'location'){
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

            
            echo json_encode(array('result' => $list));
            exit;
        }
    }
?>