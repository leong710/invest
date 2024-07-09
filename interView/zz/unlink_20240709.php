<?php
    // 检查文件是否有帶來
    if(isset($_REQUEST['unlinkFile'])) {
        extract($_REQUEST);                                 // unlinkFile、fileName

        $file_dir   = '../doc_files/';                       // 存檔路徑
        $file_from  = "../doc_temp/";                        // 過度路徑
        $file_to    = "../doc_offLine/";                     // submit後再搬移到垃圾路徑

            // switch($fun_key){
                //     case "a_pic":
                //         $uploadDir = '../image/temp/';              // 過度路徑，submit後再搬移到正是路徑
                //         $sourceDir  = '../image/a_pic/';                                    // let doc_pdf 路徑
                //         $offLineDir = '../image/a_pic/offLine/';                            // let doc_pdf/offLine 路徑
                //         break;
                //     case "a_self_desc":    
                //     case "a_others_desc":    
                //         $uploadDir = '../doc_pdf/temp/';            // 過度路徑，submit後再搬移到正是路徑
                //         $sourceDir  = '../doc_pdf/a_desc/';                             // let doc_pdf 路徑
                //         $offLineDir = '../doc_pdf/a_desc_offLine/';                     // let doc_pdf/offLine 路徑
                //         break;
                //     default:
                //         $uploadDir = "";
                // }


        // 這裡 for a_pic
        if(!isset($_REQUEST['fileName'])){
            $unlinkFile =  $uploadDir.$unlinkFile;      // 組合成 路徑+檔案
        }

        // 1.確認檔案在目錄下
        if(is_file($unlinkFile)) {

            if(!isset($_REQUEST['fileName'])){
                // 這裡 for a_pic -- 移除檔案
                $moved = unlink($unlinkFile);                                   // 移除檔案 => 盡量避免憾事發生

            }else{
                // 這裡 for pdf -- 2.確認是否屬於過度檔案...
                if(strpos($unlinkFile, "temp")){
                    $moved = unlink($unlinkFile);                                   // 移除檔案 => 盡量避免憾事發生
    
                }else{  // 2.不是過度 then 搬移~

                    if(!is_dir($offLineDir)){ mkdir($offLineDir); }                 // 检查offLineDir資料夾是否存在
    
                    $offLineDir_path_unlinkFile = str_replace($sourceDir, $offLineDir, $unlinkFile);   // 含有 offLine path+fileName
                    $offLineDir_path     = str_replace($fileName , '', $offLineDir_path_unlinkFile);   // 含有 offLine path
        
                    // 確認 offLine各階層目錄是否存在：
                    $unknow_path = str_replace($offLineDir, '', $offLineDir_path);  // 單純取得offLine以後的各階層路徑
                    $unknow_path_arr = array_filter(explode("/",$unknow_path));     // 1.炸開成array 2.filter過濾空陣列
                    foreach($unknow_path_arr as $key){                              // 逐筆繞出來
                        $offLineDir .= $key."/";                                    // 疊加
                        if(!is_dir($offLineDir)){ mkdir($offLineDir); }             // 检查資料夾是否存在 then mkdir
                    }
                    $moved = rename($unlinkFile , $offLineDir_path_unlinkFile);     // 移動文件到指定目錄
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
