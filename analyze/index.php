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
        /* #emp_id, #excelFile{    
            margin-bottom: 0px;
            text-align: center;
        } */
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
        .signature {
            box-shadow: 0px 0px 8px rgba(0,0,0,.5);
        }
        /* 使用 CSS 將 canvas 的寬度設置為 100% */
        /* canvas {
            width: 100%;
        } */
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
        .confirm_sign_div {
            height: 200px; 
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            position: relative;
        }

        .confirm_sign_div::after {
            content: '';
            display: block;
            width: 100%;
            border-top: 1px solid #000;
            margin-top: 10px;
        }
    </style>
</head>

<script src="../../libs/aos/aos.js"></script>               <!-- goTop滾動畫面jquery.min.js+aos.js 3/4-->
<script src="../../libs/aos/aos_init.js"></script>          <!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/signature_pad/signature_pad.umd.min.js"></script>     <!-- 簽名板外掛 -->
<script src="../../libs/openUrl/openUrl.js"></script>       <!-- 彈出子畫面 -->
<!-- <script src="../../libs/moment/moment.min.js"></script> -->

<script>
    // init

    var dcc_no        = '13ES100016-F002-V002b';
    var uuid          = 'fe1d5761-29f5-11ef-b605-2cfda183ef4f';
    var meeting_man_a = [];                         // 事故當事者(或其委任代理人)
    var meeting_man_o = [];                         // 其他與會人員
    var meeting_man_s = [];                         // 環安人員
    var meeting_man_target;                         // 指向目標
    var negative_arr  = [];                         // 監聽負向選項

    var big_data = [];
    
</script>

<script src="form.js?v=<?=time()?>"></script>