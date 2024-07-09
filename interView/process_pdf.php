<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    require_once("../user_info.php");
    accessDenied($sys_id);
    extract($_REQUEST);

    $document_row = [];
    $row_obj      = [];
    $get_item_arr = ["uuid", "confirm_sign", "anis_no"];  // 建立取用欄位； 例外處理："created_at", "case_year", "row_confirm_sign"
    
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
    // 例外處理
    $row_obj["case_year"] = (isset($document_row["created_at"])   ? substr($document_row["created_at"],0,4) : "");
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
        <snap class="text-white"><?php echo !empty($action_fun) ? $action_fun : '[process_pdf]'; ?> ...</snap></br>
        <div class="row justify-content-center bg-light rounded py-4 px-2 m-2">
            <!-- 上列 -->
            <div class="col-12 col-md-6 py-0">
                <h4>更新結案文件存檔：</h4>
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
            <div class="col-6 col-md-6 py-0 px-2">
                <div class="col-12 bg-white border rounded text-center">
                    <!-- pdf icon -->
                    <div class="row">
                        <div class="col-6 col-md-7 py-0 t-right" id="confirm_sign_asis" style="font-size: 12px;">
                            <?php if(!empty($document_row["confirm_sign"])){
                                    echo "<a target='_blank' class='btn text-danger add_btn' id='doc_pdf_icon' data-toggle='tooltip' data-placement='bottom' title='{$document_row["confirm_sign"]}' ";
                                    echo " href='../doc_files/{$row_obj["case_year"]}/{$row_obj["anis_no"]}/{$document_row["confirm_sign"]}' ";
                                    echo " ><i class='fa-solid fa-file-pdf fa-2x'></i></br>as is</a>"; 
                                }else{
                                    echo "-- nothing --";
                            } ?>
                        </div>
                        <div class="col-6 col-md-5 py-0 t-right" >
                            <?php 
                                echo "<button type='button' class='btn btn-outline-danger' id='del_asis' onclick='return confirm(`確定要刪除此文件？`) &&unlinkFile_pdf(`confirm_sign`,`asis`)' ".(empty($document_row["confirm_sign"]) ? "disabled" : "")." data-toggle='tooltip' data-placement='bottom' title='刪除asis檔案'><i class='fa-solid fa-delete-left'></i></button>"; 
                            ?>
                        </div>
                    </div>
                    <hr>
                    <!-- cow -->
                    <div class="col-12 t-left py-0" style="font-size: 12px;">
                        <?php 
                            foreach($row_obj as $key => $key_value){
                                echo "<li>{$key}：{$key_value}</li>";
                            };
                            echo "<snap name='row_json' id='row_json' class='t-left unblock' style='font-size: 10px;'>".$row_json."</snap>";
                        ?>
                    </div>
                </div>
            </div>

            <!-- 右側 -->
            <div class="col-6 col-md-6 py-0 px-2">
                <div class="col-12 bg-white border rounded text-center">
                    <!-- pdf icon -->
                    <div class="row">
                        <div class="col-6 col-md-7 py-0 t-right" id="confirm_sign_tobe" style="font-size: 12px;">
                            -- nothing --
                            <!-- append new pdf icon -->
                        </div>
                        <div class="col-6 col-md-5 py-0 t-right" >
                            <button type="button" class="btn btn-outline-danger" id='del_tobe' onclick="unlinkFile_pdf('confirm_sign','tobe')" disabled data-toggle="tooltip" data-placement="bottom" title="刪除tobe檔案"><i class="fa-solid fa-delete-left"></i></button>
                        </div>
                    </div>

                    <hr>
                    <!-- cow -->
                    <div class="input-group my-3">
                        <span class="input-group-text">新文件</span>
                        <input type="text" name="confirm_sign" id="confirm_sign" class="form-control mb-0" readonly required style="font-size: 12px;">
                    </div>
                    <!-- upload utility -->
                    <div class="input-group ">
                        <input type="file" name="confirm_sign_upload" id="confirm_sign_upload" class="form-control mb-0" accept=".pdf" >
                        <button type="button" class="btn btn-outline-success" onclick="uploadFile_pdf('confirm_sign')"  data-toggle="tooltip" data-placement="bottom" title="上傳PDF"><i class="fa fa-plus"></i></button> 
                    </div>
                    <span class="t-right text-danger">* 限定上傳PDF檔</span>
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

    var swal_json = {                                 // for swal_json
        "fun"     : "interView document",
        "content" : "process_PDF -- ",
        "action"  : "error"
    };
    
</script>

<script src="process_pdf.js?v=<?=time()?>"></script>



