<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("../user_info.php");
    require_once("../caseList/function.php");

    // default fab_scope
    $fab_scope = ($sys_role <=1 ) ? "All" : "allMy";    // All :allMy
    // tidy query condition：
        $_site_id    = (isset($_REQUEST["_site_id"]))    ? $_REQUEST["_site_id"]    : "";   // 問卷site
        $_fab_id     = (isset($_REQUEST["_fab_id"]))     ? $_REQUEST["_fab_id"]     : "";   // 問卷fab
        $_short_name = (isset($_REQUEST["_short_name"])) ? $_REQUEST["_short_name"] : "";   // 問卷類別

    // for select item
        $site_lists      = show_site_lists();           // get site清單
        $fab_lists       = show_fab_lists();            // get fab清單
        $shortName_lists = show_document_shortName();   // get 簡稱清單
?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>
<head>
    <link href="../../libs/aos/aos.css" rel="stylesheet">                                           <!-- goTop滾動畫面aos.css 1/4-->
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>            <!-- Jquery -->
        <!-- dataTable參照 https://ithelp.ithome.com.tw/articles/10230169 --> <!-- data table CSS+JS -->
        <!-- <link rel="stylesheet" type="text/css" href="../../libs/dataTables/jquery.dataTables.css">   -->
        <!-- <script type="text/javascript" charset="utf8" src="../../libs/dataTables/jquery.dataTables.js"></script> -->
    <script src="../../libs/sweetalert/sweetalert.min.js"></script>                                 <!-- 引入 SweetAlert 的 JS 套件 參考資料 https://w3c.hexschool.com/blog/13ef5369 -->
    <script src="../../libs/jquery/jquery.mloading.js"></script>                                    <!-- mloading JS 1/3 -->
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">                            <!-- mloading CSS 2/3 -->
    <script src="../../libs/jquery/mloading_init.js"></script>                                      <!-- mLoading_init.js 3/3 -->
    <style>
        body {
            position: relative;
        }
        .a_pic {
            width: 150px; 
            height: auto; 
            text-align: center;
        }
        .info {
            font-size: 14px;
            color: blue;
            text-shadow: 3px 3px 5px rgba(0,0,0,.3);
        }

        @keyframes fadeIn {
            from { opacity: 0;}
            to { opacity: 1;}
        }
        .unblock {
            opacity: 0;
            display: none;
            transition: opacity 1s;
            animation: none;
        }
        #session-group {
            position: sticky;
            top: 0;
            z-index: 1;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* 增加陰影以更明顯地看到效果 */
        }
        .list-group-item{
            padding: .5rem 1rem;
            /* word-wrap: break-word; */
            white-space: nowrap;            /* 防止自動換行 */
            overflow: hidden;               /* 確保溢出的文字被截斷並顯示省 */
            text-overflow: ellipsis;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            #form_top, #form_top * {
                visibility: visible;
            }
            #form_top {
                position: absolute;
                left : 0;
                top  : 0;
                width: 100%;
            }
            /* 設置紙張大小為 A4 */
            @page {
                size  : A3;
                margin: 10mm; /* 設置頁面邊距 */
            }
        }

        table tbody tr td{
            /* text-align: right; */
            /* padding: 1em; */
        }
        /* inline */
        .inb {
            display: inline-block;
            /* margin-right: 10px; */
        }
        .inf {
            display: inline-flex;
            align-items: center;
            width: 100%; /* 让父容器占满整个单元格 */
        }
    </style>
