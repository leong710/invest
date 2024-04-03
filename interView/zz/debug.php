<?php
    // 複製本頁網址藥用
    $up_href = (isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];   // 回上頁 // 回本頁

    if($_REQUEST){
        echo "<pre>";
        print_r($_REQUEST);
        echo "</pre>";
    }else{
        echo "<h5>-- nothing --</h5>";
        
    }
    
?>
<hr>
<button type="button" onclick="history.back()">回上一頁</button>