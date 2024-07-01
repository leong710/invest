<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("../user_info.php");
    require_once("../caseList/function.php");

    // default fab_scope
    $fab_scope = ($sys_role <=1 ) ? "All" : "allMy";    // All :allMy
    // tidy query condition：
        $_site_id    = (isset($_REQUEST["_site_id"]))    ? $_REQUEST["_site_id"]    : "";           // 問卷site
        $_fab_id     = (isset($_REQUEST["_fab_id"]))     ? $_REQUEST["_fab_id"]     : $fab_scope;   // 問卷fab
        $_short_name = (isset($_REQUEST["_short_name"])) ? $_REQUEST["_short_name"] : "";           // 問卷類別
    // tidy sign_code scope 
        $sfab_id_str     = get_coverFab_lists("str");   // get signCode的管理轄區
        $sfab_id_arr     = explode(',', $sfab_id_str);  // 將管理轄區字串轉陣列

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
            text-align: right;
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
                    <table id="query_item">
                        <tbody>
                            <tr>
                                <td>廠區/棟別：</td>
                                <td class="inf">
                                    <select name="_site_id" id="_site_id" class="form-select inb block" >
                                        <option value="" hidden selected >-- 請選擇 問卷site --</option>
                                        <?php 
                                            // echo '<option for="_site_id" value="All" '.($_fab_id == "All" ? " selected":"").' >-- All 所有site --</option>';
                                            foreach($site_lists as $site){
                                                echo "<option for='_site_id' value='{$site["id"]}' ". ($site["id"] == $_site_id ? " selected" : "" );
                                                echo ($site["flag"] == "Off" ? " disabled" : "" ) ;
                                                echo " >";
                                                echo $site["id"]."：".$site["site_title"]."( ".$site["site_remark"]." )"; 
                                                echo ($site["flag"] == "Off") ? " - (已關閉)":"" ."</option>";
                                            } ?>
                                    </select>
                                    &nbsp
                                    <select name="_fab_id" id="_fab_id" class="form-select inb" >
                                        <option value="" hidden selected >-- 請選擇 問卷Fab --</option>
                                        <?php 
                                            echo '<option for="_fab_id" value="All" '.($_fab_id == "All" ? " selected":"").' >-- All 所有棟別 --</option>';
                                            if($sfab_id_str){
                                                echo '<option for="_fab_id" value="'.($sfab_id_str ? $sfab_id_str : "").'" selected ';
                                                echo ' >-- allMy 部門轄下 '.($sfab_id_str ? "(".$sfab_id_str.")":"").' --</option>';
                                            }
                                            foreach($fab_lists as $fab){
                                                echo "<option for='_fab_id' value='{$fab["id"]}' ";
                                                echo ($fab["id"] == $_fab_id) ? "selected" : "" ." >";
                                                echo $fab["id"]."：".$fab["site_title"]."&nbsp".$fab["fab_title"]."( ".$fab["fab_remark"]." )"; 
                                                echo ($fab["flag"] == "Off") ? " - (已關閉)":"" ."</option>";
                                            } ?>
                                    </select>

                                </td>
                            </tr>
                            <tr>
                                <td>anis_no / 申請單號：</td>
                                <td class="inf">
                                    <input type="text" name="anis_no" id="anis_no" class="form-control inb" placeholder="-- ANIS表單編號 --">
                                    <div class="invalid-feedback" id="anis_no_feedback">編號填入錯誤 ~ (大寫ANIS+數字流水號共21碼)</div>
                                </td>
                            </tr>
                            <tr>
                                <td>created_emp_id / 申請人員：</td>
                                <td class="inf">
                                    <input type="text" name="created_emp_id" id="created_emp_id" class="form-control" placeholder="-- 申請人員工號 --">

                                </td>
                            </tr>
                            <tr>
                                <td>_short_name / 事件類別：</td>
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
                                <td>idty / 結案狀態：</td>
                                <td class="inf">
                                    <select name="idty" id="idty" class="form-select">
                                        <option value="" hidden selected >-- 請選擇 結案狀態 --</option>
                                        <option value="10" >結案</option>
                                        <option value="6" >暫存</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>s2_combo_07 / 事故分類：</td>
                                <td class="inf">
                                    <select name="s2_combo_07" id="s2_combo_07" class="form-select" >
                                        <option value="" hidden selected >-- 請選擇 事故類型 --</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>s2_combo_08 / 災害類型：</td>
                                <td class="inf">
                                    <select name="s2_combo_08" id="s2_combo_08" class="form-select" >
                                        <option value="" hidden selected >-- 請選擇 災害類型 --</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>created_at / 申請日期：</td>
                                <td class="inf">
                                    <div class="input-group">
                                        <span class="input-group-text">From</span>
                                        <input type="date" name="created_at_form" id="created_at_form" class="form-control mb-0" >
                                    </div>
                                    &nbsp
                                    <div class="input-group">
                                        <span class="input-group-text">To</span>
                                        <input type="date" name="created_at_to"   id="created_at_to"   class="form-control mb-0" >
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- Hear：H -->
                    <div class="col-12 text-center">
                        <div class="inb">
                            <!-- H：downLoad Excel -->
                            <form id="myForm" method="post" action="../_Format/download_excel.php">
                                <input type="hidden" name="htmlTable" id="htmlTable" value="">
                                <button type="submit" name="submit" class="btn btn-outline-success" disabled title="<?php echo isset($_fab["id"]) ? $_fab["fab_title"]." (".$_fab["fab_remark"].")":"";?>" value="stock" onclick="submitDownloadExcel('stock')" >
                                    <i class="fa fa-download" aria-hidden="true"></i> 匯出</button>
                            </form>
                        </div>
                        <button type="button" class="btn btn-outline-secondary search_btn" value="count" id="search_btn">&nbsp<i class="fa-solid fa-magnifying-glass"></i>&nbsp查詢</button>
                    </div>
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

    async function eventListener(){
        return new Promise((resolve) => { 
            document.getElementById('search_btn').addEventListener('click', function() {
                const btn_value = this.value;
                const queryItem = document.getElementById('query_item');
                const elements = queryItem.querySelectorAll('select, input');
                const queryItem_obj = {};

                elements.forEach(({ name, value }) => {
                    queryItem_obj[name] = name && value ? value : null;
                });
                // loadData(btn_value, queryItem_obj);
                console.log('queryItem_obj:', queryItem_obj);
            });
            resolve(); // 文件載入成功，resolve
        });
    }
    // // step_1 表單生成 function 
    // 動態表單主fun -- JSON轉表單；依據不同的key_type進行切換型別 HARD CODED
    function make_question(session_key, key_class, item_a) {        // 接收參數：session, class, 單一問項
        let int_a = '';
        let dcff = '<div class="form-floating">';
        // 共用部分的操作1 label標籤
        function commonPart() {
            let labelSuffix = item_a.required ? '<sup class="text-danger"> *</sup>' : '';
            return '<label for="' + item_a.name + '" class="form-label">' + item_a.label + '：' + labelSuffix +'</label>';
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
            case 'radio':
            case 'checkbox':
                int_a = '<div class=" border rounded p-2"><snap title="'+item_a.name+'"><b>*** ' + item_a.label + '：' + (item_a.required ? '<sup class="text-danger"> *</sup>' : '') + '</b></snap><br>';
                Object(item_a.options).forEach((option)=>{
                    // let object_type = ((typeof option.value == 'object') ? option.label : option.value);   // for other's value
                    let object_type = ((typeof option.value == 'object') ? option.label : option.value);   // for other's value
                    // int_a += '<div class="form-check bg-light rounded"><input type="' + item_a.type + '" name="' + item_a.name + (item_a.type == 'checkbox' ? '[]':'') + '" value="' + object_type + '" '
                    int_a += '<div class="form-check bg-light rounded"><input type="' + item_a.type + '" name="' + item_a.name + '[]' + '" value="' + object_type + '" '
                          + ' id="' + item_a.name + '_' + object_type + '" ' + (item_a.required ? ' required ' : '') + 'onchange="onchange_option(this.name)" ' 
                          + ' class="form-check-input ' + item_a.name  
                            + ((typeof option.value === 'object') ? ' other_item ' : '') + (option.value.only ? ' only_option ' : '') 
                            + ((item_a.negative !== undefined && item_a.negative == object_type) ? ' negative ' : '') 
                            + ((item_a.get_negative !== undefined && item_a.get_negative == object_type) ? ' get_negative ' : '') 
                            + '" ' + ((option.flag  !== undefined) ? 'flag="'+option.flag+'"' : '')
                          + ' >' + '<label class="form-check-label '
                            + ((item_a.negative != undefined && item_a.negative == object_type) ? ' negative ' : '') 
                            + ((item_a.get_negative != undefined && item_a.get_negative == object_type) ? ' get_negative ' : '') 
                          + '" for="' + item_a.name + '_' + object_type + '">' + option.label + (typeof option.value === 'object' ? '：' : '') 
                          + '</label></div>';

                    if (typeof option.value === 'object' && option.value.type == 'text') {
                        // int_a += '<input type="'+ option.value.type +'" name="' + option.value.name + (item_a.type == 'checkbox' ? '[]':'') + '" '
                        int_a += '<input type="'+ option.value.type +'" name="' + option.value.name + '[]' + '" '
                            + ' placeholder="' + option.value.label + '" id="' + item_a.name + '_' + option.label + '_o" class="form-control unblock" disabled >';

                    }else if (typeof option.value === 'object' && option.value.type == 'number') {
                        int_a += '<input type="'+ option.value.type +'" name="' + option.value.name + '[]' + '" '
                            // + ' placeholder="' + option.value.label + '" id="' + item_a.name + '_' + option.label + '_o" class="form-control unblock" disabled  min="0" max="999" maxlength="3" oninput="if(value.length>3)value=value.slice(0,3)">';
                            + ' placeholder="' + option.value.label + '" id="' + item_a.name + '_' + option.label + '_o" class="form-control unblock" disabled ';
                        if(option.value.limit != undefined){
                            int_a += option.value.limit;
                        }
                        int_a += ' >';
                        
                    }else if (typeof option.value === 'object' && option.value.type == 'select') {
                        int_a += '<div class="p-1">';
                        int_a += '<select name="' + option.value.name + '[]" id="' + item_a.name + '_' + option.label + '_o" class="form-select unblock" disabled >'
                                + '<option value="" hidden>-- [請選擇 ' + item_a.label + '] --</option>' 
                        Object(option.value.options).forEach((option)=>{
                            if (typeof option.value === 'object') {
                                Object(option.value).forEach((key_value)=>{
                                    int_a += '<option value="'+key_value['value']+'" class="'+option.label + ((item_a.correspond != undefined) ? ' correspond' : '')+'" >'
                                        + key_value['value'] + '</option>' 
                                } )
                            }else {
                                int_a += '<option value="'+option.value+'" class="'+option.label+'">'+option.value+'</option>' 
                            }
                        }) 
                        int_a += '</select>'+'</div>';
                    }
                }) 
                int_a += '</div>';
                break;
            case 'select':
                int_a = '<div class=" border rounded p-2"><snap title="'+item_a.name+'"><b>*** ' + item_a.label + '：' + (item_a.required ? '<sup class="text-danger"> *</sup>' : '') + '</b></snap><br>';
                int_a += '<select name="'+item_a.name+'" id="'+item_a.name+'" class="form-select" >'
                      + '<option value="" hidden>-- [請選擇 ' + item_a.label + '] --</option>' 

                Object(item_a.options).forEach((option)=>{
                    if (typeof option.value === 'object') {
                        Object(option.value).forEach((key_value)=>{
                            int_a += '<option value="'+key_value['value']+'" class="'+option.label + ((item_a.correspond != undefined) ? ' correspond' : '')+'" >'
                                + key_value['value'] + '</option>' 
                        } )
                    }else {
                        int_a += '<option value="'+option.value+'" class="'+option.label+'">'+option.label+'：'+option.value+'</option>' 
                    }
                }) 
                int_a += '</select>'+'</div>';
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
        

        if(item_a.correspond !== undefined){                // 240613 判斷是否需要啟動對應選項 for 災害類型
            eventListener_correspond(item_a.correspond);
        };
        if(item_a.chooseBoth !== undefined){                // 240614 判斷是否需要以上皆是
            eventListener_chooseBoth(item_a.name, item_a.chooseBoth);
        };
        if(item_a.lock_opt !== undefined){                  // 240614 判斷是否需要lock option
            if(item_a.lock_opt == ""){
                reflesh_correspond(item_a.correspond);      // 240627 更新correspond對應選項功能
            }else{
                lock_opt(item_a.name, item_a.lock_opt );
            }
        };

    }
    // 20240501 -- // 動態表單主fun -- JSON轉表單
    function bring_form(form_json){
        let combo_item     = form_json.item;            // 抓item項目for form item
        // let form_doc = document.getElementById('item_list');                                    // 定義動態表單id位置
        if(combo_item){                                                                          // confirm form_item is't empty
            // console.log('step_1-2 make_question(key_1, value_1.class, item_value) -- ');
            // step_2.生成問項...將每一筆繞出來
            Object(combo_item).forEach((item_value)=>{
                let int_a = '';
                Object(item_value.options).forEach((option)=>{
                    if (typeof option.value === 'object') {
                        Object(option.value).forEach((key_value)=>{
                            int_a += '<option value="'+key_value['value']+'" class="'+option.label + (item_value.correspond != undefined ? ' correspond' : '')+'" >'
                                + key_value['value'] + '</option>' 
                        } )
                    }else {
                        int_a += '<option value="'+option.value+'" class="'+option.label+'">'+option.label+'：'+option.value+'</option>' 
                    }
                }) 
                // console.log(int_a);
                $("#"+item_value.name).append(int_a);

            })


            return true;
        } else {
            return false;
        }
    }

    // 主功能1.抓資料
    async function load_fun(fun, parm, myCallback) {                // parm = 參數
        // console.log('fun: load_fun...', fun, parm, myCallback);
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

                    resolve(myCallback(result_obj));           // resolve(true) = 表單載入成功，then 呼叫--myCallback

                } else {
                    alert('fun load_'+fun+' failed. Please try again.');
                    reject('fun load_'+fun+' failed. Please try again.'); // 載入失敗，reject
                }
            };
            xhr.send(formData);
        });
    }

    
    // 20240502 -- (document).ready(()=> await 依序執行step 1 2 3
    async function loadData() {
        try {
            await load_fun('combo', 's3_combo', bring_form); // step_1 load_form(dcc_no);             // 20240501 -- 改由後端取得 form_a 內容
            await eventListener();                      // step_1-2 eventListener();             // 

        } catch (error) {
            console.error(error);
        }
    }
        
    $(document).ready(function(){

        // 20240502 -- 調用 loadData 函數來載入數據 await 依序執行step 1 2 3
        loadData();
        // eventListener()

    })



</script>

<!-- <script src="analyze.js?v=<=time()?>"></script> -->