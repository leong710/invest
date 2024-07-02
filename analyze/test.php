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
                                <td>anis_no / 申請單號：</td>
                                <td class="inf">
                                    <input type="text" name="anis_no" id="anis_no" class="form-control inb" placeholder="-- ANIS表單編號 --"
                                            maxlength="21" oninput="if(value.length>21)value=value.slice(0,21)" >
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
                                <td class="inf px-3">
                                    <div class="form-check px-3">
                                        <input type="radio" name="idty" id="idty_10" value="10" class="form-check-input" checked>
                                        <label for="idty_10" class="form-check-label">結案</label>
                                    </div>
                                    <div class="form-check px-3">
                                        <input type="radio" name="idty" id="idty_6" value="6" class="form-check-input">
                                        <label for="idty_6" class="form-check-label">暫存</label>
                                    </div>
                                    <div class="form-check px-3">
                                        <input type="radio" name="idty" id="idty_All" value="All" class="form-check-input" >
                                        <label for="idty_All" class="form-check-label">All</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>s2_combo_07 / 事故分類：</td>
                                <td class="inf">
                                    <select name="s2_combo_07" id="s2_combo_07" class="form-select" disabl>
                                        <option value="" hidden selected >-- 請選擇 事故類型 --</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>s2_combo_08 / 災害類型：</td>
                                <td class="inf">
                                    <select name="s2_combo_08" id="s2_combo_08" class="form-select" disabl>
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

    // 240702 監聽送出按鈕
    async function eventListener(){
        return new Promise((resolve) => { 
            document.getElementById('search_btn').addEventListener('click', function() {
                const btn_value = this.value;
                const queryItem = document.getElementById('query_item');
                const elements = queryItem.querySelectorAll('select, input');
                const queryItem_obj = {};

                elements.forEach(({ name, value, type, checked }) => {
                    if (name) {
                        if (type === 'radio') {
                            if (checked) {
                                queryItem_obj[name] = value;
                            }
                        } else {
                            queryItem_obj[name] = value ? value : null;
                        }
                    }
                });
                // loadData(btn_value, queryItem_obj);
                console.log('queryItem_obj:', queryItem_obj);
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