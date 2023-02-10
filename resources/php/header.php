<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/resources/php/lib.php");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>대전브레드투어</title>
    <link rel="stylesheet" href="./resources/css/bootstrap.min.css">
    <link rel="stylesheet" href="./resources/fontawesome/css/font-awesome.min.css">
    <!-- <link rel="stylesheet" href="./resources/css/style.css"> -->
    <link rel="stylesheet" href="./resources/css/prac.css">
    <script src="./resources/js/bootstrap.js"></script>
</head>
<body>
    <header>
        <div class="container logo-line">
            <a href="./index.php"><img src="./resources/img/logoA.png" alt="메인 로고" title="메인 로고"></a>
            <nav>
                <ul class="menu d-none d-lg-flex">
                    <li>
                        <a href="./sub.php">대전 빵집</a>
                    </li>
                    <li>
                        <a href="./stamp.php">스탬프</a>
                    </li>
                    <li>
                        <a href="./discount.php">할인 이벤트</a>
                    </li>
                    <li>
                        <a href="./myPage.php">마이 페이지</a>
                    </li>
                    <li>
                    <?php
                    if(isset($_SESSION['user'])):?>
                        <li><a href='#'><?=print_text('<' . $_SESSION['user']['name'] . '>' . '(<'. $_SESSION['user']['type'] .'>)')?></a></li>
                        <li><a href='logout.php'>로그아웃</a></li>
                        <?php else:?>
                        <li><a href="login.php">로그인</a></li>
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
                    <a href="./sub.html">대전 빵집</a>
                </li>
                <li>
                    <a href="./stamp.html">스탬프</a>
                </li>
                <li>
                    <a href="#">할인 이벤트</a>
                </li>
                <li>
                    <a href="#">마이 페이지</a>
                </li>
                <li>
                    <a href="#">로그인</a>
                </li>
                <img src="./resources/img/logo-ham-in.png" alt="">
            </ul>            
        </div>
    </header>
    <body>
    <div class="main">
    <!-- //헤더 영역 -->
