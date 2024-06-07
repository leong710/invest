<?php
    if(isset($_REQUEST['fun'])) {

        switch (isset($_REQUEST['fun'])){
            case 'form':
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
                                $_document[$jkey] = json_decode($_document[$jkey]);
                            }
                        // 返回文件

                        $result = json_encode(['result_obj' => $_document]);
                        $result['fun']     = $fun;
                        $result['success'] = 'Load '.$fun.' success.';
                        
                    }catch(PDOException $e){
                        echo $e->getMessage();
                        $result['fun'] = "error";
                        $result['error'] = 'Load '.$fun.' failed.';
                    }

                }else{
                    $result['fun'] = "error";

                }
                break;
            case 'locals':
                break;
        };

        if($result['fun'] == "error"){
            
        }else{
            
        }
        
        echo json_encode($result);

    
            
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
