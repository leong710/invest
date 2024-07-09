<?php
    // 接受來源：form.js、process_pdf.js 
    // 检查文件是否有帶來
    if(isset($_REQUEST['unlinkFile'])) {
        extract($_REQUEST);                                 // unlinkFile、row_json  、fileName

        $file_temp  = "../doc_temp/";
        $file_from  = isset($uploadDir) ? $uploadDir : "../doc_temp/";  // 過度路徑
        $file_to    = "../doc_offLine/";                    // submit後再搬移到垃圾路徑

        // // step-1.判斷並組合成 路徑$file_from 
        // if(!empty($row_json) && isset($row_json)){
        //     $row_obj = json_decode($row_json);                                  // 解開
        //     $file_from = "../doc_files/".$row_obj["case_year"]."/".$row_obj["anis_no"]."/";    // 組合路徑
        // }

        // step-2.確認檔案在目錄下
        if(is_file($file_from.$unlinkFile)) {

            if(!isset($_REQUEST['fileName'])){
                // 這裡移除檔案
                $moved = unlink($file_from.$unlinkFile);                            // 移除檔案 => 盡量避免憾事發生

            }else{
                // 這裡 for pdf -- 2.確認是否屬於過度檔案...
                if(strpos($file_from.$unlinkFile, "temp")){
                    $moved = unlink($file_from.$unlinkFile);                        // 移除檔案 => 盡量避免憾事發生
    
                }else{  // 2.不是過度 then 搬移~

                    if(!is_dir($file_to)){ mkdir($file_to); }                       // 检查$file_to資料夾是否存在
                    // step_1.疊加路徑 並 確認路徑資料夾 是否存在
                        $path_arr = [
                            0 => "case_year", 
                            1 => "anis_no" 
                        ];
                        foreach($path_arr as $key => $value){               // 逐筆繞出來
                            $file_to   .= $row_obj[$value]."/";             // 疊加to
                            if(!is_dir($file_to)){ mkdir($file_to); }       // 检查資料夾是否存在 then mkdir
                        }
                    // step_1-5.分拆檔案資訊
                        $unlinkFileInfo = pathinfo($unlinkFile);                                                        // 分解檔名
                        $baseName       = $unlinkFileInfo['filename'];                                                  // 主檔名
                        $extension      = isset($unlinkFileInfo['extension']) ? '.'.$unlinkFileInfo['extension']:'';    // 副檔名
                        $rename_time    = date('Ymd-His');

                    // step_2.移動文件到指定目錄
                        // 確認檔案在目錄下
                        if(is_file($file_from.$unlinkFile)) {
                            // unlink($file_from.$unlinkFile);     // 移除檔案
                            // 搬到垃圾桶 並返回完成訊息
                            $moved = rename( $file_from.$unlinkFile , $file_to.$baseName."_".$rename_time.$extension  );  // 搬到offLine
                        } else {
                            $moved = false;
                        }
                }
            }

            // 返回完成訊息
            echo json_encode(['success' => 'Success to unlink uploaded file...'.$moved ], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to unlink uploaded file.'], JSON_UNESCAPED_UNICODE);
        }
        
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'File unlink failed.']);
    }

?>
