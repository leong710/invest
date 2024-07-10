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
   $up_href = (isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];   // 回上頁 // 回本頁

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
            /* width:  367px; */
            /* height: 110px; */
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
   </style>
</head>

<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-mm-10 col-12 border rounded bg-white p-4 ">
                <div class="row">
                    <!-- 左上：訪問單 -->
                    <div class="col-12 col-md-3 px-2 py-0 t-center" id="btn_list">
                        <h2><span class="badge bg-primary w-100">--&nbsp<i class="fa fa-edit"></i>&nbsp填寫訪問單&nbsp--</span></h2>
                        <!-- append button here -->
                    </div>

                    <!-- 右上：各廠燈號 -->
                    <div class="col-12 col-md-9 px-2 py-0 mb-2 ">
                        <!-- 廠區燈號欄 -->
                        <h2><span class="badge bg-primary w-100">--&nbsp<i class="fa fa-edit"></i>&nbsp填寫訪問單&nbsp--</span></h2>
                        <div class="col-12 bg-white rounded p-0" id="highLight">
                        <!-- append site here -->
                        </div>
                        <!-- 說明欄 -->
                        <div class="col-12 bg-white border rounded p-3 my-2" id="remark">
                            <b>燈號說明：</b></br>
                            <span class="t-center">
                                <span class="badge rounded-pill bg-danger">紅燈</span> 職災申報逾期；
                                <span class="badge rounded-pill bg-warning text-dark">黃燈</span> 未結案案件(含暫存、未完成職災申報) >=1件；
                                <span class="badge rounded-pill bg-success">綠燈</span> 沒有未結案案件(含暫存、未完成職災申報)；
                            </span>
                            <hr>
                            <b>自動通報條件 -- 職災申報期限 <=5天：</b>
                            <ul>
                                <li>通報時間：週一～週五 08:00 & 13:00</li>
                                <li>通報方式：Mapp + eMail通知</li>
                                <li>通知對象：開單人、窗口、部門副理、部門經理、大PM</li>
                            </ul>
                        </div>
                    </div>

                    <!-- 中下：聯絡窗口 -->
                    <div class="col-12 my-2 p-3 text-center">
                        <h4><span class="badge bg-success"><i class="fa-solid fa-circle-info"></i>&nbsp各廠聯絡窗口</span></h4>
                        <table id="service_window" class="table table-striped table-hover">
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
    // var formcases = <=json_encode($formcases)?>;   // 取得表單清單
    // var formcases_length = formcases.length;
    var icon_s = '<i class="';
    var icon_e = ' fa-3x"></i>';

    function make_btn(value_1){
        if(value_1.dcc_no){
            let int_b = "<button type='button' class='btn btn-outline-primary add_btn form_btn' value='form.php?dcc_no="+ value_1.dcc_no +"' onclick='openUrl(this.value)' >"
                + "<div class='col-12 p-1 pt-3'>" + icon_s + value_1._icon + icon_e                 // + "<div class='col-12 col-md-8 py-0 px-1'><span style='font-size:12px;'>" + value_1.dcc_no + "</span></br>" + value_1.title + "</br><h5 class='mb-0'><b>- " + value_1.short_name +" -</b><h5></div></div></button>";
                + "<h5 class='mb-0 mt-1'><b>- " + value_1.short_name +" -</b><h5></div></button>";
            return int_b;
        }else{
            return '<snap class="btn btn-outline-secondary">' + value_1.title + '</br>-- 無效json表單 --</snap>';
        }
    }

    function bring_form(formcases){
        if(formcases){   
            for (const [key_1, value_1] of Object.entries(formcases)) {
                const int_btn = '<div class="col-12 text-center p-2">' + make_btn(value_1) + '</div>';
                $('#btn_list').append(int_btn);    // 渲染form
            }
        }
    }
    
    function make_site(value_1){
        if(value_1.dcc_no){
            let int_b = "<button type='button' class='btn btn-outline-primary add_btn form_btn' value='form.php?dcc_no="+ value_1.dcc_no +"' onclick='openUrl(this.value)' >"
                + "<div class='col-12 p-1 pt-3'>" + icon_s + value_1._icon + icon_e                 // + "<div class='col-12 col-md-8 py-0 px-1'><span style='font-size:12px;'>" + value_1.dcc_no + "</span></br>" + value_1.title + "</br><h5 class='mb-0'><b>- " + value_1.short_name +" -</b><h5></div></div></button>";
                + "<h5 class='mb-0 mt-1'><b>- " + value_1.short_name +" -</b><h5></div></button>";
            return int_b;
        }else{
            return '<snap class="btn btn-outline-secondary">' + value_1.title + '</br>-- 無效json表單 --</snap>';
        }
    }
    
    function bring_site(site){
        if(site){   
            for (const [key_1, value_1] of Object.entries(site)) {
                const div_site = '<div class="col-12 border rounded my-2 text-center" id="site_id_'+ value_1.id +'">' + value_1.site_title + ' / ' +value_1.site_remark + '</br></div>';
                $('#highLight').append(div_site);    // 渲染form
            }
        }
    }
    
    function bring_fab(fab){
        if(fab){   
            for (const [key_1, value_1] of Object.entries(fab)) {
                console.log('bring_fab...', value_1.site_id, value_1.fab_title, value_1.fab_remark)
                const div_fab = '<span class="badge rounded-pill bg-success">'+value_1.fab_title + ' / ' +value_1.fab_remark +'</span>&nbsp';
                $('#site_id_'+value_1.site_id).append(div_fab);    // 渲染form
            }
        }
    }


    $(function () {
        // 在任何地方啟用工具提示框
        $('[data-toggle="tooltip"]').tooltip();
    })

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
                    alert('fun load_'+fun+' failed. Please try again.');
                    reject('fun load_'+fun+' failed. Please try again.'); // 載入失敗，reject
                }
            };
            xhr.send(formData);
        });
    }

    async function doc_init() {
        try {
            mloading(); 
            await load_fun('formcase', '', bring_form); // step_1 load_form(dcc_no);        // 後端取得 form_case 內容
            await load_fun('_site', '', bring_site); // step_1 load_form(dcc_no);        // 後端取得 form_case 內容
            await load_fun('_fab', '', bring_fab); // step_1 load_form(dcc_no);        // 後端取得 form_case 內容
            // await signature_canva();                    // step_1-1 signature_canva();           // 
            // await eventListener();                      // step_1-2 eventListener();             // 

        } catch (error) {
            console.error(error);
        }
        $("body").mLoading("hide");
    }

    $(document).ready(function(){

        doc_init();
        // loadData();
        // eventListener();
    })

</script>

<?php include("../template/footer.php"); ?>
