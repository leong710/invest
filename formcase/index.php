<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    require_once("../user_info.php");
    accessDeniedAdmin($sys_id);

    // 調整flag ==> 20230712改用AJAX
    $formcases = show_formcase();

    $icon_s = '<i class="';
    $icon_e = ' fa-5x"></i>';
?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<head>
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <script src="../../libs/jquery/jquery.mloading.js"></script>
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
    <script src="../../libs/jquery/mloading_init.js"></script>
        <link rel="stylesheet" type="text/css" href="../../libs/dataTables/jquery.dataTables.css">  <!-- dataTable參照 https://ithelp.ithome.com.tw/articles/10230169 --> <!-- data table CSS+JS -->
        <script type="text/javascript" charset="utf8" src="../../libs/dataTables/jquery.dataTables.js"></script>
</head>

<div class="container my-2">
    <div class="row justify-content-center">
        <div class="col-xl-12 col-12 border rounded bg-white p-4 ">
            <div class="row">
                <div class="col-md-6 pb-0">
                    <div>
                        <h5>表單管理 列表 - 共 <?php echo count($formcases);?> 筆</h5>
                    </div>
                </div>
                <div class="col-md-6 pb-0 text-end">
                    <a href="https://fontawesome.com/v6/icons/" target="_blank" title="fontawesome" class="btn btn-info text-white"><i class="fa-solid fa-font-awesome"></i> fontawesome</a>
                    <?php if($sys_role <= 1){ ?>
                        <button type="button" id="add_formcase_btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit_modal" onclick="add_module('formcase')" > <i class="fa fa-plus"></i> 新增表單</button>
                    <?php } ?>
                    <a href="index.php" title="回上層列表" class="btn btn-secondary"><i class="fa fa-external-link" aria-hidden="true"></i> 返回管理</a>
                </div>
            </div>
            <hr>
            <!-- 分類列表 -->
            <div class="row">
                <div class="col-12 pt-0">
                    <table class="table table-striped table-hover" id="formcase_list">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>_type / title</th>
                                <th>dcc_no / short_name</th>
                                <th>_icon</th>
                                <th>flag</th>
                                <th>created/updated</th>
                                <th>updated_user/action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($formcases as $formcase){ ?>
                                <tr>
                                    <td><?php echo $formcase["id"];?></td>
                                    <td><?php echo $formcase["_type"]."</br>".$formcase["title"];?></td>
                                    <td><?php echo $formcase["dcc_no"]."</br>".$formcase["short_name"];?></td>
                                    <td class="text-primary"><?php echo $icon_s.$formcase["_icon"].$icon_e;?></td>
                                    <td><button type="button" name="_formcase" id="<?php echo $formcase['id'];?>" value="<?php echo $formcase['flag'];?>"
                                            class="btn btn-sm btn-xs flagBtn <?php echo $formcase['flag'] == 'On' ? 'btn-success':'btn-warning';?>"><?php echo $formcase['flag'];?></button>
                                    </td>
                                    <td><?php echo $formcase["created_at"]."</br>".$formcase["updated_at"];?></td>
                                    <td><?php echo $formcase["updated_user"]."&nbsp";?>    
                                        <button type="button" id="update_formcase_btn" value="<?php echo $formcase['id'];?>" class="btn btn-sm btn-xs btn-info" 
                                            data-bs-toggle="modal" data-bs-target="#edit_modal" onclick="edit_module('formcase',this.value)" >編輯</button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 模組 編輯、新增-->
<div class="modal fade" id="edit_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" aria-modal="true" role="dialog" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border rounded p-3 m-2">
                <h5 class="modal-title"><span id="modal_action"></span>表單</h5>
                <form action="process.php" method="post">
                    <input type="hidden" name="id" id="formcase_delete_id">&nbsp&nbsp&nbsp&nbsp&nbsp
                    <span id="modal_delect_btn" class="<?php echo ($sys_role == 0) ? "":" unblock ";?>"></span>
                </form>
                <button type="button" class="btn-close border rounded mx-1" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="process.php" method="post" enctype="multipart/form-data" onsubmit="this.formcase_dcc_no.disabled=false,this.dcc_no.disabled=false" >
                <div class="modal-body px-3">
                    <div class="row">
                        
                        <div class="col-12 py-1">
                            <div class="form-floating">
                                <input type="text" name="_type" id="formcase__type" class="form-control" required placeholder="表單代號">
                                <label for="formcase__type" class="form-label">_type/表單代號：<sup class="text-danger"> *</sup></label>
                            </div>
                        </div>

                        <div class="col-12 py-1">
                            <div class="form-floating">
                                <input type="text" name="title" id="formcase_title" class="form-control" required placeholder="表單名稱">
                                <label for="formcase_title" class="form-label">title/表單名稱：<sup class="text-danger"> *</sup></label>
                            </div>
                        </div>

                        <div class="col-12 py-1">
                            <div class="form-floating">
                                <input type="text" name="short_name" id="formcase_short_name" class="form-control" required placeholder="簡稱">
                                <label for="formcase_short_name" class="form-label">short_name/簡稱：<sup class="text-danger"> *</sup></label>
                            </div>
                        </div>

                        <div class="col-12 py-1">
                            <div class="col-12 border rounded">
                                <div class="form-floating">
                                    <input type="text" name="dcc_no" id="formcase_dcc_no" class="form-control" readonly placeholder="套用文件">
                                    <label for="formcase_dcc_no" class="form-label">dcc_no/套用json文件：</label>
                                </div>
    
                                <div class="input-group">
                                    <input type="file" id="dcc_no" name="upload_file" class="form-control mb-0" accept=".json" placeholder="上傳檔案">
                                    <!-- <button type="button" class="btn btn-outline-success" onclick="uploadFile('dcc_no')">Upload</button> -->
                                    <!-- <button type="button" class="btn btn-outline-danger" onclick="unlinkFile('dcc_no')">Delete</button> -->
                                </div>
                            </div>
                        </div>

                        <div class="col-12 py-1">
                            <div class="form-floating">
                                <input type="text" name="_icon" id="formcase__icon" class="form-control" placeholder="圖像代號">
                                <label for="formcase__icon" class="form-label">_icon/fa圖像代號：</label>
                            </div>
                        </div>

                        <div class="col-12 py-1">
                            <table>
                                <tr>
                                    <td style="text-align: right;">
                                        <snap for="flag" class="form-label">flag/顯示開關：</snap>
                                    </td>
                                    <td style="text-align: left;">
                                        <input type="radio" name="flag" value="On" id="formcase_On" class="form-check-input" checked >&nbsp
                                        <label for="formcase_On" class="form-check-label">On</label>
                                    </td>
                                    <td style="text-align: left;">
                                        <input type="radio" name="flag" value="Off" id="formcase_Off" class="form-check-input">&nbsp
                                        <label for="formcase_Off" class="form-check-label">Off</label>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-12 text-end py-0" id="formcase_info"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="text-end">
                        <input type="hidden" name="id" id="formcase_edit_id" >
                        <input type="hidden" name="updated_user" value="<?php echo $auth_cname;?>">
                            <span id="modal_button" class="<?php echo ($sys_role <= 1) ? "":" unblock ";?>"></span>
                        <input type="reset" class="btn btn-info" id="reset_btn" value="清除">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="../../libs/aos/aos.js"></script>
<script src="../../libs/aos/aos_init.js"></script>
<script src="../../libs/sweetalert/sweetalert.min.js"></script>

<script>

    var formcase        = <?=json_encode($formcases)?>;                                       // 引入formcases資料
    var formcase_item   = ['id', '_type', 'title', 'short_name', 'dcc_no', '_icon', 'flag'];                // 交給其他功能帶入 delete_cate_id

</script>
<script src="formcase.js?v=<?=time()?>"></script>

<?php include("../template/footer.php"); ?>