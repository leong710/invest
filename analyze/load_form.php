<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("../user_info.php");  
            
?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<head>
    <link href="../../libs/aos/aos.css" rel="stylesheet">                                           <!-- goTop滾動畫面aos.css 1/4-->
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>            <!-- Jquery -->
        <link rel="stylesheet" type="text/css" href="../../libs/dataTables/jquery.dataTables.css">  <!-- dataTable參照 https://ithelp.ithome.com.tw/articles/10230169 --> <!-- data table CSS+JS -->
        <script type="text/javascript" charset="utf8" src="../../libs/dataTables/jquery.dataTables.js"></script>
    <script src="../../libs/jquery/jquery.mloading.js"></script>                                    <!-- mloading JS 1/3 -->
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">                            <!-- mloading CSS 2/3 -->
    <script src="../../libs/jquery/mloading_init.js"></script>                                      <!-- mLoading_init.js 3/3 -->
    <style>
        .body > ul {
            padding-left: 0px;
        }
        tr, td{
            text-align: start; 
        }
        /* 凸顯可編輯欄位 */
            .fix_amount:hover {
                /* font-size: 1.05rem; */
                font-weight: bold;
                text-shadow: 3px 3px 5px rgba(0,0,0,.5);
            }
        /* 警示項目 amount、lot_num */
            .alert_itb {
                background-color: #FFBFFF;
                color: red;
                font-size: 1.2em;
            }
            .alert_it {
                background-color: #FFBFFF;
                color: red;
            }
        /* inline */
            .inb {
                display: inline-block;
            }
            .inf {
                display: inline-flex;
            }
    </style>
</head>

<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-12 bg-white rounded my-2" id="main">
        
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
    <!-- toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="liveToast" class="toast align-items-center bg-warning" role="alert" aria-live="assertive" aria-atomic="true" autohide="true" delay="1000">
            <div class="d-flex">
                <div class="toast-body" id="toast-body"></div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <div id="gotop">
        <i class="fas fa-angle-up fa-2x"></i>
    </div>
</body>

<script src="../../libs/aos/aos.js"></script>                       <!-- goTop滾動畫面jquery.min.js+aos.js 3/4-->
<script src="../../libs/aos/aos_init.js"></script>                  <!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/sweetalert/sweetalert.min.js"></script>     <!-- 引入 SweetAlert 的 JS 套件 參考資料 https://w3c.hexschool.com/blog/13ef5369 -->
<script src="../../libs/openUrl/openUrl.js"></script>               <!-- 彈出子畫面 -->

