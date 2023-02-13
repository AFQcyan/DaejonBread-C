<?php

use src\App\DB;
// 최단 경로 노드, 간선 준비
$sql = "SELECT * FROM distances";
$list = DB::fetchAll($sql);

// 지역 번호가 79까지 있으므르 79까지 배열 생성
$length = 79;

// 무한만 들어있는 배열로 간선 정보가 없는 노드 사이의 거리를 무한으로 만들어 못 가게 해둠
$ans = array_fill(1, $length, "INF");
$distance = array_fill(1, $length, $ans);

// 거리 정보를 배열에 담아줌
foreach($list as $item) {
    $distance[$item->vertex1][$item->vertex2] = $item->distance;
    $distance[$item->vertex2][$item->vertex1] = $item->distance;
}

// 데이터베이스에 저장한 date 형식을 time으로 변환해서 넣으면 0000년 00월 00일 AM00:00 형식으로 반환하는 함수입니다.
function timeFomatChange($time)
{
    return date('Y년 m월 d일 AH:i', $time);
}

// 거리(km)와 받은 시간, 탈것 정보를 받아서 걸릴 시간을 출력하는 함수입니다.
function getTime($dis, $taking_at, $tran)
{
    $time = 0;
    
    // 교통수단이 자전거일시 속도가 15km/h 아니면 50km/h
    if ($tran == 'bike') {
        $time = $dis / 15;
    } else {
        $time = $dis / 50;
    }
    // DB에 저장되어있는 date 형식이 2021-10-6 10:10:10 이런식이므로 구분자를 없에서 strtotime으로 time 타입으로 바꿀 수 있도록 합니다
    $taking_at = strtotime(str_replace(':', '', str_replace('-', '', $taking_at)));
    // 걸릴 시간 + 라이더가 주문접수한 시간
    $time = $time * 60 * 60 * 100 + $taking_at;
    // 출력
    echo timeFomatChange($time);
}

// 시작점과 도착점을 받아서 시작점에서 도착점으로 가는 가장 빠른 길의 거리를 구하는 함수입니다.
function getMin($distance ,$start, $end)
{
    // 지역 번호가 79까지 있으므르 79까지 배열 생성
    $ans = array_fill(1, 79, "INF");
    // 첫 노드는 거리를 0으로 설정
    $ans[$start] = 0;
    // 시작할 노드, 간선의 갯수, 간선 정보, 처음 노드로 부터 거리 정보(초기 값은 모든 노드가 무한)
    return getDistance($start, count($distance), $distance, $ans)[$end];
}

function getDistance($start, $length, $data, $ans, $d = 0)
{
    for ($i = 1; $i <= $length; $i++) {
        // 거리가 INF가 아닐때만 연산
        if ($data[$start][$i] != 'INF') {
            if ($ans[$i] === 'INF' ? true : $ans[$i] > $d + $data[$start][$i]) {
                // 거리 계산
                $ans[$i] = $d + $data[$start][$i];
                // 각 노드 까지의 거리 저장
                $ans = getDistance($i, $length, $data, $ans, $ans[$i]);
            }
        }
    }

    // 각 노드 까지의 거리 리턴
    return $ans;
}

// 깊이 우선 탐색 함수입니다.
function DFS($start, $end, $distance)
{
    global $newArray;
    global $distanceList;
    global $visitList;

    // 이미 방문한 노드면 바로 나갑니다
    if (in_array($start, $visitList)) {
        return;
    }

    // 도착점에 도착했으면 도착점까지 걸린 거리를 distanceList에 넣어줍니다. 
    if ($start == $end) {
        $distanceList[] = $distance;
        return;
    }

    // 방문 리스트에 현재 노드를 넣어줍니다.
    $visitList[] = (int)$start;

    for ($i = 0; $i < count($newArray[$start]); $i++) {
        // 첫번째 인자 : 현재 노드와 연결되어있는 노드를 넣어줍니다. 
        // 두번째 인자 : 도착점을 넣어줍니다
        // 세번째 인자 : 현재 노드와 연결되어있는 노드와의 거리 + 지금까지의 거리를 넣어줍니다.
        DFS($newArray[$start][$i][0], $end, $newArray[$start][$i][1] + $distance);
    }
}

