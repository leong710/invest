<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    require_once("../user_info.php");
    accessDenied($sys_id);
    extract($_REQUEST);
    
    // 複製本頁網址藥用
    $up_href = (isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];   // 回上頁 // 回本頁
    $uuid    = (isset($_REQUEST["uuid"]))   ? $_REQUEST["uuid"]   : "";

    $swal_json = array(                                 // for swal_json
        "fun"     => "interView document",
        "content" => "process_PDF處理--",
        "action"  => "error",
        "content" => '$action參數錯誤'
    );


    if(!empty($uuid)){
        $document_row = edit_document(["uuid" => $uuid]);
            if(empty($document_row)){
                echo "<script>alert('uuid-error：{$uuid}')</script>";
                echo "<script>window.close()</script>";
                return;
            }
        // 路径到 form_a.json 文件
        $confirm_sign   = (isset($document_row["confirm_sign"]) ? $document_row["confirm_sign"] : "" );

    }else{
        // 決定表單開啟方式 // 預設document_row[uuid]=空array
        $document_row = array( 
                "uuid"         => (isset($_REQUEST["uuid"])         ? $_REQUEST["uuid"] : ""),
                "confirm_sign" => (isset($_REQUEST["confirm_sign"]) ? $_REQUEST["confirm_sign"] : "" )
            );
    }


    $pdf_json = array(
        "uuid"         => (isset($document_row["uuid"])         ? $document_row["uuid"] : ""),
        "confirm_sign" => (isset($document_row["confirm_sign"]) ? $document_row["confirm_sign"] : ""),
        "fab_title"    => (isset($document_row["fab_title"])    ? $document_row["fab_title"]  : "" ),
        "short_name"   => (isset($document_row["short_name"])   ? $document_row["short_name"] : "" ),
        "case_year"    => (isset($document_row["created_at"])   ? substr($document_row["created_at"],0,4)  : "" )
    );


    // CRUD
    if(isset($_REQUEST["action"])){
        switch($_REQUEST["action"]){
    
            case "create":                                  // 開新.新增    $idty => 1(送出); 6(暫存);
                $action_fun = "submit_document";
                $swal_json  =  store_document($_REQUEST); 
                break;
    
            case "edit":                                    // 編輯.更新    $idty => 1(送出--詳細比對); 6(暫存--排除比對);
                $action_fun = "update_document";
                $swal_json  =  update_document($_REQUEST);
                break;
    
            case "delete":                                  // 刪除.刪除
                $action_fun = "delete_document";
                $swal_json  =  delete_document($_REQUEST);
                break;
    
            case "sign":                                    // 簽核
    
                break;
    
            default:                                        // 預定失效 
     
                break;
        }
    }

    // 調整flag ==> 20230712改用AJAX

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
        <snap class="text-white"><?php echo !empty($action_fun) ? $action_fun : '[process]'; ?> ...</snap></br>
        <div class="row justify-content-center bg-light rounded py-4 px-2 m-2">
            <!-- 左側 -->
            <div class="col-6 col-md-6" id="confirm_sign_preview" style="font-size: 12px;">
                <?php foreach($pdf_json as $key => $key_value){
                        echo "<li id='row_{$key}'>{$key}：{$key_value}</li>";
                };?>
            </div>

            <!-- 右側 -->
            <div class="col-6 col-md-6 py-0 px-2">
                <div class="col-12 bg-white border rounded text-center">
                    <?php if(!empty($document_row["confirm_sign"])){
                            echo "<button type='button' class='btn text-danger add_btn' data-toggle='tooltip' data-placement='bottom' title='{$document_row["confirm_sign"]}' ";
                            echo " value='../doc_pdf/{$pdf_json["fab_title"]}/{$pdf_json["short_name"]}/{$pdf_json["case_year"]}/{$document_row["confirm_sign"]}' ";
                            echo " onclick='openUrl(this.value)' ><i class='fa-solid fa-file-pdf fa-2x'></i></button>"; 
                        }else{
                            echo " nothing...";
                    } ?>
                    <input type="text" name="confirm_sign" id="confirm_sign" value="<?php echo $document_row["confirm_sign"];?>" class="form-control my-3" readonly style="font-size: 12px;">
                    <div class="input-group ">
                        <input type="file" name="confirm_sign_new" id="confirm_sign_new" class="form-control mb-0" accept=".pdf" >
                        <button type="button" class="btn btn-outline-success" onclick="uploadFile('confirm_sign')">Upload</button> 
                        <button type="button" class="btn btn-outline-danger"  onclick="unlinkFile('confirm_sign')">Delete</button> 
                    </div>
                </div>
            </div>
            <!-- 下列 -->
            <div class="col-12 pb-0 text-end">
                <snap id="submit_action">
                    <?php if($sys_role <= 2){ ?>
                        <button type="submit" value="Submit" name="submit_document" class="btn btn-primary" ><i class="fa fa-paper-plane" aria-hidden="true"></i> 送出 (Submit)</button>
                    <?php } ?>
                </snap>
                <button type="button" class="btn btn-secondary" onclick="return confirm('確認返回？') && closeWindow()"><i class="fa fa-external-link" aria-hidden="true"></i>&nbsp回上頁</button>
            </div>

        </div>  
        
    </div>
</body>

<script>    
    
    // var swal_json = <=json_encode($swal_json)?>;                   // 引入swal_json值
    // var url       = 'index.php';

    // $(document).ready(function () {
    //     if(swal_json.length != 0){
    //         // // history.back();
    //             // location.href = this.url;
    //             // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action'], {buttons: false, timer:3000});         // 3秒
    //             // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{window.close();});           // 關閉畫面

    //         if(swal_json['action'] == 'success'){
    //             // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action'], {buttons: false, timer:2000}).then(()=>{ location.href = url }); // 秒自動關閉畫面
    //             // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{history.back()});          // 手動關閉畫面
    //             // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action'], {buttons: false, timer:2000}).then(()=>{closeWindow()}); // 秒自動關閉畫面
    //             swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{closeWindow(true)});        // 秒自動關閉畫面

    //         }else if(swal_json['action'] == 'error'){
    //             swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{history.back()});          // 手動關閉畫面

    //         }
    //     }else{
    //         location.href = url;
    //     }
    // })
    
</script>

<script src="process_pdf.js?v=<?=time()?>"></script>



