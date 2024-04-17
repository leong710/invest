<?php
    // 检查文件是否有帶來
    if(isset($_REQUEST['unlinkFile'])) {
        extract($_REQUEST);
            
        $file_from = "../doc_json/";                // submit後正是路徑
        $file_to   = "../doc_json/offLine/";        // submit後再搬移到垃圾路徑

        $rename_time = date('Ymd-His');
        $ext_name = ".json";

        // $unlinkFile =  $uploadDir.$unlinkFile.'.json'; // 組合成 路徑+檔案

        // 確認檔案在目錄下
        if(is_file($file_from.$unlinkFile.$ext_name)) {
            // 移除檔案
            // unlink($unlinkFile);
            // 搬到垃圾桶
            $moved = rename($file_from.$unlinkFile.$ext_name , $file_to.$unlinkFile."_".$rename_time.$ext_name);
            if($moved){
                echo "File moved successfully";
            }

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
