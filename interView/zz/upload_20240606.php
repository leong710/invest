<?php
    date_default_timezone_set("Asia/Taipei"); 
    // 检查文件是否成功上传
    if(isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            extract($_FILES['file']);
            // switch($type){
                //     case "image/jpeg" : $ext = ".jpg"; break;
                //     case "image/png"  : $ext = ".png"; break;
                //     case "image/gif"  : $ext = ".gif"; break;
                //     case "image/bmp"  : $ext = ".bmp"; break;
                //     default : $msg = "請使用正確的圖檔"; return $msg;
                // }
                // $img_name = md5(time()).$ext;           // 雜湊生成fileName避免覆蓋
            $img_name = (date('Ymd-His'))."_".$name;   // 雜湊年月日時分秒生成fileName避免覆蓋

        // $uploadDir = '../image/a_pic/';             // 正式路徑...
        $uploadDir = '../image/temp/';              // 過度路徑，submit後再搬移到正是路徑
            if(!is_dir($uploadDir)){ mkdir($uploadDir); }
        // $uploadFile = $uploadDir . basename($_FILES['file']['name']);
            $uploadFile = $uploadDir . basename($img_name);

        // 将文件移动到指定目录
        if(move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
            // 返回文件路径
            echo json_encode([
                    'filePath' => $uploadFile,
                    'uploadDir'=> $uploadDir,
                    'fileName' => $img_name
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
