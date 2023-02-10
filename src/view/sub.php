<?php
require_once('./resources/php/header.php');
 
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

$where = "WHERE a.bread_id = b.id AND b.store_id = c.id AND c.id = d.store_id AND c.id = e.store_id AND f.id = c.id";

if(isset($_GET['type']) && isset($_GET['keyword']) && $_GET['keyword'] != ''){
    $key = $_GET['keyword'];
    if($_GET['type'] == 'name'){
        // 검색어를 포함하는 가게 이름이 있는 컬럼을 검색한다
        $where = "AND c.name like '%{$key}%'";
    }
    if($_GET['type'] == 'menu'){
        // group_concat으로 소보로빵|케이크|바게트 이런 형식으로 문자열을 만든 다음 
        // 해당 빵집의 메뉴 중 key와 같은 값이 있는지 확인하며 검색한다 
        $where = "AND '{$key}' REGEXP (SELECT GROUP_CONCAT(b.name separator '|'))";
    }
    if($_GET['type'] == 'location'){
        // concat으로 구와 동을 합쳐 지역 이름을 만든 다음 검색한다
        $where = "AND CONCAT(f.borough, ' ', f.name) like '%{$key}%'";
    }
}

// 가게 마다 그룹으로 묶고 순위 대로 오름차 순 정렬한다. 
$sql = $sql . "
{$where}
GROUP BY b.store_id
ORDER BY rank ASC;
";
$list = DB::fetchAll($sql);
?>
    <!-- 검색영역 -->
    <section id="search" class="main-section">
        <div class="container">
            <div class="section-info">
                <div class="title">검색</div>
            </div>

            <form action="#" method="get">
                <!-- type엔 무엇을 검색할지, keyword엔 검색어를 담아 현재 페이지로 전달합니다 -->
                <select name="type">
                    <option value="name">빵집이름</option>
                    <option value="menu">메뉴</option>
                    <option value="location">지역</option>
                </select>
                <input type="text" name="keyword">
                <input type="submit" class="btn btn-dark" value="검색">
            </form>

        </div>
    </section>
    <!-- //검색영역 -->

    <!-- 빵집베스트5 영역 -->
    <section id="best" class="main-section">
        <div class="container">
            <div class="section-info">
                <div class="title">빵집베스트5</div>
            </div>

            <div class="product-grid">
                <?php 
                // 빵집 베스트 5 영역에선 판매량이 가장 많은 다섯 가게가 나와야하므로
                // 5개의 가게를 출력했으면 반복문을 종료한다
                for($i = 0; $i < 5 && $i < count($list); $i++):
                    $obj = $list[$i];
                ?>
                    <div class="item">
                        <img src="./resources/img<?=$obj->image?>" alt="best" title="best">
                        <div class="text">
                            <!-- 가게이름을 클릭하면 GET에 id=가게id page=0를 담아서 order.php로 이동한다 -->
                            <div class="title"><a href="./order.php?id=<?=$obj->id?>&page=0"><?=$obj->name?></a></div>
                            <div class="name"><?=$obj->location?></div>
                            <div class="content">
                                전화번호 : <?=$obj->connect?> <br>
                                평점 : <?=$obj->score?>점 <br>
                                리뷰 : 리뷰 <?=$obj->cmt?>개 <br>
                            </div>
                        </div>
                    </div>
                <?php endfor;?>
            </div>
        </div>
    </section>
    <!-- //빵집베스트5 영역 -->

    <!-- 빵집리스트 영역 -->
    <section id="best" class="main-section">
        <div class="container">
            <div class="section-info">
                <div class="title">빵집리스트</div>
            </div>

            <div class="product-grid">
                <?php 
                // 빵집베스트5 영역에서 나왔던 가게가 또 나오면 안되므로 i의 초기값을 5로 설정하고
                // 가게 리스트 길이 만큼 반복한다
                for($i = 5;$i < count($list); $i++):
                    $obj = $list[$i];
                ?>
                    <div class="item">
                        <img src="./resources/img<?=$obj->image?>" alt="best" title="best">
                        <div class="text">
                            <div class="title"><a href="./order.php?id=<?=$obj->id?>&page=0"><?=$obj->name?></a></div>
                            <div class="name"><?=$obj->location?></div>
                            <div class="content">
                                전화번호 : <?=$obj->connect?> <br>
                                평점 : <?=$obj->score?>점 <br>
                                리뷰 : 리뷰 <?=$obj->cmt?>개 <br>
                            </div>
                        </div>
                    </div>
                <?php endfor;?>
            </div>
        </div>
    </section>
    <!-- //빵집리스트 영역 -->

<?php
require_once('./resources/php/footer.php');
?>