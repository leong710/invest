<?php
    if(isset($_REQUEST['fun'])) {
        $result = [];
        switch ($_REQUEST['fun']){
            case 'formcase':
                require_once("../pdo.php");
                $pdo = pdo();
                extract($_REQUEST);
                $sql = "SELECT * FROM _formcase WHERE flag <> 'Off' ORDER BY id ASC";
                $stmt = $pdo->prepare($sql);
                try {
                    $stmt->execute();
                    $formcase = $stmt->fetchAll(PDO::FETCH_ASSOC);        // no index
                    // 製作返回文件
                    $result = [
                        'result_obj' => $formcase,
                        'fun'        => $fun,
                        'success'    => 'Load '.$fun.' success.'
                    ];

                }catch(PDOException $e){
                    echo $e->getMessage();
                    $result['error'] = 'Load '.$fun.' failed...(e)';
                }
                break;

            case '_site':
                require_once("../pdo.php");
                $pdo = pdo();
                extract($_REQUEST);
                $sql = "SELECT _s.id, _s.site_title, _s.site_remark FROM _site _s WHERE _s.flag <> 'Off' ORDER BY _s.id ASC";
                $stmt = $pdo->prepare($sql);
                try {
                    $stmt->execute();
                    $_site = $stmt->fetchAll(PDO::FETCH_ASSOC);        // no index
                    // 製作返回文件
                    $result = [
                        'result_obj' => $_site,
                        'fun'        => $fun,
                        'success'    => 'Load '.$fun.' success.'
                    ];

                }catch(PDOException $e){
                    echo $e->getMessage();
                    $result['error'] = 'Load '.$fun.' failed...(e)';
                }
                break;

            case '_fab':
                require_once("../pdo.php");
                $pdo = pdo();
                extract($_REQUEST);
                $sql = "SELECT _f.id, _f.site_id, _f.fab_title, _f.fab_remark FROM _fab _f WHERE _f.flag <> 'Off' ORDER BY _f.id ASC";
                $stmt = $pdo->prepare($sql);
                try {
                    $stmt->execute();
                    $_fab = $stmt->fetchAll(PDO::FETCH_ASSOC);        // no index
                    // 製作返回文件
                    $result = [
                        'result_obj' => $_fab,
                        'fun'        => $fun,
                        'success'    => 'Load '.$fun.' success.'
                    ];

                }catch(PDOException $e){
                    echo $e->getMessage();
                    $result['error'] = 'Load '.$fun.' failed...(e)';
                }
                break;

            case 'form':
                if(isset($_REQUEST['parm'])) {
                    extract($_REQUEST);
                    $dcc_no = $parm;
                    $form_doc     = "../form_json/".$dcc_no.".json";
                    if(file_exists($form_doc)){
                        // 从 JSON 文件加载内容
                        $form_json = file_get_contents($form_doc);
                        // 解析 JSON 数据并将其存储在 $form_a_json 变量中
                        $form_json = (array) json_decode($form_json, true);     // 如果您想将JSON解析为关联数组，请传入 true，否则将解析为对象
                        // 製作返回文件
                        $result = [
                                'result_obj' => $form_json,
                                'fun'        => $fun,
                                'success'    => 'Load '.$fun.' success.'
                            ];
                    }else{
                        $result['error'] = 'Load '.$fun.' failed...(file not exist)';
                    }
                    
                } else {
                    $result['error'] = 'Load '.$fun.' failed...(no parm)';
                }
                break;

            case 'document':
                if(isset($_REQUEST['parm'])){
                    require_once("../pdo.php");
                    $pdo = pdo();
                    extract($_REQUEST);
                    $sql = "SELECT * FROM _document WHERE uuid = ? ";
                    $stmt = $pdo->prepare($sql);
                    try {
                        $stmt->execute([$parm]);
                        $_document = $stmt->fetch(PDO::FETCH_ASSOC);        // no index
                        // 把特定json轉物件
                            $re_json_keys = array('_focus','_content','meeting_man_a','meeting_man_o','meeting_man_s');
                            foreach ($re_json_keys as $jkey) {
                                // $_document[$jkey] = json_decode($_document[$jkey]);
                                $_document[$jkey] = isset($_document[$jkey]) ? json_decode($_document[$jkey]) : '';
                            }
                        // 製作返回文件
                        $result = [
                            'result_obj' => $_document,
                            'fun'        => $fun,
                            'success'    => 'Load '.$fun.' success.'
                        ];
                    }catch(PDOException $e){
                        echo $e->getMessage();
                        $result['error'] = 'Load '.$fun.' failed...(e)';
                    }

                }else{
                    $result['error'] = 'Load '.$fun.' failed...(no parm)';
                }
                break;

            case 'locals':
                require_once("../pdo.php");
                $pdo = pdo();
                extract($_REQUEST);
                $sql = "SELECT _l.*
                        FROM _local _l
                        -- LEFT JOIN _fab _f ON _f.id = _l.fab_id
                        -- WHERE _fab.flag = 'On' AND _site.flag = 'On'
                        ORDER BY _l.id ASC ";
                $stmt = $pdo->prepare($sql);
                try {
                    $stmt->execute();
                    $locals = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    // 製作返回文件
                    $result = [
                        'result_obj' => $locals,
                        'fun'        => $fun,
                        'success'    => 'Load '.$fun.' success.'
                    ];
                }catch(PDOException $e){
                    echo $e->getMessage();
                    $result['error'] = 'Load '.$fun.' failed...(e)';
                }
                break;
        };

        if(isset($result["error"])){
            http_response_code(500);
        }else{
            http_response_code(200);
        }
        echo json_encode($result);

    } else {
        http_response_code(500);
        echo json_encode(['error' => 'fun is lost.']);
    }
?>
