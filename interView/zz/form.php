<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("../user_info.php");
    // require_once("function.php");
    accessDenied($sys_id);

    // 複製本頁網址藥用
    $up_href = (isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];   // 回上頁 // 回本頁

    // 決定表單開啟方式
    $action = (isset($_REQUEST["action"])) ? $_REQUEST["action"] : 'create';   // 有action就帶action，沒有action就新開單


    // 路径到 form_a.json 文件
    $json_file_path = 'form_a.json';
    // 从 JSON 文件加载内容
    $form_a_json = file_get_contents($json_file_path);
    // 解析 JSON 数据并将其存储在 $form_a_json 变量中
    $form_a_json = json_decode($form_a_json, true); // 如果您想将JSON解析为关联数组，请传入 true，否则将解析为对象
    

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
        #emp_id, #excelFile{    
            margin-bottom: 0px;
            text-align: center;
        }

    </style>
</head>

<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-11 border rounded px-3 py-4" style="background-color: #D4D4D4;">
                <!-- 表頭1 -->
                <div class="row px-2">
                    <div class="col-12 col-md-6 py-0">
                        <h3><i class="fa-solid fa-3"></i>&nbsp<b>南廠區交通事故訪談表</b><?php echo empty($action) ? "":" - ".$action;?></h3>
                    </div>
                    <div class="col-12 col-md-6 py-0 text-end">
                        <a href="<?php echo $up_href;?>" class="btn btn-secondary" onclick="return confirm('確認返回？');" ><i class="fa fa-external-link" aria-hidden="true"></i>&nbsp回上頁</a>
                    </div>
                </div>
                <div class="row px-2">
                    <div class="col-12 col-md-6">
                        訪問單號：<?php echo ($action == 'create') ? "(尚未給號)": "receive_aid_".$receive_row['id']; ?></br>
                        開單日期：<?php echo ($action == 'create') ? date('Y-m-d H:i')."&nbsp(實際以送出時間為主)":$receive_row['created_at']; ?></br>
                        填單人員：<?php echo ($action == 'create') ? $auth_emp_id." / ".$auth_cname : $receive_row["created_emp_id"]." / ".$receive_row["created_cname"] ;?>
                    </div>
                    <div class="col-12 col-md-6 text-end">
                        <?php if(($sys_role <= 1 ) && (isset($receive_row['idty']) && $receive_row['idty'] != 0)){ ?>
                            <form action="" method="post">
                                <input type="hidden" name="uuid" value="<?php echo $receive_row["uuid"];?>">
                                <input type="submit" name="delete_receive" value="刪除 (Delete)" title="刪除申請單" class="btn btn-danger" onclick="return confirm('確認徹底刪除此單？')">
                            </form>
                        <?php }?>
                    </div>
                </div>
    
                <!-- container -->
                <div class="col-12 p-0">
                    <!-- 分頁標籤 -->
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" 
                                aria-controls="nav-home" aria-selected="true">1.選取器材用品</button>
                            <button class="nav-link" id="nav-shopping_cart-tab" data-bs-toggle="tab" data-bs-target="#nav-shopping_cart" type="button" role="tab" 
                                aria-controls="nav-shopping_cart" aria-selected="false">2.購物車&nbsp<span id="shopping_count" class="badge rounded-pill bg-danger"></span></button>
                            <button class="nav-link disabled" id="nav-review-tab" data-bs-toggle="tab" data-bs-target="#nav-review" type="button" role="tab" 
                                aria-controls="nav-review" aria-selected="false">3.申請單成立</button>
                        </div>
                    </nav>
                    <!-- 內頁 -->
                    <form action="store.php" method="post" onsubmit="this.cname.disabled=false,this.plant.disabled=false,this.dept.disabled=false,this.sign_code.disabled=false,this.omager.disabled=false" >
                        <div class="tab-content rounded bg-light" id="nav-tabContent">
                            <!-- 1.商品目錄 -->
                            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                <div class="col-12 px-4" id="item_list" >

                                </div>
                            </div>
    
                        </div>

                        <!-- 模組 saveSubmit-->
                        <div class="modal fade" id="saveSubmit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Do you submit this 領用申請：</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body px-5">
                                        <label for="sign_comm" class="form-check-label" >command：</label>
                                        <textarea name="sign_comm" id="sign_comm" class="form-control" rows="5"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="created_emp_id"  id="created_emp_id" value="<?php echo $auth_emp_id;?>">
                                        <input type="hidden" name="created_cname"   id="created_cname"  value="<?php echo $auth_cname;?>">
                                        <input type="hidden" name="updated_user"    id="updated_user"   value="<?php echo $auth_cname;?>">
                                        <input type="hidden" name="uuid"            id="uuid"           value="">
                                        <input type="hidden" name="step"            id="step"           value="<?php echo $step;?>">
                                        <input type="hidden" name="action"          id="action"         value="<?php echo $action;?>">
                                        <input type="hidden" name="idty"            id="idty"           value="1">
                                        <?php if($sys_role <= 3){ ?>
                                            <button type="submit" value="Submit" name="receive_submit" class="btn btn-primary" ><i class="fa fa-paper-plane" aria-hidden="true"></i> 送出 (Submit)</button>
                                        <?php } ?>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <!-- 尾段logs訊息 -->
                    <div class="col-12 pt-0 rounded bg-light unblock" id="logs_div">
                        <div class="row">
                            <div class="col-6 col-md-6">
                                表單記錄：
                            </div>
                            <div class="col-6 col-md-6">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 py-1 px-4">
                                <table class="for-table logs table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Step</th>
                                            <th>Signer</th>
                                            <th>Time Signed</th>
                                            <th>Status</th>
                                            <th>Comment</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div style="font-size: 12px;" class="text-end">
                                logs-end
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    <!-- toast -->
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="liveToast" class="toast align-items-center" role="alert" aria-live="assertive" aria-atomic="true" autohide="true" delay="1000">
                <div class="d-flex">
                    <div class="toast-body" id="toast-body">
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    <!-- 模組 cata_info -->
        <div class="modal fade" id="cata_info" tabindex="-1" aria-labelledby="cata_info" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">細項說明：</h5>
                        <button type="button" class="btn-close" aria-label="Close" data-bs-target="#catalog_modal" data-bs-toggle="modal"></button>
                    </div>
                    <div class="modal-body px-5">
                        <div class="row">
                            <div class="col-6 col-md-4" id="pic_append"></div>
                            <div class="col-6 col-md-8" id="info_append"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">返回</button>
                    </div>
                </div>
            </div>
        </div>
    <!-- 互動視窗 load_excel -->
       <div class="modal fade" id="load_excel" tabindex="-1" aria-labelledby="load_excel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">上傳Excel檔：</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form name="excelInput" action="../_Format/upload_excel.php" method="POST" enctype="multipart/form-data" target="api" onsubmit="return restockExcelForm()">
                        <div class="modal-body px-4">
                            <div class="row">
                                <div class="col-6 col-md-8 py-0">
                                    <label for="excelFile" class="form-label">需求清單 <span>&nbsp<a href="../_Format/receive_example.xlsx" target="_blank">上傳格式範例</a></span> 
                                        <sup class="text-danger"> * 限EXCEL檔案</sup></label>
                                    <div class="input-group">
                                        <input type="file" name="excelFile" id="excelFile" style="font-size: 16px; max-width: 350px;" class="form-control form-control-sm" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                                        <button type="submit" name="excelUpload" id="excelUpload" class="btn btn-outline-secondary" value="stock">上傳</button>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4 py-0">
                                    <p id="warningText" name="warning" >＊請上傳需求單Excel檔</p>
                                    <p id="sn_list" name="warning" >＊請確認Excel中的資料</p>
                                </div>
                            </div>
                                
                            <div class="row" id="excel_iframe">
                                <iframe id="api" name="api" width="100%" height="30" style="display: none;" onclick="restockExcelForm()"></iframe>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="import_excel_btn" class="btn btn-success unblock" data-bs-dismiss="modal">載入</button>
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">返回</button>
                        </div>
                    </form>
                </div>
            </div>
        </div> 

        <div id="gotop">
            <i class="fas fa-angle-up fa-2x"></i>
        </div>
    
