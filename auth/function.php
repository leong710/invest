<?php

// // // user-CRUD

    function storeUser($request){
        $pdo = pdo();
        extract($request);

        $swal_json = array(                                 // for swal_json
            "fun"       => "storeUser",
            "content"   => "新增使用者 -- "
        );

        $user = trim($user);
        // 檢查user是否已經被註冊
            $sql_check = "SELECT * FROM _users WHERE user = ?";
            $stmt_check = $pdo -> prepare($sql_check);
            $stmt_check -> execute([$user]);
            if($stmt_check -> rowCount() >0){     
                // 確認帳號是否已經被註冊掉，用rowCount最快~不要用fetch
                $swal_json["action"]   = "error";
                $swal_json["content"] .= '儲存失敗';
                echo "<script>alert('帳號已存在，請重新選擇帳號~')</script>";
                return $swal_json;
            }

        if(!isset($role)){
            $role = 2;
        }
        if(!isset($idty)){
            $idty = 1;
        }
        if(!empty($sfab_id)){
            $sfab_id = implode(",",$sfab_id);       //副fab是陣列，要儲存前要轉成字串
        }else{
            $sfab_id = "";
        }

        $sql = "INSERT INTO _users(emp_id, user, cname, sign_code, role, idty, created_at)VALUES(?,?,?,?,?,?,now())";
        $stmt = $pdo -> prepare($sql);
        try{
            $stmt -> execute([$emp_id, $user, $cname, $sign_code, $role, $idty]);
            $swal_json["action"]   = "success";
            $swal_json["content"] .= '儲存成功';
        }catch(PDOException $e){
            echo $e -> getMessage();
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '儲存失敗';
        }
        return $swal_json;
    }

    function editUser($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT * FROM _users WHERE user = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$user]);
            $user = $stmt->fetch();
            return $user;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function updateUser($request){
        $pdo = pdo();
        extract($request);

        $swal_json = array(                                 // for swal_json
            "fun"       => "updateUser",
            "content"   => "編輯使用者 -- "
        );

        $user = trim($user);
        if(!empty($sfab_id)){
            $sfab_id = implode(",",$sfab_id);       //副pm是陣列，要儲存前要轉成字串
        }else{
            $sfab_id = "";
        }

        $sql = "UPDATE _users SET emp_id=?, user=?, cname=?, sign_code=?, role=?, idty=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$emp_id, $user, $cname, $sign_code, $role, $idty, $id]);
            $swal_json["action"]   = "success";
            $swal_json["content"] .= '儲存成功';
        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '儲存失敗';
        }
        return $swal_json;
    }
    
    function deleteUser($request){
        $pdo = pdo();
        extract($request);

        $swal_json = array(                                 // for swal_json
            "fun"       => "deleteUser",
            "content"   => "刪除使用者 -- "
        );

        $sql = "DELETE FROM _users WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $swal_json["action"]   = "success";
            $swal_json["content"] .= '刪除成功';
        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '刪除失敗';
        }
        return $swal_json;
    }


    function showAllUsers($request){
        $pdo = pdo();
        // extract($request);
        $sql = "SELECT _users.*
                FROM _users ";

        switch($request){
            case "none":
                $sql .= " WHERE _users.role <= 2 AND _users.role <> '' ";
                break;
            case "new":
                $sql .= " WHERE _users.role > 2 ";
                break;
            case "pause":
                $sql .= " WHERE _users.role = '' ";
                break;
            default:
                break;
        }
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY _users.role, _users.id ASC";
                // CAST(_users.pm_id AS UNSIGNED) = 把文字型態轉成數字
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $users = $stmt->fetchAll();
            return $users;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function show_site(){
        $pdo = pdo();
        $sql = "SELECT _site.* 
                FROM _site
                WHERE _site.flag = 'On'
                ORDER BY _site.id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $sites = $stmt->fetchAll();
            return $sites;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    
    function show_fab(){
        $pdo = pdo();
        $sql = "SELECT _fab.* 
                FROM _fab
                -- WHERE _fab.flag = 'On'
                ORDER BY _fab.id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $fabs = $stmt->fetchAll();
            return $fabs;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // 20231004-改用msSQL-hrDB -R
    function show_dept(){
        $pdo = pdo_hrdb();
        $sql = "SELECT DISTINCT dp.* , d1.OSSTEXT AS up_sign_dept , u.cname AS dept_sir
                    FROM DEPT dp
                    LEFT JOIN HCM_VW_DEPT08 d1 ON dp.up_dep = d1.OSDEPNO
                    LEFT JOIN STAFF u ON dp.emp_id = u.emp_id 
                    ORDER BY dp.sign_code ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $depts = $stmt->fetchAll();
            return $depts;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

?>