// 로그인하지 않았다면 로그인 페이지로 보냅니다
if (!$_SESSION['user']) {
    header('location: /login');
}

// 유저의 아이디를 불러옵니다.
$id = user()->id;

// 유저가 고객이라면 실행합니다.
if (user()->type == 'normal') :
    // 주문영역을 구현하는데 필요한 sql문입니다. GROUP_CONCAT을 사용한 부분은 나중에 explode로 분할해줘야하기에 바로 이름으로 불러오면 구분자가 포함될 시 버그가 날 수 있기에
    // 빵의 이름으로 불러오지않고 id로 불러옵니다.
    $sql = "
    SELECT (SELECT COUNT(id) FROM reviews WHERE reviews.store_id = stores.id AND reviews.user_id = u.id) as reiview,  " . 
    // 이미 같은 가게에 리뷰를 달았었는지 확인 하기 위해 유저가 가게에 단 리뷰의 갯수를 불러옵니다.
    "(SELECT COUNT(id) FROM grades WHERE grades.store_id = stores.id AND grades.user_id = u.id) as score,  " .
    // 이미 같은 가게에 점수를 매겼는지 확인 하기 위해 유저가 가게에 매긴 점수의 갯루를 불러옵니다.
    "(SELECT rider.name FROM users rider WHERE deliveries.driver_id = rider.id) as rider_name,
    (SELECT rider.location_id FROM users rider WHERE deliveries.driver_id = rider.id) as rider_location,  " .
    // 주문을 받은 라이더가 있다면 라이더의 이름과 라이더의 주소을 가져옵니다.
    "owner.location_id store_location,  " .
    // 가게의 주소를 불러옵니다.
    "(SELECT GROUP_CONCAT(delivery_items.id) FROM breads WHERE breads.id = delivery_items.bread_id) as id,
    GROUP_CONCAT(delivery_items.cnt) as bread_cnt,
    GROUP_CONCAT(delivery_items.price) as bread_price,  " .
    // 주문한 상품의 아이디, 수량, 가격리스트를 가져옵니다
    "(SELECT rider.transportation FROM users rider WHERE deliveries.driver_id = rider.id) as rider_transportation,  " .
    // 라이더의 교통수단을 불러옵니다.
    "stores.name,
    stores.id as store_id, 
    deliveries.order_at,
    deliveries.taking_at,
    u.location_id,
    deliveries.state ".
    // 가게의 이름, 가게의 아이디, 유저의 위치, 주문상태, 주문한 시간, 주문 접수한 시간을 불러옵니다.

    "FROM deliveries, delivery_items, users u, stores, users as owner ".

    "WHERE u.id = '{$id}'
    AND stores.id = deliveries.store_id
    AND owner.id = stores.user_id
    AND delivery_items.delivery_id = deliveries.id
    AND deliveries.orderer_id = u.id ".

    "GROUP BY deliveries.id";
    //주문을 기준으로 묶습니다.

    $log = DB::fetchAll($sql);

    // 이제까지 한 예약주문을 불러옵니다.
    $sql = "
    SELECT stores.name, reservations.*

    FROM reservations, stores
    
    WHERE reservations.store_id = stores.id
    AND reservations.user_id = '{$id}'
    ";

    $log2 = DB::fetchAll($sql);
