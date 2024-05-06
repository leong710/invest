<?php 
    // 20240306_優化結構
    $aResult = array();  // 定義結果陣列

    // 加上安全性檢查，檢查請求的方法是否是 POST、驗證資料等
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        require_once("../pdo.php");
        require_once("function.php");
        
        // step-1.確認基本數值
            $function = !empty($_REQUEST['function']) ? $_REQUEST['function'] : NULL;     // 操作功能
            if(empty($function)){
                if(!empty($aResult['error'])){ 
                    $aResult['error'] .= ' 未指定function!'; 
                }else{
                    $aResult['error'] = ' 未指定function!'; 
                }
            }

        // step-2.組合查詢參數陣列
            // 接收來自前端的資料
            $su = array (
                "table"     => isset($_REQUEST["table"])     ? $_REQUEST["table"]     : NULL,    // 日期
                "id"        => isset($_REQUEST["id"])        ? (string)$_REQUEST["id"]: NULL,    // log's id
                "flag"      => isset($_REQUEST["flag"])      ? $_REQUEST["flag"]      : NULL,    // 系統id  
            );
            
        // step-3.確認基本數值具備且無誤 => 執行function
            if( !isset($aResult['error']) ) {
                $aResult['success'] = 'Run function '.$function.'!';    // 預先定義回傳內容。
        
                switch($function) {
                    // fun-1.cheng_flag 快速切換上架On/下架Off 
                    case 'cheng_flag':
                        // 宣告查詢陣列內容
                        if(empty($su['id']) || empty($su['table'])) {
                            $aResult['error'] = $function.' - 參數錯誤!';

                        } else {
                            if($su['table'] == "site"){
                                $cheng_flag = changeSite_flag($su);

                            }else if($su['table'] == "fab"){
                                $cheng_flag = changeFab_flag($su);

                            }else if($su['table'] == "local"){
                                $cheng_flag = changeLocal_flag($su);

                            }else{
                                $cheng_flag = array(
                                    'table' => $su['table'], 
                                    'id'    => $su['id'],
                                    'flag'  => $su['flag']
                                );
                            }
                            $aResult['result'] = $cheng_flag;
                        }
                        break;

                    default:
                        $aResult['error'] = 'Not found function '.$function.'!';
                        break;
                }
                // 尋到物件行為進行記錄
                // toLog($_REQUEST);
            }
            if( isset($aResult['error']) ) { unset($aResult['success']); }
                    
            // api function --- start
            header("Access-Control-Allow-Origin: *");   // 允許跨網域!!
            header("Content-Type: application/json");

        } else {
            // 如果不是 POST 請求，返回錯誤訊息
            $aResult['error'] = 'Method Not Allowed'; 
            header('HTTP/1.1 405 Method Not Allowed');
        }
        // 將回傳的結果返回給前端
        // 參數：JSON_UNESCAPED_UNICODE 中文不編碼
        echo json_encode($aResult, JSON_UNESCAPED_UNICODE );
        // api function --- end
        