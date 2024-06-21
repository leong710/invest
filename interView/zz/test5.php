<?php

    $value = '{"emp_id":"17005107","cname":"\u99ac\u5409\u5152","oftext":"9O432505\/\u53f0\u5357MOD\u88fd\u9020\u5ee0\/\u88fd\u9020\u5de5\u7a0b\u4e8c\u90e8\/\u88fd\u9020\u8ab2","cstext":"\u52a9\u7406\u6280\u8853\u54e1","hired":"2023-04-01","rload":"\u4f30\u7b97\u7d04\uff1a1 \u5e74 0 \u500b\u6708 0 \u5929","NATIO":["\u5916\u7c4d"],"GESCH":["\u5973\u6027"],"emp_type":["\u6280\u8853\u54e1"],"years_level":["20u-25d"],"a_day":"2024-04-01T08:30","a_location":"test_local","a_work_s":"2024-04-01T07:30","a_work_e":"2024-04-01T19:30","s2_combo_01":["\u65e5\u73ed"],"s2_combo_02":["\u65e5\u9593"],"s2_combo_03":["\u81ea\u6454"],"s2_combo_04":["\u5426"],"a_description":"test session3","s4_text_a":"","s6a_text_other":""}';

    $dec = json_decode($value);

    echo "<pre>";
    print_r($dec);
    echo "</pre>";
