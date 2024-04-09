<?php
    // 检查文件是否成功上传
    if(isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../upload/';
        $uploadFile = $uploadDir . basename($_FILES['file']['name']);
        // 将文件移动到指定目录
        if(move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
            // 返回文件路径
            echo json_encode(['filePath' => $uploadFile]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to move uploaded file.']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'File upload failed.']);
    }
?>
