<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("../user_info.php");
    require_once("function.php");
    accessDenied($sys_id);

    // 複製本頁網址藥用
    $up_href = (isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];   // 回上頁 // 回本頁
    $action = (isset($_REQUEST["action"])) ? $_REQUEST["action"] : 'create';   // 有action就帶action，沒有action就新開單

    if(isset($_REQUEST["uuid"])){
        $document_row = edit_document($_REQUEST);
        if(empty($document_row['uuid'])){
            echo "<script>alert('uuid-error：{$_REQUEST["uuid"]}')</script>";
            header("refresh:0;url=index.php");
            return;
        }
        // logs紀錄鋪設前處理 
        $logs_arr     = (array) json_decode($document_row["logs"]);
        $editions_arr = !empty($document_row["editions"]) ? (array) json_decode($document_row["editions"]) : [];
        // 路径到 form_a.json 文件
        $form_doc = (isset($document_row["dcc_no"]) ? "../doc_json/".$document_row["dcc_no"].".json" : "" );

    }else{
        // 決定表單開啟方式
        $document_row = array( "uuid" => "" );      // 預設document_row[uuid]=空array
        // logs紀錄鋪設前處理 
        $logs_arr     = [];                         // 預設logs_arr=空array
        $editions_arr = [];                         // 預設editions_arr=空array
        // 路径到 form_a.json 文件
        $form_doc = (isset($_REQUEST["dcc_no"]) ? "../doc_json/".$_REQUEST["dcc_no"].".json" : "" );
    }

    $fabs = show_fab_lists();

        if(file_exists($form_doc)){
            // 从 JSON 文件加载内容
            $form_json = file_get_contents($form_doc);
            // 解析 JSON 数据并将其存储在 $form_a_json 变量中
            $form_json = (array) json_decode($form_json, true);     // 如果您想将JSON解析为关联数组，请传入 true，否则将解析为对象
            $init_error = '';
        }else{
            $form_json = [];
            $init_error = ($form_doc) ? '查無表單：'.$form_doc : "無參照範本";
        }

?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>
<head>
    <link href="../../libs/aos/aos.css" rel="stylesheet">                                           <!-- goTop滾動畫面aos.css 1/4-->
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>            <!-- Jquery -->
        <link rel="stylesheet" type="text/css" href="../../libs/dataTables/jquery.dataTables.css">  <!-- dataTable參照 https://ithelp.ithome.com.tw/articles/10230169 --> <!-- data table CSS+JS -->
        <script type="text/javascript" charset="utf8" src="../../libs/dataTables/jquery.dataTables.js"></script>
    <script src="../../libs/sweetalert/sweetalert.min.js"></script>                                 <!-- 引入 SweetAlert 的 JS 套件 參考資料 https://w3c.hexschool.com/blog/13ef5369 -->
    <script src="../../libs/jquery/jquery.mloading.js"></script>                                    <!-- mloading JS 1/3 -->
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">                            <!-- mloading CSS 2/3 -->
    <script src="../../libs/jquery/mloading_init.js"></script>                                      <!-- mLoading_init.js 3/3 -->
    <style>

        /* #emp_id, #excelFile{    
            margin-bottom: 0px;
            text-align: center;
        } */
        .a_pic {
            width: 150px; 
            height: auto; 
            text-align:center;
        }
        .info {
            font-size: 14px;
            color: blue;
            text-shadow: 3px 3px 5px rgba(0,0,0,.3);
        }
        .signature {
            box-shadow: 0px 0px 8px rgba(0,0,0,.5);
        }
        /* 使用 CSS 將 canvas 的寬度設置為 100% */
        /* canvas {
            width: 100%;
        } */
    </style>
</head>

