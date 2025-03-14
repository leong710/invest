const uuid      = '3cd9a6fd-4021-11ef-9173-1c697a98a75f';       // invest
    // 241209 確認是否是測試帳號
    const debugMode = { 
        'test'     : (fun == 'debug') ? true : false,           // true  = 啟動測試 
        'mapp'     : true,                                      // false = 放棄執行
        'email'    : true,                                      // false = 放棄執行
        'toLog'    : true,                                      // false = 放棄執行
        'title'    : '!!! Now is DEBUGMODE !!!',
        'to_empId' : '10008048',
        'to_email' : 'leong.chen@innolux.com'
    };

// // 子功能--A
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
    function CountDown(seconds) {
        let delayTime = 1000;                   // 1000=1秒
        let i = seconds;                        // 參數值，15==15秒
        const loop = () => {
            if (i >= 0) {
                document.getElementById("myMessage").innerHTML = "視窗關閉倒數 "+ i +" 秒";
                i--;
                setTimeout(loop, delayTime);
            } else {
                // callback();                  // 要執行的程式
                document.getElementById("myMessage").innerHTML = "視窗關閉！";
                window.open('', '_self', '');
                window.close();
            }
        };
        loop();
    }
    // fun_3 延遲模組 延遲模組，返回 Promise
    function delayedLoop(seconds, callback) {
        return new Promise((resolve) => {
            let i = seconds;
            const loop = () => {
                if (i > 0) {
                    document.getElementById("myMessage").innerHTML = "Fun: " + callback + " 執行倒數 " + i + " 秒";
                    setTimeout(loop, 1000);
                } else {
                    document.getElementById("myMessage").innerHTML = "Fun: " + callback + " 執行！";
                    resolve();  // 延遲完成後 resolve Promise
                }
                i--;
            };
            loop();
        }).then(() => window[callback]());  // 在延遲完成後執行 callback
    }
    // fun.0-2：吐司顯示字條 +堆疊
    function inside_toast(sinn){
        // 創建一個新的 toast 元素
        var newToast = document.createElement('div');
            newToast.className = 'toast align-items-center bg-warning';
            newToast.setAttribute('role', 'alert');
            newToast.setAttribute('aria-live', 'assertive');
            newToast.setAttribute('aria-atomic', 'true');
            newToast.setAttribute('autohide', 'true');
            newToast.setAttribute('delay', '1000');
            // 設置 toast 的內部 HTML
            newToast.innerHTML = `<div class="d-flex"><div class="toast-body">${sinn}</div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div>`;
        // 將新 toast 添加到容器中
        document.getElementById('toastContainer').appendChild(newToast);
        // 初始化並顯示 toast
        var toast = new bootstrap.Toast(newToast);
        toast.show();
        // 選擇性地，在 toast 隱藏後將其從 DOM 中移除
        newToast.addEventListener('hidden.bs.toast', function () {
            newToast.remove();
        });
    }

    // 20240529 確認自己是否為彈出視窗 !! 只在完整url中可運行 = tw123456p.cminl.oa
    function checkPopup() {
        var urlParams = new URLSearchParams(window.location.search);
        if ((urlParams.has('popup') && urlParams.get('popup') === 'true') || (window.opener) || (sessionStorage.getItem('isPopup') === 'true')) {
            console.log('popup');
            sessionStorage.removeItem('isPopup');

            let nav = document.querySelector('nav');                // 獲取 <nav> 元素
                nav.classList.add('unblock');                       // class添加'unblock'進行隱藏 

            let rtn_btns = document.querySelectorAll('.rtn_btn');   // 獲取所有帶有 'rtn_btn' class 的按鈕
                rtn_btns.forEach(function(btn) {                    // 遍歷這些按鈕，並設置 onclick 事件
                    btn.onclick = function() {
                        // if (confirm('確認返回？')) {
                            closeWindow();                          // true=更新 / false=不更新
                        // }
                    };
                });
        }else{
            console.log('main');
        }
    }