</head>
<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col_xl_8 col-8 rounded pb-3" style="background-color: rgba(255, 255, 255, .8);">
                <!-- NAV分頁標籤與統計 -->
                <div class="col-12 pb-0 px-0">
                    <ul class="nav nav-tabs">
                        <li class="nav-item"><a class="nav-link "         href="count.php">案件統計</span></a></li>
                        <li class="nav-item"><a class="nav-link "         href="index.php">訪談內容統計</span></a></li>
                        <li class="nav-item"><a class="nav-link active"   href="test.php">test案件統計</span></a></li>
                    </ul>
                </div>
                
                <!-- 內頁 -->
                <div class="col-12 bg-white rounded" id="main">
                    <form id="myForm" method="post" action="">
                        <table id="query_item">
                            <tbody>
                                <tr>
                                    <td class="text-end">廠區/棟別：</td>
                                    <td class="inf">
                                        <select name="_site_id" id="_site_id" class="form-select inb block" >
                                            <option value="" hidden selected >-- 請選擇 問卷site --</option>
                                            <?php 
                                                // echo '<option for="_site_id" value="All" '.($_fab_id == "All" ? " selected":"").' >-- All 所有site --</option>';
                                                foreach($site_lists as $site){
                                                    echo "<option for='_site_id' value='{$site["id"]}' ". ($site["id"] == $_site_id ? " selected" : "" );
                                                    echo ($site["flag"] == "Off" ? " disabl" : "" ) ;
                                                    echo " >";
                                                    echo $site["site_title"]."( ".$site["site_remark"]." )"; 
                                                    echo ($site["flag"] == "Off") ? " - (已關閉)":"" ."</option>";
                                                } ?>
                                        </select>
                                        &nbsp
                                        <select name="_fab_id" id="_fab_id" class="form-select inb" >
                                            <option value="" hidden selected >-- 請選擇 問卷Fab --</option>
                                            <?php 
                                                echo '<option for="_fab_id" value="All" '.($_fab_id == "All" ? " selected":"").' >-- All 所有棟別 --</option>';
                                                foreach($fab_lists as $fab){
                                                    echo "<option for='_fab_id' value='{$fab["id"]}' ";
                                                    echo ($fab["id"] == $_fab_id) ? "selected" : "" ." >";
                                                    echo $fab["site_title"]."&nbsp".$fab["fab_title"]."( ".$fab["fab_remark"]." )"; 
                                                    echo ($fab["flag"] == "Off") ? " - (已關閉)":"" ."</option>";
                                                } ?>
                                        </select>
    
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end">anis_no / 申請單號：</td>
                                    <td class="inf">
                                        <input type="text" name="anis_no" id="anis_no" class="form-control inb" placeholder="-- ANIS表單編號 --"
                                                maxlength="21" oninput="if(value.length>21)value=value.slice(0,21); this.value = this.value.toUpperCase();" >
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end">created_emp_id / 申請人員：</td>
                                    <td class="inf">
                                        <input type="text" name="created_emp_id" id="created_emp_id" class="form-control" placeholder="-- 申請人員工號 --"
                                                maxlength="8" oninput="if(value.length>8)value=value.slice(0,8)" >
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end">_short_name / 事件類別：</td>
                                    <td class="inf">
                                        <select name="_short_name" id="_short_name" class="form-select" >
                                            <option value="" hidden selected >-- 請選擇 問卷類型 --</option>
                                            <?php 
                                                // echo "<option for='_short_name' value='All' ".(($_short_name == "All") ? " selected":"" )." >-- 全問卷類型 / All --</option>";
                                                foreach($shortName_lists as $shortName){
                                                    echo "<option for='_short_name' value='{$shortName["short_name"]}' ";
                                                    echo ($shortName["short_name"] == $_short_name ? " selected" : "" )." >".$shortName["short_name"]."</option>";
                                                } ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end">idty / 結案狀態：</td>
                                    <td class="inf px-3">
                                        <div class="form-check px-3">
                                            <input type="checkbox" name="idty[]" id="idty_1" value="1" class="form-check-input" checked>
                                            <label for="idty_1" class="form-check-label">立案/簽核中</label>
                                        </div>&nbsp
                                        <div class="form-check px-3">
                                            <input type="checkbox" name="idty[]" id="idty_10" value="10" class="form-check-input" checked>
                                            <label for="idty_10" class="form-check-label">結案</label>
                                        </div>&nbsp
                                        <div class="form-check px-3">
                                            <input type="checkbox" name="idty[]" id="idty_6" value="6" class="form-check-input">
                                            <label for="idty_6" class="form-check-label">暫存</label>
                                        </div>&nbsp
                                        <div class="form-check px-3">
                                            <input type="checkbox" name="idty[]" id="idty_3" value="3" class="form-check-input">
                                            <label for="idty_3" class="form-check-label">取消</label>
                                        </div>&nbsp
                                        <!-- <div class="form-check px-3">
                                            <input type="checkbox" name="idty[]" id="idty_All" value="All" class="form-check-input"  >
                                            <label for="idty_All" class="form-check-label">All</label>
                                        </div> -->
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end">s2_combo_07 / 事故分類：</td>
                                    <td class="inf">
                                        <select name="s2_combo_07" id="s2_combo_07" class="form-select" disabl>
                                            <option value="" hidden selected >-- 請選擇 事故類型 --</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end">s2_combo_08 / 災害類型：</td>
                                    <td class="inf">
                                        <select name="s2_combo_08" id="s2_combo_08" class="form-select" disabl>
                                            <option value="" hidden selected >-- 請選擇 災害類型 --</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end">created_at / 申請日期：</td>
                                    <td class="inf">
                                        <div class="input-group">
                                            <span class="input-group-text">From</span>
                                            <input type="date" name="created_at_form" id="created_at_form" class="form-control mb-0" >
                                            <div class="invalid-feedback" id="created_at_form_feedback">日期填入錯誤 ~ </div>
                                        </div>
                                        &nbsp
                                        <div class="input-group">
                                            <span class="input-group-text">To</span>
                                            <input type="date" name="created_at_to"   id="created_at_to"   class="form-control mb-0" >
                                            <div class="invalid-feedback" id="created_at_to_feedback">日期填入錯誤 ~ </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- Hear：H -->
                        <div class="col-12 text-center">
                            <button type="reset" class="btn btn-outline-success">清除</button>
                            <button type="button" class="btn btn-outline-secondary search_btn" value="count" id="search_btn" data-bs-target="#searchUser" data-bs-toggle="modal" >&nbsp<i class="fa-solid fa-magnifying-glass"></i>&nbsp查詢</button>
                        </div>
                    </form>
                    <hr>
                    <table id="caseList" class="table table-striped table-hover">
                        <thead>
                            <tr>

                            </tr>
                        </thead>
                        <tbody>
        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 彈出modal -->
    <div class="modal fade" id="searchUser" aria-hidden="true" aria-labelledby="searchUser" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-fullscreen">
            <div class="modal-content">

                <div class="modal-header bg-warning rounded p-3 m-2">
                    <h5 class="modal-title"><i class="fa-solid fa-circle-info"></i>&nbspsearch document for&nbsp<span id="modal_title"></span></h5>
                    <button type="button" class="btn-close border rounded mx-1" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body mx-2">
                        <div class="col-12 border rounded  result" id="result">
                            <!-- 放查詢結果-->
                                <table id="result_table" class="table table-striped table-hover mb-1">
                                    <thead>
                                        <tr></tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                        </div>
                </div>

                <div class="modal-footer">
                    <div class="inb">
                        <!-- H：downLoad Excel -->
                        <form id="myForm" method="post" action="../_Format/download_excel.php">
                            <input type="hidden" name="htmlTable" id="htmlTable" value="">
                            <button type="submit" name="submit" class="btn btn-outline-success" title="abc" value="interView" >
                                <i class="fa fa-download" aria-hidden="true"></i> 匯出</button>
                        </form>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">返回</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Alarm -->
    <div id="liveAlertPlaceholder" class="col-12 text-center mb-0 pb-0"></div>
    <div id="gotop">
        <i class="fas fa-angle-up fa-2x"></i>
    </div>
