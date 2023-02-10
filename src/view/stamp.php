<?php

require_once('./resources/php/header.php');

?>
    <!-- 스탬프 영역 -->
    <script src="./resources/js/stamp.js"></script>

    <div class="popup" id="down">
        <div>
            <div class="d-flex">
                <input type="text" placeholder="이름">
                <button class="button buttonhlt d-none d-lg-flex">발급하기</button>
            </div>
            <a class="btn btn-light" href="#">닫기</a>
        </div>
    </div>

    <div class="popup" id="file">
        <div>
            <p><button class="btn btn-dark">파일 선택</button> 선택된 파일이 없습니다.</p>
            <a href="#" class="btn btn-light">닫기</a>
        </div>
    </div>

    <section id="stamp" class="main-section">
        <div class="container">
            <div class="section-info">
                <div class="title">스탬프</div>
                <div class="text">
                    <div class="content">
                        스탬프카드에 <b>스탬프</b>를 찍으세요
                    </div>
                    <a href="#down" class="button buttonhlt d-none d-lg-flex">발급하기</a>
                </div>
            </div>
        </div>
    </section>

    <section id="code" class="main-section">
        <div class="container d-flex justify-content-center">
            <input type="text" placeholder="코드입력"> <button class="button buttonhlt d-none d-lg-flex">스탬프 찍기</button>
        </div>
    </section>


    <section id="roulette" class="main-section">
        <div class="container">
            <div class="section-info">
                <div class="title">룰렛</div>
                <div class="text">
                    <div class="content">
                        경품 <b>룰렛</b> 돌리기
                    </div>
                    <button class="button buttonhlt d-none d-lg-flex">파일선택</button>
                </div>
            </div>

            <div class="d-flex justify-content-center">      
                <div class="roulette"></div>      
                <canvas width="600" height="600"></canvas>
            </div>
        </div>
    </section>
    <!-- //스탬프 영역 -->

    <?php
require_once('./resources/php/footer.php');
?>