</body>

<script src="../../libs/aos/aos.js"></script>       <!-- goTop滾動畫面jquery.min.js+aos.js 3/4-->
<script src="../../libs/aos/aos_init.js"></script>  <!-- goTop滾動畫面script.js 4/4-->
<script>
// 開局設定init
    var form_json = <?=json_encode($form_a_json)?>;

    function make_question(id_key, key_a, item_a) {
        var now = new Date(); // 取得當前時間
        var int_a = '';
        if (item_a.data_type == "text") {
            int_a = '<div class="form-floating">' +
                '<input type="text" name="' + key_a + '" class="form-control" id="' + key_a + '" required>' +
                '<label for="' + key_a + '" class="form-label">' + item_a.title + '</label></div>';

        } else if (item_a.data_type == "date") {
            var formattedDate = now.toISOString().slice(0, 10); // 格式化日期，取得日期部分
            int_a = '<div class="form-floating">' +
                '<input type="date" name="' + key_a + '" class="form-control" id="' + key_a + '" value="' + formattedDate + '" required>' +
                '<label for="' + key_a + '" class="form-label">' + item_a.title + '</label></div>';

        } else if (item_a.data_type == "datetime") {
            var formattedDateTime = now.toISOString().slice(0, 16); // 格式化日期時間，取得到分鐘的部分
            int_a = '<div class="form-floating">' +
                '<input type="datetime-local" name="' + key_a + '" class="form-control" id="' + key_a + '" value="' + formattedDateTime + '" required>' +
                '<label for="' + key_a + '" class="form-label">' + item_a.title + '</label></div>';

        } else if (item_a.data_type == "textarea") {
            int_a = '<div class="form-floating">' +
                '<textarea name="' + key_a + '" id="' + key_a + '" class="form-control" style="height: 100px"></textarea>' +
                '<label for="' + key_a + '" class="form-label">' + item_a.title + '</label></div>';

        } else if (item_a.data_type == "radio") {
            int_a = '<div class="form-check">' +
                '<label>' + item_a.title + '</label><br>';
            for (const [radioKey, radioValue] of Object.entries(item_a.value)) {
                if (typeof radioValue === 'object') {
                    int_a += '<input type="radio" name="' + key_a + '" value="' + radioKey + '" class="form-check-input">' +
                        '<label class="form-check-label">' + radioValue.title + '</label><br>';
                } else {
                    int_a += '<input type="radio" name="' + key_a + '" value="' + radioKey + '" class="form-check-input">' +
                        '<label class="form-check-label">' + radioValue + '</label><br>';
                }
            }
            int_a += '</div>';
        }

        $('#' + id_key).append(int_a);
    }

    // 以下是你原來的迴圈，我保留了不做修改，新增部分在最後一個 else 語句裡
    var form_doc = document.getElementById('item_list');
    for (const [key_1, value_1] of Object.entries(form_json)) {
        let match;
        const regex = new RegExp('session', 'gi');
        if ((match = regex.exec(key_1)) !== null) {
            let int_1 = '<div class="col-10" id="' + key_1 + '"><hr>';
            if (value_1.title.length != 0) {
                int_1 += '<h4><b>※&nbsp' + key_1 + '&nbsp' + value_1.title + '：</b></h4>';
            }
            if (value_1.info.length != 0) {
                int_1 += '&nbsp' + value_1.info;
            }
            int_1 += '</div>'
            $('#item_list').append(int_1);
        }
        for (const [key_2, value_2] of Object.entries(value_1.item)) {
            make_question(key_1, key_2, value_2)
        }
        // 檢查是否有缺少的項目，並添加缺少的項目
        for (const [key_2, value_2] of Object.entries(value_1.item)) {
            if (!$('#' + key_2).length) {
                make_question(key_1, key_2, value_2);
            }
        }
    }


// 以下為控制 iframe
    var realName         = document.getElementById('realName');           // 上傳後，JSON存放處(給表單儲存使用)
    var iframe           = document.getElementById('api');                // 清冊的iframe介面
    var warningText      = document.getElementById('warningText');        // 清冊未上傳的提示
    var sn_list          = document.getElementById('sn_list');            // 清冊中有誤的提示
    var excel_json       = document.getElementById('excel_json');         // 清冊中有誤的提示
    var excelFile        = document.getElementById('excelFile');          // 上傳檔案名稱
    var excelUpload      = document.getElementById('excelUpload');        // 上傳按鈕
    var import_excel_btn = document.getElementById('import_excel_btn');   // 載入按鈕

</script>

<!-- <script src="receive_form.js?v=<=time()?>"></script> -->

<?php include("../template/footer.php"); ?>