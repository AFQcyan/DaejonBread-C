<?php

use src\App\DB;

?>
    <!-- 검색영역 -->
    <section id="search" class="main-section">
        <div class="container">
            <div class="section-info">
                <div class="title">검색</div>
            </div>

            <input type="hidden" name="bakza" value='박수짝'>
            <!-- type엔 무엇을 검색할지, keyword엔 검색어를 담아 현재 페이지로 전달합니다 -->
            <select name="type" id='type'>
                <option value="name" <?php if($type == 'name'){echo "selected";}?>>빵집이름</option>
                <option value="menu" <?php if($type == 'menu'){echo "selected";}?>>메뉴</option>
                <option value="location"<?php if($type == 'location'){echo "selected";}?>>지역</option>
            </select>
            <input type="text" name="keyword" id='keyword' value="<?php echo $keyword;?>">
            <input type="submit" id='searchBtn' class="btn btn-dark" value="검색">
 

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
                // 5개의 가게를 출력했으면 반복문을 종료한다?>
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
                    <button type="submit" class='bread_list'>
                    
                    </button>
            </div>
        </div>
    </section>
    <script type="text/javascript">
        init();
        function init(){
            sendData = {
                type: $('#type').val(),
                keyword: $('#keyword').val()           
            }
            $.ajax({
                method : "POST",            // HTTP method type(GET, POST) 형식이다.
                url : "/sub_search",      // 컨트롤러에서 대기중인 URL 주소이다.
                datatype : 'json',
                data : sendData, //res = XMLhttpRe... -> responseText <- php 에서 반환하는 값 (error 포함)
                //php에선 error 를 직접 화면에 띄우기 때문에, html 을 통째로 보냄.
                success : function(res){ // 비동기통신의 성공일경우 success콜백으로 들어옵니다. 'res'는 응답받은 데이터이다.
                    // 응답코드 > 0000
                    res = JSON.parse(res)['result'];
                    //top5
                    $('.top5').remove();
                    for(i = 0;i < 5 && i < res.length;i++){
                        $('#best .product-grid').append(`
                        <form action='/order' method='post'> 
                        <button type="submit" class ='top5'>
                        <div class="item target">
                            <img src="./resources/img` + res[i]['image'] + `" alt="best" title="best">
                            <div class="text">
                                <!-- 가게이름을 클릭하면 GET에 id=가게id page=0를 담아서 order.php로 이동한다 -->
                                <input type="hidden" name="page" value="0">
                                <input type="hidden" name="id" value="` + res[i]['id'] + `">
                                <div class="title"><a href="#">` + res[i]['name'] + `</a></div>
                                <div class="name">` + res[i]['location'] + `</div>
                                <div class="content">
                                    전화번호 : ` + res[i]['connect'] + ` <br>
                                    평점 : ` + res[i]['score'] + `점 <br>
                                    리뷰 : 리뷰 ` + res[i]['cmt'] + `개 <br>
                                </div>
                            </div>
                        </div>
                    </button>
                    </form>
                        `)
                    }
                    $('.bread_list').remove();
                    for(i = 5;i < res.length;i++){
                        $('#bread_list .product-grid').append(`
                        <form action='/order' method='post'> 
                        <button type="submit" class ='bread_list'>
                        <div class="item target">
                            <img src="./resources/img` + res[i]['image'] + `" alt="best" title="best">
                            <div class="text">
                                <!-- 가게이름을 클릭하면 GET에 id=가게id page=0를 담아서 order.php로 이동한다 -->
                                <input type="hidden" name="page" value="0">
                                <input type="hidden" name="id" value="` + res[i]['id'] + `">
                                <div class="title"><a href="#">` + res[i]['name'] + `</a></div>
                                <div class="name">` + res[i]['location'] + `</div>
                                <div class="content">
                                    전화번호 : ` + res[i]['connect'] + ` <br>
                                    평점 : ` + res[i]['score'] + `점 <br>
                                    리뷰 : 리뷰 ` + res[i]['cmt'] + `개 <br>
                                </div>
                            </div>
                        </div>
                    </button>
                    </form>
                        `)
                    }
                }
            })
        }
        $("#searchBtn").on('click', () =>{
            init();            
        })
  </script>
    
    <!-- //빵집리스트 영역 -->