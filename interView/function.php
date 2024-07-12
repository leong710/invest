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
            // 240709 組合上傳目的路徑 $to_path_obj
            $to_path_obj = [
                "case_year" => date('Y'),
                "anis_no"   => $anis_no
            ];

            // 處理a_pic上傳檔案
            $a_pic         = !empty($a_pic)         ? uploadFile($to_path_obj, $a_pic)         : null ; 
            $a_self_desc   = !empty($a_self_desc)   ? uploadFile($to_path_obj, $a_self_desc)   : null ;
            $a_others_desc = !empty($a_others_desc) ? uploadFile($to_path_obj, $a_others_desc) : null ;

            $_odd          = !empty($_odd)          ? $_odd         : null ;        // 這裡先帶入預設，後續呼叫通報判斷fun
            $confirm_sign  = !empty($confirm_sign)  ? $confirm_sign : null ; 
            $ruling_sign   = !empty($ruling_sign)   ? $ruling_sign  : null ; 
            $_focus        = !empty($_focus)        ? $_focus       : null ;

            if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $meeting_time)) {   // 检查值是否符合日期时间格式 'Y-m-d\TH:i'
                $meeting_time = convertDateTimeFormat($meeting_time);               // 转换日期时间格式
            }
            // 使用迴圈刪除指定的元素
            $unset_keys = array('anis_no','fab_id','local_id',  'case_title','a_dept','meeting_time','meeting_local',  'meeting_man_a','meeting_man_o','meeting_man_s','meeting_man_d',
                                'omager', '_odd', 'confirm_sign','ruling_sign','a_pic','sign_comm',  'uuid','idty','action','step','dcc_no',  'created_emp_id','created_cname','updated_cname',
                                'submit_document','update_document','save_document','a_self_desc','a_others_desc');
            // foreach ($unset_keys as $key) { unset($_content[$key]); }
            //使用 array_diff_key() 函數，它會返回兩個或多個數組之間的差異，這樣就不需要使用循環逐個 unset 了。這樣會更簡潔和高效。
            $_content = array_diff_key($_REQUEST, array_flip($unset_keys));  
            
            // 240705_事故者+目擊者自述 上傳pdf檔...
            $_focus['a_self_desc']      = !empty($a_self_desc)      ? $a_self_desc   : null;
            $_focus['a_others_desc']    = !empty($a_others_desc)    ? $a_others_desc : null;

            // 20240611 確認申報日期
            $_odd = confirm_odd($_content);

            // 把特定物件轉json
            // $to_json_keys = array('_focus','_content','meeting_man_a','meeting_man_o','meeting_man_s');  // 'meeting_man_d' 是字串
            // foreach ($to_json_keys as $jkey) { $$jkey = json_encode($$jkey); }

                    $data = [
                        '_odd'          => $_odd,                       // 職災申報
                        '_focus'        => $_focus,                     // 關注：備用
                        '_content'      => $_content,                   // 問項內容
                        'meeting_man_a' => $meeting_man_a,              // 事故人員
                        'meeting_man_o' => $meeting_man_o,              // 其他與會
                        'meeting_man_s' => $meeting_man_s               // 環安人員
                    ];

                    foreach ($data as $key => $value) { $data[$key] = json_encode($value); }
                    
                    $_odd          = $data['_odd'];
                    $_focus        = $data['_focus'];
                    $_content      = $data['_content'];
                    $meeting_man_a = $data['meeting_man_a'];
                    $meeting_man_o = $data['meeting_man_o'];
                    $meeting_man_s = $data['meeting_man_s'];

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

        $sql = "INSERT INTO _document(_odd, _focus, _content, confirm_sign, ruling_sign, a_pic 
                            , omager, idty, dcc_no, fab_id, local_id, case_title,   anis_no, a_dept, meeting_local, meeting_man_a, meeting_man_o, meeting_man_s, meeting_man_d
                            , created_emp_id, created_cname, updated_cname, logs, meeting_time,   created_at, updated_at, uuid)
                VALUES( ?,?,?,?,?,?   ,?,?,?,?,?,?  ,?,?,?,?,?,?,?  ,?,?,?,?,?  ,now() ,now() ,uuid())";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$_odd, $_focus, $_content, $confirm_sign, $ruling_sign, $a_pic
                            , $omager, $idty, $dcc_no, $fab_id, $local_id, $case_title, $anis_no, $a_dept, $meeting_local, $meeting_man_a, $meeting_man_o, $meeting_man_s, $meeting_man_d
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
        $sql = "SELECT _d.* , _fc.short_name, _f.fab_title
                FROM _document _d
                LEFT JOIN _fab _f       ON _d.fab_id   = _f.id 
                LEFT JOIN _formcase _fc ON _d.dcc_no   = _fc.dcc_no 
                WHERE ";

        if(!empty($uuid)){
            $sql .= "_d.uuid = ? ";
            $query = $uuid;
        }else if(!empty($anis_no)){
            $sql .= "_d.anis_no = ? ";
            $query = $anis_no;
        }
                
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$query]);
            $_document = $stmt->fetch(PDO::FETCH_ASSOC);        // no index

            // 把特定json轉物件
            $re_json_keys = array('_odd','_focus','_content','meeting_man_a','meeting_man_o','meeting_man_s');
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
            $swal_json["content"] .= $idty == "6" ? "暫存表單--" : "";                              // 20240506 -- 表單暫存
            $_focus     = array();
        // step1.例外處理單元：空
        // step2.舊檔案處理
            $row_document = edit_document(["uuid"=>$uuid]);                                         // 叫出舊紀錄
            $row_logs     = $row_document["logs"];
            $row_editions = $row_document["editions"];
            
            // 240709 組合上傳目的路徑 $to_path_obj
                $to_path_obj = [
                    "case_year" => substr($row_document["created_at"],0,4),
                    "anis_no"   => $row_document["anis_no"]
                ];

        // step3.使用迴圈刪除指定的元素for舊紀錄 因為不需要比對 == DB_item
            $unset_keys = array('id','uuid','dcc_no',  'action','step',  'created_emp_id','created_cname','created_at',  'updated_at','updated_cname','sign_comm',  'logs','editions', 
                                 'in_sign','in_signName','flow', 'submit_document','update_document','save_document','cancel_document', 'confirm_sign'); 
            //使用 array_diff_key() 函數，它會返回兩個或多個數組之間的差異，這樣就不需要使用循環逐個 unset 了。這樣會更簡潔和高效。
            $row_document = array_diff_key($row_document, array_flip($unset_keys));                 // 舊文件--去殼成 純舊文件
            $new_document = array_diff_key($request     , array_flip($unset_keys));                 // 新文件--去殼成 純新文件

        // step4.使用迴圈將指定的元素提前進行比對 == Basic_item
            $check_1 = array('idty', 'anis_no','fab_id','local_id',  'case_title','a_dept','meeting_time','meeting_local',  'ruling_sign','a_pic','meeting_man_d','omager');
            $check_2 = array('meeting_man_a','meeting_man_o','meeting_man_s');
            $check_desc = array('a_self_desc','a_others_desc');    // 240705 事故者+目擊者自述

            // step4-1.圈繞出來比對
            // echo "</br>step4-1.check_1圈：</br></br>";
            foreach($check_1 as $check_key){
                $edited_log[$check_key] = [];                                                       // 起始-修改項目log[主題key]
                $edit_item = [];                                                                    // 起始-修改項目 & 清空
                
                $old_item = isset(($row_document[$check_key])) ? $row_document[$check_key] : "";    // 取row值
                $new_item = isset(($new_document[$check_key])) ? $new_document[$check_key] : "";    // 取new值

                if(gettype($new_item) !== 'array' && gettype($new_item) !== 'object'){              // 針對combo項目進行判別
                    if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}$/', $new_item)) {         // 检查值是否符合日期时间格式 'Y-m-d\TH:i'
                        $new_item = convertDateTimeFormat($new_item);                               // 转换日期时间格式
                    }
                    if($check_key == "meeting_time"){
                        if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $new_item)) {           // 检查值是否符合日期时间格式 'Y-m-d\TH:i'
                            $new_item = convertDateTimeFormat($new_item);                           // 转换日期时间格式
                        }
                        $new_item = $new_item.":00";                                                // 將送進來的meeting_time進行加工
                    }
                }

                if($old_item != $new_item){
                    switch($check_key){
                        case "a_pic" :      // 處理a_pic上傳檔案
                            if(!empty($new_item)){                                       // 判斷新a_pic是否有值
                                echo "upLoad new_a_pic: ".$new_item."<br>";
                                $new_item = uploadFile($to_path_obj, $new_item);         // 有=上傳檔案，並返回新檔名
                            }
                            if(!empty($old_item)){                                       // 判斷舊a_pic是否有值
                                echo "unLink old_a_pic: ".$old_item."<br>";
                                $result = unlinkFile($to_path_obj, $old_item);           // 有=清除舊檔
                                if(!$result){
                                    $swal_json["action"]   = "error";
                                    $swal_json["content"] .= 'a_pic刪除失敗';
                                }
                            }
                            break;
                        case "confirm_sign" :   
                        case "ruling_sign"  :   
                            $new_document[$check_key] = !empty($new_document[$check_key]) ? $new_document[$check_key]  : null ;
                            break;
                        default :
                    }
                    $edit_item = $old_item." => ".$new_item;                             // 生成修改訊息
                    echo $check_key." : ".$edit_item. "</br>";                           // 螢幕顯示
                    
                    // 確認修改訊息，有需要添加SQL修改項目
                    $sql .= $check_key."=?, ";
                    array_push($stmt_arr, $new_item);

                }
                // 確認修改訊息，有值就是需要添加SQL修改項目
                if(!empty($edit_item)){
                    $edited_log[$check_key] = $edit_item;                                           // 將修改訊息倒入指定陣列位置
                }
            }
            
            // step4-2.例外處理單元：
            // echo "</br>step4-2.check_2圈：</br></br>";
            foreach($check_2 as $check_key){
                $edited_log[$check_key] = [];                                                       // 起始-修改項目log[主題key]
                $edit_item = [];                                                                    // 起始-修改項目 & 清空
                $old_item = isset(($row_document[$check_key])) ? ($row_document[$check_key]) : "";            
                $new_item = isset(($new_document[$check_key])) ? ($new_document[$check_key]) : "";

                if($old_item != $new_item){
                    $edit_item = $old_item." => ".$new_item;                                        // 生成修改訊息
                    $edit_item = str_replace(['"cname":',',"emp_id"'], '', $edit_item);
                    echo $check_key." : ".$edit_item. "</br>";                                      // 螢幕顯示

                    // 確認修改訊息，有需要添加SQL修改項目
                    $sql .= $check_key."=?, ";
                    array_push($stmt_arr, json_encode($new_item));                                 // , JSON_UNESCAPED_UNICODE--中文不編碼
                }
                // 確認修改訊息，有值就是需要添加SQL修改項目
                if(!empty($edit_item)){
                    $edited_log[$check_key] = $edit_item;                                           // 將修改訊息倒入指定陣列位置
                }
            }
            // step4-3.例外處理單元：240705
            // echo "</br>step4-3.check_desc圈：</br></br>";
            $check_desc_edit = [];
            $row_focus = isset($row_document["_focus"]) ? (array) $row_document["_focus"] : null;
            foreach($check_desc as $check_key){
                $edited_log[$check_key] = [];                                           // 起始-修改項目log[主題key]
                $edit_item = [];                                                        // 起始-修改項目 & 清空
                $old_item = isset($row_focus[$check_key])    ? $row_focus[$check_key]    : null;            
                $new_item = isset($new_document[$check_key]) ? $new_document[$check_key] : null;

                if($old_item != $new_item){
                    if(!empty($new_item)){                                              // 判斷新a_desc是否有值
                        echo "upLoad new_item: ".$new_item."<br>";
                        $new_item = uploadFile($to_path_obj, $new_item);                // 有=上傳檔案，並返回新檔名
                    }
                    if(!empty($old_item)){                                              // 判斷舊a_desc是否有值
                        echo "unLink old_item: ".$old_item."<br>";
                        $result = unlinkFile($to_path_obj, $old_item);                  // 有=清除舊檔
                        if(!$result){
                            $swal_json["action"]   = "error";
                            $swal_json["content"] .= $check_key.'刪除失敗';
                        }
                    }
                    $edit_item = $old_item." => ".$new_item;                            // 生成修改訊息
                    echo $check_key." : ".$edit_item. "</br>";                          // 螢幕顯示

                    $_focus[$check_key] = $new_item;
                }else{
                    $_focus[$check_key] = $old_item;
                }
                // 確認修改訊息，有值就是需要添加SQL修改項目
                if(!empty($edit_item)){
                    $edited_log[$check_key] = $edit_item;                               // 將修改訊息倒入指定陣列位置
                    $check_desc_edit = $edit_item;
                }
            }
            if(!empty($check_desc_edit)){
                // 確認修改訊息，有需要添加SQL修改項目
                $sql .= "_focus=?, ";
                array_push($stmt_arr, json_encode($_focus));                            // , JSON_UNESCAPED_UNICODE--中文不編碼
            }
                    
            // step4-3.*** 這裡要重新拆內容，以符合save暫存的比對需求
            // echo "</br>step4-3.check_3圈：</br></br>";
            // $new_document = array_intersect_key($new_document, array_flip($row_keys));           // new只保留指定的
            $new_content = array_diff_key($new_document, array_flip($check_1));
            $new_content = array_diff_key($new_content,  array_flip($check_2)); 
            $new_content = array_diff_key($new_content,  array_flip($check_desc)); 

            unset($new_content["_odd"]);                                                            // _odd從記錄中移除 for $_content
            // 20240611 確認申報日期 -- 呼叫通報判斷fun
            $new_document["_odd"] = !empty($row_document["_odd"]) ? $row_document["_odd"] : confirm_odd($new_content);

            $check_3 = [
                    "_odd"    => [
                            "row" => isset($row_document["_odd"]) ? $row_document["_odd"] : null ,
                            "new" => isset($new_document["_odd"]) ? $new_document["_odd"] : null
                        ], 
                    // "_focus"    => [
                    //         "row" => isset($row_document["_focus"]) ? $row_document["_focus"] : null ,
                    //         "new" => [
                    //             "a_self_desc"   => isset($new_document["a_self_desc"])   ? $new_document["a_self_desc"]   : null,
                    //             "a_others_desc" => isset($new_document["a_others_desc"]) ? $new_document["a_others_desc"] : null
                    //         ]
                    //     ], 
                    "_content"  => [
                            "row" => isset($row_document["_content"]) ? $row_document["_content"] : null ,
                            "new" => $new_content
                        ]
                ];
            
            foreach ($check_3 as $check_key => $check_key_value ) {
                // echo "step4-3.內圈({$check_key})：</br>";
                $edited_log[$check_key] = [];                                                       // 起始-修改項目log[主題key]
                // 先從記錄中帶出來 => 內圈 '_focus', '_content'
                $row_obj = (array) $check_key_value["row"];
                $new_obj = (array) $check_key_value["new"];
                
                $all_obj_keys = array_merge(array_keys($row_obj) ,array_keys($new_obj));            // 合併內圈的key_list陣列
                $all_obj_keys = array_unique(array_filter($all_obj_keys));                          // 過濾空陣列、重複值

                foreach($all_obj_keys as $a_key){
                    $edit_item = [];                                                                // 起始-修改項目 & 清空
                    $row_item_type = "";
                    $new_item_type = "";
                    $row_item = isset($row_obj[$a_key]) ? $row_obj[$a_key] : null;            
                    $new_item = isset($new_obj[$a_key]) ? $new_obj[$a_key] : null;

                    if(in_array(gettype($row_item), ['array', 'object'])){                          // 針對combo項目進行判別
                        $row_item_type = gettype($row_item);                                        // 取得row_type
                        $row_item = !is_null($row_item) ? json_encode($row_item) : null;     

                    }else if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $row_item)) {         // 检查值是否符合日期时间格式 'Y-m-d\TH:i'
                        $row_item = convertDateTimeFormat($row_item);                               // 转换日期时间格式
                    }
                    if(in_array(gettype($new_item), ['array', 'object'])){                          // 針對combo項目進行判別
                        $new_item_type = gettype($new_item);                                        // 取得new_type
                        $new_item = !is_null($new_item) ? json_encode($new_item) : null;

                    }else if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $new_item)) {         // 检查值是否符合日期时间格式 'Y-m-d\TH:i'
                        $new_item = convertDateTimeFormat($new_item);                               // 转换日期时间格式
                    }

                    if( $row_item != $new_item){
                        if(in_array($row_item_type, ['array', 'object'])){
                            $row_item = !is_null($row_item) ? json_encode($row_obj[$a_key], JSON_UNESCAPED_UNICODE ) : $row_item ;        // 中文不編碼
                        }
                        if(in_array($new_item_type, ['array', 'object'])){
                            $new_item = !is_null($new_item) ? json_encode($new_obj[$a_key], JSON_UNESCAPED_UNICODE ) : $new_item ;        // 中文不編碼
                        }

                        $edit_item = $row_item." => ".$new_item;                                    // 生成修改訊息
                        echo $a_key." : ".$edit_item. "</br>";                                      // 螢幕顯示
                        
                        $new_item = isset($new_obj[$a_key]) ? $new_obj[$a_key] : null;
                        $row_obj[$a_key] = $new_item;                                               // *** 把有修改的部分倒回去$row_obj陣列
                    }
    
                    if(!empty($edit_item)){
                        $edited_log[$check_key][$a_key] = $edit_item;                               // 將修改訊息倒入指定陣列位置
                    }
                }
                // 確認修改訊息，有值就是需要添加SQL修改項目
                if(!empty($edited_log[$check_key])){
                    $sql .= $check_key."=?, ";
                    array_push($stmt_arr, json_encode($row_obj));
                }
            }

        // step5.edited_log 處理單元：
            $edited_log = array_filter($edited_log);                                                // 過濾空陣列
            // if(count($edited_log) == 0){                                                         // 當筆數=0
            //     $edited_log["本次修改"] = "共 ".count($edited_log)." 處。";                       // 加上簡單提示。
            // }

        // step6.編輯紀錄 => 1送出 6暫存
            if($idty != "666" && count($edited_log) != 0){  // 表單狀態 6暫存&&沒log => 不進行編輯紀錄
                // step6-1.製作Editions編輯紀錄前處理：塞進去製作元素
                    $editions = array(
                        "updated_cname"   => $created_cname." (".$created_emp_id.")" ,
                        "update_document" => $edited_log ,
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
                $stmt->execute($stmt_arr);
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
        $result = "";
        // 舊檔案處理
        $row_doc = edit_document(["uuid"=>$uuid]);         // 叫出舊檔案
        $row_special = [ "a_pic", "confirm_sign", "_focus" ];

        // 240709 組合上傳目的路徑 $to_path_obj
            $to_path_obj = [
                "case_year" => substr($row_doc["created_at"],0,4),
                "anis_no"   => $row_doc["anis_no"]
            ];

        foreach($row_special as $row_item){
            $row_value = isset($row_doc[$row_item]) ? $row_doc[$row_item] : null;
            if(is_object($row_value)) { $row_value = (array)$row_value; }           // 將物件轉成陣列
            if(!empty($row_value)){                                                 // 判斷是否有值
                switch($row_item){
                    case "_focus":
                        foreach($row_value as $_key => $_value){
                            $result = unlinkFile($to_path_obj, $_value);            // 清除舊檔
                        }
                        break;
                    default:
                        $result = unlinkFile($to_path_obj, $row_value);             // 清除舊檔
                }
                if(!$result){
                    $swal_json["action"]   = "error";
                    $swal_json["content"] .= $row_item.'刪除失敗';
                }
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

    // 20240419、240705_上傳檔案a_pic、a_desc
    function uploadFile($to_path_obj, $fileName){
        $file_from = "../doc_temp/";                // 過度路徑
        $file_to   = "../doc_files/";               // submit後再搬移到垃圾路徑

        // step_1.疊加路徑 並 確認路徑資料夾 是否存在
            $path_arr       = [
                0 => "case_year", 
                1 => "anis_no" 
            ];
            foreach($path_arr as $key => $value){           // 逐筆繞出來
                $file_to .= $to_path_obj[$value]."/";       // 疊加to
                if(!is_dir($file_to)){ mkdir($file_to); }   // 检查to資料夾是否存在 then mkdir
            }

        // step_2.檢查是否已存在相同檔名的檔案，如果存在則在檔名前加上數字
            $refileName = $fileName;
            $toFile = $file_to . $fileName;         // 合成上傳路徑 + 檔名
            for ($i = 2; is_file($toFile); $i++) {
                $refileName = $i . "_" . $fileName;
                $toFile = $file_to . $refileName;       // 合成上傳路徑 + 檔名
            }
    
        // step_3.移動文件到指定目錄
            if (rename($file_from.$fileName , $toFile)) {
                return $refileName;                     // 返回檔案名稱
            } else {
                return false;                           // 返回錯誤
            }
    }    
    // 20240417、240705_移到垃圾桶a_pic、a_desc
    function unlinkFile($to_path_obj, $unlinkFile){
        $file_from = "../doc_files/";                // submit後正是路徑
        $file_to   = "../doc_offLine/";              // submit後再搬移到垃圾路徑

        // step_1.疊加路徑 並 確認路徑資料夾 是否存在
            $path_arr       = [
                0 => "case_year", 
                1 => "anis_no" 
            ];
            foreach($path_arr as $key => $value){           // 逐筆繞出來
                $file_from .= $to_path_obj[$value]."/";     // 疊加from
                $file_to   .= $to_path_obj[$value]."/";     // 疊加to
                if(!is_dir($file_to)){ mkdir($file_to); }   // 检查to資料夾是否存在 then mkdir
            }

        // step_1-5.分拆檔案資訊
            $unlinkFileInfo = pathinfo($unlinkFile);                                                        // 分解檔名
            $baseName       = $unlinkFileInfo['filename'];                                                  // 主檔名
            $extension      = isset($unlinkFileInfo['extension']) ? '.'.$unlinkFileInfo['extension']:'';    // 副檔名
            $rename_time    = date('Ymd-His');
            
        // step_2.移動文件到指定目錄
            // 確認檔案在目錄下
            if(is_file($file_from.$unlinkFile)) {
                // unlink($file_from.$unlinkFile);     // 移除檔案
                // 搬到垃圾桶 並返回完成訊息
                return rename( $file_from.$unlinkFile , $file_to.$baseName."_".$rename_time.$extension  );  // 搬到offLine
            } else {
                return false;
            }
    }
  
    // 20240417_確認JSON檔案是否存在
    function check_is_file($fileName){
        $uploadDir = '../form_json/';                    // 過度路徑，submit後再搬移到正是路徑
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
    // 20240611 損工 = 隔月5日需要進行職災申報
    function confirm_odd($_content){
        // $_content = (array) json_decode($_content);                                  // 倒進來的是無編碼...所以不需要解碼
        $s4_combo_03 = isset($_content["s4_combo_03"]) ? $_content["s4_combo_03"] : []; // 取得損限工問項答案
        $result = [];

        if(in_array("損工", $s4_combo_03)){
            $a_day = isset($_content["a_day"]) ? $_content["a_day"] : null;             // 事故發生日
            if($a_day !== null){
                // $datetime_obj = DateTime::createFromFormat("Y-m-d\TH:i", $a_day);    // 创建 DateTime 对象，并指定原始时间格式
                // $a_day = $datetime_obj->format("Y-m-d");                             // 将时间格式化为目标格式 
                $due_day = new DateTime($a_day);                                        // 將$a_day轉換為DateTime物件
                $due_day->modify('+1 month');                                           // 添加一個月
                $due_day->setDate($due_day->format('Y'), $due_day->format('m'), 5);     // 設置日期為5號
                $due_day = $due_day->format('Y-m-d');                                   // 將日期格式化為字符串並返回

            }else{
                $due_day = null;
            }

            $result = [
                // 'a_day'=> $a_day,            // 事故發生日
                // 'od'   => "職災申報",         // 判斷訊息
                'due_day' => $due_day,          // 申報截止日
                'od_day'  => null               // 申報日期
            ];
            
        // }else{
            // $result["od"] = "";
        }
        return $result;
    }