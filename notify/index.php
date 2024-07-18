<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    // accessDenied();
    if(!isset($_SESSION)){ session_start(); }                                                          // 確認session是否啟動

    $fa_check  = '<snap class="fa_check"><i class="fa fa-check" aria-hidden="true"></i> </snap>';      // 打勾符號
    $fa_remove = '<snap class="fa_remove"><i class="fa fa-remove" aria-hidden="true"></i> </snap>';    // 打叉符號
	$uri       = (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) ? 'https://' : 'http://';  // 取得開頭
	$uri      .= $_SERVER['HTTP_HOST'];                                                                // 組合成http_host
    $pc        = $_REQUEST["ip"] = $_SERVER['REMOTE_ADDR'];                                            // 取得user IP
    $check_ip  = check_ip($_REQUEST);                                                                  // 驗證IP權限 // 確認電腦IP是否受認證

    $sys_role  = (isset($_SESSION[$sys_id]["role"])) ? $_SESSION[$sys_id]["role"] : false;             // 取出$_session引用
    $fun       = (!empty($_REQUEST['fun'])) ? $_REQUEST['fun'] : false ;                               // 先抓操作功能'notify_insign'= MAPP待簽發報 // 確認有帶數值才執行
    // $notify_lists    = notify_list();                                                                  // 載入所有待簽名單

    $reloadTime = (file_exists("reloadTime.txt")) ? file_get_contents("reloadTime.txt") : "";       // 從文件加载reloadTime内容

?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<head>
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer" ></script>
    <script src="../../libs/jquery/jquery.mloading.js"></script>
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
    <script src="../../libs/jquery/mloading_init.js"></script>
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
        .fa_check {
            color: #00ff00;
        }
        .fa_remove {
            color: #ff0000;
        }
        #result {
            text-align: start;
        }
    </style>
</head>

<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <!-- <div class="col-11"> -->
                <div class="col-mm-10 col-12 border rounded p-4" style="background-color: #D4D4D4;">
                    <!-- 表頭 -->
                    <div class="row">
                        <div class="col-12 col-md-6 py-0">
                            <h3>待通報清單統計</h3>
                        </div>
                        <div class="col-12 col-md-6 py-0 text-end">
                            <?php if($sys_role == 0 && $check_ip){ ?>
                                <button type="button" id="upload_myTodo_btn" class="btn btn-sm btn-xs <?php echo !$mailTo_notify ? 'btn-primary':'btn-warning';?>" data-toggle="tooltip" data-placement="bottom" 
                                    title="send notify" onclick="return confirm('確認發報？') && notify_notify()">傳送&nbspEmail&nbsp<i class="fa-solid fa-paper-plane"></i>&nbsp+&nbspMAPP&nbsp<i class="fa-solid fa-comment-sms"></i></button>
                                    
                            <?php } ?>
                            <button type="button" class="btn btn-secondary rtn_btn" onclick="location.href = '../index.php'"><i class="fa fa-caret-up" aria-hidden="true"></i>&nbsp回首頁</button>
                        </div>
                    </div>

                        <div id="nav-receive" class="col-12 bg-white border rounded">
                            <!-- 1.領用申請單待簽名冊(receive) -->
                            <div class="row">
                                <div class="col-12 col-md-8 py-0 text-primary">
                                    <?php echo "待簽名單共："." 筆";?>
                                </div>
                                <div class="col-12 col-md-4 py-0 text-end">
                                    <button type="button" id="notify_lists_btn" title="訊息收折" class="op_tab_btn" value="notify_lists" onclick="op_tab(this.value)"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                                </div>
                            </div>
                            <div id="notify_lists" class="notify_lists col-12 mt-2 border rounded">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>姓名(工號)</th>
                                            <th>fab(signCode)</th>
                                            <th>待辦案件(anisCode)</th>
                                            <th class="text-danger">合計件數</th>
                                        </tr>
                                    </thead>
                                    <tbody>
          
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    <div class="row">
                        <div class="col-12 col-md-6 pb-0">
                            <b>執行訊息：</b>
                        </div>
                        <div class="col-12 col-md-6 pb-0 text-end">
                        </div>
                        <!-- append執行訊息 -->
                        <div class="col-12 bg-white border rounded py-2 my-0" id="result">

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-4 pb-0">
   
                        </div>
                        <div class="col-6 col-md-4 pb-0 text-center">
                            <div id="myMessage">
                                <?php if(!empty($fun)){ echo "** 自動模式 **"; }else{ echo "** 手動模式 **"; }?>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 pb-0 text-end">
                            <?php echo ($sys_role == 0) ? "* [管理者模式]" : "* [路人模式]";?>
                            <?php echo $check_ip ? $fa_check:$fa_remove; echo " ".$pc;?>
                        </div>
                    </div>
                    <!-- 20231108-資料更新時間 -->
                    <div class="col-12 py-0 px-3 text-end">
                        <span style="display: inline-block;" >
                            <button type="button" class="btn btn-outline-success add_btn" onclick="load_init(true)" data-toggle="tooltip" data-placement="bottom" title="強制更新">
                                <i class="fa-solid fa-rotate"></i></button>&nbspLast reload time：</span>
                        <span style="display: inline-block;" id="reload_time" title="" ><?php echo $reloadTime;?></span>
                    </div>
                </div>
            </div>
        <!-- </div> -->
    </div>

    <div id="gotop">
        <i class="fas fa-angle-up fa-2x"></i>
    </div>
