<?php
    // service window
    $swFile = "..\local\service_window.json";

    if(!file_exists($swFile)){          //如果檔案不存在
        $sw_json = '{"PPE系統Owner":[{"cname":"梁修豪","email":"HOWARD76.LIANG@INNOLUX.COM","tel_no":"5014-32114"}],"tnESH it":[{"cname":"陳建良","email":"LEONG.CHEN@INNOLUX.COM","tel_no":"5014-42117"}] }';    // 預設值
        $swf = fopen($swFile,"w");      //開啟檔案
        fputs($swf, $sw_json);          //初始化sw
        fclose($swf);                   //關閉檔案
    
    } else{                             //取回當前sw的值
        $swf = fopen($swFile,"r");
        $sw_json = trim(fgets($swf));
        fclose($swf);
    }

?>