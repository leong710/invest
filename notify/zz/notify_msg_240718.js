 
    // 子功能
        $(function () {
            // 在任何地方啟用工具提示框
            $('[data-toggle="tooltip"]').tooltip();
        })
        // fun_1 tab_table的顯示關閉功能
        function op_tab(tab_value){
            $("#"+tab_value+"_btn .fa-chevron-circle-down").toggleClass("fa-chevron-circle-up");
            var tab_table = document.getElementById(tab_value);
            if (tab_table && (tab_table.style.display === "none")) {
                tab_table.style.display = "table";
            } else {
                tab_table.style.display = "none";
            }
        }
        // fun_2 倒數 n秒自動關閉視窗功能
        function CountDown() {
            let delayTime = 1000;                   // 1000=1秒
            let i = 15;                             // 15次==15秒
            const loop = () => {
                if (i >= 0) {
                    document.getElementById("myMessage").innerHTML = "視窗關閉倒數 "+ i +" 秒";
                    setTimeout(loop, delayTime);
                } else {
                    // callback();                  // 要執行的程式
                    document.getElementById("myMessage").innerHTML = "視窗關閉！";
                    window.open('', '_self', '');
                    window.close();
                }
                i--;
            };
            loop();
        }
        // fun_3 延遲模組
        function delayedLoop(i, callback) {
            if(i==0 || i==null){
                i = 10;                                 // 10次==10秒
            }
            const loop = () => {
                if (i >= 0) {
                    document.getElementById("myMessage").innerHTML = "Fun: "+ callback +" 執行倒數 "+ i +" 秒";
                    setTimeout(loop, 1000);
                } else {
                    document.getElementById("myMessage").innerHTML = "Fun: "+ callback +" 執行！";
                    window[callback]();                 // 要執行的程式
                }
                i--;
            };
            loop();
        }

    // 主技能
        // 20240515 整理log記錄檔並轉拋toLog
        function swap_toLog(user_logs){
            // 打包整理Logs的陣列
            user_logs_obj = {
                thisDay  : thisToday,
                autoLogs : user_logs
            }
            user_logs_json = JSON.stringify(user_logs_obj);                                   // logs大陣列轉JSON字串
            toLog(user_logs_json);                                                            // *** call fun.step_2 寫入log記錄檔
        }
        // 20231213 寫入log記錄檔~
        function toLog(logs_msg){
            $.ajax({
                url      : '../autolog/log.php',
                method   : 'post',
                async    : false,
                dataType : 'json',
                data     : {
                    function : 'storeLog',
                    thisDay  : thisToday,
                    sys      : 'ppe',
                    logs     : logs_msg,
                    t_stamp  : ''
                },
                success: function(res){
                    toLog_result_check = true; 
                },
                error: function(res){
                    console.log("toLog -- error：", res);
                    toLog_result_check = false;
                }
            });
            return toLog_result_check;
        }
        // 20240314 將訊息推送到TN PPC(mapp)給對的人~
        function push_mapp(user_emp_id, mg_msg) {
            // return true;
            return new Promise((resolve, reject) => {
                $.ajax({
                    url:'http://tneship.cminl.oa/api/pushmapp/index.php',       // 正式2024新版
                    method:'post',
                    async: false,                                               // ajax取得數據包後，可以return的重要參數
                    dataType:'json',
                    data:{
                        uuid    : '3cd9a6fd-4021-11ef-9173-1c697a98a75f',       // invest
                        eid     : user_emp_id,                                  // 傳送對象
                        // eid     : '10008048',                                   // 傳送對象
                        message : mg_msg                                        // 傳送訊息
                    },
                    success: function(res){
                        var mapp_result_check = true; 
                        resolve(true);                                          // 成功時解析為 true 
                    },
                    error: function(res){
                        var mapp_result_check = false;
                        console.log("push_mapp -- error：",res);
                        reject(false);                                          // 失敗時拒絕 Promise
                    }
                });
                return mapp_result_check;
            });
        }
        // 20240314 將訊息郵件發送給對的人~
        function sendmail(user_email, int_msg1_title, mg_msg){
            // return true;
            return new Promise((resolve, reject) => {
                $.ajax({
                    url:'http://tneship.cminl.oa/api/sendmail/index.php',       // 正式2024新版
                    method:'post',
                    async: false,                                               // ajax取得數據包後，可以return的重要參數
                    dataType:'json',
                    data:{
                        uuid    : '3cd9a6fd-4021-11ef-9173-1c697a98a75f',       // invest
                        sysName : 'invest',                                     // 貫名
                        to      : user_email,                                   // 傳送對象
                        // to      : 'leong.chen@innolux.com',                     // 傳送對象
                        subject : int_msg1_title,                               // 信件標題
                        body    : mg_msg                                        // 訊息內容
                    },
                    success: function(res){
                        var mail_result_check = true; 
                        resolve(true);                                          // 成功時解析為 true 
                    },
                    error: function(res){
                        var mail_result_check = false;
                        console.log("send_mail -- error：",res);
                        reject(false);                                          // 失敗時拒絕 Promise
                    }
                });
                return mail_result_check;
            });
        }
        // 20240314 search user_empid return email
        function search_fun(fun, search){
            var result = null;

            if(!search || (search.length < 8)){
                // alert("查詢 工號/部門代號 字數最少 8 個字以上!!");
                $("body").mLoading("hide");
                return false;
            } 

            $.ajax({
             // url:'http://tneship.cminl.oa/hrdb/api/index.php',           // 正式舊版
             // url:'http://tneship.cminl.oa/api/hrdb/index.php',           // 正式2024新版
             url:'http://localhost/api/hrdb/index.php',                  // 正式2024新版
                method:'post',
                async : false,                                              // ajax取得數據包後，可以return的重要參數
                dataType:'json',
                data:{
                    uuid         : '3cd9a6fd-4021-11ef-9173-1c697a98a75f',  // invest
                    functionname : fun,                                     // 操作功能
                    emp_id       : search                                   // 查詢對象key_word  // 使用開單人工號查詢
                },
                success: function(res){
                    var obj_val = res["result"];
                    // 將結果進行渲染
                    if (obj_val != '') {

                        result = (fun == 'showStaff' && obj_val.comid2 != undefined) ? obj_val.comid2 : obj_val;
                        
                        return result;

                    }else{
                        console.log( fun +" 查無[ "+ search +" ]!!");
                    }
                },
                error(err){
                    console.log( fun +"search error: ", err);
                }
            })
            return result;
        }
        // 20240314 配合await將swal外移
        function show_swal_fun(push_result){

            // swal組合訊息，根據發送結果選用提示內容與符號
            var swal_title = '領用申請單-發放訊息';
            
            if((push_result['email']['error'] == 0) && (push_result['email']['success'] != 0)){
                var swal_content = '寄送成功：'+ push_result['email']['success'];
                var swal_action = 'success';
            } else if((push_result['email']['error'] != 0) && (push_result['email']['success'] == 0)){
                var swal_content = '寄送失敗：'+ push_result['email']['error'];
                var swal_action = 'error';
            } else {
                var swal_content = '寄送成功：'+ push_result['email']['success'] +'、錯誤：'+ push_result['email']['error'];
                var swal_action = 'warning';
            }

            if((push_result['mapp']['error'] == 0) && (push_result['mapp']['success'] != 0)){
                swal_content += ' 、 推送成功：'+ push_result['mapp']['success'];
                swal_action = 'success';
            } else if((push_result['mapp']['error'] != 0) && (push_result['mapp']['success'] == 0)){
                swal_content += ' 、 推送失敗：'+ push_result['mapp']['error'];
                swal_action = 'error';
            } else {
                swal_content += ' 、 推送成功：'+ push_result['mapp']['success'] +'、錯誤：'+ push_result['mapp']['error'];
                swal_action = 'warning';
            }

            $("body").mLoading("hide");                                                       // 關閉mLoading圖示
            swal(swal_title ,swal_content ,swal_action, {timer:5000});                        // popOut swal + 自動關閉
        }

        // 2024/05/09 notify_insign()整理訊息、發送、顯示發送結果。
        function notify_insign(){
            mloading("show");                                                       // 啟用mLoading
            // mloading();
            var totalUsers = 0;                                                     // 总用户数量
            var completedUsers = 0;                                                 // 已完成发送操作的用户数量
            var user_logs = [];                                                     // 宣告儲存Log用的 大-陣列Logs
            $('#result').empty();                                                   // 清空執行訊息欄位

            Object.keys(lists_obj).forEach((list_key)=>{
                let a_list = lists_obj[list_key];
                if(a_list && a_list.length >= 1){                                   // 有件數 > 0 舊執行通知
                    var promises = [];                                              // 存储所有异步操作的 Promise
                    totalUsers += a_list.length;                                    // 总用户数量
    
                    // step.0 逐筆把清單繞出來
                    Object(a_list).forEach(function(user){
                        var user_emp_id = String(user['emp_id']).trim();            // 定義 user_emp_id + 去空白
                        var user_email  = String(user['email']).trim();             // 定義 user_email + 去空白
                        var emergency_count = Number(user['ppty_3_waiting']) + Number(user['ppty_3_reject']) + Number(user['ppty_3_collect']);
                        var user_mapp   = (emergency_count > 0) ? true : false;     // 當3急件數量!=0，就使用mapp加急通知!

                        // step.1 確認工號是否有誤
                        if(!user_emp_id || (user_emp_id.length < 8)){
                            // alert("工號字數有誤 !!");                            // 避免無人職守時被alert中斷，所以取消改console.log
                            // $("body").mLoading("hide");
                            console.log("工號字數有誤：", user_emp_id);
                            push_result['mapp']['error']++; 
                            push_result['email']['error']++; 
                            return false;
    
                        } else {
                            // 宣告儲存Log內的單筆 小-物件log
                            var user_log = { 
                                emp_id          : user['emp_id'],
                                cname           : user['cname'],
                                email           : user_email,

                                issue_waiting   : user['issue_waiting'],
                                receive_waiting : user['receive_waiting'],
                                waiting         : user['total_waiting'],

                                issue_reject    : user['issue_reject'],
                                receive_reject  : user['receive_reject'],
                                Reject          : user['total_reject'],

                                collect         : user['total_collect'],
                                emergency       : emergency_count
                            }
                            // step.1-1 組合訊息文字
                            var mg_msg  = int_msg1 + "\n"; //+ " (" + user['cname'] + ")";
                            // 定義每一封mail title
                                var int_msg1_title = int_msg1 + " (";
                            
                            // 待簽核 waiting
                            if(user['total_waiting'] > 0){
                                mg_msg += "\r\n";
                                mg_msg += int_msg2 + user['total_waiting'] + int_msg3 + '(';    // 20240112 新添加 請購和領用
                                if(user['issue_waiting'] > 0){
                                    mg_msg += '請購'+user['issue_waiting']+'件'
                                }
                                if(user['receive_waiting'] > 0){
                                    if(user['issue_waiting'] > 0){
                                        mg_msg += '、';
                                    }
                                    mg_msg += '領用'+user['receive_waiting']+'件'
                                }
                                mg_msg += (user['ppty_3_waiting'] > 0) ? '、急件'+user['ppty_3_waiting']+'件)' : ')';

                                // 定義每一封mail title
                                    int_msg1_title += "待簽核" + user['total_waiting'] +'件';
                                    int_msg1_title += (user['ppty_3_waiting'] > 0) ? '、急件'+user['ppty_3_waiting']+'件)' : ')';
                            }
                            // 被退件 reject
                            if(user['total_reject'] > 0){
                                mg_msg += "\r\n";
                                mg_msg += int_msg2 + user['total_reject'] + ret_msg3 + '(';    // 20240112 新添加 請購和領用
                                if(user['issue_reject'] > 0){
                                    mg_msg += '請購'+user['issue_reject']+'件'
                                }
                                if(user['receive_reject'] > 0){
                                    if(user['issue_reject'] > 0){
                                        mg_msg += '、';
                                    }
                                    mg_msg += '領用'+user['receive_reject']+'件'
                                }
                                mg_msg += (user['ppty_3_reject'] > 0) ? '、急件'+user['ppty_3_reject']+'件)' : ')';

                                // 定義每一封mail title
                                    int_msg1_title += (user['total_waiting'] > 0) ? '、(' : '';
                                    int_msg1_title += "被退件" + user['total_reject'] +'件';
                                    int_msg1_title += (user['ppty_3_reject'] > 0) ? '、急件'+user['ppty_3_reject']+'件)' : ')'
                            }
                            // 待收發 collect
                            if(user['total_collect'] > 0){
                                mg_msg += "\r\n";
                                mg_msg += int_msg2 + user['total_collect'] + col_msg3 ;    
                                mg_msg += (user['ppty_3_collect'] > 0) ? '(急件'+user['ppty_3_collect']+'件)' : '';

                                // 定義每一封mail title
                                    int_msg1_title += (user['total_reject'] > 0) ? '、(' : '';
                                    int_msg1_title += "待收發" + user['total_collect'] +'件';
                                    int_msg1_title += (user['ppty_3_collect'] > 0) ? '、急件'+user['ppty_3_collect']+'件)' : ')';
                            }

                            var logs_source = mg_msg.replace(int_msg1, "");     // 20240514...縮減log文字內容
                            // 拼接尾段訊息
                            if((user['issue_waiting'] > 0) || (user['receive_waiting'] > 0) || (user['issue_reject'] > 0) || (user['receive_reject'] > 0)) {
                                mg_msg += int_msg4 ;    // 套用有網址長訊息
                                if((user['receive_waiting'] > 0) || (user['receive_reject'] >0 )){
                                    mg_msg += receive_url;      // 套用receive網址
                                }
                                if((user['issue_waiting'] > 0) || (user['issue_reject'] >0 )){
                                    if((user['receive_waiting'] > 0) || (user['receive_reject'] >0 )){
                                        mg_msg += '\n';
                                    }
                                    mg_msg += issue_url;        // 套用issue網址
                                }
                                mg_msg += int_msg5;
                            }else{
                                mg_msg += srt_msg4;     // 套用無網址短訊息
                            }

                            logs_source          = logs_source.replace(/文件尚未處理/g, ""); // 小-物件log 紀錄mg_msg訊息 // 20240514...縮減log文字內容
                            logs_source          = logs_source.replace(/您共有 /g, "");       // 小-物件log 紀錄mg_msg訊息 // 20240522...縮減log文字內容
                            user_log['mg_msg']   = logs_source;                             // 小-物件log 紀錄mg_msg訊息 // 20240514...縮減log文字內容
                            user_log['thisTime'] = thisTime;                                // 小-物件log 紀錄thisTime
    
                            // step.2 執行通知 --
                            // *** 2-1 發送mail
                            const mail_result_check = async () => {
                                // *** call fun.step_1 將訊息推送到TN PPC(mail)給對的人~
                                let mail_result_check = (user_email) ? await sendmail(user_email, int_msg1_title, mg_msg) : false;
                                return mail_result_check;
                            };
    
                            // *** 2-2 發送mapp
                            const mapp_result_check = async () => {
                                // *** call fun.step_1 將訊息推送到TN PPC(mapp)給對的人~
                                let mapp_result_check = (user_mapp) ? await push_mapp(user_emp_id, mg_msg) : false;
                                return mapp_result_check;
                            };
    
                            // step.3 存储每个用户的异步操作 Promise
                            promises.push(
                                // 等待mapp_result_check()和mail_result_check()都完成后再执行自定义的代码
                                Promise.all([mapp_result_check(), mail_result_check()])
                                .then(results => {
                                    const [mappResult, mailResult] = results;
                                    // 处理 mapp/mail 结果 // 標記結果顯示OK或NG，並顯示執行訊息
                                        var action_id = document.querySelector('#'+list_key+' #id_' + user_emp_id);
                                    // mail處理
                                        if(user_email){
                                            user_log.mail_res = mailResult ? 'OK' : 'NG';
                                            mailResult ? push_result['email']['success']++ : push_result['email']['error']++; 
                                            var fa_icon_mail = window['mail_' + user_log.mail_res];
                                            action_id.innerHTML = fa_icon_mail + action_id.innerText;
                                            console_log = user.cname + " (" + user.emp_id + ")" + ' ...  sendMail： ' + fa_icon_mail + user_log.mail_res;
                                        }
                                    // mapp處理
                                        if(user_mapp){
                                            user_log.mapp_res = mappResult ? 'OK' : 'NG';
                                            mappResult ? push_result['mapp']['success']++ : push_result['mapp']['error']++; 
                                            var fa_icon_mapp = window['fa_' + user_log.mapp_res];
                                            action_id.innerHTML = fa_icon_mapp + fa_icon_mail + action_id.innerText;
                                            console_log += '  /  pushMapp： ' + fa_icon_mapp + user_log.mapp_res;
                                        }
    
                                    // 自定义的代码在这里执行 -- 執行訊息渲染                                                           
                                        $('#result').append(console_log + '</br>');
    
                                    // 这里可以执行其他自定义的操作
                                    user_logs.push(user_log);                                    // 將log單筆小物件 塞入 logs大陣列中
                                    completedUsers++;                                            // 增加已完成发送操作的用户数量
                                    if (completedUsers == totalUsers) {                          // 检查是否所有用户的发送操作都已完成
                                        swap_toLog(user_logs);                                   // 所有发送操作完成后调用 swap_toLog
                                    }
                                })
                                .catch(error => {
                                    console.log('Error:', error);
                                })
                            );
                        }
                    });
                }else{                                                                          // 沒件數 == 0 就不用執行通知，但依樣要生成Log
                    var user_log = {                                                            // 宣告儲存Log內的單筆 小-物件log
                        emp_id   : '',
                        cname    : '',
                        waiting  : '0',
                        mg_msg   : '(無待簽文件)',
                        mapp_res : 'OK',
                        thisTime : thisTime
                    }
                    user_logs.push(user_log);                                                   // 將log單筆小物件 塞入 logs大陣列中 
                    $('#result').append(fa_OK + '(無待簽文件) ... done'+'</br>');                // 插入下方顯示
                    swap_toLog(user_logs);                                           // 没有用户需要通知时直接调用 swap_toLog
                }

            })
                
            show_swal_fun(push_result);                                                         // 调用 show_swal_fun
            // 將其歸零，避免汙染
                user_logs = [];
                push_result = {
                    'mapp' : {
                        'success' : 0,
                        'error'   : 0
                    },
                    'email' : {
                        'success' : 0,
                        'error'   : 0
                    }
                }

            $("body").mLoading("hide");                                                         // 關閉mLoading圖示
        }

        // 20240529 確認自己是否為彈出視窗 !! 只在完整url中可運行 = tw123456p.cminl.oa
        function checkPopup() {
            var urlParams = new URLSearchParams(window.location.search);
            if ((urlParams.has('popup') && urlParams.get('popup') === 'true') || (window.opener) || (sessionStorage.getItem('isPopup') === 'true')) {
                console.log('popup');
                sessionStorage.removeItem('isPopup');

                let nav = document.querySelector('nav');                // 獲取 <nav> 元素
                    nav.classList.add('unblock');                           // 添加 'unblock' class

                let rtn_btns = document.querySelectorAll('.rtn_btn');   // 獲取所有帶有 'rtn_btn' class 的按鈕
                    rtn_btns.forEach(function(btn) {                        // 遍歷這些按鈕，並設置 onclick 事件
                        btn.onclick = function() {
                            // if (confirm('確認返回？')) {
                                closeWindow();                                  // true=更新 / false=不更新
                            // }
                        };
                    });
            }else{
                console.log('main');
            }
        }


    $(function () {
        mloading("show");                                               // 啟用mLoading
        // 把所有名單上的人頭代上email
        Object.keys(lists_obj).forEach((list_key)=>{
            let a_list = lists_obj[list_key];
            for(i=0; i < a_list.length; i++ ){
                let q_emp_id = a_list[i]['created_emp_id'];
                let q_email = search_fun('showStaff', q_emp_id);
                if(q_email){
                    $('#'+list_key+' #id_'+q_emp_id).append('</br>'+q_email); 
                    a_list[i]['email'] = q_email;
                }
            }
        })
        // console.log(a_list)
        $("body").mLoading("hide");
    })

    // fun啟動自動執行
    $(document).ready( function () {
        // checkPopup();
        // op_tab('user_lists');            // 關閉清單
        $('#result').append('等待發報 : ');
        if(check_ip && fun){
            switch (fun) {
                case 'notify_insign':       // MAPP待簽發報
                    (async () => {
                        await notify_insign();     // 等 func1 執行完畢  // notify_insign 整理訊息、發送、顯示發送結果。
                        CountDown();        // 當 func1 執行完畢後才會執行 func2    // 倒數 n秒自動關閉視窗~
                    })();
                    break;

                default:
                    $('#result').append('function error!</br>');
            }

        }else{
            $('#result').append(' ...standBy...</br>');
        }
    } );