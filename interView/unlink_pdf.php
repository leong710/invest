<?php
    // 检查文件是否有帶來
    if(isset($_REQUEST['unlinkFile'])) {
        extract($_REQUEST);
            
        $uploadDir = '../image/temp/';              // 過度路徑，submit後再搬移到正是路徑
        $unlinkFile =  $uploadDir.$unlinkFile;      // 組合成 路徑+檔案

        // 確認檔案在目錄下
        if(is_file($unlinkFile)) {
            // 移除檔案
            unlink($unlinkFile);
            // 返回完成訊息
            echo json_encode(['success' => 'Success to unlink uploaded file.']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to unlink uploaded file.']);
        }
        
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'File unlink failed.']);
    }

?>
