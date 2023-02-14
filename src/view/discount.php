<?php

use src\App\DB;
// 할인률이 0보다 큰 상품의 가격, 할인률, 이미지. 빵집 이름. 빵집 아이디를 불러옵니다.
// 세일률을 기준으로 내림차순 정렬을 합니다.
$list = DB::fetchAll('
SELECT breads.price, breads.sale, breads.image, stores.name, stores.id
FROM breads, stores
WHERE breads.sale > 0
AND stores.id = breads.store_id

ORDER BY breads.sale DESC
');
?>

    <!-- 할인 이벤트 영역 -->
    <section id="best" class="main-section">
        <div class="container">
            <div class="section-info">
                <div class="title">빵집베스트5</div>
            </div>

            <div class="product-grid">
                <!-- 불러온 할인 상품을 전부 출력합니다 -->
                <?php foreach($list as $obj):?>
                    <div class="item">
                        <img src="./resources/img<?=$obj->image?>" alt="best" title="best">
                        <div class="text">
                            <!-- 할인률 : 가격 - 가격 * 할인률 * 0.01 -->
                            <div class="title">할인가 : <?=$obj->price - $obj->price * $obj->sale * 0.01 ?>원</div>
                            <div class="name">할인률 : <?=$obj->sale?>%</div>
                            <div class="content">
                                <a href="./order.php?id=<?=$obj->id?>&page=0"><?=$obj->name?></a><br>
                                원가 : <?=$obj->price?>원
                            </div>
                        </div>
                    </div>
                <?php endforeach;?>
            </div>
        </div>
    </section>
    <!-- //할인 이벤트 영역 -->