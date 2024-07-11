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

        // 在index表頭顯示清單：
        function show_formcase(){
            $pdo = pdo();
            $sql = "SELECT * FROM _formcase WHERE flag <> 'Off' ORDER BY id ASC";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute();
                $formcase = $stmt->fetchAll();
                return $formcase;
            }catch(PDOException $e){
                echo $e->getMessage();
            }
        }

   // 複製本頁網址藥用
   $up_href = (isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];   // 回上頁 // 回本頁

   // 決定表單開啟方式
   $action = (isset($_REQUEST["action"])) ? $_REQUEST["action"] : 'create';   // 有action就帶action，沒有action就新開單


   $formcases = show_formcase();

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
        /* a:hover {
            color: blue;
        } */
   </style>
</head>

<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-10 border rounded px-3 py-5 bg-light" >
                <div class="row" id="btn_list">
                    <!-- append button here -->
                </div>
                <hr>

                <div class="row">
                    <div class="col-12 text-center">
                        <button type="button" id="service_window_btn" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#service_window"><i class="fa-solid fa-circle-info"></i> 各廠聯絡窗口</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 模組 service window 20240319 -->
    <div class="modal fade" id="service_window" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header rounded bg-success text-white p-2 m-2">
                    <h5 class="modal-title"><i class="fa-solid fa-circle-info"></i> Service Window / 各廠聯絡窗口</h5>
                    <button type="button" class="btn-close border rounded mx-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-3 pt-1">
                    <div class="col-12 border rounded p-3">
                        <table id="service_window">
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
                
                <div class="modal-footer">
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="../../libs/openUrl/openUrl.js"></script>       <!-- 彈出子畫面 -->

<script>
    var formcases = <?=json_encode($formcases)?>;   // 取得表單清單
    var formcases_length = formcases.length;
    var icon_s = '<i class="';
    var icon_e = ' fa-8x"></i>';

    function make_btn(value_1){
        if(value_1.dcc_no){
            let int_b = "<button type='button' class='btn btn-outline-primary add_btn' value='form.php?dcc_no="+ value_1.dcc_no +"' onclick='openUrl(this.value)' ><div class='p-3'>" 
                + icon_s + value_1._icon + icon_e + "</br><hr>" + value_1.dcc_no + "</br>" + value_1.title + "</br><h4><b> - " + value_1.short_name +" - </b><h4></div></button>";
            return int_b;
        }else{
            return '<snap class="btn btn-outline-secondary">' + value_1.title + '</br>-- 無效json表單 --</snap>';
        }
    }

    $(function () {
        if(formcases){   
            // console.log(formcases_length, formcases_length % 3);                                                                       // confirm form_item is't empty
            for (const [key_1, value_1] of Object.entries(formcases)) {
                let int_btn;
                if (formcases_length === 1) {
                    int_btn = '<div class="col-12 text-center p-2">' + make_btn(value_1) + '</div>';
                } else if (formcases_length === 2) {
                    int_btn = '<div class="col-6 text-center p-2">' + make_btn(value_1) + '</div>';
                } else if (formcases_length === 3) {
                    int_btn = '<div class="col-4 text-center p-2">' + make_btn(value_1) + '</div>';
                } else {
                    // 如果formcases_length大於4，則每個元素佔用1/4的寬度
                    int_btn = '<div class="col-4 text-center p-2">' + make_btn(value_1) + '</div>';
                }
                // 渲染form
                $('#btn_list').append(int_btn);    

            }
        }
    })

</script>

<?php include("../template/footer.php"); ?>
