<?php

// // // _formcase CRUD
    function store_formcase($request){
        $pdo = pdo();
        extract($request);
        $sql = "INSERT INTO _formcase(_type, title, dcc_no, _icon, flag, updated_user, created_at, updated_at)VALUES(?,?,?,?,?,? ,now(),now())";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$_type, $title, $dcc_no, $_icon, $flag, $updated_user]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
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
        $sql = "UPDATE _formcase SET _type=?, title=?, dcc_no=?, _icon=?, flag=?, updated_user=?, updated_at=now() WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$_type, $title, $dcc_no, $_icon, $flag, $updated_user, $id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    function delete_formcase($request){
        $pdo = pdo();
        extract($request);
        $sql = "DELETE FROM _formcase WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
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

    function uploadFile(){
        // 检查文件是否成功上传
        if(isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../doc_json/';              // 過度路徑，submit後再搬移到正是路徑
                if(!is_dir($uploadDir)){ mkdir($uploadDir); }
                $fileName = basename($_FILES['file']['name']);
            
                $fileName = str_ireplace(".json","",$fileName);     // 把副檔名移除

            $uploadFile = $uploadDir . basename($_FILES['file']['name']);

            // 将文件移动到指定目录
            if(move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
                // 返回文件路径
                echo json_encode([
                        'filePath' => $uploadFile,
                        'uploadDir'=> $uploadDir,
                        'fileName' => $fileName
                    ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to move uploaded file.']);
            }
            
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'File upload failed.']);
        }
    }    
    