// // 子功能--B
    // 0-1.確認是否超過3小時；true=_db/更新時間；false=_json          // 呼叫來源：load_init
    function check3hourse(action){
        let currentDate = new Date();                               // 取得今天日期時間
        let reloadTime  = new Date(reload_time.innerText);          // 取得reloadTime時間

        let timeDifference = currentDate - reloadTime;              // 計算兩個時間之間的毫秒差異
        let hoursDifference = timeDifference / (1000 * 60 * 60);    // 將毫秒差異轉換為小時數
        let result = hoursDifference >= 3 ;                          // 判斷相差時間是否大於3小時，並顯示結果
        let _method = result ? '_db' : '_json';
        if(result || action){
            recordTime();       // 1.取得目前時間，並格式化；2.更新reloadTime.txt時間；完成後=>3.更新畫面上reload_time時間
        }
        const _title = ('時間差：'+ Number(hoursDifference.toFixed(2)) +'（小時）>= 3小時：'+ result +' => '+ _method);
        document.getElementById('reload_time').title = _title;
        return _method;
    }
    // 0-2.取得目前時間，並格式化；2.更新reloadTime.txt時間；完成後=>3.更新畫面上reload_time時間        // 呼叫來源：check3hourse
    async function recordTime(){
        let rightNow = new Date().toLocaleString('zh-TW', { hour12: false });                       // 取得今天日期時間
        try {
            await load_fun('urt' , rightNow+', true' , update_reloadTime);      
        } catch (error) {
            console.error(error);
        }
    }
    // 0-3.更新畫面上reload_time時間                  // 呼叫來源：recordTime
    function update_reloadTime(rightNow){
        reload_time.innerText = rightNow;           // 更新畫面上reload_time時間
    }

