<?php

    if(isset($_REQUEST['dcc_no'])) {
        extract($_REQUEST);
        $form_doc     = "../doc_json/".$dcc_no.".json";
        if(file_exists($form_doc)){
            // 从 JSON 文件加载内容
            $form_json = file_get_contents($form_doc);
            // 解析 JSON 数据并将其存储在 $form_a_json 变量中
            $form_json = (array) json_decode($form_json, true);     // 如果您想将JSON解析为关联数组，请传入 true，否则将解析为对象
            // 返回文件
            echo json_encode(['form_json' => $form_json]);

        }else{
            http_response_code(500);
            echo json_encode(['error' => 'Load form_doc failed.']);
        }

    } else {
        http_response_code(500);
        echo json_encode(['error' => 'form_doc is lost.']);
    }

?>
