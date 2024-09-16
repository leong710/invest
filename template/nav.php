<?php
    require_once("../user_info.php");
    require_once("function.php");

    $webroot = "..";
    
    // init

?>
    <style>
        .app-header {
            position: relative;
            -ms-flex-direction: row;
            flex-direction: row;
            height: 55px;
            padding: 0;
            margin: 0;
            background-color: #fff;
            border-bottom: 1px solid #c8ced3;
        }
        .app-header .nav-item {
            position: relative;
            min-width: 50px;
            margin: 0;
            text-align: center;
            color: #7d7d7d;
        }
        .app-header .nav-item .nav-link {
            padding-top: 1;
            padding-bottom: 0;
            background: 0;
            border: 0;
        }
        .navbar-nav .nav-link {
            padding-right: 0;
            padding-left: 0;
            color: #73818f;
        }
        .sysTitle {
            font-weight: bold;
            font-size: 20px;
            margin-top: -5px;
        }
        .h6, h6 {
            margin-bottom: .5rem;
            font-family: inherit;
            font-weight: 500;
            line-height: 1.2;
            color: inherit;
            font-size: .875rem;
        }
    </style>

<nav class="navbar navbar-expand-lg navbar-light bg-light app-header">
    <div class="container-fluid">

        <a class="navbar-brand" href="<?php echo $webroot;?>/">
            <img class="navbar-brand-full pl-3" src="../template/tnesh_logo.png" height="40" width="100" alt="tnESH Logo">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <ul class="navbar-nav me-auto sysTitle">
            <li class="nav-item px-1">
                <a class="nav-link" href="<?php echo $webroot;?>/" class="p-0">事故訪談系統</a>
                <h6 id="hFullName">Accident Invest</h6>
            </li>
        </ul>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto   my-2 my-lg-0 navbar-nav-scroll">
                <?php if($sys_auth){ ?>
                    <!-- <li class="nav-item"><a class="nav-link active" aria-current="page" href="#"><i class="fa-regular fa-square-plus"></i>&nbsp外層Link</a></li> -->
                    <!-- <li class="nav-item"><a class="nav-link active" aria-current="page" href="<php echo $webroot;?>/interView/"><i class="fa fa-edit"></i>&nbsp填寫訪談單</a></li> -->
                    <!-- 下拉式選單 -->
                    <?php if($sys_role >= 0){ 
                        if($sys_role <= 2.5 ){ ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link active dropdown-toggle" id="navbarDD_2" role="button" data-bs-toggle="dropdown" aria-expanded="false" >
                                    <i class="fas fa-warehouse"></i>&nbsp表單應用<span class="badge rounded-pill bg-danger"></span></a>
                                              
                                <ul class="dropdown-menu" aria-labelledby="navbarDD_2">
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/caseList/"><i class="fa-solid fa-list-check"></i>&nbsp<b>訪談清單</b></a></li>
                                    <?php if($sys_role <= 2 ){ ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="<?php echo $webroot;?>/analyze/"><i class="fa-solid fa-chart-column"></i>&nbsp<b>統計(試作版)</b></a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } 
                        
                        if($sys_role <= 1 ){ ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" id="navbarDD_3" role="button" data-bs-toggle="dropdown" aria-expanded="false" >
                                    <i class="fa-solid fa-gear"></i>&nbsp管理員專區</a>
                                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDD_3">
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/local/"><i class="fa-solid fa-location-dot"></i>&nbsp廠區清單管理</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/formcase/"><i class="fa-solid fa-sliders"></i>&nbsp<i class="fas fa-th-large"></i>&nbsp表單管理</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/autolog/"><i class="fa-regular fa-rectangle-list"></i>&nbsp發報記錄管理</a></li>
                                    <?php if($sys_role == 0 ){ ?>
                                        <li><a class="dropdown-item" href="<?php echo $webroot;?>/notify/"><i class="fa-solid fa-comment-sms"></i>&nbsp待通報清單統計</a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php }
                    } ?>
                <?php } ?>
            </ul>
            
            <!-- .navbar-toggler, .navbar-collapse 和 .navbar-expand{-sm|-md|-lg|-xl} -->
            <ul class="navbar-nav ms-auto   my-2 my-lg-0 navbar-nav-scroll">
                <?php if(!$sys_auth){
                    echo "<li class='nav-item mx-1'><a href='{$webroot}/auth/login.php' class=''><i class='fa fa-sign-in' aria-hidden='true'></i> 登入</a></li>";
                    // echo "<li class='nav-item mx-1 disabled'><a href='{$webroot}/auth/register.php' class='btn btn-success'>註冊</a></li>";
                } else { ?>
                    <!-- 下拉式選單 -->
                    <li class="nav-item dropdown">
                        <a class="nav-link active dropdown-toggle" href="#" id="navbarDD_reg" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo (isset($auth_pass) && $auth_pass == "ldap") ? "<i class='fa fa-user' aria-hidden='true'></i>" : "<i class='fa fa-user-secret' aria-hidden='true'></i>";
                                  echo "&nbsp".$auth_cname .($sys_auth ? '<sup class="text-danger"> - '.$sys_role.'</sup>':'').'&nbsp你好'; 
                            ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDD_reg">
                            <?php if($sys_auth){  
                                    if($sys_role <= 1){
                                        echo "<li><a class='dropdown-item' href='{$webroot}/auth/edit.php?user={$auth_user}'><i class='fa fa-user-circle' aria-hidden='true'></i> 編輯User資訊</a></li>";
                                        echo "<li><hr class='dropdown-divider'></li>";
                                    } 
                                    if($sys_role <= 1){
                                        echo "<li><a class='dropdown-item' href='{$webroot}/auth/'><i class='fa fa-address-card' aria-hidden='true'></i> 管理使用者</a></li>";
                                        echo "<li><hr class='dropdown-divider'></li>";
                                    } 
                                } else {
                                    echo "<li><a class='dropdown-item' href='{$webroot}/auth/login.php'><i class='fa fa-sign-in' aria-hidden='true'></i> SSO登入</a></li>";
                                } 
                                echo "<li><a class='dropdown-item' href='{$webroot}/auth/logout.php' class=''><i class='fa fa-sign-out' aria-hidden='true'></i> 登出</a></li>";
                            ?>
                        </ul>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>
