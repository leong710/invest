<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("../user_info.php");
    require_once("service_window.php");             // service window
    $sw_arr = (array) json_decode($sw_json);        // service window 物件轉陣列

    //    accessDenied($sys_id);
    if(!empty($_SESSION["AUTH"]["pass"]) && empty($_SESSION[$sys_id])){
        accessDenied_sys($sys_id);
    }

    // 複製本頁網址藥用
    // $up_href = (isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];   // 回上頁 // 回本頁

    $reloadTime = (file_exists("reloadTime.txt")) ? file_get_contents("reloadTime.txt") : "";       // 從文件加载reloadTime内容
        
?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>
<head>
   <link href="../../libs/aos/aos.css" rel="stylesheet">                                           <!-- goTop滾動畫面aos.css 1/4-->
   <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>            <!-- Jquery -->

   <script src="../../libs/sweetalert/sweetalert.min.js"></script>                                 <!-- 引入 SweetAlert 的 JS 套件 參考資料 https://w3c.hexschool.com/blog/13ef5369 -->
   <script src="../../libs/jquery/jquery.mloading.js"></script>                                    <!-- mloading JS 1/3 -->
   <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">                            <!-- mloading CSS 2/3 -->
   <script src="../../libs/jquery/mloading_init.js"></script>                                      <!-- mLoading_init.js 3/3 -->
   <style>
        /* 當螢幕寬度小於或等於 1366px時 */
        @media (max-width: 1366px) {
            .col-mm-10 {
                flex: 0 0 calc(100% / 12 * 12);
            }
        }
        /* 當螢幕寬度大於 1366px時 */
        @media (min-width: 1367px) {
            .col-mm-10 {
                flex: 0 0 calc(100% / 12 * 9);
            }
        }
        .form_btn {
            width:  100%;
            background: white;
            /* width:  367px; */
            /* height: 110px; */
        }
        .bs-b {
            box-shadow: 0 5px 15px -2px rgba(3 , 65 , 134 , .7);
        }
        /* 確保父元素具有定位上下文 */
        .parent {
            position: relative;
            height: 90vh; /* 或任何你希望的高度 */
        }
        /* 將 #remark 定位在父元素的底部 */
        .remark {
            position: absolute;
            bottom: 1em;
            width: 95%;
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
        .bg-c-blue {
            background: linear-gradient(45deg,#4099ff,#73b4ff);
        }

        .bg-c-success {
            /* green */
            /* background: linear-gradient(45deg,#2ed8b6,#59e0c5); */
            background: linear-gradient(45deg,#7CFC00,#ADFF2F);
        }

        .bg-c-warning {
            /* yellow */
            /* background: linear-gradient(45deg,#FFB64D,#ffcb80); */
            background: linear-gradient(45deg,#FFA600,#ffcb80);
        }

        .bg-c-danger {
            /* pink */
            background: linear-gradient(45deg,#FF5370,#ff869a);
        }
   </style>
</head>

<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-mm-10 col-12 border rounded bg-light p-4 ">
                <div class="row">
                    <!-- 左上：訪問單 -->
                    <div class="col-3 col-md-3 px-2 py-0 t-center">
                        <h2><span class="badge bg-primary w-100">--&nbsp<i class="fa fa-edit"></i>&nbsp填寫訪問單&nbsp--</span></h2>
                        <div class="col-12 p-0" id="btn_list">

                        </div>
                        <!-- append button here -->
                    </div>

                    <!-- 右上：各廠燈號 -->
                    <div class="col-9 col-md-9 px-2 py-0 mb-2 ">
                        <!-- 廠區燈號欄 -->
                        <h2><span class="badge bg-c-blue w-100">--&nbsp各廠管控燈號&nbsp--</span></h2>
                        <div class="col-12 bg-white rounded p-0" id="highLight">
                            <!-- append site here -->
                        </div>
                        <!-- 說明欄 -->
                        <div class="col-12 bg-white border rounded p-3 my-2 bs-b" id="remark">
                            <b>燈號說明：</b></br>
                            <span class="t-center">
                                <span class="badge rounded-pill bg-danger">紅燈</span> 職災申報逾期；
                                <span class="badge rounded-pill bg-warning text-dark">黃燈</span> 未結案案件(含暫存、未完成職災申報) >= 1件；
                                <span class="badge rounded-pill bg-success">綠燈</span> 沒有未結案案件(含暫存、未完成職災申報)；
                            </span>
                            <hr>
                            <b>自動通報條件：職災申報期限 <= 5天</b>
                            <ul>
                                <li>通報時間：週一～週五 08:00 & 13:00</li>
                                <li>通報方式：Mapp + eMail通知</li>
                                <li>通知對象：開單人、窗口、部門副理、部門經理、大PM</li>
                            </ul>
                            <!-- 20231108-資料更新時間 -->
                            <div class="col-12 py-0 px-3 text-end">
                                <span style="display: inline-block;" >
                                    <button type="button" class="btn btn-outline-success add_btn" onclick="dashboard_init(true)" data-toggle="tooltip" data-placement="bottom" title="強制更新">
                                        <i class="fa-solid fa-rotate"></i></button>&nbspLast reload time：</span>
                                <span style="display: inline-block;" id="reload_time" title="" ><?php echo $reloadTime;?> </span>
                            </div>
                        </div>
                    </div>

                    <!-- 中下：聯絡窗口 -->
                    <div class="col-12 my-3 p-3 text-center">
                        <h4><span class="badge bg-info"><i class="fa-solid fa-circle-info"></i>&nbsp各廠聯絡窗口</span></h4>
                        <table id="service_window" class="table table-striped table-hover bs-b">
                            <thead>
                                <tr>
                                    <th>FAB</th>
                                    <th>窗口姓名</th>
                                    <th>分機</th>
                                    <th>email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($sw_arr as $sw_key => $sw_value){
                                    $value_length = count($sw_value);
                                    if($value_length < 1){
                                        $append_str = '<tr><td>'.$sw_key.'</td><td>null</td><td></td><td></td></tr>';
                                    }else{
                                        if(is_object($sw_value)) { $sw_value = (array)$sw_value; }                      // 物件轉陣列
                                        $td_key = '<td rowspan="'.$value_length.'">'.$sw_key.'</td>';
                                        $append_str = "";
                                        $i = 1;
                                        foreach($sw_value as $sw_item => $sw_item_value){
                                            if(is_object($sw_item_value)) { $sw_item_value = (array)$sw_item_value; }   // 物件轉陣列
                                            $td_value = '. '.$sw_item_value["cname"].'</td><td>'.$sw_item_value["tel_no"].'</td><td>'.strtolower($sw_item_value["email"]).'</td></tr>';
                                            if($i === 1){
                                                $append_str .= '<tr>'.$td_key.'<td>'.$i.$td_value;
                                            }else{
                                                $append_str .= '<tr><td>'.$i.$td_value;
                                            }
                                            $i++;
                                        }
                                    };
                                    echo $append_str;
                                }?>
                            </tbody>
                        </table>
                    </div>
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
<script src="../../libs/openUrl/openUrl.js"></script>       <!-- 彈出子畫面 -->

<script>

    $(function () {
        // 在任何地方啟用工具提示框
        $('[data-toggle="tooltip"]').tooltip();
    })
    // 1-1.仔子功能--生成fom的btn
    function make_formBtn(fc_value){
        const icon_s = '<i class="';
        const icon_e = ' fa-3x"></i>';
        if(fc_value.dcc_no){
            let int_b = "<button type='button' class='btn btn-outline-primary add_btn form_btn bs-b' value='../interView/form.php?dcc_no="+ fc_value.dcc_no +"' onclick='openUrl(this.value)' >"
                + "<div class='col-12 p-1 pt-3'>" + icon_s + fc_value._icon + icon_e + "<h5 class='mb-0 mt-1'><b>- " + fc_value.short_name +" -</b><h5></div></button>";
            return int_b;
        }else{
            return '<snap class="btn btn-outline-secondary">' + fc_value.title + '</br>-- 無效json表單 --</snap>';
        }
    }
    // 1.子功能--鋪設form的btn外框
    function bring_form(formcases){
        $('#btn_list').empty();     // 鋪設前清除
        if(formcases){   
            for (const [key_1, value_1] of Object.entries(formcases)) {
                const int_btn = '<div class="col-12 text-center p-2">' + make_formBtn(value_1) + '</div>';
                $('#btn_list').append(int_btn);    // 渲染form
            }
        }
    }
    // 2.子功能--鋪設site外框
    function bring_site(site){
        $('#highLight').empty();     // 鋪設前清除
        if(site){   
            for (const [key_2, value_2] of Object.entries(site)) {
                const div_site = '<div class="card my-3 bs-b">'+'<h5 class="card-header">'+ value_2.site_title +' / '+ value_2.site_remark +'</h5>' 
                    +'<div class="card-body row" id="site_id_'+ value_2.id +'">'+'</div>'+'</div>';
                $('#highLight').append(div_site);    // 渲染form
            }
        }
    }
    // 3.子功能--鋪設fab按鈕+預設燈號
    function bring_fab(fab){
        if(fab){   
            for (const [key_3, value_3] of Object.entries(fab)) {
                const div_fab = '<div class="col-md-2 p-2 py-3 inb t-center">'
                        +'<a href="../caseList/?_month=All&_fab_id='+ value_3.id +'" class="btn rounded-pill btn-secondary text-light" id="btn_fab_'+value_3.id+'">&nbsp'+ value_3.fab_title +'&nbsp</a>'+'</div>';
                $('#site_id_'+value_3.site_id).append(div_fab);    // 渲染form
            }
        }
    }
    // 4.子功能--鋪設fab燈號
    function bring_light(highlight){
        // console.log(highlight);
        if(highlight){   
            for (const [key_4, value_4] of Object.entries(highlight)) {
                target_btn = document.getElementById('btn_fab_'+value_4['id']);
                if(target_btn){
                    target_btn.classList.remove('btn-secondary');
                    target_btn.classList.add('bg-c-'+value_4.light);
                }
            }
        }
    }

    // 0-1.確認是否超過3小時；true=_db/更新時間；false=_json          // 呼叫來源：dashboard_init
    function check3hourse(action){
        // let currentDate = new Date().toLocaleString('zh-TW', { hour12: false });                      // 取得今天日期時間
        // let reloadTime  = new Date(reload_time.innerText).toLocaleString('zh-TW', { hour12: false }); // 取得reloadTime時間
        let currentDate = new Date();                               // 取得今天日期時間
        let reloadTime  = new Date(reload_time.innerText);          // 取得reloadTime時間

        let timeDifference = currentDate - reloadTime;              // 計算兩個時間之間的毫秒差異
        let hoursDifference = timeDifference / (1000 * 60 * 60);    // 將毫秒差異轉換為小時數
        let result = hoursDifference >= 3 ;                          // 判斷相差時間是否大於3小時，並顯示結果
        let _method = result ? '_db' : '_json';
        if(result || action){
            recordTime();       // 1.取得目前時間，並格式化；2.更新reloadTime.txt時間；完成後=>3.更新畫面上reload_time時間
        }
        const _title = ('時間差：'+ Number(hoursDifference.toFixed(2)) +'（小時）>= 3小時：'+ result +' => '+ _method);
        document.getElementById('reload_time').title = _title;
        return _method;
    }
    // 0-3.更新畫面上reload_time時間                  // 呼叫來源：recordTime
    function update_reloadTime(rightNow){
        reload_time.innerText = rightNow;       // 更新畫面上reload_time時間
    }

// // // 
    // 0-0.多功能擷取fun
    async function load_fun(fun, parm, myCallback) {        // parm = 參數
        return new Promise((resolve, reject) => {
            let formData = new FormData();
            formData.append('fun', fun);
            formData.append('parm', parm);                  // 後端依照fun進行parm參數的採用
            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'load_fun.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);    // 接收回傳
                    let result_obj = response['result_obj'];        // 擷取主要物件
                    resolve(myCallback(result_obj))                 // resolve(true) = 表單載入成功，then 呼叫--myCallback
                                                                    // myCallback：form = bring_form() 、document = edit_show() 、locals = ? 還沒寫好
                } else {
                    let err_msg = 'fun load '+fun+' failed. Please try again.';
                    // alert(err_msg);
                    reject(err_msg); // 載入失敗，reject
                }
            };
            xhr.send(formData);
        });
    }
    // 0-2.取得目前時間，並格式化；2.更新reloadTime.txt時間；完成後=>3.更新畫面上reload_time時間          // 呼叫來源：check3hourse
    async function recordTime(){
        let rightNow = new Date().toLocaleString('zh-TW', { hour12: false });                     // 取得今天日期時間
        try {
            await load_fun('urt' , rightNow+', true' , update_reloadTime);      
        } catch (error) {
            console.error(error);
        }
    }

    async function dashboard_init(action) {
        const _method = check3hourse(action);
        const _type = action ?  "_db" : _method;      // action來決定 false=自動判斷check3hourse 或 true=強制_db
        try {
            mloading(); 
                // await load_fun('_db',   'formcase,'       , bring_form);     // step_1 直接抓db(true/false 輸出json檔)，取得 formcase 內容後鋪設內容
                // await load_fun('_json', 'formcase, true'  , bring_form);     // step_1 先抓json，沒有then抓db(true/false 輸出json檔)，取得 formcase 內容後鋪設內容
            await load_fun(_type, 'formcase, true'  , bring_form);     // step_1 先抓json，沒有then抓db(true/false 輸出json檔)，取得 formcase 內容後鋪設內容
            await load_fun(_type, '_site, true'     , bring_site);     // step_2 先抓json，沒有then抓db(true/false 輸出json檔)，取得 _site    內容後鋪設內容
            await load_fun(_type, '_fab, true'      , bring_fab);      // step_3 先抓json，沒有then抓db(true/false 輸出json檔)，取得 _fab     內容後鋪設內容
            await load_fun(_type, 'highlight, true' , bring_light);    // step_4 先抓json，沒有then抓db(true/false 輸出json檔)，取得 highlight內容後鋪設內容

        } catch (error) {
            console.error(error);
        }
        await $("body").mLoading("hide");
    }

    $(document).ready(function(){

        dashboard_init(false);

    })

</script>

<?php include("../template/footer.php"); ?>
