<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("../user_info.php");
    // require_once("function.php");
    accessDenied($sys_id);

    // 複製本頁網址藥用
    $up_href = (isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];   // 回上頁 // 回本頁

    // 決定表單開啟方式
    $action = (isset($_REQUEST["action"])) ? $_REQUEST["action"] : 'create';   // 有action就帶action，沒有action就新開單


    // 路径到 form_a.json 文件
    $json_file_path = 'form_a.json';
    // 从 JSON 文件加载内容
    $form_a_json = file_get_contents($json_file_path);
    // 解析 JSON 数据并将其存储在 $form_a_json 变量中
    $form_a_json = json_decode($form_a_json, true); // 如果您想将JSON解析为关联数组，请传入 true，否则将解析为对象
    

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

    </style>
</head>

<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-10 border rounded px-3 py-4" style="background-color: #D4D4D4;">
                <!-- 表頭1 -->
                <div class="row px-2">
                    <div class="col-12 col-md-6 py-0" id="home_title">
                        <h3><i class="fa-solid fa-3"></i>&nbsp<b>南廠區交通事故訪談表</b><?php echo empty($action) ? "":" - ".$action;?></h3>
                    </div>
                    <div class="col-12 col-md-6 py-0 text-end">
                        <a href="#" target="_blank" title="Submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveSubmit"> <i class="fa fa-paper-plane" aria-hidden="true"></i> 送出</a>
                        <a href="<?php echo $up_href;?>" class="btn btn-secondary" onclick="return confirm('確認返回？');" ><i class="fa fa-external-link" aria-hidden="true"></i>&nbsp回上頁</a>
                    </div>
                </div>
                <div class="row px-2">
                    <div class="col-12 col-md-6">
                        訪問單號：<?php echo ($action == 'create') ? "(尚未給號)": "receive_aid_".$receive_row['id']; ?></br>
                        開單日期：<?php echo ($action == 'create') ? date('Y-m-d H:i')."&nbsp(實際以送出時間為主)":$receive_row['created_at']; ?></br>
                        填單人員：<?php echo ($action == 'create') ? $auth_emp_id." / ".$auth_cname : $receive_row["created_emp_id"]." / ".$receive_row["created_cname"] ;?>
                    </div>
                    <div class="col-12 col-md-6 text-end">
                        <?php if(($sys_role <= 1 ) && (isset($receive_row['idty']) && $receive_row['idty'] != 0)){ ?>
                            <form action="" method="post">
                                <input type="hidden" name="uuid" value="<?php echo $receive_row["uuid"];?>">
                                <input type="submit" name="delete_receive" value="刪除 (Delete)" title="刪除申請單" class="btn btn-danger" onclick="return confirm('確認徹底刪除此單？')">
                            </form>
                        <?php }?>
                    </div>
                </div>
    
                <!-- container -->
                <div class="col-12 py-0">
                    <!-- 內頁 -->
                    <form action="./zz/debug.php" method="post" onsubmit="this.cname.disabled=false,this.plant.disabled=false,this.dept.disabled=false,this.sign_code.disabled=false,this.omager.disabled=false" >
                        <div class="row rounded bg-light" id="form_container">
                            <div class="col-12 p-3 ">
                                <div class="row">
                                    <!-- line 1 -->
                                    <div class="col-6 col-md-6 py-0">
                                        <div class="form-floating">
                                            <input type="text" name="case_title" id="case_title" class="form-control" require >
                                            <label for="case_title" class="form-label">case_title/事件名稱：<sup class="text-danger"> * </sup></label>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-6 py-0">
                                        <div class="form-floating">
                                            <input type="text" name="a_dept" id="a_dept" class="form-control" require >
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
                                            <input type="text" name="meeting_local" id="meeting_local" class="form-control" require >
                                            <label for="meeting_local" class="form-label">meeting_local/會議地點：<sup class="text-danger"> * </sup></label>
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
                                                <span class="input-group-text" style="width:25%;">其他與會人員<sup class="text-danger"> * </sup></span>
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
                            
                            <div class="col-12 p-3">
                                <div class="row">
                                    <div class="col-6 col-md-6 py-0 "><span class="from-label"><b>內容：</b></span></div>
                                    <div class="col-6 col-md-6 py-0 text-end"></div>
                                </div>
                                <div class="col-12 border rounded bg-white" id="item_list" >
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
                                    <div class="modal-body px-5">
                                        <label for="sign_comm" class="form-check-label" >command：</label>
                                        <textarea name="sign_comm" id="sign_comm" class="form-control" rows="5"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="created_emp_id"  id="created_emp_id" value="<?php echo $auth_emp_id;?>">
                                        <input type="hidden" name="created_cname"   id="created_cname"  value="<?php echo $auth_cname;?>">
                                        <input type="hidden" name="updated_user"    id="updated_user"   value="<?php echo $auth_cname;?>">
                                        <input type="hidden" name="uuid"            id="uuid"           value="">
                                        <input type="hidden" name="step"            id="step"           value="<?php echo $step;?>">
                                        <input type="hidden" name="action"          id="action"         value="<?php echo $action;?>">
                                        <input type="hidden" name="idty"            id="idty"           value="1">
                                        <?php if($sys_role <= 3){ ?>
                                            <button type="submit" value="Submit" name="receive_submit" class="btn btn-primary" ><i class="fa fa-paper-plane" aria-hidden="true"></i> 送出 (Submit)</button>
                                        <?php } ?>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <!-- 尾段logs訊息 -->
                    <div class="col-12 pt-0 rounded bg-light unblock" id="logs_div">
                        <div class="row">
                            <div class="col-6 col-md-6">
                                表單記錄：
                            </div>
                            <div class="col-6 col-md-6">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 py-1 px-4">
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
                    </div>
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
    <!-- 模組 cata_info -->
        <div class="modal fade" id="cata_info" tabindex="-1" aria-labelledby="cata_info" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">細項說明：</h5>
                        <button type="button" class="btn-close" aria-label="Close" data-bs-target="#catalog_modal" data-bs-toggle="modal"></button>
                    </div>
                    <div class="modal-body px-5">
                        <div class="row">
                            <div class="col-6 col-md-4" id="pic_append"></div>
                            <div class="col-6 col-md-8" id="info_append"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">返回</button>
                    </div>
                </div>
            </div>
        </div>
    <!-- 互動視窗 load_excel -->
        <div class="modal fade" id="load_excel" tabindex="-1" aria-labelledby="load_excel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">上傳Excel檔：</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form name="excelInput" action="../_Format/upload_excel.php" method="POST" enctype="multipart/form-data" target="api" onsubmit="return restockExcelForm()">
                        <div class="modal-body px-4">
                            <div class="row">
                                <div class="col-6 col-md-8 py-0">
                                    <label for="excelFile" class="form-label">需求清單 <span>&nbsp<a href="../_Format/receive_example.xlsx" target="_blank">上傳格式範例</a></span> 
                                        <sup class="text-danger"> * 限EXCEL檔案</sup></label>
                                    <div class="input-group">
                                        <input type="file" name="excelFile" id="excelFile" style="font-size: 16px; max-width: 350px;" class="form-control form-control-sm" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                                        <button type="submit" name="excelUpload" id="excelUpload" class="btn btn-outline-secondary" value="stock">上傳</button>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4 py-0">
                                    <p id="warningText" name="warning" >＊請上傳需求單Excel檔</p>
                                    <p id="sn_list" name="warning" >＊請確認Excel中的資料</p>
                                </div>
                            </div>
                                
                            <div class="row" id="excel_iframe">
                                <iframe id="api" name="api" width="100%" height="30" style="display: none;" onclick="restockExcelForm()"></iframe>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="import_excel_btn" class="btn btn-success unblock" data-bs-dismiss="modal">載入</button>
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">返回</button>
                        </div>
                    </form>
                </div>
            </div>
        </div> 
    <!-- 彈出畫面-查詢user模組 -->
        <div class="modal fade" id="searchUser" aria-hidden="true" aria-labelledby="searchUser" tabindex="-1">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">

                    <div class="modal-header bg-warning  border rounded p-3 m-2">
                        <h5 class="modal-title"><i class="fa-solid fa-circle-info"></i>&nbspsearch & append User for&nbsp<span id="modal_title"></span></h5>
                        <button type="button" class="btn-close border rounded mx-1" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body mx-2">
                        <div class="row">
                            <div class="col-12 border rounded p-3 " id="selectScomp_no">
                                <div class="row">
                                    <!-- 第一排的功能 : 顯示已加入名單+input -->
                                    <div class="col-12 px-4 py-0">
                                        <div id="selectScomp_noItem"></div>
                                        <input type="hidden" class="form-control" name="scomp_no[]" id="scomp_no" placeholder="已加入的">
                                    </div>
                                    <!-- 第二排的功能 : 搜尋功能 -->
                                    <div class="col-12 px-4">
                                        <div class="input-group search">
                                            <input type="text" class="form-control mb-0" id="key_word" required placeholder="-- 工號 / 姓名 查詢 --" >
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
<script>
// 開局設定init
    var form_json = <?=json_encode($form_a_json)?>;
    var meeting_man_a = [];                         //
    var meeting_man_o = [];                         //
    var meeting_man_s = [];                         // 
    var meeting_man_target;                         // 指向目標
    var searchUser_modal = new bootstrap.Modal(document.getElementById('searchUser'), { keyboard: false });

    // JSON轉表單
    function make_question(id_key, key_a, item_a) {
        var now = new Date(); // 取得當前時間
        var int_a = '';
        if (item_a.data_type == "text") {
            int_a = '<input type="text" name="' + key_a + '" id="' + key_a + '" class="form-control mb-0"  required>' +
                '<label for="' + key_a + '" class="form-label">' + item_a.title + '：<sup class="text-danger"> *</sup></label>';

            if(key_a == 'emp_id'){
                int_a = '<div class="form-floating input-group">' + int_a +
                '<button type="button" class="btn btn-outline-primary search_btn" id="emp_id_btn" data-toggle="tooltip" data-placement="bottom" title="以工號自動帶出其他資訊" '+
                ' data-bs-target="#searchUser" data-bs-toggle="modal" >'+'<i class="fa-solid fa-magnifying-glass"></i> 搜尋</button>'+'</div>';
            }else{
                int_a = '<div class="form-floating">' + int_a +'</div>';
            }

        } else if (item_a.data_type == "date") {
            var formattedDate = now.toISOString().slice(0, 10);     // 格式化日期，取得日期部分
            int_a = '<div class="form-floating">' +
                // '<input type="date" name="' + key_a + '" class="form-control" id="' + key_a + '" value="' + formattedDate + '" required>' +
                '<input type="date" name="' + key_a + '" class="form-control" id="' + key_a + '" value="" required>' +
                '<label for="' + key_a + '" class="form-label">' + item_a.title + '：<sup class="text-danger"> *</sup></label></div>';

        } else if (item_a.data_type == "datetime") {
            var formattedDateTime = now.toISOString().slice(0, 16); // 格式化日期時間，取得到分鐘的部分
            int_a = '<div class="form-floating">' +
                // '<input type="datetime-local" name="' + key_a + '" class="form-control" id="' + key_a + '" value="' + formattedDateTime + '" required>' +
                '<input type="datetime-local" name="' + key_a + '" class="form-control" id="' + key_a + '" value="" required>' +
                '<label for="' + key_a + '" class="form-label">' + item_a.title + '：<sup class="text-danger"> *</sup></label></div>';

        } else if (item_a.data_type == "textarea") {
            int_a = '<div class="form-floating">' +
                '<textarea name="' + key_a + '" id="' + key_a + '" class="form-control " style="height: 100px" ></textarea>' +
                '<label for="' + key_a + '" class="form-label">' + item_a.title + '：</label></div>';

        } else if (item_a.data_type == "radio") {
            int_a = '<div class=" border rounded p-2"><div class="form-check">' +
                '<label><b>***' + item_a.title + '：</b></label><br>';
            for (const [radioKey, radioValue] of Object.entries(item_a.value)) {
                if (typeof radioValue === 'object') {
                    int_a += '<input type="radio" name="' + key_a + '" value="' + radioKey + '" class="form-check-input">' +
                        '<label class="form-check-label">' + radioValue.title + '</label><br>';
                } else {
                    int_a += '<input type="radio" name="' + key_a + '" value="' + radioKey + '" class="form-check-input">' +
                        '<label class="form-check-label">' + radioValue + '</label><br>';
                }
            }
            int_a += '</div></div>';
        }

        if(id_key == 'session_1'){
            int_a = '<div class="col-6 col-md-4 p-2">'+int_a+'</div>'

        // } else if(id_key == 'session_2'){
        //     int_a = '<div class="col-6 col-md-6 p-2">'+int_a+'</div>'

        } else if(id_key == 'session_4'){
            int_a = '<div class="col-6 col-md-6 p-2">'+int_a+'</div>'
        }


        $('#' + id_key).append(int_a);
    }

    // // // searchUser function 
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
            var search = $('#key_word').val().trim();                   // search keyword取自user欄位
            var request = {
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
                    var res_r = res["result"];
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
            // $("#result").addClass("border rounded bg-white");
            $("#result").addClass("bg-white");
            // 定義表格頭段
            var div_result_table = document.querySelector('.result table');
            var Rinner = "<thead><tr>"+
                            "<th>員工編號</th>"+"<th>員工姓名</th>"+"<th>職稱</th>"+"<th>user_ID</th>"+"<th>部門代號</th>"+"<th>部門名稱</th>"+"<th>select</th>"+
                        "</tr></thead>" + "<tbody id='tbody'>"+"</tbody>";
            // 鋪設表格頭段thead
            div_result_table.innerHTML += Rinner;
            // 定義表格中段tbody
            var div_result_tbody = document.querySelector('.result table tbody');
            $('#tbody').empty();
            for (let i=0; i < res_r.length; i++) {
                // 把user訊息包成json字串以便夾帶
                    // let user_json = JSON.stringify({
                    //         'emp_id'    : res_r[i].emp_id.trim(),
                    //         'cname'     : res_r[i].cname.trim(),
                    //     });
                let user_json = res_r[i].emp_id.trim() +','+ res_r[i].cname.trim() +','+ res_r[i].cstext.trim();
                div_result_tbody.innerHTML += 
                    '<tr>' +
                        '<td>' + res_r[i].emp_id +'</td>' +
                        '<td>' + res_r[i].cname + '</td>' +
                        '<td>' + res_r[i].cstext + '</td>' +
                        '<td>' + res_r[i].user + '</td>' +
                        '<td>' + res_r[i].dept_no + '</td>' +
                        '<td>' + res_r[i].dept_c +'/'+ res_r[i].dept_d + '</td>' +
                        '<td>' + '<button type="button" class="btn btn-default btn-xs" id="'+res_r[i].emp_id+'" value='+user_json+' onclick="tagsInput_me(this.value)">'+
                        '<i class="fa-regular fa-circle"></i></button>' + '</td>' +
                    '</tr>';
            }

        }
        // fun_3.點選、渲染模組
        function tagsInput_me(val) {
            // let emp_id = val.substr(0, val.search(','));                    // 指定emp_id
            // let cname  = val.substr(val.search(',',)+1);                    // 指定cname
            // let cstext = val.substr(val.lastIndexOf(',')+1)                 // 指定cstext
            let emp_id = val.split(',')[0];
            let cname  = val.split(',')[1].trim();
            let cstext = val.split(',')[2].trim();

            if (val !== '') {
                if(meeting_man_target == 'emp_id_btn'){     // 來自事故者基本資訊
                    let personal_inf = {
                        'emp_id' : emp_id, 
                        'cname'  : cname, 
                        'cstext' : cstext
                    }
                    Object.keys(personal_inf).forEach((_key)=>{
                        let _key_elem = document.querySelector('#'+_key)
                        if(_key_elem){
                            _key_elem.value = personal_inf[_key]
                        }
                    })
                    searchUser_modal.hide();      // 關閉searchUser_modal

                }else{                                      // 來自會議title
                    let parts = val.split(',');
                    parts.pop();                 // 移除最后一个元素--職稱
                    val = parts.join(',');

                    window[meeting_man_target].push(val);
                    $('#'+meeting_man_target+'_show').append('<div class="tag">' + cname + '<span class="remove">x</span></div>');
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
    // // // searchUser function 

    $(function () {

        // 監聽myModal被關閉時就執行--清除表格
        var searchUser_elm = document.getElementById('searchUser');
        searchUser_elm.addEventListener('hidden.bs.modal', function () {
            resetMain();                    // do something...清除欄位
            $('#modal_title').empty();      // 清除標題
        })
        // 20230817 禁用Enter鍵表單自動提交 
        document.onkeydown = function(event) { 
            var target, code, tag; 
            if (!event) { 
                event = window.event;       //針對ie瀏覽器 
                target = event.srcElement; 
                code = event.keyCode; 
                if (code == 13) { 
                    tag = target.tagName; 
                    if (tag == "TEXTAREA") { return true; } 
                    else { return false; } 
                } 
            } else { 
                target = event.target;      //針對遵循w3c標準的瀏覽器，如Firefox 
                code = event.keyCode; 
                if (code == 13) { 
                    tag = target.tagName; 
                    if (tag == "INPUT") { return false; } 
                    else { return true; } 
                } 
            } 
        };
        // JSON轉表單
        var form_doc = document.getElementById('item_list');
        for (const [key_1, value_1] of Object.entries(form_json)) {
            let match;
            const regex = new RegExp('session', 'gi');
            if ((match = regex.exec(key_1)) !== null) {
                let int_1 = '<div class="col-12">';
                if (value_1.title.length != 0) {
                    int_1 += '<h5><b>※&nbsp' + key_1 + '&nbsp' + value_1.title + '：</b></h5>';
                }
                if (value_1.info.length != 0) {
                    int_1 += '&nbsp' + value_1.info;
                }
                int_1 += '<div class="row" id="' + key_1 + '"></div></div>'
                $('#item_list').append(int_1);
            }
            for (const [key_2, value_2] of Object.entries(value_1.item)) {
                make_question(key_1, key_2, value_2)
            }
            // 檢查是否有缺少的項目，並添加缺少的項目
            for (const [key_2, value_2] of Object.entries(value_1.item)) {
                if (!$('#' + key_2).length) {
                    make_question(key_1, key_2, value_2);
                }
            }
        }

        // 定義+監聽按鈕
        var search_btns = Array.from(document.querySelectorAll(".search_btn"));
        search_btns.forEach((s_btn)=>{
            s_btn.addEventListener('mousedown',function(){
                // 標籤
                let modal_title
                if(this.id == 'meeting_man_a'){
                    modal_title = '事故當事者(或其委任代理人)'
                }else if(this.id == 'meeting_man_o'){
                    modal_title = '其他與會人員'
                }else if(this.id == 'meeting_man_s'){
                    modal_title = '環安人員'
                }else if(this.id == 'emp_id_btn'){
                    modal_title = '事故者基本資料'
                }
                $('#modal_title').append(modal_title)
                meeting_man_target = this.id;               // 搜尋meeting_man_target
            })
        })

    })


    $(document).ready(function(){
        // 監聽到職日欄位(id=hired)，自動計算年資並output(id=rload)
        $('#hired').change(function() {
            var selectedDate = new Date(document.getElementById("hired").value);    // 取得到職日日期
            var currentDate = new Date();                                           // 取得今天日期
            var difference = currentDate - selectedDate;                            // 計算日期差距（毫秒單位）
            // 轉換毫秒為年月日
                var years  = Math.floor(difference / (365.25 * 24 * 60 * 60 * 1000));
                var months = Math.floor((difference % (365.25 * 24 * 60 * 60 * 1000)) / (30.44 * 24 * 60 * 60 * 1000));
                var days   = Math.floor((difference % (30.44 * 24 * 60 * 60 * 1000)) / (24 * 60 * 60 * 1000));
            // 输出结果
                document.querySelector("#rload").value = "估算約： " + years + " 年 " + months + " 個月 " + days + " 天";
        });
        // 監聽工作起訖日欄位(id=a_work_e)，自動確認是否結束大於開始
        $('#a_work_s, #a_work_e').change(function() {
            console.log(this.id)
            var a_work_s = new Date(document.getElementById("a_work_s").value);    // 取得起始
                // 工作起始需不需要小於現在時間....需要確認
                if(this.id == 'a_work_s'){
                    var currentDate = new Date();                   // 取得今天日期
                    if ($("#"+this.id).hasClass("is-valid"))   { $("#"+this.id).removeClass("is-valid");}
                    if ($("#"+this.id).hasClass("is-invalid")) { $("#"+this.id).removeClass("is-invalid");}
                    if (a_work_s < currentDate) {
                        $("#"+this.id).addClass("is-valid");        // true
                    } else {
                        $("#"+this.id).addClass("is-invalid");      // false
                    }
                }
            var a_work_e = new Date(document.getElementById("a_work_e").value);    // 取得訖止
            if ($("#a_work_e").hasClass("is-valid"))   { $("#a_work_e").removeClass("is-valid");}
            if ($("#a_work_e").hasClass("is-invalid")) { $("#a_work_e").removeClass("is-invalid");}
            if (a_work_s < a_work_e) {
                $("#a_work_e").addClass("is-valid");        // true
            } else {
                $("#a_work_e").addClass("is-invalid");      // false
            }
        });
    })

// 以下為控制 iframe
    var realName         = document.getElementById('realName');           // 上傳後，JSON存放處(給表單儲存使用)
    var iframe           = document.getElementById('api');                // 清冊的iframe介面
    var warningText      = document.getElementById('warningText');        // 清冊未上傳的提示
    var sn_list          = document.getElementById('sn_list');            // 清冊中有誤的提示
    var excel_json       = document.getElementById('excel_json');         // 清冊中有誤的提示
    var excelFile        = document.getElementById('excelFile');          // 上傳檔案名稱
    var excelUpload      = document.getElementById('excelUpload');        // 上傳按鈕
    var import_excel_btn = document.getElementById('import_excel_btn');   // 載入按鈕

</script>

<!-- <script src="receive_form.js?v=<=time()?>"></script> -->

<?php include("../template/footer.php"); ?>