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

var invest_url   = `\n事故訪談系統：${uri}/invest/`;
var invest_url_mail = `<a href="${uri}/invest/" target="_blank">事故訪談系統</a>`;
var int_msg1     = '【tnESH事故訪談系統】待您處理文件提醒';
var int_msg2     = '您轄下共有 ';
var int_msg3     = ' 件訪談單尚未完成結案';
var int_msg4     = '** 請至以下連結查看待處理文件： ';
var srt_msg4     = ' ，如已處理完畢，請忽略此訊息！\n';
var int_msg5     = '溫馨提示：登入過程中如出現提示輸入帳號密碼，請以cminl\\NT帳號格式\n';


function sendmail1(to_email, int_msg1_title, mg_msg){
    if(!debugMode.email){
        console.log('debug mode:');
        console.log(to_email);
        console.log(int_msg1_title);
        console.log( mg_msg);
        return Promise.resolve(true); // 在 debug 模式也回傳 Promise，保持一致性
    }
    return new Promise((resolve, reject) => {
        var formData = new FormData();  // 創建 FormData 物件
        // 將已有的參數加入 FormData
            formData.append('uuid', '7ce8a24d-eb27-11ee-927e-1c697a98a75f');              // todo
            formData.append('sysName', 'todo');          // 貫名
            formData.append('to', to_email);            // 1.傳送對象
            // formData.append('to', `leong.chen;`);       // 3.送件-傳送測試對象
            // formData.append('cc', `leong.chen;`);       // 4.副件-傳送-管理員
            formData.append('subject', int_msg1_title); // 信件標題
            formData.append('body', mg_msg);            // 訊息內容

        // 假設你有一個檔案輸入框，其 ID 是 'fileInput'
            var fileInput = document.getElementById('fileInput');
            if (fileInput && fileInput.files.length > 0) {
                formData.append('file', fileInput.files[0]);  // 把檔案添加到 FormData
            }

        $.ajax({
            url:'http://tneship.cminl.oa/api/sendmail/index.php',       // 正式 202503可夾檔+html內文
            method:'post',
            async: false,                                               // ajax取得數據包後，可以return的重要參數
            dataType:'json',
            data: formData,
            processData: false,                                         // 不處理資料
            contentType: false,                                         // 不設置 Content-Type，讓瀏覽器自動設置
            success: function(res){
                resolve(true);                                          // 成功時解析為 true 
            },
            error: function(res){
                console.error("send_mail -- error：",res);
                reject(false);                                          // 失敗時拒絕 Promise
            }
        });
    });
}


// 20240314 將訊息郵件發送給對的人~
function sendmail2(to_email, int_msg1_title, mg_msg){
    if(!debugMode.email){
        console.log('debug mode:');
        console.log(to_email);
        console.log(int_msg1_title);
        console.log( mg_msg);
        return Promise.resolve(true); // 在 debug 模式也回傳 Promise，保持一致性
    }
    return new Promise((resolve, reject) => {
        console.log(int_msg1_title); 
        $.ajax({
            url:'http://tneship.cminl.oa/api/sendmail/index.php',       // 正式2024新版
            method:'post',
            // async: false,                                               // ajax取得數據包後，可以return的重要參數
            dataType:'json',
            data:{
                uuid    : uuid,                                         // invest
                sysName : 'invest',                                     // 貫名
                to      : to_email,                                     // 傳送對象
                subject : int_msg1_title,                               // 信件標題
                body    : mg_msg                                        // 訊息內容
            },
            processData: false,                                         // 不處理資料
            contentType: false,                                         // 不設置 Content-Type，讓瀏覽器自動設置
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

$(async function () {

    let base_anis_msg =  `${int_msg2} 0 ${int_msg3}`;  // 組合訊息2元素 

    let mg_msg   = `${int_msg1}\n\n${base_anis_msg}\n\n${int_msg4}${invest_url}${srt_msg4}${int_msg5}`;         // 組合mapp訊息
    let mail_msg = `${int_msg1}\n\n${base_anis_msg}\n\n${int_msg4}${invest_url_mail}${srt_msg4}${int_msg5}`;    // 組合mail訊息
    // 定義每一封mail title
    let int_msg1_title = `${int_msg1}--未完結案共 0 件`;
// _fun1.step3. 訊息打包 
    let mg_arr = {
        title     : int_msg1_title,                     // 信件title
        anis_msg  : base_anis_msg,                      // 核心訊息
        mg_msg    : mg_msg,                             // 組合信件
        mail_msg  : mail_msg,                           // 組合信件
        emergency : ''                           // 急件統計
    }

    const mailMsg = mg_arr.mail_msg.replace(/(\r\n|\n)/g, '<br>');   // 251017 切換成mail格式
    await sendmail1((debugMode.test ? debugMode.to_email : to_email), mg_arr.title, mailMsg);
})