?>


    <section id="log-list" class="main-section">
        <div class="container">
            <div class="section-info">
                <div class="title">주문 조회</div>
                <div class="text">
                    <div class="content">
                        나의 <b>주문 조회</b>
                    </div>
                </div>
            </div>

            <div>
                <table class="table table-bordered normal_order">
                    <thead>
                        <tr>
                            <th>
                                빵집이름
                            </th>
                            <th>
                                주문 일시
                            </th>
                            <th>
                                빵 종류 및 가격, 수량
                            </th>
                            <th>
                                라이더 이름
                            </th>
                            <th>
                                도착 예정 시간
                            </th>
                            <th>
                                리뷰 / 평점
                            </th>
                            <th>
                                주문 상태
                            </th>
                        </tr>
                    </thead>
                    <!-- 리뷰 파트에 버그 있음 -->
                    <tbody>
                        <?php foreach ($log as $obj) : ?>
                            <tr>
                                <td>
                                    <?= $obj->name ?>
                                </td>
                                <td>
                                    <!-- 문제에 맞는 형식으로 시간을 출력합니다. -->
                                    <?= timeFomatChange(strtotime(str_replace(':', '', str_replace('-', '', $obj->order_at)))) ?>
                                </td>
                                <td>
                                    <?php

                                    $idList = explode(',', $obj->id);
                                    $cnt = explode(',', $obj->bread_cnt);
                                    $price = explode(',', $obj->bread_price);

                                    for ($i = 0; $i < count($idList); $i++) {
                                        $current = $idList[$i];
                                        // id에 맞는 빵의 이름을 불러와서 출력합니다.
                                        echo DB::fetch("SELECT breads.name FROM breads WHERE breads.id = ?", [$current])->name . ' ' . $cnt[$i] . '개 ' . $price[$i] . '원<br>';
                                    }

                                    ?>
                                </td>
                                <td>
                                    <?= $obj->rider_name ?>
                                </td>
                                <td>
                                    <?php
                                    // 라이더 이름이 있다면 도착 예정시간을 구해서 출력합니다.
                                    if (!is_null($obj->rider_name)) {
                                        getTime(getMin($distance ,$obj->location_id, $obj->store_location) + getMin($distance, $obj->rider_location, $obj->store_location), $obj->order_at, $obj->rider_transportation);
                                    }
                                    ?>
                                </td>
                                <td class='review_buttons'>
                                    <!-- 지금까지 쓴 리뷰가 없다면 리뷰를 쓸 수 있는 모달창과 버튼을 생성합니다 -->
                                    <?php
                                    $complList = DB::fetchAll("SELECT store_id, user_id FROM reviews WHERE store_id = ? AND user_id = ?", [$obj->store_id,user()->id]);                                  
                                    if ($obj->state == 'complete' && !$complList) :
                                    ?>

                                        <button class='button buttonhlt p-2' data-toggle='modal' data-target='#myModal' tabindex="-1" aria-hidden="true" >리뷰</button>
                                        <div class="modal fade" id="myModal">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action= "/insert/review" method="post" enctype="multipart/form-data">
                                                <div class="modal-header">
                                                    <input type="hidden" name="id" value="<?= $obj->store_id ?>">
                                                    <h3>리뷰 작성</h3>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="d-flex align-items-center p-3 border-bottom border-secondary">
                                                        <p class="col-2">제목<span class="must_req">*</span></p>
                                                        <input class="col-10" type="text" name="title" value="" placeholder='제목을 입력해주세요.' required>
                                                    </div>
        
                                                    <div class="d-flex align-items-center p-3 border-bottom border-secondary">
                                                        <p class="col-2">본문<span class="must_req">*</span></p>
                                                        <textarea class="col-10" name="content" rows="10" required></textarea>
                                                    </div>
        
                                                    <div class="d-flex align-items-center p-3 border-bottom border-secondary">
                                                        <p class="col-2">파일 선택</p>
                                                        <input class="col-10" type="file" name="img">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <div class="mt-3">
                                                        <a href="#" class="button buttonhlt" data-dismiss="modal">닫기</a>
                                                        <input class="button buttonhlt" type="submit" value="업로드">
                                                    </div>
                                                </div>
        
         
                                                </div>
                                                </form>
                                            </div>
                                        </div>
                                            <div class="text-center review-form">
                                        </div>

                                    <?php endif; ?>
                                    <!-- 지금까지 준 점수가 없다면 점수를 줄 수 있는 모달창과 버튼을 생성합니다. -->
                                    <?php
                                    if ($obj->score == '0') :
                                    ?>

                                        <button class='button buttonhlt p-2' data-toggle='modal' data-target='#g<?= $obj->location_id ?>' tabindex="-1" aria-hidden="true" >평점</button>
                                        <div class="modal fade" id="g<?= $obj->location_id ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action= "/insert/grade" method="post">
                                                        <div class="modal-header">
                                                            <input type="hidden" name="id" value="<?= $obj->store_id ?>">
                                                            <div>줄 점수를 입력해주세요</div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="d-flex justify-content-center mt-3">
                                                                <input type="number" name="score" max="5" min="0" value="0" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <div class="d-flex justify-content-between">
                                                                <a href="#" class="button buttonhlt m-1" data-dismiss='modal' data-target='#g<?= $obj->location_id ?>'>닫기</a>
                                                                <input type="submit" class="button buttonhlt m-1" value="확인">
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div>
                                            </div>
                                        </div>

                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    // 주문상태를 출력합니다.
                                    $a =  ['order' => '주문대기', 'accept' => '상품 준비 중', 'reject' => '주문 거절', 'taking' => '배달 중', 'complete' => '배달 완료'];
                                    echo $a[$obj->state];
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section id="book-list" class="main-section">
        <div class="container">
            <div class="section-info">
                <div class="title">예약 목록</div>
                <div class="text">
                    <div class="content">
                        나의 <b>예약 목록</b>
                    </div>
                </div>
            </div>

            <div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>
                                빵집 이름
                            </th>
                            <th>
                                예약 일시
                            </th>
                            <th>
                                에약 신청 일시
                            </th>
                            <th>
                                상태
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($log2 as $obj) : ?>
                            <tr>
                                <td>
                                    <?= $obj->name ?>
                                </td>
                                <td>
                                    <?= timeFomatChange(strtotime(str_replace(':', '', str_replace('-', '', $obj->request_at)))) ?>
                                </td>
                                <td>
                                    <?= timeFomatChange(strtotime(str_replace(':', '', str_replace('-', '', $obj->reservation_at)))) ?>
                                </td>
                                <td>
                                    <?php
                                    $a = ['order' => '신청', 'accept' => '승인', 'reject' => '거절'];
                                    echo $a[$obj->state];
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

