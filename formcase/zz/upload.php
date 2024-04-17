<?php
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
?>