// // 子技能--C
    // 20240515 整理log記錄檔並轉拋toLog
    async function swap_toLog(user_logs){
        // 打包整理Logs的陣列
        user_logs_obj = {
            thisDay  : thisToday,
            autoLogs : user_logs
        }
        user_logs_json = JSON.stringify(user_logs_obj);                                   // logs大陣列轉JSON字串
        await toLog(user_logs_json);                                                            // *** call fun.step_2 寫入log記錄檔
    }
    // 20231213 寫入log記錄檔~
    function toLog(logs_msg){
        if(!debugMode.toLog){
            return true;
        }
        return new Promise((resolve, reject) => {
            $.ajax({
                url      : '../autolog/log.php',
                method   : 'post',
                async    : false,
                dataType : 'json',
                data     : {
                    function : 'storeLog',
                    thisDay  : thisToday,
                    sys      : 'invest',
                    logs     : logs_msg,
                    t_stamp  : ''
                },
                success: function(res){
                    resolve(true);                                          // 成功時解析為 true 
                },
                error: function(res){
                    console.log("toLog -- error：", res);
                    reject(false);                                          // 失敗時拒絕 Promise
                }
            });
        });
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
    // 20240314 search user_empid return email
    function search_fun(fun, search){
        let result = null;

        if(!search || (search.length < 8)){
            let reject_msg = "查詢 工號/部門代號 字數最少 8 個字!! 請確認："+search;
            console.log(reject_msg);
            alert(reject_msg);
            $("body").mLoading("hide");
            return false;                                               // 失敗時拒絕 Promise
        } 

        $.ajax({
            url:'http://tneship.cminl.oa/api/hrdb/index.php',           // 正式2024新版
            method   :'post',
            async    : false,                                           // ajax取得數據包後，可以return的重要參數
            dataType :'json',
            data:{
                uuid         : uuid,                                    // invest
                functionname : fun,                                     // 操作功能
                emp_id       : search,                                  // 查詢對象key_word  // 使用開單人工號查詢
                sign_code    : search                                   // 查詢對象key_word  // 使用開單人工號查詢
            },
            success: function(res){
                let obj_val = res["result"];
                // 將結果進行渲染
                if (obj_val) {
                    result = (fun == 'showStaff' && obj_val.comid2 != undefined) ? obj_val.comid2 : obj_val;
                }else{
                    result = fun +" 查無[ "+ search +" ]!!";
                }
            },
            error(err){
                result = fun +"search error: "+ err;
            }
        })
        return result;                                          // 成功時解析為 true 
    }

    // 0-0.多功能擷取fun 新版改用fetch
    async function load_fun(fun, parm, myCallback) { // parm = 參數
        try {
            let formData = new FormData();
            formData.append('fun', fun);
            formData.append('parm', parm); // 後端依照fun進行parm參數的採用

            let response = await fetch('load_fun.php', {
                method : 'POST',
                body   : formData
            });

            if (!response.ok) {
                throw new Error('fun load ' + fun + ' failed. Please try again.');
            }

            let responseData = await response.json();
            let result_obj = responseData['result_obj']; // 擷取主要物件
            
            return myCallback(result_obj);               // resolve(true) = 表單載入成功，then 呼叫--myCallback
                                                         // myCallback：form = bring_form() 、document = edit_show() 、locals = ? 還沒寫好
        } catch (error) {
            throw error;                                 // 載入失敗，reject
        }
    }

    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    // 將bpm的email找出來
    function step1(bpm_obj){
        return new Promise((resolve) => {
            Object.keys(bpm_obj).forEach((bpm_key)=>{                               // 依序處理...
                let emp_id = bpm_obj[bpm_key].emp_id;                               // 依序 取出bpm工號
                bpm_obj[bpm_key].email = search_fun('showStaff', emp_id);           // 查詢 showStaff
            })
            bpm = bpm_obj;
            resolve(true);                                                          // 成功時解析為 true 
        });
    }
    // 把所有名單上的人頭代上email
    function step2(lists_obj){
        return new Promise((resolve) => {
            Object.keys(lists_obj).forEach((list_key)=>{                            // 依序處理...
                // s1.先找開單人
                    let created_emp_id = lists_obj[list_key].created_emp_id;        // 取出開單人工號
                    let created_email = search_fun('showStaff', created_emp_id);    // 查詢 showStaff
                    lists_obj[list_key].created_email = created_email;              // 帶入lists_obj
                // s2.再找site-pm窗口
                    lists_obj[list_key].spm = [];                                   // 建立初始陣列
                    let pm_emp_id = lists_obj[list_key].pm_emp_id;                  // 取出窗口名單
                    let pm_emp_id_arr = pm_emp_id.split(',');                       // 分拆成陣列
                    for (let i = 0; i < pm_emp_id_arr.length; i += 2) {             // 依序處理...
                        let spm_emp_id = pm_emp_id_arr[i];                          // 依i序 取出窗口工號
                        let spm_email = search_fun('showStaff', spm_emp_id);        // 查詢 showStaff
                        lists_obj[list_key].spm.push({                              // 組合成Obj並帶入
                            'emp_id' : spm_emp_id,
                            'cname'  : pm_emp_id_arr[i + 1],
                            'email'  : spm_email
                        })
                    }
                // s3.把所有名單上代上dept
                    let sign_code = lists_obj[list_key].sign_code;                  // 取出sign_code
                    let signDept = search_fun('showSignDept', sign_code);           // 查詢 showSignDept
                    lists_obj[list_key].signDept = signDept;                        // 帶入lists_obj
                // s4.將_odd反解
                    lists_obj[list_key]._odd = JSON.parse(lists_obj[list_key]._odd);// 將_odd反解
                
                // s5.把bpm帶入每一筆紀錄....這裡可以優化程序
                    lists_obj[list_key].bpm = bpm;                                  // 帶入bpm陣列
            })
            doc_lists = lists_obj;                                                  // lists_obj傾倒回主清單陣列doc_lists
            resolve(true);                                                          // 成功時解析為 true 
        });
    }
    // notifyLists 資料清洗
    function step3(lists_obj, myCallback){
        return new Promise((resolve) => {
            Object(lists_obj).forEach((list_i)=>{                        // 依序處理...
                const {
                    _remaining, anis_no, _odd : { due_day }, short_name, fab_title, sign_code,
                    created_cname, created_emp_id, created_email, idty, spm, signDept, bpm
                } = list_i;
    
                const idty_value = {
                    '1' : '立案/簽核中',
                    '10': '完成訪談',
                    '6' : '暫存',
                    '3' : '取消'
                }[idty] || 'NA';
    
                const anis_no_arr = {
                    fab_title       : fab_title,
                    short_name      : short_name,
                    sign_code       : sign_code,
                    due_date        : due_day,
                    remaining_day   : _remaining,
                    idty            : idty + '_' + idty_value,
                    created_cname   : created_cname,
                    created_emp_id  : created_emp_id
                };

                const addToNotifyList = (i_emp_id, i_cname, i_email, i_action) => {
                    if(notifyLists[i_emp_id] == undefined){
                        notifyLists[i_emp_id] = {};
                    }
                    if(notifyLists[i_emp_id].anis_no == undefined){
                        notifyLists[i_emp_id].anis_no = {};
                    }
                    notifyLists[i_emp_id].cname = i_cname;
                    notifyLists[i_emp_id].email = i_email;
                    notifyLists[i_emp_id].anis_no[anis_no] = anis_no_arr;
                    notifyLists[i_emp_id].action = i_action;
                };

                const action = ( _remaining > 3) ? ['email'] : ['email', 'mapp'];

                // console.log(anis_no, _remaining , '1.窗口、課副理 (未結案+開單人) => email');
                // s1. 建立spm窗口名單
                    Object(spm).forEach((spm_i)=>{
                        const spm_emp_id = spm_i.emp_id
                        addToNotifyList(spm_emp_id, spm_i.cname, spm_i.email, action);
                    })

                // s2. 課副理
                    const signDept_emp_id   = signDept.emp_id;
                    addToNotifyList(signDept_emp_id, signDept.cname, signDept.email, action);

                // s3. 未結案+開單人
                    if(idty !== '10'){
                        addToNotifyList(created_emp_id, created_cname, created_email, action);
                    }

                if ( _remaining <= 3 && _remaining >= 0 ){
                    // console.log(anis_no, _remaining , '2.窗口、課副理、部經理、大PM (未結案+開單人) => email + mapp')
                    // s4. 部經理
                        const signDept_up_emp_id   = signDept.up_emp_id;
                        addToNotifyList(signDept_up_emp_id, signDept.up_cname, signDept.up_email, action);

                    // s5. 建立bpm大pm名單
                        Object(bpm).forEach((bpm_i)=>{
                            const bpm_emp_id = bpm_i.emp_id
                            if(bpm_emp_id >= 90000000 && bpm_emp_id.includes("9000000")){  // 排除管理員+測試帳號
                                return; 
                            }else{
                                addToNotifyList(bpm_emp_id, bpm_i.cname, bpm_i.email, action);
                            }
                        })
                } 

                if ( _remaining < 0 ){
                    // console.log(anis_no, _remaining , '3.窗口、課副理、部經理、大PM、處長 (未結案+開單人) => email + mapp')
                    // s6. 處長
                        const signDept_uup_emp_id   = signDept.uup_emp_id;
                        addToNotifyList(signDept_uup_emp_id, signDept.uup_cname, signDept.uup_email, action);
                }
            })
            resolve(myCallback(notifyLists));                            // 成功時執行myCallback，並解析為 true 
        });
    }
    // post_result 將notifyLists渲染到畫面Table
    function step4(lists_obj){
        return new Promise((resolve) => {
            $("#notify_lists table tbody").empty();
            let totalUsers_i = 0;                       // 計算通知筆數
            Object.keys(lists_obj).forEach((lists_i)=>{
                const _action = lists_obj[lists_i].action;
                let a0 = ''
                Object(_action).forEach((_a)=>{
                    a0 += '&nbsp'+ _a +'&nbsp<span id="'+ lists_i +'_'+ _a +'">'+'</span>';
                })
                const anis_no_obj = lists_obj[lists_i].anis_no;
                let a1 = '';
                let a2 = '';
                let a3 = '';
                let a4 = '';
                let a5 = '';
                let i  = 0;
                Object.keys(anis_no_obj).forEach((anis_no_key)=>{
                    a1 += (i>0 ? '</br>':'') + '<span>'+ anis_no_obj[anis_no_key].fab_title +' ('+ anis_no_obj[anis_no_key].sign_code +')</span>';
                    a2 += (i>0 ? '</br>':'') + '<span>'+ anis_no_key +' ('+ anis_no_obj[anis_no_key].short_name +')</span>';
                    a3 += (i>0 ? '</br>':'') + '<span>'+ anis_no_obj[anis_no_key].due_date +' ('+ anis_no_obj[anis_no_key].remaining_day +')</span>';
                    a4 += (i>0 ? '</br>':'') + '<span>'+ anis_no_obj[anis_no_key].created_cname +' ('+ anis_no_obj[anis_no_key].created_emp_id +')</span>';
                    a5 += (i>0 ? '</br>':'') + '<span>'+ anis_no_obj[anis_no_key].idty +'</span>';
                    i++;
                })
                const inner_Text = '<tr>'+'<td>'+ a0 +'</td>'+'<td>'+ lists_obj[lists_i].cname +'('+ lists_i +')</td>'
                                    +'<td class="text-start">'+a1+'</td>'+'<td class="text-start">'+a2+'</td>'+'<td>'+a3+'</td>'+'<td>'+a4+'</td>'+'<td class="text-start">'+a5+'</td>'+'<td>'+i+'</td>'+'</tr>'
                
                notifyLists[lists_i].case_count = i;                // 將件數到回notifyLists主檔
                $("#notify_lists table tbody").append(inner_Text);  // 渲染畫面
                totalUsers_i++;                                     // 計算通知筆數++
            })
            totalUsers_length.innerText = totalUsers_i;             // 渲染通知筆數
            resolve(true);                                          // 成功時執行myCallback，並解析為 true 
        });
    }


// // 主技能--發報用 be await
    // 20240314 將訊息推送到TN PPC(mapp)給對的人~
    function push_mapp(to_emp_id, mg_msg) {
        if(!debugMode.mapp){
            console.log(to_emp_id, mg_msg);
            return true;
        }
        return new Promise((resolve, reject) => {
            $.ajax({
                url:'http://tneship.cminl.oa/api/pushmapp/index.php',       // 正式2024新版
                method:'post',
                async: false,                                               // ajax取得數據包後，可以return的重要參數
                dataType:'json',
                data:{
                    uuid         : uuid,                                    // invest
                    kind         : 'broadChat',                             // 訊息頻道
                    ask          : 'to',                                    // 個人
                    ACCOUNT_LIST : to_emp_id,                               // 傳送對象
                    TEXT_CONTENT : mg_msg,                                  // 傳送訊息
                },
                success: function(res){
                    // console.log("push_mapp -- success",res);
                    resolve(true);                                          // 成功時解析為 true 
                },
                error: function(res){
                    console.error("push_mapp -- error：",res);
                    reject(false);                                          // 失敗時拒絕 Promise
                }
            });
        });
    }
    // 20240314 將訊息郵件發送給對的人~
    function sendmail(to_email, int_msg1_title, mg_msg){
        if(!debugMode.email){
            console.log(to_email, int_msg1_title, mg_msg);
            return true;
        }
        return new Promise((resolve, reject) => {
            $.ajax({
                url:'http://tneship.cminl.oa/api/sendmail/index.php',       // 正式2024新版
                method:'post',
                async: false,                                               // ajax取得數據包後，可以return的重要參數
                dataType:'json',
                data:{
                    uuid    : uuid,                                         // invest
                    sysName : 'invest',                                     // 貫名
                    to      : to_email,                                     // 傳送對象
                    subject : int_msg1_title,                               // 信件標題
                    body    : mg_msg                                        // 訊息內容
                },
                success: function(res){
                    // console.log("push_mapp -- success",res);
                    resolve(true);                                          // 成功時解析為 true 
                },
                error: function(res){
                    console.error("send_mail -- error：",res);
                    reject(false);                                          // 失敗時拒絕 Promise
                }
            });
        });
    }


// 主技能
    // 2024/07/23 notify_process()整理訊息、發送、顯示發送結果。
    async function notify_process(){
        mloading("show");                                                       // 啟用mLoading
        $('#result').empty();                                                   // 清空執行訊息欄位
        
        // step0.init
            var invest_url   = '事故訪談系統：'+ uri +'/invest/dashboard/';
            var int_msg1     = '【tnESH事故訪談系統】待您處理文件提醒';
            var int_msg2     = '您共有 ';
            var int_msg3     = ' 件訪談單尚未完成申報';
            var int_msg4     = '，如已處理完畢，請忽略此訊息！\n\n** 請至以下連結查看待處理文件：\n';
            var srt_msg4     = '，如已處理完畢，請忽略此訊息！\n\n';
            var int_msg5     = '\n\n溫馨提示：\n    1.登錄過程中如出現提示輸入帳號密碼，請以cminl\\NT帳號格式\n';
    
            var push_result  = {                                                // count push time to show_swal_fun
                'mapp' : {
                    'success' : 0,
                    'error'   : 0
                },
                'email' : {
                    'success' : 0,
                    'error'   : 0
                }
            }

            var totalUsers = Object.keys(notifyLists).length;                   // 獲取總用戶數量
            var completedUsers = 0;                                             // 已完成发送操作的用户数量
            var user_logs = [];                                                 // 宣告儲存Log用的 大-陣列Logs

                // _fun1.製作主要信息
                function make_msg(_value_obj){
                    return new Promise((resolve) => {
                        // _fun1.step0. init 
                            let i         = 0;                                          // 計算anis_no陣列下的anis件數
                            let nok       = 0;                                          // 未結案
                            let emergency = 0;                                          // 小於等於 0天的急件
                            const { anis_no } = _value_obj;                             // 取出ANIS_NO這一個陣列
                            var anis_msg = '';                                          // 初始化ANIS訊息值
                        // _fun1.step1. 分拆出ANIS訊息 &#9
                            for (const [anis_k, anis_v] of Object.entries(anis_no)){     
                                const { fab_title, short_name, due_date, remaining_day, idty } = anis_v;
                                // anis信息組合
                                anis_msg += (i > 0 ? '\r\n\r\n':'') +'事故廠區/類別：'+ fab_title +' / '+ short_name +'\nANIS單號：'+ anis_k
                                        + '\n申報截止日：'+ due_date +'\n剩餘天數：'+ remaining_day + '天' +'\n表單狀態：'+ (idty == '10_結案' ? '未申報' : '未申報 / 未完成訪談' );
                                i++;
                                nok += (idty !='10_結案') ? 1 : 0 ;             // 未結案統計
                                emergency += (remaining_day <= 0 ) ? 1 : 0;     // 統計小於等於 0天的急件
                            }
                        // _fun1.step2. 組合訊息文字
                            var base_anis_msg =  int_msg2 + i + int_msg3 + (nok !=0 ? ' (其中 '+ nok + ' 件尚未完成訪談)' : '')+'\n\n'+ anis_msg;
                            var mg_msg = int_msg1 +"\r\n"+ base_anis_msg +'\n\n'+ int_msg4 + invest_url + int_msg5;
                            // 定義每一封mail title
                            var int_msg1_title = int_msg1 + " (未完成申報共"+ i +"件"+ (nok !=0 ? '，其中'+ nok +'件尚未完成訪談)' : ')');
                        // _fun1.step3. 訊息打包 
                            var mg_arr = {
                                title     : int_msg1_title,                     // 信件title
                                anis_msg  : base_anis_msg,                      // 核心訊息
                                mg_msg    : mg_msg,                             // 組合信件
                                emergency : emergency                           // 急件統計
                            }
                        resolve(mg_arr);     // 當搜尋完成後，回傳結果
                    });
                }

            var promises = [];                                                  // 存储所有异步操作的 Promise

        // step1. 將notifyLists逐筆進行分拆作業
        for (const [_key, _value] of Object.entries(notifyLists)){              // 表頭1.外層
            console.log(`發送請求給: ${_key}`);
            await sleep(1000);                                                  // 先等待時間
            // step.1-0 init
            const to_cname  = String(_value.cname).trim();
            const to_email  = String(_value.email).trim();                      // 定義 to_email + 去空白
            const to_emp_id = String(_key).trim();                              // 定義 to_emp_id + 去空白
            const to_action = _value.action;

            // 宣告儲存Log內的單筆 小-物件log
            let user_log = { 
                emp_id          : to_emp_id,
                cname           : to_cname,
                email           : to_email,
                thisTime        : thisTime                                      // 小-物件log 紀錄thisTime
            };

            // step.1-2 調用_fun make_msg 帶入個人單筆紀錄進行訊息製作
            mail_msg_arr = await make_msg(_value);                                
            // var logs_source = mail_msg_arr.anis_msg.replace(int_msg1, "");      // 20240514...縮減log文字內容
            // user_log['mg_msg']   = logs_source;                                 // 小-物件log 紀錄mg_msg訊息 // 20240514...縮減log文字內容
            user_log['mg_msg']    = mail_msg_arr.anis_msg;                         // 小-物件log 紀錄mg_msg訊息
            user_log['emergency'] = mail_msg_arr.emergency;                        // 小-物件log 紀錄emergency訊息
            
            // step.2 執行通知 --
            // *** 2-1 發送mail     // *** call fun.step_1 將訊息推送到TN PPC(mail)給對的人~
            const mail_result_check = async () => {
                // --- 確認email是否有誤
                if(!to_email || (to_email.length < 12)){
                    // alert("email字數有誤 !!");                            // 避免無人職守時被alert中斷，所以取消改console.log
                    console.log("email 有誤：", to_emp_id, to_email);
                    push_result['mapp']['error']++; 
                    push_result['email']['error']++; 
                    return false;
                }
                return (to_action.includes('email') && to_email) ? await sendmail((debugMode.test ? debugMode.to_email : to_email), mail_msg_arr.title, mail_msg_arr.mg_msg) : false;
            };
            // *** 2-2 發送mapp     // *** call fun.step_1 將訊息推送到TN PPC(mapp)給對的人~
            const mapp_result_check = async () => {
                // --- 確認工號是否有誤
                if(!to_emp_id || (to_emp_id.length < 8) || (to_emp_id >= 90000000 && to_emp_id.includes("9000000"))){
                    // alert("工號字數有誤 !!");                            // 避免無人職守時被alert中斷，所以取消改console.log
                    console.log("工號 有誤：", to_emp_id);
                    push_result['mapp']['error']++; 
                    push_result['email']['error']++; 
                    return false;
                }
                return (to_action.includes('mapp') && to_emp_id) ? await push_mapp((debugMode.test ? debugMode.to_empId : to_emp_id), mail_msg_arr.mg_msg) : false;
            };

            // step.3 等待每個異步操作Promise...
            promises.push(
                // 等待mapp_result_check() 和mail_result_check()都完成后再執行自定義工作...table渲染完成icon、執行訊息渲染
                Promise.all([mapp_result_check(), mail_result_check()])
                .then(results => {
                    const [mappResult, mailResult] = results;
                    var console_log = '';                                   // 初始化下方執行訊息
                    // 處理 mapp/mail 结果 // 標記結果顯示OK或NG，並顯示執行訊息
                    // mail處理
                        if(to_action.includes('email') && to_email){
                            user_log.mail_res = mailResult ? 'OK' : 'NG';
                            mailResult ? push_result['email']['success']++ : push_result['email']['error']++; 
                            let id_mail = document.getElementById(to_emp_id +'_email');
                            let fa_icon_mail = window['mail_' + user_log.mail_res];
                            id_mail.innerHTML = fa_icon_mail;
                            console_log = to_cname + " (" + to_emp_id + ")" + ' ...  sendMail：' + fa_icon_mail + user_log.mail_res;
                        }
                    // mapp處理
                        if(to_action.includes('mapp') && to_emp_id){
                            user_log.mapp_res = mappResult ? 'OK' : 'NG';
                            mappResult ? push_result['mapp']['success']++ : push_result['mapp']['error']++; 
                            let id_mapp = document.getElementById(to_emp_id +'_mapp');
                            let fa_icon_mapp = window['mapp_' + user_log.mapp_res];
                            id_mapp.innerHTML = fa_icon_mapp;
                            console_log += '  /  pushMapp：' + fa_icon_mapp + user_log.mapp_res;
                        }
                    // 其他自定義操作
                    $('#result').append(console_log + '</br>');                  // 自定義代碼执行 -- 執行訊息渲染 

                    user_logs.push(user_log);                                    // 將log單筆小物件 塞入 logs大陣列中
                    completedUsers++;                                            // 增加已完成发送操作的用户数量
                })
                .catch(error => {
                    console.log('Error:', error);
                })
            );
        }
        // step.4 等待所有異步操作完成后再向下執行...
        await Promise.all(promises);
        // step.5 確認發送筆數完成，並調用swap_toLog 將user_logs寫入autoLog
        if (completedUsers == totalUsers) {                          // 检查是否所有用户的发送操作都已完成
            swap_toLog(user_logs);                                   // 所有发送操作完成后调用 swap_toLog
        }
        // step.6 調用 show_swal_fun帶入push_result統計
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

        // $("body").mLoading("hide");                                                         // 關閉mLoading圖示
        return;
    }

    async function load_init(action){
        const _method = check3hourse(action);
        const _type = action ?  "_db" : _method;            // action來決定 false=自動判斷check3hourse 或 true=強制_db
        try {
            mloading("show");                               // 啟用mLoading

            await load_fun(_type, 'bpm, true',     step1);  // load_fun查詢大PM bpm，並用step1找出email
            await load_fun(_type, 'notify, true' , step2);  // load_fun先抓json，沒有then抓db(true/false 輸出json檔)，取得highlight內容後用step2把所有名單上的人頭代上emai
            await step3(doc_lists, step4);                  // step3資料清洗，後用step4鋪設內容

            // op_tab('user_lists');                        // 關閉清單
            $('#result').append('等待發報 : ');

            if(check_ip && fun){
                switch (fun) {
                    case 'debug':                               // debug mode，mapp&mail=>return true
                        break;
                    case 'notify_process':                      // notify_process待簽發報auto_run
                        await delayedLoop(3, 'notify_process'); // delayedLoop延遲3秒後執行 notify_process：整理訊息、發送、顯示發送結果。
                        CountDown(15);                          // 倒數 15秒自動關閉視窗~
                        break;
                    default:
                        $('#result').append('function error!</br>');
                }
            }else{
                $('#result').append(' ...standBy...</br>');
            }
            $("body").mLoading("hide");

        } catch (error) {
            console.error('發生錯誤:', error);
        }
    }
    
    // document.ready啟動自動執行fun
    $(function () {
        checkPopup();                                       // 確認自己是否為彈出視窗 
        $('[data-toggle="tooltip"]').tooltip();             // 在任何地方啟用工具提示框
        if(debugMode.test){
            const dm = document.getElementById("dabugTitle");
            dm.innerHTML = debugMode.title;
            inside_toast(debugMode.title)
            console.log(debugMode.title);
            console.table(debugMode);
        }
        load_init(false);
    })
