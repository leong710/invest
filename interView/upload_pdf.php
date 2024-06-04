<?php
    date_default_timezone_set("Asia/Taipei"); 
    // 检查文件是否成功上传
    if(isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            extract($_FILES['file']);
            $pdf_name = $name."_".(date('Ymd-His'));   // 雜湊年月日時分秒生成fileName避免覆蓋

        $uploadDir = '../doc_pdf/temp/';              // 過度路徑，submit後再搬移到正是路徑
            if(!is_dir($uploadDir)){ mkdir($uploadDir); }
            // $uploadFile = $uploadDir . basename($_FILES['file']['name']);
            $uploadFile = $uploadDir . basename($pdf_name);

        // 将文件移动到指定目录
        if(move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
            // 返回文件路径
            echo json_encode([
                    'filePath' => $uploadFile,
                    'uploadDir'=> $uploadDir,
                    'fileName' => $pdf_name
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
