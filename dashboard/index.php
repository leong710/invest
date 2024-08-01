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
   </style>
</head>

<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-mm-10 col-12 rounded p-4 " style="background-color: rgba(255, 255, 255, .7);">
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
                                <li>通報方式：>3days：eMail；<=3days：Mapp + eMail通知</li>
                                <li>通知對象：remaining_days<ul>
                                        <li>>3：窗口、課副理</li>    
                                        <li>>=0：窗口、課副理、部經理、大PM</li>    
                                        <li>&nbsp&nbsp<&nbsp0：窗口、課副理、部經理、大PM、處長</li>    
                                        <li>*** 以上表單狀態若是未結案，將一併通知 開單人</li>
                                </ul></li>
                            </ul>
                            <!-- 20231108-資料更新時間 -->
                            <div class="col-12 py-0 px-3 text-end">
                                <span style="display: inline-block;" >
                                    <button type="button" class="btn btn-outline-success add_btn" onclick="dashboard_init(true)" data-toggle="tooltip" data-placement="bottom" title="強制更新" 
                                        <?php echo ($sys_role <= 1) ? "":"disabled";?> > <i class="fa-solid fa-rotate"></i></button>&nbspLast reload time：</span>
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

</script>
<script src="dashboard.js?v=<?=time()?>"></script>

<?php include("../template/footer.php"); ?>