<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-10 border rounded px-4 py-4" style="background-color: #D4D4D4;">
                <!-- 表頭1 -->
                <div class="row px-4">
                    <div class="col-12 col-md-6 py-0" id="home_title">
                        <h3><i class="fa-solid fa-list-check"></i>&nbsp<b><snap id="form_title">通用表單Form</snap></b><?php echo empty($action) ? "":" - ".$action;?></h3>
                    </div>
                    <div class="col-12 col-md-6 py-0 text-end">
                        <span id="submit_btn">
                            <?php if(!$init_error){ ?>
                                <a href="#" target="_blank" title="Submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveSubmit"> <i class="fa fa-paper-plane" aria-hidden="true"></i> 送出</a>
                            <?php } ?>
                        </span>
                        <a href="<?php echo $up_href;?>" class="btn btn-secondary" onclick="return confirm('確認返回？');" ><i class="fa fa-external-link" aria-hidden="true"></i>&nbsp回上頁</a>
                    </div>
                </div>
                <div class="row px-2">
                    <div class="col-12 col-md-6 py-1">
                        訪談單號：<?php echo ($action == 'create') ? "(尚未給號)": "aid_".$document_row['id']; ?></br>
                        開單日期：<?php echo ($action == 'create') ? date('Y-m-d H:i')."&nbsp(實際以送出時間為主)":$document_row['created_at']; ?></br>
                        填單人員：<?php echo ($action == 'create') ? $auth_emp_id." / ".$auth_cname : $document_row["created_emp_id"]." / ".$document_row["created_cname"] ;?>
                    </div>
                    <div class="col-12 col-md-6 py-1 text-end">
                        <span id="dcc_no_head"><?php echo ($init_error) ? '<snap class="text-danger">*** '.$init_error.' ***</snap>' :'';?></span>
                        <span id="delete_btn">
                            <?php if(($sys_role <= 1 ) && (isset($document_row['idty']) && $document_row['idty'] != 0)){ ?>
                                <form action="process.php" method="post">
                                    <input type="hidden" name="uuid" value="<?php echo $document_row["uuid"];?>">
                                    <input type="submit" name="delete_document" value="刪除 (Delete)" title="刪除申請單" class="btn btn-danger" onclick="return confirm('確認徹底刪除此單？')">
                                </form>
                            <?php }?>
                        </span>
                    </div>
                </div>
    
                <!-- container -->
                <div class="col-12">
                    <!-- 內頁 -->
                    <form action="process.php" method="post" enctype="multipart/form-data" onsubmit="this.cname.disabled=false" id="mainForm">
                    <!-- <form action="./zz/debug.php" method="post" enctype="multipart/form-data" onsubmit="this.cname.disabled=false" id="mainForm"> -->
                        <div class="row rounded bg-light py-3" id="form_container">
                            <div class="col-12 p-3 ">
                                <span class="from-label"><b>表單分類：</b></span><br>
                                <div class="col-12 p-3 border rounded bg-white">
                                    <div class="row">
                                        <!-- line 0 -->
                                        <div class="col-6 col-md-6 py-0">
                                            <div class="form-floating">
                                                <select name="fab_id" id="fab_id" class="form-select" required>
                                                    <option value="" hidden>-- [請選擇 廠別] --</option>
                                                    <?php foreach($fabs as $fab){ ?>
                                                            <option value="<?php echo $fab["id"];?>" title="<?php echo $fab["fab_title"];?>" <?php echo ($fab["flag"] == "Off") ? "disabled":"";?>>
                                                                <?php echo $fab["id"]."：".$fab["site_title"]."&nbsp".$fab["fab_title"]."&nbsp(".$fab["fab_remark"].")"; echo ($fab["flag"] == "Off") ? "&nbsp(已關閉)":"";?></option>
                                                    <?php } ?>
                                                </select>
                                                <label for="fab_id" class="form-label">fab_id/廠別：<sup class="text-danger"> *</sup></label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-6 py-0">
                                            <div class="form-floating">
                                                <input type="text" name="local_id" id="local_id" class="form-control" placeholder="小廠單位：" require >
                                                <label for="local_id" class="form-label">local_id/小廠單位：<sup class="text-danger"> * </sup></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <span class="from-label"><b>訪談摘要：</b></span><br>
                                <div class="col-12 p-3 border rounded bg-white">
                                    <div class="row">
                                        <!-- line 1 -->
                                        <div class="col-6 col-md-6 py-0">
                                            <div class="form-floating">
                                                <input type="text" name="case_title" id="case_title" class="form-control" placeholder="事件名稱：" require >
                                                <label for="case_title" class="form-label">case_title/事件名稱：<sup class="text-danger"> * </sup></label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-6 py-0">
                                            <div class="form-floating">
                                                <input type="text" name="a_dept" id="a_dept" class="form-control" placeholder="事故單位：" require >
                                                <label for="a_dept" class="form-label">a_dept/事故單位：<sup class="text-danger"> * </sup></label>
                                            </div>
                                        </div>
                                        <!-- line 2 -->
                                        <div class="col-6 col-md-6 pb-0">
                                            <div class="form-floating">
                                                <input type="datetime-local" name="meeting_time" id="meeting_time" class="form-control" value="<?=date('Y-m-d\TH:i')?>" require>
                                                <label for="meeting_time" class="form-label">meeting_time/會議時間：<sup class="text-danger"> * </sup></label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-6 pb-0">
                                            <div class="form-floating">
                                                <input type="text" name="meeting_local" id="meeting_local" class="form-control" placeholder="會議地點：" require >
                                                <label for="meeting_local" class="form-label">meeting_local/會議地點：<sup class="text-danger"> * </sup></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <!-- line 3 -->
                                <span class="from-label"><b>與會人員：</b></span><br>
                                <div class="col-12 p-3 border rounded bg-white" id="selectUser">
                                    <div class="row">
                                        <!-- 第一排的功能 : 顯示已加入名單+input -->
                                        <div class="col-12 py-0">
                                            <div class="input-group py-1">
                                                <span class="input-group-text" style="width:25%;">事故當事者(或其委任代理人)<sup class="text-danger"> * </sup></span>
                                                <input type="hidden" id="meeting_man_a_select" name="meeting_man_a">
                                                <span type="text" id="meeting_man_a_show" class="form-control mb-0" ></span>
                                                <button type="button" class="btn btn-outline-secondary search_btn" id="meeting_man_a" data-bs-target="#searchUser" data-bs-toggle="modal" >&nbsp<i class="fa fa-plus"></i>&nbsp</button>
                                            </div>
    
                                            <div class="input-group py-1">
                                                <span class="input-group-text" style="width:25%;">其他與會人員/勞工代表<sup class="text-danger"> * </sup></span>
                                                <input type="hidden" id="meeting_man_o_select" name="meeting_man_o">
                                                <span type="text" id="meeting_man_o_show" class="form-control mb-0" ></span>
                                                <button type="button" class="btn btn-outline-secondary search_btn" id="meeting_man_o" data-bs-target="#searchUser" data-bs-toggle="modal" >&nbsp<i class="fa fa-plus"></i>&nbsp</button>
                                            </div>
    
                                            <div class="input-group py-1">
                                                <span class="input-group-text" style="width:25%;">環安人員<sup class="text-danger"> * </sup></span>
                                                <input type="hidden" id="meeting_man_s_select" name="meeting_man_s">
                                                <span type="text" id="meeting_man_s_show" class="form-control mb-0" ></span>
                                                <button type="button" class="btn btn-outline-secondary search_btn" id="meeting_man_s" data-bs-target="#searchUser" data-bs-toggle="modal" >&nbsp<i class="fa fa-plus"></i>&nbsp</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <hr> -->
                            <div class="col-12 p-3 pt-0">
                                <div class="row">
                                    <div class="col-6 col-md-6 py-0 "><span class="from-label"><b>內容：</b></span></div>
                                    <div class="col-6 col-md-6 py-0 text-end"></div>
                                </div>
                                <div class="accordion" id="item_list" >
                                    <!-- append -->
                                </div>
                            </div>
                        </div>

                        <!-- 模組 saveSubmit-->
                        <div class="modal fade" id="saveSubmit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Do you submit this 事故訪談表：</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body px-4">
                                        <label for="sign_comm" class="form-check-label" >command：</label>
                                        <textarea name="sign_comm" id="sign_comm" class="form-control" rows="5"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden"  name="created_emp_id"  id="created_emp_id"  value="<?php echo $auth_emp_id;?>">
                                        <input type="hidden"  name="created_cname"   id="created_cname"   value="<?php echo $auth_cname;?>">
                                        <input type="hidden"  name="action"          id="action"          value="<?php echo $action;?>">
                                        <input type="hidden"  name="step"            id="step"            value="1">
                                        <input type="hidden"  name="idty"            id="idty"            value="1">
                                        <input type="hidden"  name="uuid"            id="uuid"            value="">
                                        <input type="hidden"  name="dcc_no"          id="dcc_no"          value="">
                                        <snap id="submit_action">
                                            <?php if($sys_role <= 3){ ?>
                                                <button type="submit" value="Submit" name="submit_document" class="btn btn-primary" ><i class="fa fa-paper-plane" aria-hidden="true"></i> 送出 (Submit)</button>
                                            <?php } ?>
                                        </snap>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <!-- 尾段logs訊息 -->
                    <div class="row rounded bg-light unblock" id="logs_div">
                        <div class="col-6 col-md-6 pb-0">
                            流程記錄：
                        </div>
                        <div class="col-6 col-md-6 pb-0">
                        </div>
                        <div class="col-12 pt-1 px-4">
                            <table class="for-table logs table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Step</th>
                                        <th>Signer</th>
                                        <th>Time Signed</th>
                                        <th>Status</th>
                                        <th>Comment</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div style="font-size: 12px;" class="text-end">
                            logs-end
                        </div>
                    </div>
                    <hr>
                    <!-- 尾段Editions訊息 -->
                    <div class="row rounded bg-light unblock" id="editions_div">
                        <div class="col-6 col-md-6 pb-0">
                            編輯記錄：
                        </div>
                        <div class="col-6 col-md-6 pb-0">
                        </div>
                        <div class="col-12 pt-1 px-4">
                            <table class="for-table editions table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Step</th>
                                        <th>Editor</th>
                                        <th>Time Edited</th>
                                        <th>Edit content</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div style="font-size: 12px;" class="text-end">
                            editions-end
                        </div>
                    </div>
                </div>
                
                <div style="font-size: 12px;" class="pb-0 text-end">
                    universalForm v0
                </div>
            </div>
        </div>
    </div>

    <!-- toast -->
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="liveToast" class="toast align-items-center" role="alert" aria-live="assertive" aria-atomic="true" autohide="true" delay="1000">
                <div class="d-flex">
                    <div class="toast-body" id="toast-body">
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>

    <!-- 彈出畫面-查詢user模組 -->
        <div class="modal fade" id="searchUser" aria-hidden="true" aria-labelledby="searchUser" tabindex="-1">
            <div class="modal-dialog modal-dialog-scrollable modal-xl">
                <div class="modal-content">

                    <div class="modal-header bg-warning  border rounded p-3 m-2">
                        <h5 class="modal-title"><i class="fa-solid fa-circle-info"></i>&nbspsearch & append User for&nbsp<span id="modal_title"></span></h5>
                        <button type="button" class="btn-close border rounded mx-1" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body mx-2">
                        <div class="row">
                            <div class="col-12 border rounded p-3 " id="selectScomp_no">
                                <div class="row justify-content-center">
                                    <!-- 第一排的功能 : 顯示已加入名單+input -->
                                    <div class="col-12 px-4 py-0">
                                        <div id="selectScomp_noItem"></div>
                                        <input type="hidden" class="form-control" name="scomp_no[]" id="scomp_no" placeholder="已加入的">
                                    </div>
                                    <!-- 第二排的功能 : 搜尋功能 -->
                                    <div class="col-12 col-md-6 px-4">
                                        <div class="input-group search">
                                            <span class="input-group-text">查詢</span>
                                            <input type="text" class="form-control text-center mb-0" id="key_word" required placeholder="-- 工號 / 姓名 查詢 --" >
                                            <button type="button" class="btn btn-outline-success" onclick="resetMain()">清除</button>
                                            <button type="button" class="btn btn-outline-primary" onclick="search_fun()"><i class="fa-solid fa-magnifying-glass"></i> 搜尋</button>
                                        </div>
                                    </div>
                                    <!-- 第三排的功能 : 放查詢結果-->
                                    <div class="result" id="result">
                                        <table id="result_table" class="table table-striped table-hover"></table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">返回</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="gotop">
            <i class="fas fa-angle-up fa-2x"></i>
        </div>

