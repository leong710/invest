<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    require_once("../user_info.php");
    accessDenied($sys_id);
    extract($_REQUEST);
    
    $swal_json = array(                                 // for swal_json
        "fun"     => "interView document",
        "content" => "process_PDF 處理--",
        "action"  => "error",
        "content" => '參數錯誤'
    );

    $uuid = (isset($_REQUEST["uuid"])) ? $_REQUEST["uuid"] : "";
    if(!empty($uuid) && !isset($_REQUEST["submit"])){
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
                "uuid"         => (isset($_REQUEST["uuid"])         ? $_REQUEST["uuid"]         : ""),
                "confirm_sign" => (isset($_REQUEST["confirm_sign"]) ? $_REQUEST["confirm_sign"] : ""),
                "fab_title"    => (isset($_REQUEST["fab_title"])    ? $_REQUEST["fab_title"]    : ""),
                "short_name"   => (isset($_REQUEST["short_name"])   ? $_REQUEST["short_name"]   : ""),
                "created_at"   => (isset($_REQUEST["case_year"])    ? $_REQUEST["case_year"]    : "")
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
    if(isset($_REQUEST["submit_a"])){
        switch($_REQUEST["submit"]){
            case "uploadFile_pdf":                                  // 更新上傳PDF文件
                $action_fun = "uploadFile_pdf";
                $swal_json  =  uploadFile_pdf($_REQUEST); 
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
        <!-- ./zz/debug.php -->
        <form action="" method="post" enctype="multipart/form-data" onsubmit="this.confirm_sign.disabled=false" id="mainForm">
            <div class="row justify-content-center bg-light rounded py-4 px-2 m-2">
                <!-- 左側 -->
                <div class="col-6 col-md-6" style="font-size: 12px;">
                    <?php 
                    foreach($pdf_json as $key => $key_value){
                        echo "<li>{$key}：{$key_value}</li>";
                    };

                    if(isset($_REQUEST)){
                        echo "<hr>";                    
                        echo "<pre>";
                        print_r($_REQUEST);
                        echo "</pre>";
                    }
                    
                    ?>
                </div>

                <!-- 右側 -->
                <div class="col-6 col-md-6 py-0 px-2">
                    <div class="col-12 bg-white border rounded text-center">
                        <!-- pdf icon -->
                        <div class="col-12 py-0" id="confirm_sign_preview" style="font-size: 12px;">
                            <?php if(!empty($document_row["confirm_sign"])){
                                    echo "<button type='button' class='btn text-danger add_btn' id='doc_pdf_icon' data-toggle='tooltip' data-placement='bottom' title='{$document_row["confirm_sign"]}' ";
                                    echo " value='../doc_pdf/{$pdf_json["fab_title"]}/{$pdf_json["short_name"]}/{$pdf_json["case_year"]}/{$document_row["confirm_sign"]}' ";
                                    echo " onclick='openUrl(this.value)' ><i class='fa-solid fa-file-pdf fa-2x'></i></button>"; 
                                }else{
                                    echo "-- nothing --";
                            } ?>
                        </div>
                        <!-- cow -->
                        <input type="text" name="confirm_sign" id="confirm_sign" value="<?php echo $document_row["confirm_sign"];?>" class="form-control my-3" readonly style="font-size: 12px;">
                        <!-- upload utility -->
                        <div class="input-group ">
                            <input type="file" name="confirm_sign_new" id="confirm_sign_new" class="form-control mb-0" accept=".pdf" >
                            <button type="button" class="btn btn-outline-success" onclick="uploadFile_pdf('confirm_sign')"  data-toggle="tooltip" data-placement="bottom" title="上傳PDF"><i class="fa fa-plus"></i></button> 
                            <button type="button" class="btn btn-outline-danger"  onclick="unlinkFile_pdf('confirm_sign')"  data-toggle="tooltip" data-placement="bottom" title="刪除檔案"><i class="fa-solid fa-delete-left"></i></button> 
                        </div>
                    </div>
                </div>

                <!-- 下列 -->
                <div class="col-12 pb-0 text-end">
                    <snap id="submit_action">
                        <input type="hidden" name="uuid"        id="uuid"       value="<?php echo $pdf_json["uuid"];?>"       readonly>
                        <input type="hidden" name="fab_title"   id="fab_title"  value="<?php echo $pdf_json["fab_title"];?>"  readonly>
                        <input type="hidden" name="short_name"  id="short_name" value="<?php echo $pdf_json["short_name"];?>" readonly>
                        <input type="hidden" name="case_year"   id="case_year"  value="<?php echo $pdf_json["case_year"];?>"  readonly>
                        <?php if($sys_role <= 2){ ?>
                            <button type="submit" value="uploadFile_pdf" name="submit" class="btn btn-primary" ><i class="fa fa-paper-plane" aria-hidden="true"></i> 送出 (Submit)</button>
                        <?php } ?>
                    </snap>
                    <button type="button" class="btn btn-secondary" onclick="return confirm('確認返回？') && closeWindow()"><i class="fa fa-external-link" aria-hidden="true"></i>&nbsp回上頁</button>
                </div>
            </div>  
        </form>
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



