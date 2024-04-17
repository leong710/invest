<?php
    // 複製本頁網址藥用
    $up_href = (isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];   // 回上頁 // 回本頁

    if($_REQUEST){
        echo "<pre>";
        print_r($_REQUEST);
        echo "</pre>";
    }else{
        echo "<h5>-- _REQUEST nothing --</h5>";
        
    }
    if($_FILES){
        echo $_FILES['upload_file']['name'] ? 'true' : 'false';
        echo "</br><pre>";
        print_r($_FILES);
        echo "</pre>";
    }else{
        echo "<h5>-- _FILES nothing --</h5>";
        
    }
    
?>
<hr>
<button type="button" onclick="history.back()">回上一頁</button>