</body>

<script src="../../libs/aos/aos.js"></script>       <!-- goTop滾動畫面jquery.min.js+aos.js 3/4-->
<script src="../../libs/aos/aos_init.js"></script>  <!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/signature_pad/signature_pad.umd.min.js"></script>
<script src="../../libs/moment/moment.min.js"></script>

<script>
    // 開局設定init
    var action        = '<?=$action?>';                 // 取得表單開啟方式
    var check_action  = ( action == "review") ? true : false;  // 唯讀狀態
    var form_json     = <?=json_encode($form_json)?>;   // 取得表單
    var form_item     = form_json.form_item;            // 抓item項目for form item
    var json          = <?=json_encode($logs_arr)?>;
    var editions_arr  = <?=json_encode($editions_arr)?>;
    var meeting_man_a = [];                         // 事故當事者(或其委任代理人)
    var meeting_man_o = [];                         // 其他與會人員
    var meeting_man_s = [];                         // 環安人員
    var meeting_man_target;                         // 指向目標
    
    var searchUser_modal = new bootstrap.Modal(document.getElementById('searchUser'), { keyboard: false });
    var document_row = <?=json_encode($document_row)?>;   // 取得表單資料

    // // searchUser function 
        // fun_0.清除searchUser_modal
        function resetMain(){
            $("#result").removeClass("border rounded bg-white");
            $('#result_table').empty();                                 // 搜尋清單
            // $('#modal_title').empty();                                  // 標題
            document.querySelector('#key_word').value = '';             // 搜尋key_word input
        }
        // fun_1.search Key_word
        function search_fun(){
            mloading("show");                                           // 啟用mLoading
            const uuid = '39aad298-a041-11ed-8ed4-2cfda183ef4f';        // hrdb
            let search = $('#key_word').val().trim();                   // search keyword取自user欄位
            let request = {
                functionname : 'search',                                // 操作功能
                uuid         : uuid,                                    // ppe
                search       : search                                   // 查詢對象key_word
            }
            $.ajax({
                url: 'http://tneship.cminl.oa/api/hrdb/index.php',      // 正式2024新版
                method: 'post',
                dataType: 'json',
                data: request,
                success: function(res){
                    let res_r = res["result"];
                    postList(res_r);                                    // 將結果轉給postList進行渲染
                },
                error (err){
                    console.log("search error:", err);
                    $("body").mLoading("hide");
                    alert("查詢錯誤!!");
                }
            })

            $("body").mLoading("hide");                                 // 關閉mLoading
        }
        // fun_2.渲染功能
        function postList(res_r){
            // 清除表頭
            $('#result_table').empty();
            $("#result").addClass("bg-white");
            // 定義表格頭段
            let div_result_table = document.querySelector('.result table');
            let Rinner = "<thead><tr>"+
                            "<th>員工編號</th>"+"<th>員工姓名</th>"+"<th>職稱</th>"+"<th>user_ID</th>"+"<th>部門代號</th>"+"<th>部門名稱</th>"+"<th>select</th>"+
                        "</tr></thead>" + "<tbody id='tbody'>"+"</tbody>";
            // 鋪設表格頭段thead
            div_result_table.innerHTML += Rinner;
            // 定義表格中段tbody
            let div_result_tbody = document.querySelector('.result table tbody');
            $('#tbody').empty();
            for (let i=0; i < res_r.length; i++) {
                // 把user訊息包成json字串以便夾帶
                    let user_json = {
                            'emp_id'    : res_r[i].emp_id.trim(),
                            'cname'     : res_r[i].cname.trim(),
                            'cstext'    : res_r[i].cstext.trim(),
                            'oftext'   : res_r[i].dept_no.trim() +'\/'+ res_r[i].oftext
                        };
                // let user_json = res_r[i].emp_id.trim() +','+ res_r[i].cname.trim() +','+ res_r[i].cstext.trim() + ',' + res_r[i].dept_no.trim() + '\/' + res_r[i].oftext;
                div_result_tbody.innerHTML += 
                    '<tr>' +
                        '<td>' + res_r[i].emp_id.trim() +'</td>' +
                        '<td>' + res_r[i].cname.trim() + '</td>' +
                        '<td>' + res_r[i].cstext.trim() + '</td>' +
                        '<td>' + res_r[i].user.trim() + '</td>' +
                        '<td>' + res_r[i].dept_no.trim() + '</td>' +
                        '<td>' + res_r[i].oftext.trim() + '</td>' +
                        '<td>' + '<button type="button" class="btn btn-default btn-xs" id="'+res_r[i].emp_id+'" value=\''+ JSON.stringify(user_json) +'\' onclick="tagsInput_me(this.value)">'+
                        '<i class="fa-regular fa-circle"></i></button>' + '</td>' +
                    '</tr>';
            }

        }
        // fun_3.點選、渲染模組
        function tagsInput_me(val) {
            if (val !== '') {
                let personal_inf = JSON.parse(val);
                let emp_id = personal_inf['emp_id'];        // 指定emp_id 
                let cname  = personal_inf['cname'];         // 指定cname 

                if(meeting_man_target == 'emp_id_btn'){     // 來自事故者基本資訊
                    Object.keys(personal_inf).forEach((_key)=>{
                        let _key_elem = document.querySelector('#'+_key)
                        if(_key_elem){
                            _key_elem.value = personal_inf[_key]
                        }
                    })
                    searchUser_modal.hide();      // 關閉searchUser_modal

                }else{                                      // 來自會議title
                    val = JSON.stringify({
                        "cname"  : cname,
                        "emp_id" : emp_id
                    })
                    window[meeting_man_target].push(val);
                    $('#'+meeting_man_target+'_show').append('<div class="tag">' + cname + ' / '+ emp_id + '<span class="remove">x</span></div>');
                    let tag_user = document.getElementById(emp_id);
                    if(tag_user){ tag_user.value = ''; }
                    let meeting_man_target_select = document.getElementById(meeting_man_target+'_select');
                    if(meeting_man_target_select){
                        meeting_man_target_select.value = window[meeting_man_target];
                    }
                }
            }
            // resetMain();
            // searchUser_modal.hide();      // 切到searchUser頁面
        }
        // fun_4.移除單項模組
        $('#meeting_man_a_show, #meeting_man_o_show, #meeting_man_s_show ').on('click', '.remove', function() {
            let this_parent     = $(this).parent().parent();                // 取得爺層的元素
            let this_parent_id  = this_parent[0].id.replace('_show', '');   // 取得爺層的id，並去除_show
            let tagIndex        = $(this).closest('.tag').index();          // 取得點擊index位置
            let tagg            = window[this_parent_id][tagIndex];         // 取得目標數值 emp_id,cname
            let emp_id          = tagg.substr(0, tagg.search(','));         // 指定 emp_id
                let tag_user        = document.getElementById(emp_id);
                if(tag_user){ 
                    tag_user.value = tagg; 
                }
            window[this_parent_id].splice(tagIndex, 1);                     // 自陣列中移除
            $(this).closest('.tag').remove();                               // 自畫面中移除
            let _select = document.getElementById(this_parent_id+'_select');
            if(_select){
                _select.value = window[this_parent_id];
            }
        });
    // // searchUser function 

    $(function () {
        // 監聽myModal被關閉時就執行--清除表格
        var searchUser_elm = document.getElementById('searchUser');
        searchUser_elm.addEventListener('hidden.bs.modal', function () {
            resetMain();                    // do something...清除欄位
            $('#modal_title').empty();      // 清除標題
        })
    })

    // fun3-3：吐司顯示字條 // init toast
    function inside_toast(sinn){
        var toastLiveExample = document.getElementById('liveToast');
        var toast = new bootstrap.Toast(toastLiveExample);
        var toast_body = document.getElementById('toast-body');
        toast_body.innerHTML = sinn;
        toast.show();
    }

    // DOMContentLoaded 事件监听器
    // document.addEventListener('DOMContentLoaded', function () {
    //     // 绑定事件监听器
    //     attachEventListeners();
    // });

        console.log('editions_arr:' , editions_arr);

</script>

<script src="form.js?v=<?=time()?>"></script>

<?php include("../template/footer.php"); ?>