<?php 
    // 20240306_優化結構
    $aResult = array();  // 定義結果陣列

    // 加上安全性檢查，檢查請求的方法是否是 POST、驗證資料等
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        require_once("../pdo.php");

    // step-1.確認基本數值
        $fun = !empty($_REQUEST['fun']) ? $_REQUEST['fun'] : NULL;     // 操作功能

        if (empty($fun) && empty($aResult['error'])) {
            $aResult['error'] = '未指定function!';
        } elseif (empty($fun) && !empty($aResult['error'])) {
            $aResult['error'] .= ' 未指定function!';
        }

    // step-2.組合查詢參數陣列
        // 接收來自前端的資料
        $su = array (
            "parm"     => !empty($_REQUEST["parm"])     ? $_REQUEST["parm"]     : NULL,    // 參數
        );
        
    // step-3.確認基本數值具備且無誤 => 執行function
        if( !isset($aResult['error']) ) {
            $aResult['success'] = 'Run function '.$fun.'!';    // 預先定義回傳內容。

            switch($fun) {
                // fun-1.analyze 
                case 'analyze':
                    if(empty($su['parm'])) {
                        $aResult['error'] = $fun.' - 參數錯誤!';
                    } else {
                        $_year       = !empty($su["parm"]["_year"])       ? $su["parm"]["_year"]       : "";
                        $_month      = !empty($su["parm"]["_month"])      ? $su["parm"]["_month"]      : "";
                        $short_name  = !empty($su["parm"]["_short_name"]) ? $su["parm"]["_short_name"] : "";
                        $fab_id      = !empty($su["parm"]["_fab_id"])     ? $su["parm"]["_fab_id"]     : "";
                        $sfab_id     = !empty($su["parm"]["_sfab_id"])    ? $su["parm"]["_sfab_id"]    : "";
                        
                        $pdo = pdo();
                        $stmt_arr = array();    // 初始查詢陣列
                        $sql = "SELECT _d.id, _d.uuid, _d.idty, _d.anis_no, _d.local_id, _d.case_title, _d.a_dept, _d.meeting_time, _d.meeting_local, _odd
                                    , _d.created_emp_id, _d.created_cname, _d.created_at, _d.updated_cname, _d.updated_at, year(_d.created_at) AS case_year , _d.confirm_sign
                                    , _l.local_title, _l.local_remark , _f.fab_title, _f.fab_remark, _f.sign_code AS fab_signCode, _f.pm_emp_id, _fc.short_name, _fc._icon
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
                            $caseList = $stmt->fetchAll(PDO::FETCH_ASSOC);        // no index
       
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

                    }
                    break;

                default:
                    $aResult['error'] = 'Not found function '.$fun.'!';
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
    echo json_encode($aResult , JSON_UNESCAPED_UNICODE );
    // api function --- end
