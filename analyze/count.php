<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("../user_info.php");
    require_once("../caseList/function.php");

    // default fab_scope
    $fab_scope = ($sys_role <=1 ) ? "All" : "allMy";               // All :allMy
    // tidy query condition：
        $_fab_id     = (isset($_REQUEST["_fab_id"]))     ? $_REQUEST["_fab_id"]     : $fab_scope;   // 問卷fab
        $_year       = (isset($_REQUEST["_year"]))       ? $_REQUEST["_year"]       : date('Y');    // 問卷年度
        $_month      = (isset($_REQUEST["_month"]))      ? $_REQUEST["_month"]      : date('m');    // 問卷月份
        $_short_name = (isset($_REQUEST["_short_name"])) ? $_REQUEST["_short_name"] : "All";        // 問卷類別
    // tidy sign_code scope 
        $sfab_id_str     = get_coverFab_lists("str");   // get signCode的管理轄區
        $sfab_id_arr     = explode(',', $sfab_id_str);  // 將管理轄區字串轉陣列

    // for select item
        $fab_lists       = show_fab_lists();            // get 廠區清單
        $year_lists      = show_document_GB_year();     // get 立案year清單
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
        table tbody tr td{
            text-align: left;
            /* padding: 1.5em; */
        }
    </style>
</head>
<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col_xl_11 col-12 rounded pb-3" style="background-color: rgba(255, 255, 255, .8);">
                <!-- NAV分頁標籤與統計 -->
                <div class="col-12 pb-0 px-0">
                    <ul class="nav nav-tabs">
                        <li class="nav-item"><a class="nav-link active"   href="count.php">案件統計</span></a></li>
                        <li class="nav-item"><a class="nav-link "         href="index.php">訪談內容統計</span></a></li>
                    </ul>
                </div>
                
                <!-- 內頁 -->
                <div class="col-12 bg-white rounded" id="main">
                    <!-- Hear：H -->
                    <div class="row">
                        <!-- H：sort/groupBy function -->
                        <div class="col-md-10 pb-0">
                            <div class="input-group" id="query_item">
                                <span class="input-group-text">篩選</span>

                                <select name="_year" id="_year" class="form-select" >
                                    <option value="" hidden selected >-- 請選擇 問卷年度 --</option>
                                    <?php 
                                        echo '<option for="_year" value="All" '.($_year == "All" ? " selected":"" ).($sys_role >= "0" ? " disabled":"" ).' >-- All 所有年度 --</option>';
                                        foreach($year_lists as $list_year){
                                            echo "<option for='_year' value='{$list_year["_year"]}' ";
                                            echo ($list_year["_year"] == $_year ? "selected" : "" )." >".$list_year["_year"]."y</option>";
                                        } ?>
                                </select>
                                <select name="_month" id="_month" class="form-select">
                                    <?php 
                                        echo "<option for='_month' value='All' ".(($_month == "All") ? " selected":"" )." >-- 全月份 / All --</option>";
                                        foreach (range(1, 12) as $month_lists) {
                                            $month_str = str_pad($month_lists, 2, '0', STR_PAD_LEFT);
                                            echo "<option for='_month' value='{$month_str}' ".(($month_str == $_month ) ? " selected":"" )." >{$month_str}m</option>";
                                        } ?>
                                </select>
                                <select name="_short_name" id="_short_name" class="form-select" >
                                    <option value="" hidden selected >-- 請選擇 問卷類型 --</option>
                                    <?php 
                                        echo "<option for='_short_name' value='All' ".(($_short_name == "All") ? " selected":"" )." >-- 全問卷類型 / All --</option>";
                                        foreach($shortName_lists as $shortName){
                                            echo "<option for='_short_name' value='{$shortName["short_name"]}' ";
                                            echo ($shortName["short_name"] == $_short_name ? " selected" : "" )." >".$shortName["short_name"]."</option>";
                                        } ?>
                                </select>

                                <select name="_fab_id" id="_fab_id" class="form-select" >
                                    <option value="" hidden selected >-- 請選擇 問卷Fab --</option>
                                    <?php 
                                        echo '<option for="_fab_id" value="All" '.($_fab_id == "All" ? " selected":"").' >-- All 所有棟別 --</option>';
                                        echo '<option for="_fab_id" value="allMy" '.($_fab_id == "allMy" ? " selected":"");
                                        echo ' >-- allMy 部門轄下 '.($sfab_id_str ? "(".$sfab_id_str.")":"").' --</option>';
                                        foreach($fab_lists as $fab){
                                            echo "<option for='_fab_id' value='{$fab["id"]}' ";
                                            echo ($fab["id"] == $_fab_id) ? "selected" : "" ." >";
                                            echo $fab["id"]."：".$fab["site_title"]."&nbsp".$fab["fab_title"]."( ".$fab["fab_remark"]." )"; 
                                            echo ($fab["flag"] == "Off") ? " - (已關閉)":"" ."</option>";
                                        } ?>
                                </select>

                                <select name="_sfab_id" id="_sfab_id" class="form-select unblock" >
                                    <?php 
                                        echo '<option for="_sfab_id" value="'.($sfab_id_str ? $sfab_id_str : "").'" selected ';
                                        echo ' >-- allMy 部門轄下 '.($sfab_id_str ? "(".$sfab_id_str.")":"").' --</option>';
                                    ?>
                                </select>
                                
                                <button type="button" class="btn btn-outline-secondary search_btn" value="count" id="search_btn">&nbsp<i class="fa-solid fa-magnifying-glass"></i>&nbsp查詢</button>

                            </div>
                        </div>

                        <!-- H：Button -->
                        <div class="col-md-2 pb-0 text-end inb">
                            <div class="inb">
                                <!-- H：downLoad Excel -->
                                <form id="myForm" method="post" action="../_Format/download_excel.php">
                                    <input type="hidden" name="htmlTable" id="htmlTable" value="">
                                    <button type="submit" name="submit" class="btn btn-success" disabled title="<?php echo isset($_fab["id"]) ? $_fab["fab_title"]." (".$_fab["fab_remark"].")":"";?>" value="stock" onclick="submitDownloadExcel('stock')" >
                                        <i class="fa fa-download" aria-hidden="true"></i> 匯出</button>
                                </form>
                            </div>
                        </div>
                        <!-- Bootstrap Alarm -->
                        <div id="liveAlertPlaceholder" class="col-12 text-center mb-0 pb-0"></div>
                    </div>
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
        
</script>

<script src="analyze.js?v=<?=time()?>"></script>