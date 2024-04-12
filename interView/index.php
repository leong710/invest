<?php
   require_once("../pdo.php");
   require_once("../sso.php");
   require_once("../user_info.php");
//    require_once("../formcase/function.php");
   accessDenied($sys_id);

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
        a:hover {
            color: blue;
        }
   </style>
</head>

<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-10 border rounded px-3 py-5 bg-light" >
                <div class="row" id="btn_list">
                </div>
            </div>
        </div>
    </div>
</body>

<script>
    var formcases = <?=json_encode($formcases)?>;   // 取得表單清單
    var formcases_length = formcases.length;

    function make_btn(value_1){
        var int_b = '<a class="btn btn-outline-primary " href="form.php?dcc_no='+ value_1.dcc_no +'" >' + value_1._icon + '</br>' + value_1.title + '</a>' 
        return int_b;
    }

    $(function () {
        if(formcases){                                                                          // confirm form_item is't empty
            for (const [key_1, value_1] of Object.entries(formcases)) {
                if(formcases_length % 3 == 0){
                    int_btn = '<div class="col-12 col-md-4 text-center p-2">' + make_btn(value_1) + '</div>';
                }else if(formcases_length % 2 == 0){
                    int_btn = '<div class="col-12 col-md-6 text-center p-2">' + make_btn(value_1) + '</div>';
                }else{
                    int_btn = '<div class="col-12 text-center p-2">' + make_btn(value_1) + '</div>';
                }
                // 渲染form
                $('#btn_list').append(int_btn);    

            }
        }
    })

</script>

<?php include("../template/footer.php"); ?>
