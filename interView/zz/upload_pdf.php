<?php
    date_default_timezone_set("Asia/Taipei"); 
    // 检查文件是否成功上传
    if(isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        extract($_FILES['file']);

        $uploadDir = '../doc_pdf/temp/';                            // let doc_pdf/temp 路徑
        if(!is_dir($uploadDir)){ mkdir($uploadDir); }               // 检查uploadDir資料夾是否存在

        // 確保資料夾是否有同名檔案存在
        // $upload_FileName = (is_file($uploadDir.$name)) ? (date('s'))."_".$name : $name; // 雜湊秒生成fileName避免覆蓋
        // 分解路徑
        $fileInfo = pathinfo($name);
        $extension = isset($fileInfo['extension']) ? '.' . $fileInfo['extension'] : '';     // 副檔名
        $baseName = $fileInfo['filename'];                                                  // 檔名(不含副檔名)
        
        // 初始化變量
        $i = 1;
        $upload_FileName = $baseName.$extension;   // 組合成 檔名+副檔名
        // // 迴圈檢查檔案是否存在
        // while (is_file($uploadDir.$upload_FileName)) {
        //     $upload_FileName =  $baseName ."_". $i. $extension;
        //     $i++;
        // }
        if(is_file($uploadDir.$upload_FileName)){  // 直接unlink
            unlink($uploadDir.$upload_FileName);
        }

        $uploadFile = $uploadDir . basename($upload_FileName);

        // 将文件移动到指定目录
        if(move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
            // 返回文件路径
            echo json_encode([
                    'filePath' => $uploadFile,
                    'uploadDir'=> $uploadDir,
                    'fileName' => $upload_FileName
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
