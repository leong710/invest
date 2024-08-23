<?php
    if(isset($_REQUEST['fun'])) {
        require_once("../pdo.php");
        extract($_REQUEST);
        $result = [];
        switch ($_REQUEST['fun']){
            case 'form':        // analyze
                if(isset($parm)) {
                    $dcc_no = $parm;
                    $form_doc     = "../form_json/".$dcc_no.".json";
                    if(file_exists($form_doc)){
                        // 从 JSON 文件加载内容
                        $form_json = file_get_contents($form_doc);
                        // 解析 JSON 数据并将其存储在 $form_a_json 变量中
                        $form_json = (array) json_decode($form_json, true);     // 如果您想将JSON解析为关联数组，请传入 true，否则将解析为对象
                        // 製作返回文件
                        $result = [
                                'result_obj' => $form_json,
                                'fun'        => $fun,
                                'success'    => 'Load '.$fun.' success.'
                            ];
                    }else{
                        $result['error'] = 'Load '.$fun.' failed...(file not exist)';
                    }
                    
                } else {
                    $result['error'] = 'Load '.$fun.' failed...(no parm)';
                }
                break;

            case 'document':    // analyze
                if(isset($parm)){
                    $pdo = pdo();
                    $sql = "SELECT * FROM _document WHERE uuid = ? ";
                    $stmt = $pdo->prepare($sql);
                    try {
                        $stmt->execute([$parm]);
                        $_document = $stmt->fetch(PDO::FETCH_ASSOC);        // no index
                        // 把特定json轉物件
                            $re_json_keys = array('_focus','_content','meeting_man_a','meeting_man_o','meeting_man_s');
                            foreach ($re_json_keys as $jkey) {
                                // $_document[$jkey] = json_decode($_document[$jkey]);
                                $_document[$jkey] = isset($_document[$jkey]) ? json_decode($_document[$jkey]) : '';
                            }
                        // 製作返回文件
                        $result = [
                            'result_obj' => $_document,
                            'fun'        => $fun,
                            'success'    => 'Load '.$fun.' success.'
                        ];
                    }catch(PDOException $e){
                        echo $e->getMessage();
                        $result['error'] = 'Load '.$fun.' failed...(e)';
                    }

                }else{
                    $result['error'] = 'Load '.$fun.' failed...(no parm)';
                }
                break;

            case 'caseList':    // analyze
                if(isset($_short_name)){
                    $pdo = pdo();
                    $stmt_arr = array();    // 初始查詢陣列

                    $_year       = !empty($_year)       ? $_year       : "";
                    $_month      = !empty($_month)      ? $_month      : "";
                    $short_name  = !empty($_short_name) ? $_short_name : "";
                    $fab_id      = !empty($_fab_id)     ? $_fab_id     : "";
                    $sfab_id     = !empty($_sfab_id)    ? $_sfab_id    : "";
                    $get_dccNo   = !empty($_get_dccNo)  ? $_get_dccNo  : false;

                    $sql = "SELECT _d.id, _d.uuid, _d.dcc_no, year(_d.created_at) AS case_year 
                            FROM _document _d
                            LEFT JOIN _local _l     ON _d.local_id = _l.id 
                            LEFT JOIN _fab _f       ON _l.fab_id   = _f.id 
                            LEFT JOIN _formcase _fc ON _d.dcc_no   = _fc.dcc_no ";
                    // tidy query condition：
                    if($_year != 'All'){
                        $sql .= " WHERE (year(_d.created_at) = ? )";              // ? = $year
                        array_push($stmt_arr, $_year);
                    }
                    if($_month != 'All'){
                        $sql .= ($_year != "All" ? " AND ":" WHERE ") ;
                        $sql .= " (month(_d.created_at) = ? )";                  // ? = $month
                        array_push($stmt_arr, $_month);
                    }
                    if($fab_id != "All"){                                           // 處理 fab_id != All 進行二階   
                        $sql .= ($_year != "All" || $_month != "All" ? " AND ":" WHERE ") ;
                        if($fab_id == "allMy"){                                     // 處理 fab_id = allMy 我的轄區
                            $sql .= " _d.fab_id IN ({$sfab_id}) ";
                        }else{                                                      // 處理 fab_id != allMy 就是單點fab_id
                            $sql .= " _d.fab_id = ? ";
                            array_push($stmt_arr, $fab_id);
                        }
                    }                                                               // 處理 fab_id = All 就不用套用，反之進行二階
                    if($short_name != "All"){                                        // 處理過濾 short_name != All  
                        $sql .= ($_year != "All" || $_month != "All" || $fab_id != "All" ? " AND ":" WHERE ") ;
                        $sql .= " _fc.short_name = ? ";                             // 查詢條件 short_name
                        array_push($stmt_arr, $short_name);
                    }
                    // 後段-堆疊查詢語法：加入排序
                    $sql .= " ORDER BY _d.created_at DESC";

                    $stmt = $pdo->prepare($sql);
                    try {
                        if(($_year != 'All') || ($_month != 'All') || (($fab_id != "All") && ($fab_id != "allMy")) || ($short_name != "All")){
                            $stmt->execute($stmt_arr);                          //處理 byUser & byYear
                        }else{
                            $stmt->execute();                                   //處理 byAll
                        }
                        // $caseList = ($get_dccNo) ? $stmt->fetch(PDO::FETCH_ASSOC) : $stmt->fetchAll(PDO::FETCH_ASSOC);        // get_dccNo  取一筆for dcc_no / 取全部
                        $caseList = ($get_dccNo) ? $stmt->fetch(PDO::FETCH_ASSOC) : $stmt->fetchAll(PDO::FETCH_ASSOC);        // get_dccNo  取一筆for dcc_no / 取全部
                        // 製作返回文件
                        $result = [
                            'result_obj' => $caseList,
                            'fun'        => $fun,
                            'success'    => 'Load '.$fun.' success.'
                        ];
                    }catch(PDOException $e){
                        echo $e->getMessage();
                        $result['error'] = 'Load '.$fun.' failed...(e)';
                    }

                }else{
                    $result['error'] = 'Load '.$fun.' failed...(no parm)';
                }
                break;


            case 'count':       // analyze
                if(isset($_short_name)){
                    $pdo = pdo();
                    $stmt_arr = array();    // 初始查詢陣列

                    $_year       = !empty($_year)       ? $_year       : "";
                    $_month      = !empty($_month)      ? $_month      : "";
                    $short_name  = !empty($_short_name) ? $_short_name : "";
                    $fab_id      = !empty($_fab_id)     ? $_fab_id     : "";
                    $sfab_id     = !empty($_sfab_id)    ? $_sfab_id    : "";

                    $sql = "SELECT _f.fab_title, _l.local_title, _fc.short_name , COUNT(*) as case_count , CONCAT(year(_d.created_at)) AS case_year
                            FROM `_document` _d
                            LEFT JOIN _fab _f ON _d.fab_id = _f.id
                            LEFT JOIN _local _l ON _d.local_id = _l.id
                            LEFT JOIN _formcase _fc ON _d.dcc_no = _fc.dcc_no ";
                    // tidy query condition：
                    if($_year != 'All'){
                        $sql .= " WHERE (year(_d.created_at) = ? )";              // ? = $year
                        array_push($stmt_arr, $_year);
                    }
                    if($_month != 'All'){
                        $sql .= ($_year != "All" ? " AND ":" WHERE ") ;
                        $sql .= " (month(_d.created_at) = ? )";                  // ? = $month
                        array_push($stmt_arr, $_month);
                    }
                    if($fab_id != "All"){                                           // 處理 fab_id != All 進行二階   
                        $sql .= ($_year != "All" || $_month != "All" ? " AND ":" WHERE ") ;
                        if($fab_id == "allMy"){                                     // 處理 fab_id = allMy 我的轄區
                            $sql .= " _d.fab_id IN ({$sfab_id}) ";
                        }else{                                                      // 處理 fab_id != allMy 就是單點fab_id
                            $sql .= " _d.fab_id = ? ";
                            array_push($stmt_arr, $fab_id);
                        }
                    }                                                               // 處理 fab_id = All 就不用套用，反之進行二階
                    if($short_name != "All"){                                        // 處理過濾 short_name != All  
                        $sql .= ($_year != "All" || $_month != "All" || $fab_id != "All" ? " AND ":" WHERE ") ;
                        $sql .= " _fc.short_name = ? ";                             // 查詢條件 short_name
                        array_push($stmt_arr, $short_name);
                    }
                    // 後段-堆疊查詢語法：加入排序
                    $sql .= " GROUP BY _d.fab_id, _d.local_id, _fc.short_name 
                             ORDER BY _d.fab_id, _d.local_id, case_count DESC ";     //ORDER BY _d.fab_id, _d.local_id, _d.a_dept, case_count DESC

                    $stmt = $pdo->prepare($sql);
                    try {
                        if(($_year != 'All') || ($_month != 'All') || (($fab_id != "All") && ($fab_id != "allMy")) || ($short_name != "All")){
                            $stmt->execute($stmt_arr);                          //處理 byUser & byYear
                        }else{
                            $stmt->execute();                                   //處理 byAll
                        }

                        $caseList = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // 製作返回文件
                        $result = [
                            'result_obj' => $caseList,
                            'fun'        => $fun,
                            'success'    => 'Load '.$fun.' success.'
                        ];
                    }catch(PDOException $e){
                        echo $e->getMessage();
                        $result['error'] = 'Load '.$fun.' failed...(e)';
                    }

                }else{
                    $result['error'] = 'Load '.$fun.' failed...(no parm)';
                }
                break;
        };

        if(isset($result["error"])){
            http_response_code(500);
        }else{
            http_response_code(200);
        }
        echo json_encode($result);

    } else {
        http_response_code(500);
        echo json_encode(['error' => 'fun is lost.']);
    }

?>
