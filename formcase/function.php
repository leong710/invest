<?php

// // // _formcase CRUD
    function store_formcase($request){
        $pdo = pdo();
        extract($request);
        $swal_json = array(                                 // for swal_json
            "fun"       => "store_formcase",
            "content"   => "新增表單--"
        );
        // 處理dcc_no上傳檔案
        if($_FILES["upload_file"]["name"]){
            // 確認是否有同檔名存在 true / false
            if(check_is_file($_FILES["upload_file"]["name"])){
                $name = $_FILES["upload_file"]["name"];
                    $swal_json["action"]   = "error";
                    $swal_json["content"] .= "檔案名稱：{$name} 已存在，儲存失敗";
                return $swal_json;
            }else{
                $dcc_no = uploadFile($_FILES["upload_file"]);
            }
        }else{
            $flag = "Off";
        }

        $sql = "INSERT INTO _formcase(_type, title, short_name, dcc_no, _icon, flag, updated_user, created_at, updated_at)VALUES(?,?,?,?,?,?,? ,now(),now())";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$_type, $title, $short_name, $dcc_no, $_icon, $flag, $updated_user]);
            $swal_json["action"]   = "success";
            $swal_json["content"] .= '儲存成功';

        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '儲存失敗';
        }
        return $swal_json;
    }
    function edit_formcase($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT * FROM _formcase WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $formcase = $stmt->fetch();
            return $formcase;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    function update_formcase($request){
        $pdo = pdo();
        extract($request);
        $swal_json = array(                                 // for swal_json
            "fun"       => "update_formcase",
            "content"   => "更新表單--"
        );
        // 處理dcc_no上傳更換檔案
        if($_FILES["upload_file"]["name"]){                                         // 檢查是否有上傳檔案
            $upload_fileNameExt = $_FILES["upload_file"]["name"];                   // 取得上傳檔名
            $upload_fileName = str_ireplace(".json", "", $upload_fileNameExt);      // 把副檔名移除

            if ($dcc_no && ($dcc_no == $upload_fileName)){          // 如果已有 dcc_no 且與上傳檔案名稱相符
                // 清除舊檔
                unlinkFile($dcc_no); 
            }else{
                // 檢查是否已存在同名檔案
                if(check_is_file($upload_fileNameExt)){
                    $swal_json["action"]   = "error";
                    $swal_json["content"] .= "檔案名稱：{$upload_fileNameExt} 已存在，更新失敗";
                    return $swal_json;
                }
                // 清除舊檔
                unlinkFile($dcc_no); 
            }
            // 處理上傳檔案
            $dcc_no = uploadFile($_FILES["upload_file"]);
        }

        $sql = "UPDATE _formcase SET _type=?, title=?, short_name=?, dcc_no=?, _icon=?, flag=?, updated_user=?, updated_at=now() WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$_type, $title, $short_name, $dcc_no, $_icon, $flag, $updated_user, $id]);
            $swal_json["action"]   = "success";
            $swal_json["content"] .= '儲存成功';
        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '儲存失敗';
        }
        return $swal_json;
    }
    function delete_formcase($request){
        $pdo = pdo();
        extract($request);
        $swal_json = array(                                 // for swal_json
            "fun"       => "delete_formcase",
            "content"   => "刪除表單--"
        );
        // 舊檔案處理
        $row_formcase = edit_formcase(["id"=>$id]);         // 叫出舊檔案
        $row_formcase_dcc_no = $row_formcase["dcc_no"];     // 取得舊dcc_no
        if(!empty($row_formcase_dcc_no)){                           // 判斷是否有值
            $result = unlinkFile($row_formcase_dcc_no);             // 清除舊檔
            if(!$result){
                $swal_json["action"]   = "error";
                $swal_json["content"] .= 'dcc-json刪除失敗';
                return $swal_json;
            }
        }
        $sql = "DELETE FROM _formcase WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
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

        $sql_check = "SELECT _formcase.* FROM _formcase WHERE id=?";
        $stmt_check = $pdo -> prepare($sql_check);
        $stmt_check -> execute([$id]);
        $row = $stmt_check -> fetch();

        if($row['flag'] == "Off" || $row['flag'] == "chk"){
            $flag = "On";
        }else{
            $flag = "Off";
        }

        $sql = "UPDATE _formcase SET flag=? WHERE id=?";
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
    function show_formcase(){
        $pdo = pdo();
        $sql = "SELECT * FROM _formcase ORDER BY id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $formcase = $stmt->fetchAll();
            return $formcase;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // 20240417
    function uploadFile($files){
        extract($files);

        $uploadDir = '../doc_json/';                    // 過度路徑，submit後再搬移到正是路徑
            if(!is_dir($uploadDir)){ mkdir($uploadDir); }   // 检查資料夾是否存在
        $row_fileName = basename($name);                // 取得檔案名稱
        $uploadFile = $uploadDir.$row_fileName;         // 合成上船路境+檔名
        // // 假如已經有了....
            // if(is_file($uploadFile)){
            //     $rename_time = date('Ymd-His');
            //     $row_fileName = str_ireplace(".json", "", $row_fileName);     // 把副檔名移除
            //     $row_fileName .= "_".$rename_time.".json";
            //     $uploadFile = $uploadDir.$row_fileName;         // 合成上船路境+檔名
            // }

        // 将文件移动到指定目录
        if(move_uploaded_file($tmp_name, $uploadFile)) {
            $success_fileName = str_ireplace(".json", "", $row_fileName);     // 把副檔名移除
            return $success_fileName;       // 返回 文件名稱
        } else {
            return false;           // 返回 錯誤
        }
    }    
    // 20240417
    function unlinkFile($unlinkFile){
                
        $file_from = "../doc_json/";                // submit後正是路徑
        $file_to   = "../doc_json/offLine/";        // submit後再搬移到垃圾路徑

        $rename_time = date('Ymd-His');
        $ext_name = ".json";

        // 確認檔案在目錄下
        if(is_file($file_from .$unlinkFile .$ext_name)) {
            // // 移除檔案 unlink($unlinkFile); 
            // 搬到垃圾桶
            $moved = rename( $file_from .$unlinkFile .$ext_name , $file_to .$unlinkFile ."_" .$rename_time .$ext_name );
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
    // 20240417
    function check_is_file($fileName){
        $uploadDir = '../doc_json/';                    // 過度路徑，submit後再搬移到正是路徑
        if(is_file($uploadDir .$fileName )) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }

