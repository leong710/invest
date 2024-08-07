<?php
    // 20240306_優化API結構
    $aResult = array();  // 定義結果陣列

    // 加上安全性檢查，檢查請求的方法是否是 POST、驗證資料等
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        require_once("../pdo.php");
        require_once("update_api_function.php");

    // step-1.確認基本數值
        $function = !empty($_REQUEST['function']) ? $_REQUEST['function'] : NULL;     // 操作功能

        if (empty($function) && empty($aResult['error'])) {
            $aResult['error'] = '未指定function!';
        } else if (empty($function) && !empty($aResult['error'])) {
            $aResult['error'] .= ' 未指定function!';
        }
        
    // step-3.確認基本數值具備且無誤 => 執行function
        if( !isset($aResult['error']) ) {
            $aResult['success'] = 'Run function '.$function.'!';    // 預先定義回傳內容。

            switch($function) {
                // fun-1.update_confirm_sign 快速更換上傳pdf文件 
                case 'update_confirm_sign':
                    // step-2.組合查詢參數陣列
                    $su = ["case_year", "confirm_sign", "confirm_sign_new", "anis_no", "uuid"]; // update_confirm_sign
                    // 接收來自前端的資料
                        foreach($_REQUEST as $key => $key_value){
                            // if(empty($key_value) && $key != "confirm_sign_new"){        // 但書for刪除=>
                            //     $aResult['error'] = (empty($aResult['error'])) ? $key.' - 空值錯誤!' : $aResult['error'].$key.' - 空值錯誤!';
                            // }else{
                            // }
                            $su[$key] = $key_value;
                        };
                    if(empty($su['uuid'])) {
                        $aResult['error'] = $function.'：uuid - 參數錯誤!';
                    } else {
                        $aResult['result'] = update_confirm_sign($su);
                    }
                    break;

                // fun-2.update_odd 快速更換 職災申報日期_odd
                case 'update_odd':
                    // step-2.組合查詢參數陣列
                    $su = ["_odd", "uuid"]; // update_confirm_sign
                    // 接收來自前端的資料
                        foreach($_REQUEST as $key => $key_value){
                            $su[$key] = $key_value;
                        };
                    if(empty($su['uuid'])) {
                        $aResult['error'] = $function.'：uuid - 參數錯誤!';
                    } else {
                        $aResult['result'] = update_odd($su);
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
        http_response_code(500);
        $aResult['error'] = 'Method Not Allowed'; 
        header('HTTP/1.1 405 Method Not Allowed');
    }
    // 將回傳的結果返回給前端 // 參數：JSON_UNESCAPED_UNICODE 中文不編碼
    echo json_encode($aResult , JSON_UNESCAPED_UNICODE );
    // api function --- end

?>
