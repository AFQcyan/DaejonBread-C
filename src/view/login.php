<?php

require_once('./resources/php/header.php');

?>

<div class="subpage_section">
    <?php
    // 이미 로그인이 되어 있지 않으면 로그인을 할 수 있는 폼을 띄웁니다.
    if(isset($_SESSION['user'])):
        echo "<h1 class='alrdy'>이미 로그인 되어 있습니다.<h1>";
    else:
    ?>
        <form action="./login_ok.php" method="post">
            <div class="form-group">
                <label for="userid">ID</label>
                <input type="text" id="userid" name="id" class="form-control" placeholder="아이디를 입력하세요.">
            </div>

            <div class="form-group">
                <label for="userpw">비밀번호</label>
                <input type="password" id="userpw" name="pw" class="form-control" placeholder="비밀번호를 입력하세요.">
            </div>
            <div class="form-group d-flex justify-content-end mt-5">
                <button type="submit" class="button buttonhlt">로그인</button>
            </div>
        </form>
    <?php endif;?>
</div>


<?php
require_once('./resources/php/footer.php');
?>