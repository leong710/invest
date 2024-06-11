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

    // $path_arr  = [
    //     0 => "fab_title", 
    //     1 => "short_name", 
    //     2 => "case_year"
    // ];

    // foreach($path_arr as $value){             // 逐筆繞出來
    //     echo "</br> => ".$value;
    // }


    function confirm_odd($request){
        $_content = (array) json_decode($request);
        $s8_combo_8 = isset($_content["s8_combo_8"]) ? $_content["s8_combo_8"] : [];    // 取得損限工問項答案
        $result = [];

        if(in_array("限工", $s8_combo_8)){
            $a_day  = isset($_content["a_day"]) ? $_content["a_day"] : null;            // 事故發生日
            if($a_day !== null){
                // $datetime_obj = DateTime::createFromFormat("Y-m-d\TH:i", $a_day);    // 创建 DateTime 对象，并指定原始时间格式
                // $a_day = $datetime_obj->format("Y-m-d");                             // 将时间格式化为目标格式 
                $due_day = new DateTime($a_day);                                        // 將$a_day轉換為DateTime物件
                $due_day->modify('+1 month');                                           // 添加一個月
                $due_day->setDate($due_day->format('Y'), $due_day->format('m'), 5);     // 設置日期為5號
                $due_day = $due_day->format('Y-m-d');                                   // 將日期格式化為字符串並返回

            }else{
                $due_day = null;
            }

            $result = [
                // 'a_day'=> $a_day,            // 事故發生日
                'od'      => "損工 -- 需要申報", // 判斷訊息
                'due_day' => $due_day,          // 申報截止日
                'o_day'   => null               // 申報日期
            ];
            
        }else{
            $result["od"] = "限工 -- 不需要申報";
        }
        return $result;
    }


    $_content = '{"emp_id":"17005107","cname":"\u99ac\u5409\u5152","cstext":"\u52a9\u7406\u6280\u8853\u54e1","a_day":"2024-05-01T08:00","hired":"2023-04-01"
                ,"rload":"\u4f30\u7b97\u7d04\uff1a1 \u5e74 0 \u500b\u6708 0 \u5929","a_work_s":"2024-04-01T07:30","a_work_e":"2024-04-01T19:30","a_location":"test_local"
                ,"a_description":"test session3","s4_combo_1":["\u4e0a\u73ed\u9014\u4e2d"],"s4_combo_4":["\u7121"]
                ,"s4_combo_5":["\u5831\u6848\u4e09\u806f\u55ae","\u8a3a\u65b7\u8b49\u660e\u66f8","\u5176\u4ed6","\u73fe\u5834\u7167\u7247"]
                ,"s6_combo_2":["\u662f"],"remark":"test session7","s8_combo_7":["\u5176\u4ed6","\u9999\u8549\u76ae"],"s8_combo_6":["\u5176\u4ed6","\u8dd1\u5f97\u5feb"]
                ,"s8_combo_8":["\u9650\u5de5","30"],"s8_textarea_a":"a_info","s4_combo_2":["\u662f"],"s4_combo_3":["\u662f"],"s5_combo_1":["\u7121"]
                ,"s5_combo_2":["\u7121"],"s5_combo_3":["\u7121"],"s5_combo_4":["\u7121"],"s5_combo_5":["\u7121"],"s5_combo_6":["\u7121"],"s5_combo_7":["\u7121"]
                ,"s5_combo_8":["\u7121"],"s5_combo_9":["\u7121"],"s5_combo_10":["\u7121"],"s6_combo_1":["\u5426"],"s8_combo_1":["\u5916\u7c4d"],"s8_combo_2":["\u5973\u6027"]
                ,"s8_combo_4":["\u65e5\u73ed"],"s8_combo_5":["\u65e5\u9593"],"s8_combo_3":["20u-25d"]}';
    // $_content = (array) json_decode($_content);

    // $s8_combo_8 = isset($_content["s8_combo_8"]) ? $_content["s8_combo_8"] : [];    // 取得損限工問項答案
    // $result = [];

    // echo "<pre>";
    // print_r(($s8_combo_8));
    // echo "</pre>";

    // if(in_array("損工", $s8_combo_8)){
    //     echo "限工 -- 不需要申報";

    // }else{
    //     echo "損工 -- 需要申報";
    //     $a_day  = isset($_content["a_day"])      ? $_content["a_day"]      : null;  // 事故發生日
    //     if($a_day !== null){
    //         $datetime_obj = DateTime::createFromFormat("Y-m-d\TH:i", $a_day);       // 创建 DateTime 对象，并指定原始时间格式
    //         $a_day = $datetime_obj->format("Y-m-d");                                // 将时间格式化为目标格式 
                                          
    //         $due_day = new DateTime($a_day);                                          // 將$a_day轉換為DateTime物件
    //         $due_day->modify('+1 month');                                             // 添加一個月
    //         $due_day->setDate($due_day->format('Y'), $due_day->format('m'), 5);           // 設置日期為5號

    //         // $a_day_date     = new DateTime($a_day);
    //         // $toDay          = new DateTime();                                       // 取得今天日期
    //         // $interval       = $toDay->diff($o_day);
    //         // $remaining_days = $interval->days;                                      // 差距天數

    //         // // 判斷目標日期是否在今天之後
    //         // if ($o_day > $toDay) {
    //         //     $remaining_days = $remaining_days;
    //         // } else {
    //         //     $remaining_days = -$remaining_days;
    //         // }
    //         // $toDay = $toDay->format('Y-m-d');                                       // 將日期格式化為字符串並返回

    //         $due_day = $due_day->format('Y-m-d');                                       // 將日期格式化為字符串並返回
    //         $result = [
    //             'a_day'   => $a_day,        // 事故發生日
    //             'due_day' => $due_day,      // 申報截止日
    //             'o_day'   => null           // 申報日期
    //         ];
    //     }else{
    //         $result = [
    //             'a_day'   => $a_day,        // 事故發生日
    //             'due_day' => null,          // 申報截止日
    //             'o_day'   => null           // 申報日期
    //         ];
    //     }

        // echo "</br>今天：",$toDay;
        // echo "</br>a_day：",$a_day;
        // echo "</br>o_day：",$o_day;
        // echo "</br>remaining_days：",$remaining_days;
    // }

    $result = confirm_odd($_content);

    echo "<pre>";
    print_r($result);
    echo "</pre>";