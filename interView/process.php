<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    require_once("../user_info.php");
    accessDenied($sys_id);
    extract($_REQUEST);
    
    // 複製本頁網址藥用
    $up_href = (isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];   // 回上頁 // 回本頁

    $swal_json = array(                                 // for swal_json
        "fun"     => "interView document",
        "content" => "process處理--",
        "action"  => "error",
        "content" => '$action參數錯誤'
    );

    // CRUD
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
        body{
            color: white;
        }
    </style>
</head>

<body>
    <div class="col-12">
        <snap><?php echo !empty($action_fun) ? $action_fun : '[process]'; ?> ...</snap>
    </div>
</body>

<script>    
    
    var swal_json = <?=json_encode($swal_json)?>;                   // 引入swal_json值
    var url       = 'index.php';

    $(document).ready(function () {
        if(swal_json.length != 0){
            // // history.back();
                // location.href = this.url;
                // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action'], {buttons: false, timer:3000});         // 3秒
                // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{window.close();});           // 關閉畫面

            if(swal_json['action'] == 'success'){
                // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action'], {buttons: false, timer:2000}).then(()=>{ location.href = url }); // 秒自動關閉畫面
                // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{history.back()});          // 手動關閉畫面
                // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action'], {buttons: false, timer:2000}).then(()=>{closeWindow()}); // 秒自動關閉畫面
                swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{closeWindow(true)});        // 秒自動關閉畫面

            }else if(swal_json['action'] == 'error'){
                swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{history.back()});          // 手動關閉畫面

            }
        }else{
            location.href = url;
        }
    })
    
</script>




