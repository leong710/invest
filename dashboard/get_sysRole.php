<?php
    require_once("../pdo.php");
    require_once("../user_info.php");
    if(!isset($_SESSION)){                                              // 確認session是否啟動
        session_start();
    }

    echo json_encode($sys_role);