</body>

<script src="../../libs/aos/aos.js"></script>
<script src="../../libs/aos/aos_init.js"></script>
<script src="../../libs/sweetalert/sweetalert.min.js"></script>
<script src="../../libs/openUrl/openUrl.js?v=<?=time();?>"></script>           <!-- 彈出子畫面 -->
<script>
    // init
        var fa_OK        = '<?=$fa_check?>';            // 打勾符號
        var fa_NG        = '<?=$fa_remove?>';           // 打叉符號
        var mail_OK      = '<snap class="fa_check"><i class="fa-solid fa-paper-plane"></i> </snap>';                // 寄信符號
        var mail_NG      = '<snap class="fa_remove"><i class="fa-solid fa-triangle-exclamation"></i> </snap>';      // 打叉符號

        const uri        = '<?=$uri?>';
        var fun          = '<?=$fun?>';                 // 是否啟動寄送信件給人員
        var check_ip     = '<?=$check_ip?>';
        // var notify_lists = <=json_encode($notify_lists)?>;
        // var lists_obj    = { notify_lists : notify_lists }
        var notify_lists    = {}

        var receive_url  = '領用路徑：'+uri+'/ppe/receive/';
        var issue_url    = '請購路徑：'+uri+'/ppe/issue/';

        var int_msg1     = '【環安PPE系統】待您處理文件提醒';
        var int_msg2     = ' 您共有 ';
        var int_msg3     = ' 件待簽核文件尚未處理';
        var ret_msg3     = ' 件被退件文件尚未處理';
        var col_msg3     = ' 件待收發文件尚未處理';
        var int_msg4     = '，如已處理完畢，請忽略此訊息！\n\n** 請至以下連結查看待處理文件：\n';
        var srt_msg4     = '，如已處理完畢，請忽略此訊息！\n\n';
        var int_msg5     = '\n\n溫馨提示：\n    1.登錄過程中如出現提示輸入帳號密碼，請以cminl\\NT帳號格式\n';

        var push_result  = {
                'mapp' : {
                    'success' : 0,
                    'error'   : 0
                },
                'email' : {
                    'success' : 0,
                    'error'   : 0
                }
            }

        var Today       = new Date();
        const thisToday = Today.getFullYear() +'/'+ String(Today.getMonth()+1).padStart(2,'0') +'/'+ String(Today.getDate()).padStart(2,'0');  // 20230406_bug-fix: 定義出今天日期，padStart(2,'0'))=未滿2位數補0
        const thisTime  = String(Today.getHours()).padStart(2,'0') +':'+ String(Today.getMinutes()).padStart(2,'0');                           // 20230406_bug-fix: 定義出今天日期，padStart(2,'0'))=未滿2位數補0
        
        const uuid = '3cd9a6fd-4021-11ef-9173-1c697a98a75f';       // invest

</script>

<script src="notify.js?v=<?=time()?>"></script>


<?php include("../template/footer.php"); ?>
