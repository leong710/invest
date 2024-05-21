<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("../user_info.php");
    require_once("function.php");
    accessDenied($sys_id);

    // for return
    $up_href = (isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];   // 回上頁 // 回本頁

    // default fab_scope
        $fab_scope = ($sys_role <=1 ) ? "All" : "allMy";               // All :allMy
    // tidy query condition：
        $select_fab_id     = (isset($_REQUEST["select_fab_id"]))     ? $_REQUEST["select_fab_id"]     : $fab_scope;   // 問卷fab
        $select_year       = (isset($_REQUEST["select_year"]))       ? $_REQUEST["select_year"]       : date('Y');    // 問卷年度
        $select_short_name = (isset($_REQUEST["select_short_name"])) ? $_REQUEST["select_short_name"] : "All";        // 問卷類別
    // tidy sign_code scope 
        $sfab_id_str     = get_coverFab_lists("str");   // get signCode的管理轄區
        $sfab_id_arr     = explode(',', $sfab_id_str);  // 將管理轄區字串轉陣列
    // merge quesy array
        $query_arr = array(
            'fab_id'        => $select_fab_id,
            '_year'         => $select_year,
            'short_name'    => $select_short_name,
            'sfab_id'       => $sfab_id_str,
        );
    // get mainData = caseList
        $caseLists       = show_caseList($query_arr);   // get case清單
    // for select item
        $fab_lists       = show_fab_lists();            // get 廠區清單
        $year_lists      = show_document_GB_year();     // get 立案year清單
        $shortName_lists = show_document_shortName();   // get 簡稱清單

        $icon_s = '<i class="';
        $icon_e = ' fa-2x"></i>&nbsp&nbsp';

    // echo "<pre>";
    // print_r($query_arr);
    // echo "</pre>";
            
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
            <div class="col_xl_12 col-12 rounded" style="background-color: rgba(255, 255, 255, .8);">
                <!-- NAV分頁標籤與統計 -->
                <div class="col-12 pb-0 px-0">
                    <ul class="nav nav-tabs">
                        <li class="nav-item"><a class="nav-link active" href="index.php">訪談清單管理</span></a></li>
                        <li class="nav-item"><a class="nav-link"   href=""> UB </span></a></li>
                    </ul>
                </div>
                <!-- 內頁 -->
                <div class="col-12 bg-white">
                    <!-- by各Local儲存點： -->
                    <div class="row">
                        <div class="col-md-3 pb-0 ">
                            <h5><?php echo isset($select_fab["id"]) ? $select_fab["id"].".".$select_fab["fab_title"]." (".$select_fab["fab_remark"].")":"$select_fab_id";?>_訪談清單管理： </h5>
                        </div>

                        <!-- sort/groupBy function -->
                        <div class="col-md-6 pb-0 ">
                            <form action="" method="POST">
                                <div class="input-group">
                                    <span class="input-group-text">篩選</span>

                                    <select name="select_year" id="select_year" class="form-select" >
                                        <option value="" hidden selected >-- 請選擇 問卷年度 --</option>
                                        <option for="select_year" value="All" <?php echo $select_year == "All" ? "selected":"";?>>-- All 所有年度 --</option>
                                        <?php foreach($year_lists as $_year){
                                            echo "<option for='select_year' value='{$_year["_year"]}' ";
                                            echo $_year["_year"] == $select_year ? "selected" : "";
                                            echo " >".$_year["_year"]."y</option>";
                                        } ?>
                                    </select>

                                    <select name="select_short_name" id="select_short_name" class="form-select" >
                                        <option value="" hidden selected >-- 請選擇 問卷類型 --</option>
                                        <option for="select_short_name" value="All" <?php echo $select_short_name == "All" ? "selected":"";?>>-- All 所有類型 --</option>
                                        <?php foreach($shortName_lists as $shortName){
                                            echo "<option for='select_short_name' value='{$shortName["short_name"]}' ";
                                            echo $shortName["short_name"] == $select_short_name ? "selected" : "";
                                            echo " >".$shortName["short_name"]."</option>";
                                        } ?>
                                    </select>

                                    <select name="select_fab_id" id="select_fab_id" class="form-select" >
                                        <option value="" hidden selected >-- 請選擇 問卷Fab --</option>
                                        <option for="select_fab_id" value="All" <?php echo $select_fab_id == "All" ? "selected":"";?>>-- All 所有棟別 --</option>
                                        <option for="select_fab_id" value="allMy" <?php echo $select_fab_id == "allMy" ? "selected":"";?>>
                                            -- allMy 部門轄下 <?php echo $sfab_id_str ? "(".$sfab_id_str.")":"";?> --</option>
                                        <?php foreach($fab_lists as $fab){
                                            echo "<option for='select_fab_id' value='{$fab["id"]}' ";
                                            echo ($fab["id"] == $select_fab_id) ? "selected" : "" ." >";
                                            echo $fab["id"]."：".$fab["site_title"]."&nbsp".$fab["fab_title"]."( ".$fab["fab_remark"]." )"; 
                                            echo ($fab["flag"] == "Off") ? " - (已關閉)":"" ."</option>";
                                        } ?>
                                    </select>

                                    <button type="submit" class="btn btn-outline-secondary search_btn" >&nbsp<i class="fa-solid fa-magnifying-glass"></i>&nbsp查詢</button>

                                </div>
                            </form>
                        </div>

                        <!-- 表頭按鈕 -->
                        <div class="col-md-3 pb-0 text-end inb">
                            <div class="inb">
                                <button type="button" id="receive_btn" class="btn btn-danger disabled" data-bs-toggle="modal" data-bs-target="#receive"><i class="fa-solid fa-clipboard-list" aria-hidden="true"></i>&nbsp填寫</button>
                            </div>
                            <div class="inb">
                                <!-- 20231128 下載Excel -->
                                <form id="myForm" method="post" action="../_Format/download_excel.php">
                                    <input type="hidden" name="htmlTable" id="htmlTable" value="">
                                    <button type="submit" name="submit" class="btn btn-success" disabled title="<?php echo isset($select_fab["id"]) ? $select_fab["fab_title"]." (".$select_fab["fab_remark"].")":"";?>" value="stock" onclick="submitDownloadExcel('stock')" >
                                        <i class="fa fa-download" aria-hidden="true"></i> 匯出</button>
                                </form>
                                <button type="button" id="test_btn" class="btn btn-success" onclick="openUrl('google.com')" > test </button>
                            </div>
                        </div>
                        <!-- Bootstrap Alarm -->
                        <div id="liveAlertPlaceholder" class="col-12 text-center mb-0 pb-0"></div>
                    </div>
                    <table id="caseList" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ANSI單號</th>
                                <th>案件簡稱</th>
                                <th>事件名稱</th>

                                <th>棟別</th>
                                <th>廠處別</th>
                                <th>事故單位</th>

                                <th>表單狀態</th>
                                <th>立案日期 / 填單人</th>
                                <th>最後更新 / 更新人</th>

                                <th data-toggle="tooltip" data-placement="bottom" title="操作">action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php foreach($caseLists as $caseList){ ?>
                                <tr>
                                    <td><?php echo $caseList["anis_no"] ?></td>
                                    <td><?php echo $icon_s.$caseList['_icon'].$icon_e.$caseList['short_name'];?></td>
                                    <td class="word_bk" title="aid_<?php echo $caseList['id'];?>"><?php echo $caseList['id'].".".$caseList['case_title'];?></td>

                                    <td><?php echo $caseList['fab_title']."(".$caseList['fab_remark'].")";?></td>
                                    <td><?php echo $caseList['local_title'];?></td>
                                    <td><?php echo $caseList['a_dept'];?></td>
                                    
                                    <td><?php echo $caseList['idty'];?></td>
                                    <td><?php echo $caseList["created_at"]."</br>".$caseList['created_cname'];?></td>
                                    <td><?php echo $caseList["updated_at"]."</br>".$caseList['updated_cname'];?></td>

                                    <td>
                                        <!-- <a href="..\interView\show.php?uuid=<php echo $caseList['uuid'];?>&action=review" class="btn btn-sm btn-xs btn-primary"  -->
                                            <!-- target="_blank" data-toggle="tooltip" data-placement="bottom" title="檢視"><i class="fa-regular fa-folder-open"></i></a> -->
                                        <!-- <a href="..\interView\form.php?uuid=<php echo $caseList['uuid'];?>&action=edit" class="btn btn-sm btn-xs btn-success"  -->
                                            <!-- target="_blank" data-toggle="tooltip" data-placement="bottom" title="編輯"><i class="fa-solid fa-pen-to-square"></i></a> -->

                                        <button type="button" value="..\interView\show.php?action=review&uuid=<?php echo $caseList['uuid'];?>" class="btn btn-sm btn-xs btn-primary" 
                                            onclick="openUrl(this.value)" data-toggle="tooltip" data-placement="bottom" title="檢視"><i class="fa-regular fa-folder-open"></i></button>
                                        <button type="button" value="..\interView\form.php?action=edit&uuid=<?php echo $caseList['uuid'];?>" class="btn btn-sm btn-xs btn-success" 
                                            onclick="openUrl(this.value)" data-toggle="tooltip" data-placement="bottom" title="編輯"><i class="fa-solid fa-pen-to-square"></i></button>
                                    </td>
                                </tr>

                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                </br>

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
// // // 開局導入設定檔
    $(document).ready(function () {
        // 在任何地方啟用工具提示框
        $('[data-toggle="tooltip"]').tooltip();
        // dataTable 2 https://ithelp.ithome.com.tw/articles/10272439
        $('#caseList').DataTable({
            "autoWidth": false,
            // 排序
            // "order": [[ 4, "asc" ]],
            // 顯示長度
            "pageLength": 25,
            // 中文化
            "language":{
                url: "../../libs/dataTables/dataTable_zh.json"
            }
        });
    })
    
</script>

<!-- <script src="caseList.js?v=<=time()?>"></script> -->

<?php include("../template/footer.php"); ?>