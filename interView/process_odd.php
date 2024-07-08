<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    require_once("../user_info.php");
    accessDenied($sys_id);
    extract($_REQUEST);

    $document_row = [];
    $row_obj      = [];
    $get_item_arr = ["uuid", "_odd" ];  // 建立取用欄位； 例外處理："created_at", "case_year", "row_confirm_sign"
    
    $uuid         = (isset($_REQUEST["uuid"])) ? $_REQUEST["uuid"] : "";
    if(!empty($uuid)){
        $document_row = edit_document(["uuid" => $uuid]);
        if(empty($document_row)){
            echo "<script>alert('uuid-error：{$uuid}')</script>";
            echo "<script>window.close()</script>";
            return;
        }
    }
    // 轉存打包
    foreach($get_item_arr as $key){
        $row_obj[$key] = (isset($document_row[$key]) ? $document_row[$key] : "");
    }

    // 轉字串
    $row_json = json_encode($row_obj , JSON_UNESCAPED_UNICODE);

?>
<?php include("../template/header.php"); ?>
<head>
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>    <!-- Jquery -->
    <script src="../../libs/sweetalert/sweetalert.min.js"></script>                         <!-- 引入 SweetAlert -->
    <script src="../../libs/jquery/jquery.mloading.js"></script>                            <!-- mloading JS 1/3 -->
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">                    <!-- mloading CSS 2/3 -->
    <script src="../../libs/jquery/mloading_init.js"></script>                              <!-- mLoading_init.js 3/3 -->
    <script src="../../libs/openUrl/openUrl.js"></script>                                   <!-- 彈出子畫面 -->
    <style>
        ul, li {
            list-style: square inside;
        }
    </style>
</head>

<body>
    <div class="col-12">
        <snap class="text-white"><?php echo !empty($action_fun) ? $action_fun : '[process _odd]'; ?> ...</snap></br>
        <div class="row justify-content-center bg-light rounded py-4 px-2 m-2">
            <!-- 上列 -->
            <div class="col-12 col-md-6 py-0">
                <h4>更新職災申報日期：</h4>
            </div>
            <div class="col-12 col-md-6 py-0 text-end">
                <snap id="submit_action">
                    <?php if($sys_role <= 2){ ?>
                        <button type="button" class="btn btn-primary" id="submit"><i class="fa fa-paper-plane" aria-hidden="true"></i> 送出 (Submit)</button>
                    <?php } ?>
                </snap>
                <button type="button" class="btn btn-secondary" onclick="return confirm('確認返回？') && closeWindow(true)"><i class="fa fa-external-link" aria-hidden="true"></i>&nbsp回上頁</button>
            </div>
            <div class="col-12 pt-0 pb-1">
                <hr>
            </div>

            <!-- 左側 -->
            <div class="col-6 col-md-6 px-2">
                <div class="col-12 bg-white border rounded text-center">
                    <!-- cow -->
                    <div class="col-12 t-left py-0" style="font-size: 12px;">
                        <?php 
                            foreach($row_obj as $key => $key_value){
                                if(in_array(gettype($key_value), ['array', 'object'])){
                                    $key_value = (array) $key_value;
                                    echo "<li>{$key}：<ul>";
                                    foreach($key_value as $k => $k_v){
                                        echo "<li>{$k}：{$k_v}</li>";
                                    }
                                    echo "</ul></li>";
                                }else{
                                    echo "<li>{$key}：{$key_value}</li>";
                                }

                            };
                            echo "<snap name='row_json' id='row_json' class='t-left unblock' style='font-size: 12px;'>".$row_json."</snap>";
                        ?>
                    </div>
                </div>
            </div>

            <!-- 右側 -->
            <div class="col-6 col-md-6 px-2">
                <div class="col-12 bg-white border rounded text-center">
                           <!-- upload utility -->
                    <div class="form-floating">
                        <input type="date" name="_odd['od_day']" id="od_day" class="form-control mb-0" value="" require>
                        <label for="od_day" class="form-label">od_day/職災申報完成日期：<sup class="text-danger"> * </sup></label>
                    </div>
                </div>
            </div>

            <!-- 下列 -->
            <div class="col-12 pb-0 text-end">
                <hr>
                <button type="button" class="btn btn-secondary" onclick="return confirm('確認返回？') && closeWindow(true)"><i class="fa fa-external-link" aria-hidden="true"></i>&nbsp回上頁</button>
            </div>

        </div>  
    </div>
</body>

<script>    

    swal_json = {                                 // for swal_json
        "fun"     : "interView document",
        "content" : "process_odd -- ",
        "action"  : "error"
    };
    
</script>

<script src="process_odd.js?v=<?=time()?>"></script>



