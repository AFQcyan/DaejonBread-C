<?php

use src\App\DB;

?>
    <!-- 검색영역 -->
    <section id="search" class="main-section">
        <div class="container">
            <div class="section-info">
                <div class="title">검색</div>
            </div>

            <form action="/sub" method="post">
                <!-- type엔 무엇을 검색할지, keyword엔 검색어를 담아 현재 페이지로 전달합니다 -->
                <select name="type">
                    <option value="name" <?php if($type == 'name'){echo "selected";}?>>빵집이름</option>
                    <option value="menu" <?php if($type == 'menu'){echo "selected";}?>>메뉴</option>
                    <option value="location"<?php if($type == 'location'){echo "selected";}?>>지역</option>
                </select>
                <input type="text" name="keyword" value="<?php echo $keyword;?>">
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
                    <form action="/order" method="post">
                        <button type="submit">
                            <div class="item target">
                                <img src="./resources/img<?=$obj->image?>" alt="best" title="best">
                                <div class="text">
                                    <!-- 가게이름을 클릭하면 GET에 id=가게id page=0를 담아서 order.php로 이동한다 -->
                                    <input type="hidden" name="page" value="0">
                                    <input type="hidden" name="id" value="<?=$obj->id?>">
                                    <div class="title"><a href="/order"><?=$obj->name?></a></div>
                                    <div class="name"><?=$obj->location?></div>
                                    <div class="content">
                                        전화번호 : <?=$obj->connect?> <br>
                                        평점 : <?=$obj->score?>점 <br>
                                        리뷰 : 리뷰 <?=$obj->cmt?>개 <br>
                                    </div>
                                </div>
                            </div>
                        </button>
                    </form>
                <?php endfor;?>
            </div>
        </div>
    </section>
    <!-- //빵집베스트5 영역 -->

    <!-- 빵집리스트 영역 -->
    <section id="bread_list" class="main">
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
                <form action="/order" method="post">
                    <input type="hidden" name="id" value='<?php echo $obj->id ?>'>
                    <input type="hidden" name="id" value='<?php echo $obj->id ?>'>
                    <button type="submit">
                    <div class="item target">
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
                    </button>
                </form>
                <?php endfor;?>
            </div>
        </div>
    </section>
    <!-- //빵집리스트 영역 -->