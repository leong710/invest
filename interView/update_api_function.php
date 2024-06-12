<?php
    // 此檔案process_api_function.php只搭配API(update_api.php)使用!!
    // 20240606_(主)上傳檔案pdf並更新confirm_sign
    function update_confirm_sign($request){
        $pdo = pdo();
        extract($request);

        $result = [];
        $swal_json = array(                                 // for swal_json
            "fun"       => "update_confirm_sign",
            "content"   => "更新PDF文件 -- "
        );
        // step.1-處理舊pdf文件
        if(!empty($confirm_sign)){                          // 舊文件已有綁定檔案需要處理
            $result["unlink"] = unlinkFile_pdf($request);
        }

        // step.2-處理新pdf文件
        if(!empty($confirm_sign_new)){                      // 舊文件已有綁定檔案需要處理
            $result["upload"] = uploadFile_pdf($request);
        }else{
            $result["upload"] = "";                         // for 刪除檔案
        }

        // step.3-處理doc文件
        if(isset($result["upload"])){
            $uuid = $request["uuid"];
            $confirm_sign = $result["upload"];
            $sql = "UPDATE _document SET confirm_sign=? WHERE uuid=?";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$confirm_sign, $uuid]);
                $swal_json["action"]   = "success";
                $swal_json["content"] .= '儲存成功';
            }catch(PDOException $e){
                echo $e->getMessage();
                $swal_json["action"]   = "error";
                $swal_json["content"] .= '儲存失敗';
            }
        }else{
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '上傳失敗';
        }

        return $swal_json;
    }
    // 20240606_(子)上傳檔案pdf
    function uploadFile_pdf($row_obj){
        $rename_time    = date('Ymd-His');
        $uploadFile     = $row_obj["confirm_sign_new"];
        $uploadFileInfo = pathinfo($uploadFile);                                                        // 分解檔名
        $baseName       = $uploadFileInfo['filename'];                                                  // 主檔名
        $extension      = isset($uploadFileInfo['extension']) ? '.'.$uploadFileInfo['extension']:'';    // 副檔名
        $file_from      = "../doc_pdf/temp/";                // submit後正是路徑
        $file_to        = "../doc_pdf/";                     // submit後再搬移到垃圾路徑
        $path_arr       = [
                            0 => "fab_title", 
                            1 => "short_name", 
                            2 => "case_year"
                        ];
        // 疊加路徑 並 確認路徑資料夾
        foreach($path_arr as $key => $value){               // 逐筆繞出來
            $file_to   .= $row_obj[$value]."/";             // 疊加to
            if(!is_dir($file_to)){ mkdir($file_to); }       // 检查資料夾是否存在 then mkdir
        }

        // 確認檔案在form目錄下
        if(is_file($file_from.$uploadFile)){
            $new_uploadFile = (is_file($file_to.$uploadFile)) ? $baseName."_".$rename_time.$extension : $uploadFile ;
            $uploadResult = rename( $file_from.$uploadFile , $file_to.$new_uploadFile );                // 搬到to
            return $uploadResult ? $new_uploadFile : false;
        }else{
            return false;
        }
    }    
    // 20240606_(子)移到offLine垃圾桶pdf
    function unlinkFile_pdf($row_obj){
        $rename_time    = date('Ymd-His');
        $unlinkFile     = $row_obj["confirm_sign"];
        $unlinkFileInfo = pathinfo($unlinkFile);                                                        // 分解檔名
        $baseName       = $unlinkFileInfo['filename'];                                                  // 主檔名
        $extension      = isset($unlinkFileInfo['extension']) ? '.'.$unlinkFileInfo['extension']:'';    // 副檔名
        $file_from      = "../doc_pdf/";                    // submit後正是路徑
        $file_to        = "../doc_pdf/offLine/";            // submit後再搬移到垃圾路徑
        $path_arr       = [
                            0 => "fab_title", 
                            1 => "short_name", 
                            2 => "case_year"
                        ];
        // 疊加路徑 並 確認路徑資料夾
        foreach($path_arr as $key => $value){               // 逐筆繞出來
            $file_from .= $row_obj[$value]."/";             // 疊加from
            $file_to   .= $row_obj[$value]."/";             // 疊加to
            if(!is_dir($file_to)){ mkdir($file_to); }       // 检查資料夾是否存在 then mkdir
        }

        // 確認檔案在form目錄下
        if(is_file($file_from.$unlinkFile)){
            // unlink($unlinkFile);                         // 移除檔案 => 盡量避免憾事發生
            return rename( $file_from.$unlinkFile , $file_to.$baseName."_".$rename_time.$extension );    // 搬到offLine
        }else{
            return false;
        }
    }

    // 20240612_(主)更新odd日期
    function update_odd($request){
        $pdo = pdo();
        extract($request);

        $swal_json = array(                                 // for swal_json
            "fun"       => "update_odd",
            "content"   => "更新職災申報日期 -- "
        );

        $_odd = json_encode($_odd);

        // step.1-處理doc文件
        $sql = "UPDATE _document SET _odd=? WHERE uuid=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$_odd, $uuid]);
            $swal_json["action"]   = "success";
            $swal_json["content"] .= '儲存成功';
        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '儲存失敗';
        }

        return $swal_json;
    }