<?php
// 유저가 사장일시 실행합니다.
elseif (user()->type == 'owner') :
    // 자신의 가게에 들어온 주문 리스트를 불러오는 sql입니다. GROUP_CONCAT은 출력할때는 explode로 분할합니다.
    $sql = "SELECT orderer.name, deliveries.id deliveries_id, concat(locations.borough, ' ', locations.name) as location_name, ".
    // 주문자 이름, 주문 번호, 주문자 주소를 불러옵니다.
    "(SELECT GROUP_CONCAT(delivery_items.id) FROM breads WHERE breads.id = delivery_items.bread_id) as id,
    GROUP_CONCAT(delivery_items.cnt) as bread_cnt,
    GROUP_CONCAT(delivery_items.price) as bread_price, " .
    // 주문한 상품의 아이디, 수량, 가격리스트를 가져옵니다
    "(SELECT rider.transportation FROM users rider WHERE deliveries.driver_id = rider.id) as rider_transportation,
    (SELECT rider.name FROM users rider WHERE deliveries.driver_id = rider.id) as rider_name,
    (SELECT rider.location_id FROM users rider WHERE deliveries.driver_id = rider.id) as rider_location, " .
    // 주문 접수만 라이더가 있다면 라이더의 교통수단, 이름, 주소 아이디를 불러 옵니다
    "owner.location_id store_location,
    locations.id orderer_location, ".
    // 가게의 주소아이디와 주문자의 주소 아이디를 가져옴
    "deliveries.state,
    deliveries.order_at " .
    // 주문 상태, 주문 일시를 가져옴
        
    "FROM deliveries, users orderer, users owner, stores, locations, delivery_items
    
    WHERE stores.user_id = owner.id
    AND owner.id = '{$id}'
	AND deliveries.store_id = stores.id
    AND orderer.id = deliveries.orderer_id
    AND locations.id = orderer.location_id
    AND delivery_items.delivery_id = deliveries.id
    
    GROUP BY deliveries.id;";
    // 주문을 기준으로 묶습니다
    $list = DB::fetchAll($sql);
