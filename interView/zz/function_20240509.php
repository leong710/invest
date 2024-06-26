<?php

// // // _document CRUD
    function store_document($request){
        $pdo = pdo();
        extract($request);
        $swal_json = array(                                 // for swal_json
            "fun"       => "store_document",
            "content"   => "新增表單--"
        );
        $swal_json["content"] .= $idty == "6" ? "暫存表單--":"";                // 20240506 -- 表單暫存

        // 例外處理單元：s
            // 處理a_pic上傳檔案
            $a_pic = !empty($a_pic) ? uploadFile($a_pic) : null; 

            $confirm_sign = !empty($confirm_sign) ? $confirm_sign : null ; 
            $ruling_sign  = !empty($ruling_sign)  ? $ruling_sign  : null ; 
            $_focus       = !empty($_focus)       ? $_focus       : null ; 

            if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $meeting_time)) {   // 检查值是否符合日期时间格式 'Y-m-d\TH:i'
                $meeting_time = convertDateTimeFormat($meeting_time);               // 转换日期时间格式
            }
            $_content   = $_REQUEST;
            // 使用迴圈刪除指定的元素
            $unset_keys = array( 'confirm_sign','ruling_sign','a_pic'   ,'anis_no','case_title','a_dept','meeting_time','meeting_local'   ,'submit_document','fab_id','local_id','sign_comm'
                                ,'meeting_man_a','meeting_man_o','meeting_man_s','meeting_man_d','created_emp_id','created_cname','action','step','idty','uuid','dcc_no');
            // foreach ($unset_keys as $key) { unset($_content[$key]); }
            //使用 array_diff_key() 函數，它會返回兩個或多個數組之間的差異，這樣就不需要使用循環逐個 unset 了。這樣會更簡潔和高效。
            $_content = array_diff_key($_content, array_flip($unset_keys));  

            // 把特定物件轉json
            $to_json_keys = array('_focus','_content','meeting_man_a','meeting_man_o','meeting_man_s');  // 'meeting_man_d' 是字串
            foreach ($to_json_keys as $jkey) { $$jkey = json_encode($$jkey); }

        // 例外處理單元：e

        // 製作log紀錄前處理：塞進去製作元素
            $logs_request = array(
                "action" => $action,
                "step"   => $step,
                "idty"   => $idty,
                "cname"  => $created_cname." (".$created_emp_id.")",
                "logs"   => "" ,
                "remark" => $sign_comm
            );
        // 呼叫toLog製作log檔
            $logs_enc = toLog($logs_request);

        $sql = "INSERT INTO _document( _focus, _content, confirm_sign, ruling_sign, a_pic 
                            , idty, dcc_no, fab_id, local_id, case_title,   anis_no, a_dept, meeting_local, meeting_man_a, meeting_man_o, meeting_man_s, meeting_man_d
                            , created_emp_id, created_cname, updated_cname, logs, meeting_time,   created_at, updated_at, uuid)
                VALUES( ?,?,?,?,?   ,?,?,?,?,?  ,?,?,?,?,?,?,?  ,?,?,?,?,?  ,now() ,now() ,uuid())";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$_focus, $_content, $confirm_sign, $ruling_sign, $a_pic
                            , $idty, $dcc_no, $fab_id, $local_id, $case_title, $anis_no, $a_dept, $meeting_local, $meeting_man_a, $meeting_man_o, $meeting_man_s, $meeting_man_d
                            , $created_emp_id, $created_cname, $created_cname, $logs_enc, $meeting_time]);
            $swal_json["action"]   = "success";
            $swal_json["content"] .= '儲存成功';

        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '儲存失敗';
        }
        return $swal_json;
    }
    function edit_document($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT * FROM _document WHERE uuid = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$uuid]);
            $_document = $stmt->fetch(PDO::FETCH_ASSOC);        // no index

            // 把特定json轉物件
            $re_json_keys = array('_focus','_content','meeting_man_a','meeting_man_o','meeting_man_s');
            foreach ($re_json_keys as $jkey) { $_document[$jkey] = json_decode($_document[$jkey]); }
            // 可以使用 array_map() 函数结合匿名函数，这样就不需要显式地使用 foreach 循环了
            // array_map(function($jkey) use (&$_document) { $_document[$jkey] = json_decode($_document[$jkey]); }, $re_json_keys);

            return $_document;
            
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    function update_document($request){
        $pdo = pdo();
        extract($request);

        // step0.起始運作設定
            $sql        = " UPDATE _document SET ";     // 起始-sql指令
            $stmt_arr   = array();                      // 初始查詢陣列
            $editions   = [];                           // 起始-修改項目log
            $edited_log = [];                           // 起始-修改項目log
            $swal_json  = array(                        // for swal_json
                "fun"       => "update_document",
                "content"   => "更新表單--"
            );
            $swal_json["content"] .= $idty == "6" ? "暫存表單--":"";                // 20240506 -- 表單暫存

        // step1.例外處理單元：s
        $a_pic        = !empty($a_pic)        ? $a_pic        : null ;                               // *** 上傳檔案要後面處理
        $confirm_sign = !empty($confirm_sign) ? $confirm_sign : null ; 
        $ruling_sign  = !empty($ruling_sign)  ? $ruling_sign  : null ; 
        $_focus       = !empty($_focus)       ? $_focus       : null ; 
            if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $meeting_time)) {   // 检查值是否符合日期时间格式 'Y-m-d\TH:i'
                $meeting_time = convertDateTimeFormat($meeting_time);               // 转换日期时间格式
            }

        // step2.舊檔案處理
            $row_document = edit_document(["uuid"=>$uuid]);         // 叫出舊紀錄
            $row_logs     = $row_document["logs"];
            $row_editions = $row_document["editions"];

        // step3.使用迴圈刪除指定的元素for舊紀錄 因為不需要比對
            $unset_keys = array( 'id','uuid','idty','created_emp_id','created_cname','created_at','updated_at','updated_cname','update_document','logs','editions'); 
            // foreach ($unset_keys as $key) { unset($row_document[$key]); }
            //使用 array_diff_key() 函數，它會返回兩個或多個數組之間的差異，這樣就不需要使用循環逐個 unset 了。這樣會更簡潔和高效。
            $row_document = array_diff_key($row_document, array_flip($unset_keys));  

        // step4-1.使用迴圈刪除指定的元素，並將指定的元素提前進行比對(小圈)
            $unset_keys = array('_focus', '_content' );
            foreach ($unset_keys as $unset_key) {
                $$unset_key = (array) $row_document[$unset_key];    // 先從記錄中帶出來 => 內圈
                unset($row_document[$unset_key]);                   // 從記錄中移除
                $edited_log[$unset_key] = [];                       // 起始-修改項目log[主題key]

                $row_keys = array_keys($$unset_key);                // 取出內圈的 key_list
                foreach($row_keys as $row_key){
                    $edit_item = [];                                // 起始-修改項目 & 清空
                    if(gettype($$unset_key[$row_key]) == 'array' || gettype($$unset_key[$row_key]) == 'object'){        // 針對combo項目進行判別
                        $old_item = json_encode($$unset_key[$row_key]);            
                        $new_item = json_encode($$row_key);
                        if($new_item != $old_item ){
                            $old_item = json_encode($$unset_key[$row_key], JSON_UNESCAPED_UNICODE );        // 中文不編碼
                            $new_item = json_encode($$row_key,             JSON_UNESCAPED_UNICODE );
                            echo $row_key." : ".$old_item ." => ".$new_item. "</br>";                       // 螢幕顯示
                            $edit_item = $old_item." => ".$new_item;                                        // 生成修改訊息
                            $$unset_key[$row_key] = $$row_key;                                              // 把有修改的部分倒回去陣列
                        }
                    }else{
                        if(isset($$row_key)){
                            if(gettype($$row_key) != 'array' && gettype($$row_key) != 'object'){            // 預防value是陣列或物件
                                if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $$row_key)) {               // 检查值是否符合日期时间格式 'Y-m-d\TH:i'
                                    $$row_key = convertDateTimeFormat($$row_key);                               // 转换日期时间格式
                                }
                                if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $$unset_key[$row_key])) {   // 检查值是否符合日期时间格式 'Y-m-d\TH:i'
                                    $$unset_key[$row_key] = convertDateTimeFormat($$unset_key[$row_key]);       // 转换日期时间格式
                                }
                            }
                        }else{
                            $$row_key = null;
                        }

                        if($$row_key != $$unset_key[$row_key]){
                            echo $row_key." : ".$$unset_key[$row_key]." => ".$$row_key. "</br>";            // 螢幕顯示
                            $edit_item = $$unset_key[$row_key]." => ".$$row_key;                            // 生成修改訊息
                            $$unset_key[$row_key] = $$row_key;                                              // 把有修改的部分倒回去陣列
                        }
                    }
                    if(!empty($edit_item)){
                        $edited_log[$unset_key][$row_key] = $edit_item;                                     // 將修改訊息倒入指定陣列位置
                    }
                }
                // 確認修改訊息，有值就是需要添加SQL修改項目
                if(!empty($edited_log[$unset_key])){
                    $sql .= $unset_key."=?, ";
                    $$unset_key = json_encode($$unset_key);
                    array_push($stmt_arr, $$unset_key);
                }
            }

        // step4-2.將大圈繞出來比對
            $row_document_keys = array_keys($row_document);         // 取出外圈的 key_list
            foreach($row_document_keys as $row_key){
                $edited_log[$row_key] = [];                         // 起始-修改項目log[主題key]
                $edit_item = [];                                    // 起始-修改項目 & 清空
                if(isset($$row_key)){
                    if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}$/', $$row_key)) {                 // 检查值是否符合日期时间格式 'Y-m-d\TH:i'
                        $$row_key = convertDateTimeFormat($$row_key);                                       // 转换日期时间格式
                    }
                    if($row_key == "meeting_time"){
                        $meeting_time = $meeting_time.":00";                                                // 將送進來的meeting_time進行加工
                    }
                }else{
                    $$row_key = null;
                }


                if($$row_key != $row_document[$row_key]){
                    // 處理a_pic上傳檔案
                    if($row_key == "a_pic"){
                        if(!empty($row_document[$row_key])){                            // 判斷舊a_pic是否有值
                            $result = unlinkFile($row_document[$row_key]);              // 有=清除舊檔
                            if(!$result){
                                $swal_json["action"]   = "error";
                                $swal_json["content"] .= 'a_pic刪除失敗';
                                // return $swal_json;
                            }
                        }
                        if(!empty($$row_key)){                                          // 判斷新a_pic是否有值
                            $$row_key = uploadFile($$row_key);                          // 有=上傳檔案，並返回新檔名
                        }
                    }

                    $edit_item = $row_document[$row_key]." => ".$$row_key;                                  // 生成修改訊息
                    echo $row_key." : ".$edit_item. "</br>";                  // 螢幕顯示
                    
                    // 確認修改訊息，有需要添加SQL修改項目
                    $sql .= $row_key."=?, ";
                    array_push($stmt_arr, $$row_key);
                }
                // 確認修改訊息，有值就是需要添加SQL修改項目
                if(!empty($edit_item)){
                    // array_push($edited_log[$unset_key], $edit_item);
                    $edited_log[$row_key] = $edit_item;                                         // 將修改訊息倒入指定陣列位置
                }
            }

            $edited_log = array_filter($edited_log);                                            // 過濾空陣列
            // if(count($edited_log) == 0){                                                        // 當筆數=0
            //     $edited_log["本次修改"] = "共 ".count($edited_log)." 處。";                      // 加上簡單提示。
            // }

        // step5.例外處理單元：s
            // 把特定物件轉json
            $to_json_keys = array('_focus','_content','meeting_man_a','meeting_man_o','meeting_man_s');  // 'meeting_man_d' 是字串
            foreach ($to_json_keys as $jkey) { $$jkey = json_encode($$jkey); }
        
        // step6.編輯紀錄 => 1送出 6暫存
            if($idty != "6" && count($edited_log) != 0){  // 表單狀態 6暫存&&沒log => 不進行編輯紀錄
                // step6-1.製作Editions編輯紀錄前處理：塞進去製作元素
                    $editions = array(
                        "updated_cname"   => $created_cname." (".$created_emp_id.")",
                        "update_document" => $edited_log,
                        "editions"        => !empty($row_editions) ? $row_editions : ""
                    );
                // step6-2.呼叫toEditLog製作EditionsLog檔
                    $editions_enc = toEditLog($editions);
                // step6-3.確認修改訊息，有需要添加SQL修改項目
                    $sql .= "editions=?, ";
                    array_push($stmt_arr, $editions_enc);
            }

        // step7-1.製作log紀錄前處理：塞進去製作元素
            $logs_request = array(
                "action" => $action,
                "step"   => $step,
                "idty"   => $idty,
                "cname"  => $created_cname." (".$created_emp_id.")",
                "logs"   => $row_logs ,
                "remark" => $sign_comm
            );
        // step7-2.呼叫toLog製作log檔
            $logs_enc = toLog($logs_request);
        // step7-3.確認修改訊息，有需要添加SQL修改項目
            $sql .= "logs=?, ";
            array_push($stmt_arr, $logs_enc);
        // step8.sql指令帶入基本要項
            $sql .= "updated_cname=?, updated_at=now() WHERE uuid=? ";
            array_push($stmt_arr, $created_cname);
            array_push($stmt_arr, $uuid);
        // step9.儲存工作
            // // $sql = "UPDATE _document SET _type=?, title=?, dcc_no=?, _icon=?, flag=?, updated_user=?, updated_at=now() WHERE id=?";
            // // $stmt->execute([$_type, $title, $dcc_no, $_icon, $flag, $updated_user, $id]);
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute($stmt_arr);                          // 處理 byUser & byYear
                $swal_json["action"]   = "success";
                $swal_json["content"] .= "儲存成功";
            }catch(PDOException $e){
                echo $e->getMessage();
                $swal_json["action"]   = "error";
                $swal_json["content"] .= "儲存失敗";
            }
        // step9.返回swal
        return $swal_json;
    }

    function delete_document($request){
        $pdo = pdo();
        extract($request);
        $swal_json = array(                                 // for swal_json
            "fun"       => "delete_document",
            "content"   => "刪除表單--"
        );
        // 舊檔案處理
        $row_document = edit_document(["uuid"=>$uuid]);         // 叫出舊檔案
        $row_document_a_pic = $row_document["a_pic"];           // 取得舊a_pic
        if(!empty($row_document_a_pic)){                           // 判斷是否有值
            $result = unlinkFile($row_document_a_pic);             // 清除舊檔
            if(!$result){
                $swal_json["action"]   = "error";
                $swal_json["content"] .= 'a_pic刪除失敗';
                // return $swal_json;
            }
        }
        $sql = "DELETE FROM _document WHERE uuid = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$uuid]);
            $swal_json["action"]   = "success";
            $swal_json["content"] .= '刪除成功';
        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '刪除失敗';
        }
        return $swal_json;
    }

    // formcase 隱藏或開啟
    function changeformcase_flag($request){
        $pdo = pdo();
        extract($request);

        $sql_check = "SELECT _document.* FROM _document WHERE id=? ";
        $stmt_check = $pdo -> prepare($sql_check);
        $stmt_check -> execute([$id]);
        $row = $stmt_check -> fetch(PDO::FETCH_ASSOC);

        $flag = ($row['flag'] == "Off" || $row['flag'] == "chk") ? "On" : "Off" ;        

        $sql = "UPDATE _document SET flag=? WHERE id=? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$flag, $id]);
            $Result = array(
                'id'   => $id,
                'flag' => $flag
            );
            return $Result;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 在index表頭顯示清單：
    function show_document(){
        $pdo = pdo();
        $sql = "SELECT * FROM _document ORDER BY id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $formcase = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $formcase;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // 20240419_上傳檔案json
    function uploadFile($fileName){
        $file_from = "../image/temp/";              // 過度路徑
        $file_to   = "../image/a_pic/";             // submit後正是路徑
            if(!is_dir($file_to)){                  // 检查資料夾是否存在
                mkdir($file_to); 
            }

        $refileName = $fileName;
        $toFile = $file_to . $fileName;          // 合成上傳路徑 + 檔名

        // 檢查是否已存在相同檔名的檔案，如果存在則在檔名前加上數字
        for ($i = 2; is_file($toFile); $i++) {
            $refileName = $i . "_" . $fileName;
            $toFile = $file_to . $refileName;    // 合成上傳路徑 + 檔名
        }
    
        // 移動文件到指定目錄
        if (rename($file_from . $fileName , $toFile)) {
            return $refileName;                   // 返回檔案名稱
        } else {
            return false;                         // 返回錯誤
        }
    }    
    // 20240417_移到垃圾桶json
    function unlinkFile($unlinkFile){
                
        $file_from = "../image/a_pic/";                // submit後正是路徑
        $file_to   = "../image/a_pic_offLine/";        // submit後再搬移到垃圾路徑

        $rename_time = date('Ymd-His');

        // 確認檔案在目錄下
        if(is_file($file_from .$unlinkFile)) {
            // // 移除檔案 unlink($unlinkFile); 
            // 搬到垃圾桶
            $moved = rename( $file_from .$unlinkFile , $file_to .$rename_time ."_" .$unlinkFile );
            // 返回完成訊息
            if($moved){
                return true;
            }else{
                return false;
            }
        } else {
            return false;
        }
    }
    // 20240417_確認檔案是否存在
    function check_is_file($fileName){
        $uploadDir = '../doc_json/';                    // 過度路徑，submit後再搬移到正是路徑
        if(is_file($uploadDir .$fileName )) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }

            // 20240419 檔名編碼
            function encode_filename($filename) {
                $encoded_filename = base64_encode($filename);
                return $encoded_filename;
            }
            // 20240419 檔名解碼
            function decode_filename($encoded_filename) {
                $decoded_filename = base64_decode($encoded_filename);
                return $decoded_filename;
            }

// // // CSV & Log tools
    // 製作記錄JSON_Log檔   20230803
    function toLog($request){
        extract($request);
        // log資料前處理
        // 交易狀態：0完成/1待收/2退貨/3取消/12發貨
        switch($idty){
            case "0":   $action = '同意 (Approve)';        break;
            case "1":   $action = '送出 (Submit)';         break;
            case "2":   $action = '退回 (Reject)';         break;
            case "3":   $action = '作廢 (Abort)';          break;
            case "4":   $action = '編輯 (Edit)';           break;
            case "5":   $action = '轉呈 (Forwarded)';      break;
            case "6":   $action = '暫存 (Save)';           break;
            case "10":  $action = '同意 (Approve)';        break;    // 結案 (Close)
            case "11":  $action = '同意 (Approve)';        break;    // 承辦 (Undertake)
            case "12":  $action = '待收發貨 (Awaiting collection)';   break;
            case "13":  $action = '交貨 (Delivery)';       break;
            case "14":  $action = '庫存-扣帳 (Debit)';      break;
            case "15":  $action = '庫存-回補 (Replenish)';  break;
            case "16":  $action = '庫存-入賬 (Account)';    break;
            default:    $action = '錯誤 (Error)';         return;
        }

        if(!isset($logs)){
            $logs = [];
            $logs_arr =[];
        }else{
            $logs_dec = json_decode($logs);
            $logs_arr = (array) $logs_dec;
        }

        $app = [];  // 定義app陣列=appry
        // 因為remark=textarea會包含換行符號，必須用str_replace置換/n標籤
        $log_remark = str_replace(array("\r\n","\r","\n"), "_rn_", $remark);
        $app = array(   "step"      => $step,
                        "cname"     => $cname,
                        "datetime"  => date('Y-m-d H:i:s'), 
                        "action"    => $action,
                        "remark"    => $log_remark);

        array_push($logs_arr, $app);
        $logs = json_encode($logs_arr);
        return $logs;        
    }
    // 製作記錄JSON_EditLog檔   202404
    function toEditLog($request){
        extract($request);
        // log資料前處理
        if(!isset($editions)){
            $editions = [];
            $editions_arr = [];
        }else{
            $editions_arr = (array) json_decode($editions);
        }
        $app = [];  // 定義app陣列=appry
        $app = array(   "updated_at"        => date('Y-m-d H:i:s'), 
                        "updated_cname"     => $updated_cname,
                        'update_document'   => $update_document
                    );
        array_push($editions_arr, $app);
        return json_encode($editions_arr);       
    }
            // 讀取所有JSON_Log記錄 20230804
            function showLogs($request){
                $pdo = pdo();
                extract($request);
                $sql = "SELECT _receive.logs
                        FROM `_receive`
                        WHERE _receive.uuid = ?";
                $stmt = $pdo->prepare($sql);
                try {
                    $stmt->execute([$uuid]);
                    $receive_logs = $stmt->fetch(PDO::FETCH_ASSOC);
                    return $receive_logs;

                }catch(PDOException $e){
                    echo $e->getMessage();

                }
            }
            // 刪除單項log值-20230804
            function updateLogs($request){
                $pdo = pdo();
                extract($request);

                $query = array('uuid'=> $uuid );
                // 把_receive表單叫近來處理
                $receive = showLogs($query);
                //這個就是JSON格式轉Array新增字串==搞死我
                $logs_dec = json_decode($receive['logs']);
                $logs_arr = (array) $logs_dec;
                // unset($logs_arr[$log_id]);  // 他會產生index導致原本的表亂掉
                array_splice($logs_arr, $log_id, 1);  // 用這個不會產生index

                $logs = json_encode($logs_arr);
                $sql = "UPDATE _receive 
                        SET logs = ? 
                        WHERE uuid = ?";
                $stmt = $pdo->prepare($sql);
                try {
                    $stmt->execute([$logs, $uuid]);
                }catch(PDOException $e){
                    echo $e->getMessage();
                }
            }
// // // CSV & Log tools -- end

    function show_fab_lists(){
        $pdo = pdo();
        $sql = "SELECT _fab.*, _site.site_title, _site.site_remark, _site.flag AS site_flag
                FROM _fab
                LEFT JOIN _site ON _site.id = _fab.site_id
                -- WHERE _fab.flag = 'On' AND _site.flag = 'On'
                ORDER BY _site.id, _fab.id ASC ";
        $stmt = $pdo->prepare($sql);                                // 讀取全部=不分頁
        try {
            $stmt->execute();
            $fabs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $fabs;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function show_local_lists(){
        $pdo = pdo();
        $sql = "SELECT _l.*
                FROM _local _l
                -- LEFT JOIN _fab _f ON _f.id = _l.fab_id
                -- WHERE _fab.flag = 'On' AND _site.flag = 'On'
                ORDER BY _l.id ASC ";
        $stmt = $pdo->prepare($sql);                                // 讀取全部=不分頁
        try {
            $stmt->execute();
            $locals = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $locals;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 日期時間格式轉換
    function convertDateTimeFormat($datetime) {
        // 创建 DateTime 对象，并指定原始时间格式
        $datetime_obj = DateTime::createFromFormat("Y-m-d\TH:i", $datetime);

        // 将时间格式化为目标格式
        $formatted_datetime = $datetime_obj->format("Y-m-d H:i");

        return $formatted_datetime;
    }