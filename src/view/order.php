<?php

use src\App\DB;

$list = DB::fetchAll("
            SELECT reviews.*, users.name,
            (SELECT COUNT(likes.id) FROM likes WHERE likes.review_id = reviews.id) as count,
            replies.contents replies

            FROM stores, users, reviews LEFT JOIN replies ON replies.review_id = reviews.id

            WHERE stores.id = reviews.store_id
            AND users.id = reviews.user_id
            AND store_id = ?

            GROUP BY reviews.id
            ORDER BY (SELECT COUNT(likes.id) FROM likes WHERE likes.review_id = reviews.id) DESC;
            ", [$pageId]);


?>
<!-- 리뷰 영역 -->
<section id="review" class="main-section">
    <div class="container">
        <div class="section-info">
            <div class="title">리뷰</div>
        </div>

        <div class="d-flex justify-content-between position-relative">
            <?php for ($i = $page * 2; $i < $page* 2 + 2 && $i < count($list); $i++) :  $obj = $list[$i];?>
                <div class="col-5 item">
                    <?php if (!is_null($obj->image)) : ?>
                        <img src="./resources/img<?= print_text($obj->image) ?>" alt="image" title="image" width="50">
                    <?php endif; ?>

                    <div class="text">
                        <!-- print_text은 html 이스케이프 처리를 해주는 함수입니다
                            제목, 작성자, 본문을 출력합니다.
                        -->
                        <div class="title"><?=print_text($obj->title)?></div>
                        <div class="name">
                            <?=print_text($obj->name)?>
                        </div>
                        <div class="content">
                            <span><?=print_text($obj->contents)?></span>
                        </div>
                    </div>
                    <?php
                        // 만약 해당 리뷰에 공감을 하지 않았다면 공감을 할 수 있는 버튼을 출력합니다.
                        $like = DB::fetch("SELECT * FROM `likes` WHERE review_id = ? AND user_id = ?", [$obj->id, user()->id]);
                        if (!$like) {
                            echo "<form action='/like' method='post'>
                            <input type='hidden' name='id' class='button buttonhlt' value='" . $obj->id . "'>
                            <input type='submit' class='button buttonhlt'value='공감'>
                            </form>";
                        }
                    ?>
                    <!-- 공감갯수, 작성일자를 출력합니다. -->
                    <span class="text-danger like-count">공감개수 : <?=print_text($obj->count)?></span>
                    <span class="text-primary date"><?=print_text($obj->write_at)?></span>
                </div>
                <?php if ($obj->replies) : ?>
                    <!-- replies0 클래스는 왼쪽, replies1 클래스는 오른쪽에 답변을 답니다. 
                        i - page*2를 해주어 왼쪽 리뷰의 답변인지 오른쪽 리뷰의 답변인지 구합니다.
                    -->
                    <div class="replies replies<?=$i-$page * 2?>">
                        <?=print_text($obj->replies)?>
                    </div>
                <?php endif;?>
            <?php endfor; ?>
        </div>
        
        <ul class="pagination d-flex justify-content-center">
            <!-- 페이지의 수 만큼 페이지 버튼을 출력합니다. -->
            <?php for ($i = 0; $i < count($list) / 2; $i++) : ?>
                <form action="/order" method="post"></form><li class="page-item"><button type='submit' class="btn"><?= $i + 1 ?></a>
                <input type="hidden" name="page" value='<?php $i + 1?>'>
                <input type="hidden" name="id" value='<?php $obj->id ?>'>
            </li>
            <?php endfor; ?>
        </ul>
    </div>
</section>
<!-- //리뷰 영역 -->

<?php
// 빵집 아이디에 맞는 메뉴를 가져오는 sql입니다.
$list = DB::fetchAll("
SELECT breads.id, breads.name, breads.price, breads.image, breads.sale
FROM breads
WHERE breads.store_id = ?;", [$pageId]);
?>


<!-- 주문 영역 -->
<section id="order" class="main-section">
    <div class="container">
        <div class="section-info">
            <div class="title">주문</div>
        </div>

        <form action="/order/order" method="post">

            <!-- 주문할 수 있는 상품의 id를 모두 불러옵니다. (공백으로 구분) -->
            <input type="hidden" name="id-list" value="<?php foreach ($list as $obj) : ?><?= $obj->id ?> <?php endforeach; ?>">      
            <!-- 주문하고자 하는 가게를 주문할때 알 수 있도록 합니다. (공백으로 구분) -->
            <input type="hidden" name="store" value="<?= $pageId ?>">

            <div class="product-grid">
                <?php foreach ($list as $obj) : ?>
                    <div class="item">
                        <img src="./resources/img<?= $obj->image ?>" alt="best" title="best">
                        <div class="text">
                            <div class="title"><?= print_text($obj->name) ?></div>
                            <!-- name을 c+상품아이디로 하여 주문을 했을때 
                            상품 아이디를 가지고 그 상품의 주문 수량을 가져올 수 있게 한다
                            -->
                            <div class="name">
                                수량 : <input type="number" min="0" value="0" name="c<?= $obj->id ?>">
                            </div>
                            <div class="content">
                                가격 : <?= print_text($obj->price) ?>원 <br>
                                <!-- 가격 - 가격 * 할인률 * 0.01로 할인가를 구합니다. 할인을 하지 않는다면 출력하지 않습니다.-->
                                <?php if ($obj->sale > 0) : ?>
                                    할인가 : <?= $obj->price - $obj->price * $obj->sale * 0.01 ?>원(<?= $obj->sale ?>%)
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- name을 p+상품아이디로 하여 주문을 했을때 
                        상품 아이디를 가지고 그 상품의 가격을 가져올 수 있게 한다
                        -->
                        <input type="hidden" name="p<?= $obj->id ?>" value="<?= $obj->price - $obj->price * $obj->sale * 0.01 ?>">
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="d-flex justify-content-center">
                <input type="submit" value="주문" class="button buttonhlt">
            </div>
        </form>
    </div>
</section>
<!-- //주문 영역 -->

<!-- 예약영역 -->
<section id="reservation" class="main-section">
    <div class="container">
        <div class="section-info">
            <div class="title">예약</div>
        </div>

        <form action="/order/reserve" method="post">
            <div class="d-flex justify-content-center">
                <!-- 예약 영역에선 연도-월-일과 시간을 설정할 수 있어야하기 떄문에 date타입 input과 time타입 input이 존재해야합니다. -->
                <input type="date" name="date" required>
                <input type="time" name="time" required>
                <!-- 전달할때 stores의 id를 같이 보내줘야합니다. -->
                <input type="hidden" name="store" value="<?= $pageId ?>">
            </div>

            <div class="d-flex justify-content-center m-5">
                <input type="submit" value="예약하기" class="button buttonhlt">
            </div>
        </form>
    </div>
</section>
<!-- //예약영역 -->