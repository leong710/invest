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
        table {
            /* border: 1px solid #aaa; */
            width: 100%;
            border-collapse: collapse;
        }

        table td, table th {
            border: 1.5px solid black;
            text-align: left;
            padding: 8px;
        }

        /* 平均分配每列的宽度 */
        table td {
            width: calc(100% / 6); /* 例如，如果你有3列 */
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
                    <!-- 內頁 -->
                    <form action="store.php" method="post" onsubmit="this.cname.disabled=false,this.plant.disabled=false,this.dept.disabled=false,this.sign_code.disabled=false,this.omager.disabled=false" >
                        <div class="rounded bg-light p-3">
                            <table>
                                <tbody>
                                    <tr>
                                        <td class="text-center" >事件名稱</td>
                                        <td colspan="5"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center" >會議日期</td>
                                        <td></td>
                                        <td class="text-center" >會議時間</td>
                                        <td></td>
                                        <td class="text-center" >地點</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center" >事故單位</td>
                                        <td colspan="5"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"  rowspan="4">與會人員</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">事故當事者(或其委任代理人)：</td>
                                        <td colspan="4"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">其他與會人員：</td>
                                        <td colspan="4"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">環安人員：</td>
                                        <td colspan="4"></td>
                                    </tr>

                                    <tr>
                                        <td class="text-center" colspan="6">內容</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">單位</td>
                                        <td colspan="5"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center" >工號</td>
                                        <td></td>
                                        <td class="text-center" >姓名</td>
                                        <td></td>
                                        <td class="text-center" >職稱</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">到職日</td>
                                        <td colspan="2"></td>
                                        <td class="text-center">年資</td>
                                        <td colspan="2"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">事故時間</td>
                                        <td colspan="5"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">發生事故當日當事者應工作起始時間：</td>
                                        <td colspan="2"></td>
                                        <td class="text-center">發生事故當日當事者應工作訖止時間：</td>
                                        <td colspan="2"></td>
                                    </tr>

                                    <tr>
                                        <td class="text-center">事故地點</td>
                                        <td colspan="5"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">事故位置簡圖 ( 描繪路線並標出1. 就業場所2. 日常居住處所3.上下班應經途徑4.事故地點 )：</td>
                                        <td colspan="5"></td>
                                    </tr>

                                    <tr>
                                        <td class="text-center">事故原因及經過說明</td>
                                        <td colspan="5"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">傷害類型</td>
                                        <td colspan="5">口.上班  口.下班途中 口.公出事故 口.執行職務 口.其他__________</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">是否為上下班合理路徑</td>
                                        <td colspan="5">口.是  口.否</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">是否為上下班合理時間</td>
                                        <td colspan="5">口.是  口.否</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">有無因處理私事而中斷或脫離應經之途徑</td>
                                        <td colspan="5">口.有處理私事而中斷或脫離應經之途徑（請於備註說明）  口.無處理私事而中斷或脫離應經之途徑</td>
                                    </tr>

                                    <tr>
                                        <td class="text-center"  rowspan="3">舉證資料(無須影印)</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">口.有：</td>
                                        <td colspan="4">口.報案三聯單 口.保險證明 口.交通裁決書 口.其他__________</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">口.無：</td>
                                        <td colspan="4">原因：</td>
                                    </tr>


                                    <tr>
                                        <td class="text-center"  rowspan="11">有無下列相關情事：</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end" colspan="3">1.領有駕駛車種之執照駕車：</br>（領有駕駛車種之執照駕車者，請附駕駛人駕照正、背面影本）</td>
                                        <td colspan="2">口 有    口 無</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end" colspan="3">2.受吊扣期間或吊銷駕駛執照處分駕車：</td>
                                        <td colspan="2">口 有    口 無</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end" colspan="3">3.經有燈光號誌管制之交岔路口違規闖紅燈：</td>
                                        <td colspan="2">口 有    口 無</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end" colspan="3">4.闖越鐵路平交道：</td>
                                        <td colspan="2">口 有    口 無</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end" colspan="3">5.酒精濃度超過規定標準駕車：</td>
                                        <td colspan="2">口 有    口 無</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end" colspan="3">6.吸食毒品、迷幻藥或管制藥品駕駛車輛：</td>
                                        <td colspan="2">口 有    口 無</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end" colspan="3">7.違規行駛高速公路路肩：</td>
                                        <td colspan="2">口 有    口 無</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end" colspan="3">8.不按遵行之方向行駛：</td>
                                        <td colspan="2">口 有    口 無</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end" colspan="3">9.在道路上競駛、競技、蛇行或以其他危險方式駕駛車輛：</td>
                                        <td colspan="2">口 有    口 無</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end" colspan="3">10.不依規定駛入來車道：</td>
                                        <td colspan="2">口 有    口 無</td>
                                    </tr>
   
                                    <tr>
                                        <td colspan="6">
                                            <span class="text-center">
                                                <h5><b>當事人 ___________ 同意上述描述符合實際情形</b></h5>
                                            </span>
                                            <span class="text-start">如欲請公傷假，請檢附醫生診斷證明書影本。</span></br>
                                            <span class="text-start">以上各項均由本人依照事實填具，如有不實，願負民事、刑事責任，並歸還溢領之勞保給付及工傷假天數，特此具結。</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">是否住院：</td>
                                        <td colspan="5">口 是    口 否</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"  rowspan="3">公傷假判定：</br>( 由環安人員填寫 )</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">口.是：</td>
                                        <td colspan="4"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">口.否：</td>
                                        <td colspan="4">原因：</td>
                                    </tr>

                                    <tr>
                                        <td class="text-end" colspan="4">
                                            <span>如環安判定為否時，需另行知會當事人，並簽名確認或備註相關原因。</span></br>
                                            <span>	判定為非公傷，當事人簽認 (如判斷為公傷則不需再簽認)：</span>
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>

                                </tbody>
                                
                            </table>
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


<?php include("../template/footer.php"); ?>