</body>
<script src="../../libs/aos/aos.js"></script>               <!-- goTop滾動畫面jquery.min.js+aos.js 3/4-->
<script src="../../libs/aos/aos_init.js"></script>          <!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/signature_pad/signature_pad.umd.min.js"></script>     <!-- 簽名板外掛 -->
<script src="../../libs/openUrl/openUrl.js"></script>       <!-- 彈出子畫面 -->
<!-- <script src="../../libs/moment/moment.min.js"></script> -->

<script>
    // init
    var doc_keys = [];
    var big_data = [];
    var doc_list_keys = {
        'anis_no'         : 'ANIS單號', 
        'idty'            : '簽核狀態', 
        'created_cname'   : '申請人員', 
        'created_at'      : '申請日期', 
        'short_name'      : '表單名稱', 
        'site_title'      : '廠區', 
        'fab_title'       : '棟別', 
        'local_title'     : '廠別'
        // '_content'        : '訪談內容'
    };
    var content_keys = {
        // 'a_day'           : '發生日期',
        'a_day'           : '發生時間',
        'a_location'      : '發生地點',
        // 'a_description'   : '事件詳述',
        's2_combo_06'     : '事件等級',
        's2_combo_07'     : '事件主類型',
        's2_combo_08'     : '災害主類型',
        's8_direct_cause' : '直接原因',
        's8_combo_02'     : '間接原因',
        // 's8_combo_02'     : '間接原因分類',
        // 's8_combo_02'     : '間接大項分類',
        // 's8_combo_02'     : '間接項目'
        's8_basic_reasons_combo':'事故基本原因'
    };

    $(function () {
        // 在任何地方啟用工具提示框
        $('[data-toggle="tooltip"]').tooltip();
    })
    // 吐司顯示字條 // init toast
    function inside_toast(sinn){
        let toastLiveExample = document.getElementById('liveToast');
        let toast = new bootstrap.Toast(toastLiveExample);
        let toast_body = document.getElementById('toast-body');
        toast_body.innerHTML = sinn;
        toast.show();
    }

    // 20231128_下載Excel
    // function submitDownloadExcel() {
        // 這裡的功能已經在post_result中的excel預備工作1~4完成，所以取消此FUN
    // }

    // 240702 監聽送出按鈕
    async function eventListener(){
        return new Promise((resolve) => { 
            document.getElementById('search_btn').addEventListener('click', function() {

                // step0.初始定義
                    mloading(); 
                    const btn_value = this.value;
                    const queryItem = document.getElementById('query_item');
                    const elements = queryItem.querySelectorAll('select, input');
                    const queryItem_obj = {};
                
                // step1.取出查詢條件並打包queryItem_obj：
                    elements.forEach(({ name, value, type, checked }) => {
                        if (name) {
                            if (type === 'radio') {                     // 個案處理：radio要找出被checked的項目才能帶入
                                if (checked) {              
                                    queryItem_obj[name] = value;
                                }
                            } else if (type === 'checkbox') {           // 個案處理：checkbox屬於陣列
                                if(queryItem_obj[name] === undefined){  // 初始建立[]
                                    queryItem_obj[name] = [];
                                }
                                if (checked) {                          // 找出被checked的項目才能帶入
                                    queryItem_obj[name].push(value);
                                }
                            } else {
                                queryItem_obj[name] = value ? value : null;
                            }
                        }
                    });
                
                // step2.清空result_table裡的內容
                    $('#result_table thead tr').empty();
                    $('#result_table tbody').empty();
                    $('#htmlTable').value = '';                         // excel預備工作 0.清空接收欄位

                // step3.呼叫load_fun，帶入查詢條件queryItem_obj，完成後callBack post_result進行渲染+鋪設
                    load_fun('page3', queryItem_obj, post_result);
            });

            // 監聽工作起訖日欄位(id=a_work_e)，自動確認是否結束大於開始
            $('#created_at_form, #created_at_to').change(function() {
                let currentDate = new Date().toISOString().split('T')[0];   // 取得今天的日期部分
                let created_at_form = $("#created_at_form").val();          // 取得起始
                let created_at_to   = $("#created_at_to").val();            // 取得訖止

                let pet_created_at_form = created_at_form ? new Date(created_at_form).toISOString().split('T')[0] : '2000-01-01';
                let pet_created_at_to   = created_at_to   ? new Date(created_at_to).toISOString().split('T')[0]   : currentDate;

                // 工作起始需不需要小於現在時間....需要確認
                if(this.id == 'created_at_form'){
                    let confirm_pet_from = pet_created_at_form <= currentDate ;
                    $("#created_at_form").removeClass("is-valid is-invalid").addClass(confirm_pet_from ? "is-valid" : "is-invalid");
                }
                // 訖止時間需大於起始時間....
                let confirm_pet_to = (pet_created_at_to <= currentDate && pet_created_at_to >= pet_created_at_form) ;
                $("#created_at_to").removeClass("is-valid is-invalid").addClass( confirm_pet_to ? "is-valid" : "is-invalid");
            });
            resolve(); // 文件載入成功，resolve
        });
    }
    // 240702 correspond對應選項功能
    async function eventListener_correspond(target_class){
        return new Promise((resolve) => { 
            const corresponds = document.querySelectorAll(".correspond");
            const targetClass = document.querySelector('#'+target_class);
                corresponds.forEach((element)=> {
                    element.hidden = true;
                });
                
            targetClass.addEventListener('change', function() {
                corresponds.forEach((correspondElement) => {
                    correspondElement.hidden = true;
                });
                const selectedOption = this.options[this.selectedIndex];    // 取得所選中的<option>元素
                const this_flag = selectedOption.getAttribute('flag');      // 用.getAttribute('flag')取得自訂flag屬性
                document.querySelectorAll("." + this_flag).forEach((selectedElement) => {
                    selectedElement.hidden = false; 
                    let parentId = selectedElement.parentElement.id;        // 查詢 selectedElement 上一層的 ID
                    let parentElement = document.getElementById(parentId);
                    if (parentElement) {
                        parentElement.value = "";                           // 将 value 设为默认选项的 value
                    }
                });
            });
            resolve(); // 文件載入成功，resolve
        });
    }
    // 240702 監聽事件類別(表單類別)-對應選項功能
    async function eventListener_shortName(target_class){
        return new Promise((resolve) => { 
            const targetClass = document.querySelector('#'+target_class);
            const s2_combo_07 = document.getElementById('s2_combo_07');     // 事故分類
            const s2_combo_08 = document.getElementById('s2_combo_08');     // 災害類型
                
            targetClass.addEventListener('change', function() {
                if(this.value.includes('廠外交通')){   
                    s2_combo_07.value = "";
                    s2_combo_08.value = "";
                    s2_combo_07.setAttribute("disabled", "disabled");
                    s2_combo_08.setAttribute("disabled", "disabled");
                }else{
                    s2_combo_07.removeAttribute("disabled");
                    s2_combo_08.removeAttribute("disabled");
                }
            });
            resolve(); // 文件載入成功，resolve
        });
    }
    
    // 20240501 -- // 動態表單主fun -- JSON轉表單
    function bring_form(form_json){
        let combo_item = form_json.item;                                // 抓item項目for form item
        if(combo_item){                                                 // confirm form_item is't empty
            // step_2.生成問項...將每一筆繞出來
            Object(combo_item).forEach((item_value)=>{
                let int_a = '';
                Object(item_value.options).forEach((option)=>{
                    if (typeof option.value === 'object') {
                        Object(option.value).forEach((key_value)=>{
                            int_a += '<option value="'+key_value['value']+'" class="' + option.label + (item_value.correspond != undefined ? ' correspond' : '') + '" ' 
                                + ((option.flag !== undefined) ? 'flag="' + option.flag + '"' : '')
                                + ' >'
                                + option.label + ' ' + key_value['label'] + '</option>' 
                        } )
                        
                    }else {
                        int_a += '<option value="'+option.value+'" class="' + item_value.name + '" '
                        + ((option.flag !== undefined) ? 'flag="' + option.flag + '"' : '') + ' id="' + item_value.name + '_' + option.value + '" '
                        // + ' onchange="console.log(this.value)" ' 
                        + ' >' + option.value + ' ' + option.label+'</option>' 
                    }
                }) 

                $("#"+item_value.name).append(int_a);
                if(item_value.correspond !== undefined){                // 240613 判斷是否需要啟動對應選項 for 災害類型
                    eventListener_correspond(item_value.correspond);
                }
            })
            return true;
        } else {
            return false;
        }
    }
    // 主功能1.抓資料
    async function load_fun(fun, parm, myCallback) {                // parm = 參數
        return new Promise((resolve, reject) => {
            let formData = new FormData();
            let fun_temp = (parm['_get_dccNo'] !== undefined && parm['_get_dccNo'] === true ) ? 'caseList' : fun;
            formData.append('fun', fun_temp);
            // 主要for doc多參數
                if (typeof parm === 'object') {
                    for (const [_key, _value] of Object.entries(parm)){
                        formData.append(_key, _value);              // 後端依照fun進行parm參數的採用
                    } 
                }else {
                    formData.append('parm', parm);                  // 後端依照fun進行parm參數的採用
                }
            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'load_fun.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);    // 接收回傳
                    let result_obj = response['result_obj'];        // 擷取主要物件
                    resolve(myCallback(result_obj));                // resolve(true) = 表單載入成功，then 呼叫--myCallback
                    // resolve(myCallback(response));                // resolve(true) = 表單載入成功，then 呼叫--myCallback
                } else {
                    alert('fun load_'+fun+' failed. Please try again.');
                    reject('fun load_'+fun+' failed. Please try again.'); // 載入失敗，reject
                }
            };
            xhr.send(formData);
        });
    }
    // 子功能：將查詢得到的資料進行整理與渲染
    async function post_result(result_obj){
        // console.log('post_result...', result_obj);
        
        let sort_listData = [];                 // excel預備工作 1.建立大陣列

        if(result_obj.length != 0){
            // step1.鋪表頭
                for (const [_key, _value] of Object.entries(doc_list_keys)){
                    $('#result_table thead tr').append('<th>'+_value+'</th>');
                }
                for (const [_key, _value] of Object.entries(content_keys)){
                    $('#result_table thead tr').append('<th>'+_value+'</th>');
                }

            // step2.鋪body
                Object(result_obj).forEach((_doc)=>{
                    o_doc_item = '';
                    let sort_listRow = {};      // excel預備工作 2.建立小物件
                    // step2-1.先處理doc外層doc_list_keys
                    for (const [list_key, _value] of Object.entries(doc_list_keys)){
                        if(list_key == 'anis_no'){
                            o_doc_item += '<td><button type="button" value="../interView/form.php?action=review&uuid='+_doc['uuid']+'" '
                            o_doc_item += 'class="tran_btn" onclick="openUrl(this.value)" data-toggle="tooltip" data-placement="bottom" title="檢視問卷">'+_doc[list_key]+'</button></td>';
                            // excel預備工作 3.採集資料
                            sort_listRow[_value] = (typeof _doc[list_key] === 'array' || typeof _doc[list_key] === 'object') ? _doc[list_key].toString() : _doc[list_key];

                        }else if(list_key == '_content'){
                            o_doc_item += '<td>'+_doc[list_key]['s2_combo_06']+'</td>';
                            // excel預備工作 3.採集資料
                            sort_listRow[_value] = (typeof _doc[list_key]['s2_combo_06'] === 'array' || typeof _doc[list_key]['s2_combo_06'] === 'object') ? _doc[list_key]['s2_combo_06'].toString() : _doc[list_key]['s2_combo_06'];
                        
                        }else if(list_key == 'idty'){
                            let _idty = _doc[list_key];
                            switch(_idty){
                                case '1':   _idty = '立案/簽核中';  break;
                                case '10':  _idty = '結案';         break;
                                case '6':   _idty = '暫存';         break;
                                case '3':   _idty = '取消';         break;
                                default:
                            }

                            o_doc_item += '<td>'+_idty+'</td>';
                            // excel預備工作 3.採集資料
                            sort_listRow[_value] = _idty;
                        
                        }else{
                            o_doc_item += '<td>'+_doc[list_key]+'</td>';
                            // excel預備工作 3.採集資料
                            sort_listRow[_value] = (typeof _doc[list_key] === 'array' || typeof _doc[list_key] === 'object') ? _doc[list_key].toString() : _doc[list_key];
                        }
                    }
                    // step2-2.再處理doc內層content_keys
                    for (const [_key, _value] of Object.entries(content_keys)){
                        if (typeof _doc['_content'][_key] === 'string' && _doc['_content'][_key].match(/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/)) {
                            _doc['_content'][_key] = _doc['_content'][_key].replace('T', ' ');
                        }
                        o_doc_item += (_doc['_content'][_key] !== undefined) ? '<td>'+_doc['_content'][_key]+'</td>' : '<td>--</td>';
                        // excel預備工作 3.採集資料
                        if(_doc['_content'][_key] !== undefined ){
                            sort_listRow[_value] = (typeof _doc['_content'][_key] === 'array' || typeof _doc['_content'][_key] === 'object') ? _doc['_content'][_key].toString() : _doc['_content'][_key];
                        }else{
                            sort_listRow[_value] = '--';
                        }
                    }
                    $('#result_table tbody').append('<tr>'+ o_doc_item +'</tr>');
                    sort_listData.push(sort_listRow);                                   // excel預備工作 4.匯入採集資料
                })
            htmlTable.value = JSON.stringify(sort_listData);

        }else{
            $('#result_table tbody').append('-- 查無符合條件記錄 --');
        }
        $("body").mLoading("hide");
    }

    // 20240502 -- (document).ready(()=> await 依序執行step 1 2 3
    async function loadData() {
        try {
            await load_fun('combo', 's2_combo', bring_form); // step_1 load_form(dcc_no);             // 20240501 -- 改由後端取得 form_a 內容
            await eventListener();                           // step_1-2 eventListener();             // 
            await eventListener_shortName('_short_name');    // step_1-2 eventListener();             // 
        } catch (error) {
            console.error(error);
        }
    }
        
    $(document).ready(function(){
        // 20240502 -- 調用 loadData 函數來載入數據 await 依序執行step 1 2 3
        loadData();
    })

</script>

<!-- <script src="analyze.js?v=<=time()?>"></script> -->