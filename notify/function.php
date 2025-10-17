<?php

// // // mapp init -- end
    function check_ip($request){
        extract($request);
        $local_pc = array(                      // 建立local_pc查詢陣列
            '127.0.0.1'   => '7132e2545d301024dfb18da07cceccedb41b4864',   // 127.0.0.1
            'tw059332n'   => 'c3cbef3ccc9a63e2132d5afebce8af281515e5ab',   // tw059332n-10.53.202.173 (default)
            // 'tw074163p'   => 'c2cb37acb2c9eb3e4068ac55d278ac7d9bea85e3'    // tw074163p-10.53.90.114
            'tw074163p'   => '0fedf000e3dc270deb726179e16ee700dbd5b46c'    // tw074163p-10.53.203.97
        );
        $ip = sha1(md5($ip));
        
        if(in_array($ip, $local_pc)){
            return true;
        }else{
            return false;
        }
    }