?>

    <section id="log-list" class="main-section">
        <div class="container">
            <div class="section-info">
                <div class="title">주문 조회</div>
                <div class="text">
                    <div class="content">
                        내 가게의 <b>주문 조회</b>
                    </div>
                </div>
            </div>

            <div>
                <table class="table table-bordered owner_order_list">
                    <thead>
                        <tr>
                            <th>
                                주문자 이름
                            </th>
                            <th>
                                배달 주소
                            </th>
                            <th>
                                라이더 이름
                            </th>
                            <th>
                                도착 예정 시간
                            </th>
                            <th>
                                빵 종류 및 가격, 수량
                            </th>
                            <th>
                                주문 상태
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list as $obj) : ?>
                            <tr>
                                <td>
                                    <?= $obj->name ?>
                                </td>
                                <td>
                                    <?= $obj->location_name ?>
                                </td>
                                <td>
                                    <?= $obj->rider_name ?>
                                </td>
                                <td>
                                    <?php
                                    // 라이더가 있다면 예상도착 시간을 구합니다.
                                    if (!is_null($obj->rider_name)) {
                                        getTime(getMin($distance, $obj->orderer_location, $obj->store_location) + getMin($distance, $obj->rider_location, $obj->store_location), $obj->order_at, $obj->rider_transportation);
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    // 문자열로 되어있던 리스트를 나눕니다
                                    $idList = explode(',', $obj->id);
                                    $cnt = explode(',', $obj->bread_cnt);
                                    $price = explode(',', $obj->bread_price);

                                    for ($i = 0; $i < count($idList); $i++) {
                                        $current = $idList[$i];

                                        // id에 맞는 빵의 이름을 가져와서 출력합니다.
                                        echo DB::fetch("SELECT breads.name FROM breads WHERE breads.id = ?", [$current])->name . ' ' . $cnt[$i] . '개 ' . $price[$i] . '원<br>';
                                    }

                                    ?>
                                </td>
                                <td class='order_state'>

                                    <?php
                                    // 상태에 따라 수락, 거절 버튼 또는 수락한 주문, 거절한 주문, 배달 중, 배달 완료 등을 출력합니다.
                                    $a =  ['order' => "<form action='/accept' method='post'>
                                                <input type='hidden' name='id' value='{$obj->deliveries_id}'>
                                                <input type='submit' value='수락' class='button buttonhlt px-4'>
                                            </form>
                                            <form action='/deny' method='post'>
                                                <input type='hidden' name='id' value='{$obj->deliveries_id}'>
                                                <input type='submit' value='거절' class='button buttonhlt px-4'>
                                            </form>
                                        ", 'accept' => '수락한 주문', 'reject' => '거절한 주문', 'taking' => '배달 중', 'complete' => '배달 완료'];
                                    echo $a[$obj->state];
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <?php
    // 자신의 가게에 들어온 예약 주문을 가져옵니다.
    $list = DB::fetchAll("
    SELECT r.*, u.name FROM reservations r, users u
    WHERE r.store_id in (select stores.id from users, stores where stores.user_id = '{$id}')
    AND r.user_id = u.id");
    ?>

    <section id="book-list" class="main-section">
        <div class="container">
            <div class="section-info">
                <div class="title">예약 목록</div>
                <div class="text">
                    <div class="content">
                        내 가게의 <b>예약 목록</b>
                    </div>
                </div>
            </div>

            <div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>
                                예약자 이름
                            </th>
                            <th>
                                예약 일시
                            </th>
                            <th>
                                예약 신청 일시
                            </th>
                            <th>
                                상태
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list as $obj) : ?>
                            <tr>
                                <td>
                                    <?= $obj->name ?>
                                </td>
                                <td>
                                    <?= $obj->request_at ?>
                                </td>
                                <td>
                                    <?= $obj->reservation_at ?>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-around">
                                        <?php
                                        // 상태에 따라 수락, 거절 버튼 또는 수락한 주문, 거절한 주문, 배달 중, 배달 완료 등을 출력합니다.
                                        $a =  ['order' => "<form action='./accept_reservation.php' method='post'>
                                                    <input type='hidden' name='id' value='{$obj->id}'>
                                                    <input type='submit' value='수락' class='button buttonhlt'>
                                                </form>
                                                <form action='./reject_reservation.php' method='post'>
                                                    <input type='hidden' name='id' value='{$obj->id}'>
                                                    <input type='submit' value='거절' class='button buttonhlt'>
                                                </form>
                                            ", 'accept' => '수락한 예약', 'reject' => '거절한 예약'];
                                        echo $a[$obj->state];
                                        ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <?php
    // 가게의 메뉴 리스트를 가져옵니다.
    $list = DB::fetchAll("SELECT * FROM stores s, breads b WHERE s.user_id = '{$id}' AND b.store_id = s.id;");
    ?>

    <section id="log-list" class="main-section">
        <div class="container">
            <div class="section-info">
                <div class="title">메뉴리스트</div>
                <div class="text">
                    <div class="content">
                        내 가게의 <b>메뉴리스트</b>
                    </div>
                </div>
            </div>

            <div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>이름</th>
                            <th>가격</th>
                            <th>할인율</th>
                            <th>할인가</th>
                            <th>총 팔린 개수</th>
                            <th>할인 버튼</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($list as $product) :
                        ?>
                            <!-- 할인률을 설정하는 모달팝업입니다. -->
                            <div class="modal fade" id="discount<?= $product->id ?>" tabindex='-1' aria-hidden="true" >
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action= "/discount" method="post">
                                            <input type="hidden" name="id" value="<?= $product->id ?>">
                                            <div class="modal-header">
                                                <h1>할인율 설정</h1>
                                            </div>
                                            <div class="modal-body">
                                                <p>할인률 : <input type="number" min="0" max="99" name="number" required>%</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="button buttonhlt">확인</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div>
                                </div>
                            </div>
                            <tr>
                                <td><?= $product->name ?></td>
                                <td><?= $product->price ?>원</td>
                                <?php if (isset($product->sale)) : ?>
                                    <td><?= $product->sale ?>%</td>
                                    <!-- 가격 - (가격 * 할인률 * 0.01)은 할인가입니다. -->
                                    <td><?= floor($product->price - $product->sale * $product->price * 0.01) ?>원</td>
                                <?php else : ?>
                                    <td></td>
                                    <td></td>
                                <?php endif; ?>
                                <!-- 총 팔린 갯수를 가져와서 출력합니다. -->
                                <td><?= DB::fetch("SELECT COUNT(delivery_items.cnt) count FROM delivery_items WHERE '{$product->id}' = delivery_items.bread_id; AND ")->count; ?>개</td>
                                <td>
                                    <button data-toggle='modal' data-target="#discount<?= $product->id ?>" class="button buttonhlt">할인</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

<?php
// 유저가 라이더일시 실  행합니다.
elseif (user()->type == 'rider') :
    // 다른 라이더가 받지 않은 주문을 가져오는 sql입니다 시간 순대로 정렬되어 있습니다.
    $sql = "SELECT stores.name as store_name,
    (SELECT concat(locations.borough, locations.name) FROM locations WHERE locations.id = owner_.location_id) as store_location,  ".
    // 가게주소를 불러옵니다
    "(SELECT concat(locations.borough, locations.name) FROM locations WHERE locations.id = orderer.location_id) as orderer_location,  ".
    // 주문자주소를 불러옵니다.
    "(SELECT locations.id FROM locations WHERE locations.id = owner_.location_id) as store_location_id,  ".
    // 가게 주소의 아이디를 불러옵니다.
    "(SELECT locations.id FROM locations WHERE locations.id = orderer.location_id) as orderer_location_id,  ".
    // 주문자 주소의 아이디를 불러옵니다
    "deliveries.order_at,
    deliveries.taking_at,
    deliveries.state, 
    deliveries.id as deliveries_id,  " .
    // 주문 일시, 주문 수락 일시, 주문 상태, 주문 아이디를 불러옵니다.
    "(SELECT GROUP_CONCAT(delivery_items.bread_id) FROM delivery_items WHERE delivery_items.delivery_id = deliveries.id) as id,
    (SELECT GROUP_CONCAT(delivery_items.cnt) FROM breads, delivery_items WHERE breads.id = delivery_items.bread_id AND delivery_items.delivery_id = deliveries.id) as bread_cnt,
    (SELECT GROUP_CONCAT(delivery_items.price) FROM breads, delivery_items WHERE breads.id = delivery_items.bread_id AND delivery_items.delivery_id = deliveries.id) as bread_price  " .
    // 상품의 아이디 리스트, 상품의 수량 리스트, 상품의 가격 리스트를 가져옵니다. 
    
    "FROM stores, users owner_, users orderer, users, deliveries
    
    WHERE (deliveries.driver_id = '{$id}' OR deliveries.driver_id = '' OR deliveries.driver_id is null)  " .
    // 내가 배달하거나 배달 중인 주문이다 배달을 수락한 라이더가 없는 주문을 검색합니다.
    "AND stores.id = deliveries.store_id
    AND orderer.id = deliveries.orderer_id
    AND owner_.id = stores.user_id
    AND (deliveries.state = 'accept' OR deliveries.state = 'taking' OR deliveries.state = 'complete')".
    // 내가 배달하거나 배달 중인 주문이다 배달을 수락한 라이더가 없는 주문을 검색합니다.
    
    "GROUP BY deliveries.id
    ORDER BY deliveries.order_at DESC, deliveries.taking_at DESC";
    // 주문으로 그룹을 묶고 주문시간 대로 내림차 순, 주문 수락한 순서대로 내림차 순 정렬을 합니다.
    $list = DB::fetchAll($sql);

    echo "<pre>";
    // var_dump($list);
    echo "</pre>";
?>
    <section id="my-info" class="main-section">
        <div class="container">
            <div class="section-info">
                <div class="title">내 정보</div>
                <div class="text">
                    <div class="content">
                        나의 <b>정보</b>
                    </div>
                </div>
            </div>

            <div>
                <form action= "./change_userinfo.php" method="POST">
                    <input type="hidden" name="id" value="<?= user()->id ?>">
                    <div class="d-flex justify-content-center">
                        <!-- 내 교통수단이나 위치를 변경할 수 있는 영역입니다. 내 정보 수정을 누르면 수정됩니다. -->
                        <p class="p-3">
                            내 교통수단은
                            <select name="transportation" id="transportation">
                                <?php
                                // 유저의 교통수단을 불러옵니다.
                                user()->transportation = DB::fetch("SELECT transportation FROM users WHERE id='{$id}';")->transportation;

                                // 유저의 교통수단이 select에서 선택되어 있게 합니다.
                                if (user()->transportation == 'bike') {
                                    echo '<option value="bike" selected>자전거</option>
                                        <option value="motorcycle">오토바이</option>';
                                } else {
                                    echo '<option value="bike">자전거</option>
                                        <option value="motorcycle" selected>오토바이</option>';
                                }
                                ?>
                            </select>
                            이고
                        </p>
                        <p class="p-3">
                            내 위치는
                            <select name="location" id="location">
                                <?php
                                // 유저의 주소가 select에서 선택되어 있게 하고 다른 모든 주소를 출력합니다.
                                foreach (DB::fetchAll("SELECT * FROM locations") as $location) {
                                    if (user()->location_id == $location->id) {
                                        echo "<option value='{$location->id}}' selected>{$location->borough} {$location->name}</option>";
                                    } else {
                                        echo "<option value='{$location->id}}'>{$location->borough} {$location->name}</option>";
                                    }
                                }
                                ?>
                            </select>
                            입니다
                        </p>
                    </div>
                    <div class="d-flex justify-content-center">
                        <input type="submit" value="내 정보 수정" class="button buttonhlt">
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section id="order-log" class="main-section">
        <div class="container">
            <div class="section-info">
                <div class="title">배달 리스트</div>
                <div class="text">
                    <div class="content">
                        <b>배달 리스트</b>
                    </div>
                </div>
            </div>

            <div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>
                                빵집 이름
                            </th>
                            <th>
                                빵집 주소
                            </th>
                            <th>
                                배달 주소
                            </th>
                            <th>
                                도착 예정 시간
                            </th>
                            <th>
                                빵 종류 및 가격, 수량
                            </th>
                            <th>
                                상태
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list as $log) :?>
                            <tr>
                                <td><?= $log->store_name ?></td>
                                <td><?= $log->store_location ?></td>
                                <td><?= $log->orderer_location ?></td>
                                <td>
                                    <?php
                                    // 받은, 받았던 주문일시 예상시간을 출력합니다.
                                    if ($log->state == 'taking' || $log->state == 'complete') {
                                        echo getTime(getMin($distance, $log->orderer_location_id, $log->store_location_id) + /**/getMin($distance, user()->location_id, $log->store_location_id), $log->taking_at,  user()->transportation);
                                    }
                                    ?>

                                </td>
                                <td>
                                    <?php
                                    // 상품의 아이디 리스트, 상품의 수량 리스트, 상품의 가격 리스트를 문자열에서 배열로 변환합니다. (구분자 : ,)
                                    $idList = explode(',', $log->id);
                                    $cnt = explode(',', $log->bread_cnt);
                                    $price = explode(',', $log->bread_price);

                                    for ($i = 0; $i < count($idList); $i++) {
                                        $current = $idList[$i];
                                        // id에 맞는 빵의 이름을 출력합니다.
                                        echo DB::fetch("SELECT breads.name FROM breads WHERE breads.id = ?", [$current])->name . ' ' . $cnt[$i] . '개 ' . $price[$i] . '원<br>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <!-- 배달 상태에 따라 출력하는 버튼 또는 텍스트를 다르게 합니다. -->
                                    <!-- 사장이 주문 접수를 했다면 수락버튼이 뜨고 -->
                                    <!-- 내가 수락한 주문이라면 완료버튼이 뜹니다 -->
                                    <!-- 그 이외에 주문은 완료한 배달이므로 '완료한 배달'이라는 텍스트가 씁니다. -->
                                    <?php if ($log->state == 'accept') : ?>
                                        <form action= "./taking.php" method="post">
                                            <input type="hidden" name="id" value="<?= $log->deliveries_id ?>">
                                            <input class="button buttonhlt" type="submit" value="수락">
                                        </form>
                                    <?php elseif ($log->state == 'taking') : ?>
                                        <form action= "./complete.php" method="post">
                                            <input type="hidden" name="id" value="<?= $log->deliveries_id ?>">
                                            <input class="button buttonhlt" type="submit" value="완료">
                                        </form>
                                    <?php else : ?>
                                        완료한 배달
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach;
                        endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>