<script>
    // init
    var doc_keys = [];
    var big_data = [];

    $(function () {
        // 在任何地方啟用工具提示框
        $('[data-toggle="tooltip"]').tooltip();
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
    })

    // 吐司顯示字條 // init toast
    function inside_toast(sinn){
        let toastLiveExample = document.getElementById('liveToast');
        let toast = new bootstrap.Toast(toastLiveExample);
        let toast_body = document.getElementById('toast-body');
        toast_body.innerHTML = sinn;
        toast.show();
    }

    async function load_fun(fun, parm, myCallback) {                // parm = 參數
        // console.log('fun: load_fun...', fun, parm);
        return new Promise((resolve, reject) => {
            let formData = new FormData();
            let fun_temp = (parm['_get_dccNo'] === true ) ? 'caseList' : fun;
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
                    resolve(myCallback(fun, result_obj));           // resolve(true) = 表單載入成功，then 呼叫--myCallback

                } else {
                    alert('fun load_'+fun+' failed. Please try again.');
                    reject('fun load_'+fun+' failed. Please try again.'); // 載入失敗，reject
                }
            };
            xhr.send(formData);
        });
    }

    async function gain_bigData(fun, gain_obj){
        switch(fun){
            case 'form':
                // console.log('fun: gain_bigData...form:' , fun , gain_obj);
                $('#main table tbody').empty();
                $('#main table thead tr').empty().append('<th>'+'label / name'+'</th>')
                for (const [key, value] of Object.entries(gain_obj)) {
                    if (typeof value === 'object') {
                        for (const [o_key, o_value] of Object.entries(value)){
                            if (typeof o_value === 'object') {
                                // innerText1 = '<td>'+ o_key + ' / ' + o_value.label + '</td>';
                                o_value.item.forEach((o_value_item)=>{
                                    let innerText2 = (typeof o_value_item === 'object')
                                        ? '<td>'+o_value_item.label +', '+ o_value_item.name +'</td>'
                                        : '<td>'+o_value_item +', '+ o_value_item +'</td>';
                                    // $('#main table tbody').append('<tr id="'+ o_value_item.name +'">'+innerText1 + innerText2+'</tr>');
                                    $('#main table tbody').append('<tr id="'+ o_value_item.name +'">'+innerText2+'</tr>');
                                    doc_keys[o_value_item.name] = { 'label' : o_value_item.label};       // ** 建立表單key：主要是document中有可能填寫不完整，而造成缺項
                                    // innerText1 = '<td></td>';
                                }) 
                            }else {
                                // console.log('not-object_2:', o_key, o_value);
                            }
                        } 
                    }else {
                        // console.log('not-object_1: ',key, value);
                    }
                }
                break;

            case 'document':
                // console.log('fun: gain_bigData...document:' , fun , gain_obj);
                // 表頭標題
                        $('#main table thead tr').append('<th>'+gain_obj.anis_no+'</th>');
                Object.keys(doc_keys).forEach((doc_key)=>{
                    let value = (gain_obj[doc_key] !== undefined) ? gain_obj[doc_key] : gain_obj._content[doc_key];
                        // 文字日期轉換
                        if (typeof value === 'string' && value.match(/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/)) {
                            value = value.replace('T', ' ');
                        }
                        
                        let innerText = '';
                        if (typeof value === 'object') {
                            // for (const [o_key, o_value] of Object.entries(value)){
                            //     innerText += (innerText == '') ? o_value : '</br>'+ o_value;
                            // } 
                            innerText = Object.values(value).join('</br>');
                        }else{
                            innerText = (value !== undefined) ? value : '- NA -'; 
                        }
                        $('#main table tbody tr[id="'+ doc_key +'"]').append('<td>'+ innerText +'</td>');
                    // 建立統計資料
                    if(doc_key.match("_combo_")){

                        if (!doc_keys[doc_key]['value']) {
                            doc_keys[doc_key]['value'] = {};
                        }
                        doc_keys[doc_key]['value'][value] = (doc_keys[doc_key]['value'][value] || 0) + 1;
                    }
                })
                break;
                
            default :
                throw new Error(`Unknown function: ${fun}`);
        }
    }
    // step1:
    async function get_dccNo(fun, gain_obj){
        if(doc_keys.length == 0 && gain_obj['dcc_no'] !== undefined) {
            let dcc_no = gain_obj['dcc_no'];
            await load_fun('form', dcc_no, gain_bigData);             // step_1 load_form(dcc_no);             // 20240501 -- 改由後端取得 form_a 內容
        }
    }
    // step2:
    async function caseList(fun, gain_obj){
        const promises = gain_obj.map(async (_doc) => {
            let uuid = _doc['uuid'];
            await load_fun('document', uuid, gain_bigData);  // step_2 load_document(uuid);           // 20240501 -- 改由後端取得 _document內容
        });
        await Promise.all(promises);  // 確保所有文檔加載完成
    }
    // step3:
    async function analyze(gain_obj){
        // console.log('fun: analyze...', gain_obj);
        $('#main table thead tr').append('<th>'+'- 統計 -'+'</th>');
        Object.keys(gain_obj).forEach((doc_key)=>{
            let key_value = gain_obj[doc_key]['value'];

            let innerText = '';
            if (key_value) {
                if (typeof key_value === 'object') {
                    innerText = Object.entries(key_value).map(([o_key, o_value]) => `${o_key} : ${o_value}`).join('</br>');
                } else {
                    innerText = (key_value !== undefined) ? key_value : '- NA -';
                }
            }
            $('#main table tbody tr[id="'+ doc_key +'"]').append('<td>'+ innerText +'</td>');
        })
    }

    // 20240502 -- (document).ready(()=> await 依序執行step 1 2 3
    async function loadData(query_obj) {
        try {
                
            // query_obj['_get_dccNo'] = true;
            // await load_fun('get_dccNo', query_obj, get_dccNo);
            
            // delete query_obj['_get_dccNo'];
            // await load_fun('caseList', query_obj, caseList);
            
            // await analyze(doc_keys);



        } catch (error) {
            console.error(error);
        }
    }


    $(document).ready(function(){

        // 20240502 -- 調用 loadData 函數來載入數據 await 依序執行step 1 2 3
        // loadData();
        // eventListener()
        
        let dcc_no = '13ES100016-F003-V003c'
        load_fun('form', dcc_no, gain_bigData);             // step_1 load_form(dcc_no);             // 20240501 -- 改由後端取得 form_a 內容

    })
    
</script>