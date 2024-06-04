<?php

// 在index表頭顯示清單：
    function show_caseList($request){
        $pdo = pdo();
        extract($request);
        $stmt_arr = array();    // 初始查詢陣列
        $sql = "SELECT _d.id, _d.uuid, _d.idty, _d.anis_no, _d.local_id, _d.case_title, _d.a_dept, _d.meeting_time, _d.meeting_local
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
            if($fab_id != "All"){                                           // 處理 fab_id != All 進行二階   
                $sql .= ($_year != "All" ? " AND ":" WHERE ") ;
                if($fab_id == "allMy"){                                     // 處理 fab_id = allMy 我的轄區
                    $sql .= " _d.fab_id IN ({$sfab_id}) ";
                }else{                                                      // 處理 fab_id != allMy 就是單點fab_id
                    $sql .= " _d.fab_id = ? ";
                    array_push($stmt_arr, $fab_id);
                }
            }                                                               // 處理 fab_id = All 就不用套用，反之進行二階
            if($short_name != "All"){                                        // 處理過濾 short_name != All  
                $sql .= ($_year != "All" || $fab_id != "All" ? " AND ":" WHERE ") ;
                $sql .= " _fc.short_name = ? ";                             // 查詢條件 short_name
                array_push($stmt_arr, $short_name);
            }

        // 後段-堆疊查詢語法：加入排序
            $sql .= " ORDER BY _d.created_at DESC";
        // 決定是否採用 page_div 20230803
            if(isset($start) && isset($per)){
                $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per);   // 讀取選取頁的資料=分頁
            }else{
                $stmt = $pdo->prepare($sql);                                // 讀取全部=不分頁
            }
        try {
            if(($_year != 'All') || (($fab_id != "All") && ($fab_id != "allMy")) || ($short_name != "All")){
                $stmt->execute($stmt_arr);                          //處理 byUser & byYear
            }else{
                $stmt->execute();                                   //處理 byAll
            }
            $caseList = $stmt->fetchAll(PDO::FETCH_ASSOC);          // no index
            return $caseList;

        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    
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

    // 20240125 4.組合我的sign_code所涵蓋的廠區
    function get_coverFab_lists($type){
        $sfab_id = [];
        // 1-1b 將sign_code涵蓋的fab_id加入sfab_id
        if(isset($_SESSION["AUTH"]["sign_code"])){
            $auth_sign_code["sign_code"] = $_SESSION["AUTH"]["sign_code"];
            $coverFab_lists = show_coverFab_lists($auth_sign_code);
            if(!empty($coverFab_lists)){
                foreach($coverFab_lists as $coverFab){
                    if(!in_array($coverFab["id"], $sfab_id)){
                        array_push($sfab_id, $coverFab["id"]);
                    }
                }
            }
        }
        // 根據需求類別進行編碼 arr=陣列、str=字串
        if($type == "str"){
            $result = implode(",", $sfab_id);                   // 1-1c sfab_id是陣列，要轉成字串
        }else{
            $result = $sfab_id;
        }
        // 1-1c sfab_id是陣列，要轉成字串
        return $result;
    }

        // 20231026 在index表頭顯示my_coverFab區域 = 使用signCode去搜尋
        function show_coverFab_lists($request){
            $pdo = pdo();
            extract($request);
                $sign_code = substr($sign_code, 0, -2);     // 去掉最後兩個字 =>
                $sign_code = "%".$sign_code."%";            // 加上模糊包裝
    
            $sql = "SELECT _f.*
                    FROM _fab AS _f 
                    WHERE _f.sign_code LIKE ?
                    ORDER BY _f.id ASC ";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$sign_code]);
                $coverFab_lists = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $coverFab_lists;
            }catch(PDOException $e){
                echo $e->getMessage();
            }
        }

    // 取出年份清單 => 供面篩選
    function show_document_GB_year(){
        $pdo = pdo();
        $sql = "SELECT DISTINCT year(_d.created_at) AS _year
                FROM `_document` _d
                GROUP BY _d.created_at
                ORDER BY _d.created_at DESC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $document_years = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $document_years;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // 取出年份清單 => 供面篩選
    function show_document_shortName(){
        $pdo = pdo();
        $sql = "SELECT DISTINCT _fc.short_name
                FROM _document _d
                LEFT JOIN _formcase _fc ON _d.dcc_no = _fc.dcc_no
                ORDER BY _d.id ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $shortName_lists = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $shortName_lists;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }