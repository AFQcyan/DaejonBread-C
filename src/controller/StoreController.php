<?php 
    namespace src\Controller;

    use src\App\DB;

    class StoreController{
        function dcUpdt(){
            extract($_POST);

            $id = $_POST['id'];
            $number = $_POST['number'];

            DB::fetch("UPDATE breads SET sale = {$number} WHERE id={$id}");
            back("성공적으로 수정되었습니다.");
        }
    }
?>