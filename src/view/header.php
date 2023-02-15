<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $title . "페이지" ?></title>
    <link rel="stylesheet" href="./resources/css/bootstrap.min.css">
    <link rel="stylesheet" href="./resources/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="./resources/css/prac.css">
    <script src="./resources/jQuery/jquery-3.6.0.min.js"></script>
    <script src="./resources/js/bootstrap.js"></script>
</head>
<body>
    <header>
        <div class="container logo-line">
            <a href="/"><img src="./resources/img/logoA.png" alt="메인 로고" title="메인 로고"></a>
            <nav>
                <ul class="menu d-none d-lg-flex">
                    <li>
                        <form action="/sub" method="get"><input type="submit" value="대전 빵집"></form>
                    </li>
                    <li>
                        <a href="./stamp.php">스탬프</a>
                    </li>
                    <li>
                        <a href="/discount">할인 이벤트</a>
                    </li>
                    <li>
                        <a href="/myPage">마이 페이지</a>
                    </li>
                    <li>
                    <?php
                    if(user()):?>
                        <li><a href='#'><<?php echo user()->name?>>(<<?php 
                            if(user()->type == 'owner'){echo "사장님";}                        
                            else if(user()->type == 'rider'){echo "라이더";}                        
                            else if(user()->type == 'normal'){echo "고객";}                        
                        ?>>)</a></li>
                        <li><a href='/logout'>로그아웃</a></li>
                        <?php else:?>
                        <li><a href="/login">로그인</a></li>
                        <?php endif;?>
                        </li>
                </ul>                                
            </nav>
        </div>
    </header>
    <header class="mobile-header d-none">
        <input type="checkbox" name="hamburg" id="hamburg" class="d-none">
        <label for="hamburg">
            <img src="./resources/img/logo-hbg.png" alt="">
        </label>
        <div class="hamburg_container">
            <ul class="ham_nav">
                <li>
                    <a href="/sub">대전 빵집</a>
                </li>
                <li>
                    <a href="./stamp.html">스탬프</a>
                </li>
                <li>
                    <a href="/discount">할인 이벤트</a>
                </li>
                <li>
                    <a href="/myPage">마이 페이지</a>
                </li>
                <li>
                    <a href="/login">로그인</a>
                </li>
                <img src="./resources/img/logo-ham-in.png" alt="">
            </ul>            
        </div>
    </header>
    <body>
    <div class="main">
    <!-- //헤더 영역 -->
