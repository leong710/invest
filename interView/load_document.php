<?php
    date_default_timezone_set("Asia/Taipei"); 
    // 检查文件是否成功上传
    if(isset($_REQUEST['uuid'])) {
        $pdo = pdo();
        extract($_REQUEST);
        $sql = "SELECT * FROM _document WHERE uuid = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$uuid]);
            $_document = $stmt->fetch(PDO::FETCH_ASSOC);        // no index

            // 把特定json轉物件
                $re_json_keys = array('_focus','_content','meeting_man_a','meeting_man_o','meeting_man_s');
                foreach ($re_json_keys as $jkey) {
                    $_document[$jkey] = json_decode($_document[$jkey]);
                }

            // 返回文件路径
                echo json_encode([
                    '_document_row' => $_document
                ]);
            
        }catch(PDOException $e){
            echo $e->getMessage();
            http_response_code(500);
            echo json_encode(['error' => 'Load document failed.']);
        }

    } else {
        http_response_code(500);
        echo json_encode(['error' => 'uuid is lost.']);
    }
?>
