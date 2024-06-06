<?php

    // $offLineDir = '../doc_pdf/offLine/';           // 過度路徑，submit後再搬移到正是路徑
    // $unknow_path = "FAB1棟/廠外交通事故/2024/";

    // $unknow_path_arr = array_filter(explode("/",$unknow_path));

    // foreach($unknow_path_arr as $key){
    //     $offLineDir .= $key."/";
    //     echo "</br>".$offLineDir;
    // }

    // $pdf_file = "../doc_pdf/temp/1_FAB1棟_交通事故_ANIS20240603114700123.pdf";

    // echo "<per>";
    // print_r(pathinfo($pdf_file));
    // echo "</per>";

    // $to_path_arr = array (
    //     0 => "fab_title",
    //     1 => "short_name",
    //     2 => "case_year"
    // );

    // foreach($to_path_arr as $key){
    //     if($key == "short_name"){
    //         continue;
    //     }
    //     echo "</br>".$key;
    // }


    // $path_arr = ["fab_title", "short_name", "case_year"];
    // $path_o = [];
    // // foreach($path_arr as $path_key)

    // for($i=1; $i <= count($path_arr); $i++ ){
    //     echo "</br>".count($path_arr)." - ".$i;

    //     $path_o[$i] = $i;
    // }

    // echo "</br>";

    $path_arr  = [
        0 => "fab_title", 
        1 => "short_name", 
        2 => "case_year"
    ];

    foreach($path_arr as $value){             // 逐筆繞出來
        echo "</br> => ".$value;
    }


