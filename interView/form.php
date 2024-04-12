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
        // $json_file_path = 'form_a.json';
        // $test_doc = '13ES100016-F002-V002';
    $form_doc = (isset($_REQUEST["dcc_no"]) ? "../doc_json/".$_REQUEST["dcc_no"].".json" : "" );
    // $form_doc = (isset($_REQUEST["dcc_no"]) ? "../doc_json/".$_REQUEST["dcc_no"].".json" : "../doc_json/".$test_doc.".json" );
    if(file_exists($form_doc)){
        // 从 JSON 文件加载内容
        $form_json = file_get_contents($form_doc);
        // 解析 JSON 数据并将其存储在 $form_a_json 变量中
        $form_json = json_decode($form_json, true);     // 如果您想将JSON解析为关联数组，请传入 true，否则将解析为对象
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
        /* 使用 CSS 將 canvas 的寬度設置為 100% */
        /* canvas {
            width: 100%;
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
                        <h3><i class="fa-solid fa-list-check"></i>&nbsp<b><snap id="form_title">通用表單Form</snap></b><?php echo empty($action) ? "":" - ".$action;?></h3>
                    </div>
                    <div class="col-12 col-md-6 py-0 text-end">
                        <a href="#" target="_blank" title="Submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveSubmit"> <i class="fa fa-paper-plane" aria-hidden="true"></i> 送出</a>
                        <a href="<?php echo $up_href;?>" class="btn btn-secondary" onclick="return confirm('確認返回？');" ><i class="fa fa-external-link" aria-hidden="true"></i>&nbsp回上頁</a>
                    </div>
                </div>
                <div class="row px-2">
                    <div class="col-12 col-md-6 py-1">
                        訪問單號：<?php echo ($action == 'create') ? "(尚未給號)": "receive_aid_".$receive_row['id']; ?></br>
                        開單日期：<?php echo ($action == 'create') ? date('Y-m-d H:i')."&nbsp(實際以送出時間為主)":$receive_row['created_at']; ?></br>
                        填單人員：<?php echo ($action == 'create') ? $auth_emp_id." / ".$auth_cname : $receive_row["created_emp_id"]." / ".$receive_row["created_cname"] ;?>
                    </div>
                    <div class="col-12 col-md-6 py-1 text-end">
                        <span id="dcc_no_head"><?php echo ($init_error) ? '<snap class="text-danger">*** '.$init_error.' ***</snap>' :'';?></span>
                        <?php if(($sys_role <= 1 ) && (isset($receive_row['idty']) && $receive_row['idty'] != 0)){ ?>
                            <form action="" method="post">
                                <input type="hidden" name="uuid" value="<?php echo $receive_row["uuid"];?>">
                                <input type="submit" name="delete_receive" value="刪除 (Delete)" title="刪除申請單" class="btn btn-danger" onclick="return confirm('確認徹底刪除此單？')">
                            </form>
                        <?php }?>
                    </div>
                </div>
    
                <!-- container -->
                <div class="col-12">
                    <!-- 內頁 -->
                    <form action="./zz/debug.php" method="post" onsubmit="this.cname.disabled=false" >
                        <div class="row rounded bg-light py-3" id="form_container">
                            <div class="col-12 p-3 ">
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
                                    <div class="modal-body px-5">
                                        <label for="sign_comm" class="form-check-label" >command：</label>
                                        <textarea name="sign_comm" id="sign_comm" class="form-control" rows="5"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="created_emp_id"  id="created_emp_id" value="<?php echo $auth_emp_id;?>">
                                        <input type="hidden" name="created_cname"   id="created_cname"  value="<?php echo $auth_cname;?>">
                                        <input type="hidden" name="updated_user"    id="updated_user"   value="<?php echo $auth_cname;?>">
                                        <input type="hidden" name="uuid"            id="uuid"           value="">
                                        <input type="text" name="dcc_no"          id="dcc_no"         value="">
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
<script src="../../libs/signature_pad/signature_pad.umd.min.js"></script>

<script>
    // 開局設定init
    var form_json = <?=json_encode($form_json)?>;   // 取得表單
    var form_item = form_json.form_item;            // 抓item項目for form item
    var meeting_man_a = [];                         // 事故當事者(或其委任代理人)
    var meeting_man_o = [];                         // 其他與會人員
    var meeting_man_s = [];                         // 環安人員
    var meeting_man_target;                         // 指向目標
    var searchUser_modal = new bootstrap.Modal(document.getElementById('searchUser'), { keyboard: false });
    // 以下為控制 iframe
        // var realName         = document.getElementById('realName');           // 上傳後，JSON存放處(給表單儲存使用)
        // var iframe           = document.getElementById('api');                // 清冊的iframe介面
        // var warningText      = document.getElementById('warningText');        // 清冊未上傳的提示
        // var sn_list          = document.getElementById('sn_list');            // 清冊中有誤的提示
        // var excel_json       = document.getElementById('excel_json');         // 清冊中有誤的提示
        // var excelFile        = document.getElementById('excelFile');          // 上傳檔案名稱
        // var excelUpload      = document.getElementById('excelUpload');        // 上傳按鈕
        // var import_excel_btn = document.getElementById('import_excel_btn');   // 載入按鈕

    // 動態表單主fun -- JSON轉表單；依據不同的key_type進行切換型別 HARD CODED
    function make_question(session_key, key_class, item_a) {        // 接收參數：session, class, 單一問項
        var int_a = '';
        var dcff = '<div class="form-floating">';
        // 共用部分的操作1 label標籤
        function commonPart() {
            var labelSuffix = item_a.required ? '<sup class="text-danger"> *</sup>' : '';
            return '<label for="' + item_a.name + '" >' + item_a.label + '：' + labelSuffix +'</label>';
        }
        // 共用部分的操作2 驗證回饋
        function validPart() {
            return '<div class="invalid-feedback" id="' + item_a.name + '_feedback">數值填入錯誤 ~ </div>';
        }
        // 共用部分的操作3 info   有單文字和物件
        function infoPart() {
            let info_temp = '';
            if(typeof item_a.info !== 'object'){
                info_temp += ' >>> ' + item_a.info;
            }else{
                for (const [key_1, value_1] of Object.entries(item_a.info)) {
                    if(info_temp){
                        info_temp += '<br/>'
                    }
                    info_temp += key_1 + '.' + value_1
                }
            }
            return '<span class="info">' + info_temp + '</span>';
        }
        // 日期格式化函數
        function formatDate(date) {
            return date.toISOString().slice(0, item_a.type === 'date' ? 10 : 16);
        }
        // 主要fun：內層問項生成：根據字段類型生成相應的表單元素
        switch(item_a.type) {
            case 'text':
                int_a = '<input type="text" name="' + item_a.name + '" id="' + item_a.name + '" class="form-control mb-0" placeholder="' + item_a.label + '" '+ (item_a.required ? 'required' : '') + '>' + commonPart();
                if(item_a.name == 'emp_id'){
                    int_a = '<div class="input-group form-floating">' + int_a 
                        + '<button type="button" class="btn btn-outline-primary search_btn" id="emp_id_btn" data-toggle="tooltip" data-placement="bottom" title="以工號自動帶出其他資訊" '
                        + ' data-bs-target="#searchUser" data-bs-toggle="modal" >'+'<i class="fa-solid fa-magnifying-glass"></i> 搜尋</button>'+'</div>';
                }else{
                    int_a = dcff + int_a + '</div>';
                }
                break;
            case 'date':
            case 'datetime':
                int_a = dcff +
                        // '<input type="' + (item_a.type === 'date' ? 'date' : 'datetime-local') + '" name="' + item_a.name + '" class="form-control" id="' + item_a.name + '" value="' + formatDate(new Date()) + '" ' +
                        '<input type="' + (item_a.type === 'date' ? 'date' : 'datetime-local') + '" name="' + item_a.name + '" class="form-control" id="' + item_a.name + '" value="" ' +
                        (item_a.required ? 'required' : '') + '>' + commonPart() + (item_a.valid ? validPart() : '') + '</div>';
                break;
            case 'textarea':
                int_a = dcff +
                    '<textarea name="' + item_a.name + '" id="' + item_a.name + '" class="form-control " style="height: 100px" placeholder="' + item_a.label + '"' 
                    + (item_a.required ? 'required' : '') + '>' + '</textarea>' + commonPart() + '</div>';

                int_a = '<div class="p-2">' + int_a + '</div>';
                break;

            case 'radio':
            case 'checkbox':
                int_a = '<div class=" border rounded p-2"><snap><b>*** ' + item_a.label + '：' + (item_a.required ? '<sup class="text-danger"> *</sup>' : '') + '</b></snap><br>';
                Object(item_a.options).forEach((option)=>{
                    let object_type = ((typeof option.value !== 'object') ? option.value : option.label);   // for other's value
                    int_a += '<div class="form-check bg-light rounded"><input type="' + item_a.type + '" name="' + item_a.name + (item_a.type == 'checkbox' ? '[]':'') + '" value="' + object_type + '" '
                          + ' id="' + item_a.name + '_' + object_type + '" ' + (item_a.required ? ' required ' : '') + 'onchange="onchange_option(this.name)" ' 
                          + ' class="form-check-input ' + ((typeof option.value === 'object') ? ' other_item ' : '') + (option.value.only ? ' only_option ' : '') + '" >'
                          + '<label class="form-check-label" for="' + item_a.name + '_' + object_type + '">' + object_type + (typeof option.value === 'object' ? '：' : '') +'</label></div>';

                    if (typeof option.value === 'object' && option.value.type == 'text') {
                        int_a += '<input type="'+ option.value.type +'" name="' + option.value.name + (item_a.type == 'checkbox' ? '[]':'') + '" '
                            + ' placeholder="' + option.value.label + '" id="' + item_a.name + '_' + option.label + '_o" class="form-control unblock">';
                    }
                }) 
                int_a += '</div>';
                break;

            case 'file':       // session_2 事故位置簡圖
                int_a = '<div class="col-6 col-md-6 py-0 px-2"><div class="col-12 bg-white border rounded ">' 
                    + commonPart()
                    + '<div class="input-group "><input type="file" name="' + item_a.name + '" id="' + item_a.name + '" class="form-control mb-0" accept=".jpg,.png,.gif,.bmp" ' + (item_a.required ? 'required' : '' ) + '>'
                    + '<button type="button" class="btn btn-outline-success" onclick="uploadFile(\'' + item_a.name + '\')">Upload</button>' 
                    + '<button type="button" class="btn btn-outline-danger" onclick="unlinkFile(\'' + item_a.name + '\')">Delete</button>' 
                    + '</div>'
                    + '<input type="hidden" name="' + item_a.name + '_md5" id="' + item_a.name + '_md5" ' + (item_a.required ? 'required' : '' ) + '>'
                    +'</div></div>'
                    + '<div class="col-6 col-md-6 p-0 a_pic" id="preview_' + item_a.name + '" > -- preView -- </div>';
                break;
                
            case 'signature':   // 簽名模組
                int_a = '<div class="col-12 border rounded">'
                    +'<snap class="p-0" ><b>*** ' + item_a.label + '：' + (item_a.required ? '<sup class="text-danger"> *</sup>' : '') + '</b></snap>'
                    + '<div class="row">'
                        + '<div class="col-12 col-md-6 text-center">'
                        + '<canvas id="' + item_a.name + '_signaturePad" width="350" height="250" class=" border rounded p-2 bg-light"></canvas>'
                        + '<div class="py-1">'
                        + '<button type="button" class="btn btn-outline-info clear-btn" data-pad="' + item_a.name + '">Clear</button>'+'&nbsp'
                        + '<button type="button" class="btn btn-outline-success save-btn" data-pad="' + item_a.name + '">Save Signature</button>'
                        + '</div>' + '</div>'
                        + '<div class="col-12 col-md-6 text-center"><img id="' + item_a.name + '_signature-image" src="../image/signin_empty.png" alt="Signature Image" class="img-thumbnail" >'
                        + '<br><input type="hidden" name="' + item_a.name + '" id="' + item_a.name + '_signature-input" ' + (item_a.required ? 'required' : '' ) + '>'
                        +'</div>' + '</div>' + '</div>'
                break;
        }
        // 有info就呼叫fun崁入
        int_a += (item_a.info) ? infoPart() : '';
        // 外層session包裝 // 將表單元素添加到特定的容器中
        if(key_class && item_a.type != 'signature'){
            int_a = '<div class="'+ key_class +'">' + int_a + '</div>';
        }else if(item_a.type == 'signature'){
            int_a = '<div class="col-12 p-2">' + int_a + '</div>';
        }
        // 渲染form
        $('#' + session_key +' .accordion-body').append(int_a);      
    }

    // Option選項遮蔽：On、Off
    function onchange_option(name){
        var opts = document.querySelectorAll('[name="'+name+'"].other_item')
        opts.forEach((opt)=>{
            let opt_id_o = document.querySelector('#'+opt.id+'_o');
            if(opt.checked){
                opt_id_o.classList.remove('unblock');
                opt_id_o.focus();

                if ($("#"+opt.id).hasClass("only_option")){
                    let only_opts = document.querySelectorAll('[name="'+opt.name+'"]')
                    only_opts.forEach(function(checkbox) {
                        // Check if the current checkbox is not the fourth one and is checked
                        if ((checkbox.id !== opt.id ) && checkbox.checked) {
                            // If so, uncheck it
                            checkbox.checked = false;
                            $("#"+checkbox.id+'_o').addClass('unblock');
                        }
                    });
                }

            }else{
                // opt_id_o.value = "";
                opt_id_o.classList.add('unblock');
            }
        })
    }

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
                        '<td>' + res_r[i].emp_id.trim() +'</td>' +
                        '<td>' + res_r[i].cname.trim() + '</td>' +
                        '<td>' + res_r[i].cstext.trim() + '</td>' +
                        '<td>' + res_r[i].user.trim() + '</td>' +
                        '<td>' + res_r[i].dept_no.trim() + '</td>' +
                        '<td>' + res_r[i].dept_c.trim() +'/'+ res_r[i].dept_d.trim() + '</td>' +
                        '<td>' + '<button type="button" class="btn btn-default btn-xs" id="'+res_r[i].emp_id+'" value='+user_json+' onclick="tagsInput_me(this.value)">'+
                        '<i class="fa-regular fa-circle"></i></button>' + '</td>' +
                    '</tr>';
            }

        }
        // fun_3.點選、渲染模組
        function tagsInput_me(val) {
            let emp_id = val.split(',')[0];  // 指定emp_id
            let cname  = val.split(',')[1];  // 指定cname
            let cstext = val.split(',')[2];  // 指定cstext

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
    // // searchUser function 

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
        
        // 動態表單主fun -- JSON轉表單
        // step_0.前置工作、生成表頭
        if(form_json.form_title){ $('#form_title').empty().append(form_json.form_title);  }     // 文件標題
        if(form_json.dcc_no){     $('#dcc_no_head').empty().append(form_json.dcc_no); }         // DCC編號
        if(form_json.version){    $('#dcc_no_head').append('-' + form_json.version); }          // 文件版本
        let dcc_no_input = document.querySelector('#dcc_no');                                   // 
        if(dcc_no_input && form_json.dcc_no && form_json.version){ 
            dcc_no_input.value = form_json.dcc_no+'-'+form_json.version;
        }
        var form_doc = document.getElementById('item_list');                                    // 定義動態表單id位置
        if(form_item){                                                                          // confirm form_item is't empty
            for (const [key_1, value_1] of Object.entries(form_item)) {
                // step_1.生成session_title
                let match;
                const regex = new RegExp('session', 'gi');
                if ((match = regex.exec(key_1)) !== null) {
                    let int_1 = '<div class="accordion-item">';                 // 使用手風琴模組
                    if (value_1.label.length != 0) {
                        int_1 += '<h5 class="accordion-header" id="' + key_1 + '_head">'+
                            '<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#' + key_1 + '" aria-expanded="true" aria-controls="' + key_1 + '">'+
                            '<b>※&nbsp' + key_1 + '&nbsp' + value_1.label + '：</b>'+ '</button></h5>';
                    }
                    int_1 += '<div id="' + key_1 + '"  class="accordion-collapse collapse show" aria-labelledby="' + key_1 + '_head" > '
                        + (value_1.info ? '&nbsp' + value_1.info : '') 
                        +'<div class="row accordion-body">'
                        +'</div></div></div>'
    
                    $('#item_list').append(int_1);
                }
                // step_2.生成問項...將每一筆繞出來
                Object(value_1.item).forEach((item_value)=>{
                    make_question(key_1, value_1.class, item_value);
                })
            }

            let int_end = '<div class="col-12 mt-3 py-0 rounded bg-success text-white text-center">-- 問卷底部 --</div>'
            $('#item_list').append(int_end);
        }

    })

    window.onload = function() {
        // 簽名板
        var signaturePads = {};
        // Initialize Signature Pad for each canvas
        var canvases = document.querySelectorAll('canvas');
        canvases.forEach((canvas, index)=>{
            var signaturePad = new SignaturePad(canvas);
            signaturePads[canvas.id] = signaturePad;
        })

        // Attach event listeners to clear and save buttons
        var clearButtons = document.querySelectorAll('.clear-btn');
        clearButtons.forEach(function(button) {
            button.addEventListener('click', function(event) {
                var padNumber = button.dataset.pad;
                var signaturePad = signaturePads[padNumber + '_signaturePad'];
                var signatureImage = document.getElementById(padNumber + '_signature-image');
                $('#' + padNumber + '_signature-input').val('');            // base64儲存格
                signaturePad.clear();                                       // 手寫盤
                signatureImage.src = '../image/signin_empty.png';           // 預覽圖
            });
        });

        var saveButtons = document.querySelectorAll('.save-btn');
        saveButtons.forEach(function(button) {
            button.addEventListener('click', function(event) {
                var padNumber = button.dataset.pad;
                var signaturePad = signaturePads[padNumber + '_signaturePad'];
                var signatureImage = document.getElementById(padNumber + '_signature-image');
                if (signaturePad.isEmpty()) {
                    alert("Please provide a signature first.");
                } else {
                    var dataURL = signaturePad.toDataURL();
                    signatureImage.src = dataURL;                           // 預覽圖
                    $('#' + padNumber + '_signature-input').val(dataURL);   // base64儲存格
                }
            });
        });
    };
    
    // DOMContentLoaded 事件监听器
    // document.addEventListener('DOMContentLoaded', function () {
    //     // 绑定事件监听器
    //     attachEventListeners();
    // });

    $(document).ready(function(){
        // 定義+監聽按鈕for與會人員...search btn id
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

</script>

<script src="form.js?v=<?=time()?>"></script>

<?php include("../template/footer.php"); ?>