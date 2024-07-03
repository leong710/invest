<?php

    $stmt_arr = array();    // 初始查詢陣列
    $a = null;
    array_push($stmt_arr, $a);


    echo empty($a) ? "空" : "不空" ;
    echo "</br><pre>";
    print_r($a);
    echo "</pre>";


                    $variables = [
                        'site_id'         => '1',
                        'fab_id'          => null,
                        'anis_no'         => null,
                        'created_emp_id'  => null,
                        'short_name'      => null,
                        'idty'            => '10',
                        'created_at_form' => '2024-06-01',
                        'created_at_to'   => null
                    ];
                    
                    foreach ($variables as $key => $value) {
                        $$key = !empty($value) ? $value : null;
                    }

                    $sql = "SELECT _d.anis_no , _d.idty , _d.created_emp_id , _d.created_at   , _fc.short_name , _s.site_title , _f.fab_title , _l.local_title FROM `_document` _d
LEFT JOIN _local _l ON _d.local_id = _l.id LEFT JOIN _fab _f ON _d.fab_id = _f.id LEFT JOIN _site _s ON _f.site_id = _s.id LEFT JOIN _formcase _fc ON _d.dcc_no = _fc.dcc_no ";

                    $conditions = [];
                    if ($site_id != 'All' && !empty($site_id)) {
                        $conditions[] = "_s.id = ?";
                        $stmt_arr[] = $site_id;
                    }
                    if ($fab_id != 'All' && !empty($fab_id)) {
                        $conditions[] = "_d.fab_id = ?";
                        $stmt_arr[] = $fab_id;
                    }
                    if (!empty($anis_no)) {
                        $conditions[] = "_d.anis_no = ?";
                        $stmt_arr[] = $anis_no;
                    }
                    if (!empty($created_emp_id)) {
                        $conditions[] = "_d.created_emp_id = ?";
                        $stmt_arr[] = $created_emp_id;
                    }
                    if (!empty($short_name)) {
                        $conditions[] = "_fc.short_name = ?";
                        $stmt_arr[] = $short_name;
                    }
                    if (!empty($idty)) {
                        $conditions[] = "_d.idty = ?";
                        $stmt_arr[] = $idty;
                    }
                    if (!empty($created_at_form) && !empty($created_at_to)) {
                        $conditions[] = "_d.created_at BETWEEN ? AND ?";
                        $stmt_arr[] = $created_at_form;
                        $stmt_arr[] = $created_at_to;
                    } elseif (!empty($created_at_form)) {
                        $conditions[] = "_d.created_at >= ?";
                        $stmt_arr[] = $created_at_form;
                    } elseif (!empty($created_at_to)) {
                        $conditions[] = "_d.created_at <= ?";
                        $stmt_arr[] = $created_at_to;
                    }
                    
                    if (!empty($conditions)) {
                        $sql .= ' WHERE ' . implode(' AND ', $conditions);
                    }
                    // 後段-堆疊查詢語法：加入排序
                    $sql .= " ORDER BY _d.fab_id, _d.local_id, _d.created_at DESC ";     //ORDER BY _d.fab_id, _d.local_id, _d.a_dept, case_count DESC

                    echo "</br><pre>";
                    print_r($sql);
                    echo "</pre>";