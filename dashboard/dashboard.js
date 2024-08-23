    // 0.取得sysRole
    function get_sysRole() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_sysRole.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                sys_role = JSON.parse(xhr.responseText);
            }
        };
        xhr.send();
    }
    // 1-1.仔子功能--生成fom的btn
    function make_formBtn(fc_value){
        const icon_s = '<i class="';
        const icon_e = ' fa-3x"></i>';
        if(fc_value.dcc_no){
            let int_b = "<button type='button' class='btn btn-outline-primary add_btn form_btn bs-b' value='../interView/form.php?dcc_no="+ fc_value.dcc_no +"' ";
                int_b += ((sys_role > 2.5 || !sys_role) ? "disabled":"onclick='openUrl(this.value)'") + " >";
                int_b += "<div class='col-12 p-1 pt-3'>" + icon_s + fc_value._icon + icon_e + "<h5 class='mb-0 mt-1'><b>- " + fc_value.short_name +" -</b><h5></div></button>";
            return int_b;
        }else{
            return '<snap class="btn btn-outline-secondary">' + fc_value.title + '</br>-- 無效json表單 --</snap>';
        }
    }
    // 1.子功能--鋪設form的btn外框
    function bring_form(formcases){
        $('#btn_list').empty();     // 鋪設前清除
        if(formcases){   
            for (const [key_1, value_1] of Object.entries(formcases)) {
                const int_btn = '<div class="col-12 text-center p-2">' + make_formBtn(value_1) + '</div>';
                $('#btn_list').append(int_btn);    // 渲染form
            }
        }
    }
    // 2.子功能--鋪設site外框
    function bring_site(site){
        $('#highLight').empty();     // 鋪設前清除
        if(site){   
            for (const [key_2, value_2] of Object.entries(site)) {
                const div_site = '<div class="card my-3 bs-b">'+'<h5 class="card-header">'+ value_2.site_title +' / '+ value_2.site_remark +'</h5>' 
                    +'<div class="card-body row" id="site_id_'+ value_2.id +'">'+'</div>'+'</div>';
                $('#highLight').append(div_site);    // 渲染form
            }
        }
    }
    // 3.子功能--鋪設fab按鈕+預設燈號
    function bring_fab(fab){
        if(fab){   
            for (const [key_3, value_3] of Object.entries(fab)) {
                const div_fab = '<div class="col-md-2 p-2 py-3 inb t-center">'
                        +'<a href="../caseList/?_month=All&_fab_id='+ value_3.id +'" class="btn rounded-pill btn-secondary " id="btn_fab_'+value_3.id+'">&nbsp'+ value_3.fab_title +'&nbsp</a>'+'</div>';
                $('#site_id_'+value_3.site_id).append(div_fab);    // 渲染form
            }
        }
    }
    // 4.子功能--鋪設fab燈號
    function bring_light(highlight){
        // console.log(highlight);
        if(highlight){   
            for (const [key_4, value_4] of Object.entries(highlight)) {
                target_btn = document.getElementById('btn_fab_'+value_4['id']);
                if(target_btn){
                    target_btn.classList.remove('btn-secondary');
                    target_btn.classList.add('btn-'+value_4.light);
                }
            }
        }
    }

    // 0-1.確認是否超過3小時；true=_db/更新時間；false=_json          // 呼叫來源：dashboard_init
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
    // 0-3.更新畫面上reload_time時間                  // 呼叫來源：recordTime
    function update_reloadTime(rightNow){
        reload_time.innerText = rightNow;       // 更新畫面上reload_time時間
    }

// // // 
    // 0-0.多功能擷取fun 新版改用fetch
    async function load_fun(fun, parm, myCallback) { // parm = 參數
        try {
            let formData = new FormData();
            formData.append('fun', fun);
            formData.append('parm', parm); // 後端依照fun進行parm參數的採用

            let response = await fetch('load_fun.php', {
                method: 'POST',
                body  : formData
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
    // 0-2.取得目前時間，並格式化；2.更新reloadTime.txt時間；完成後=>3.更新畫面上reload_time時間          // 呼叫來源：check3hourse
    async function recordTime(){
        let rightNow = new Date().toLocaleString('zh-TW', { hour12: false });                     // 取得今天日期時間
        try {
            await load_fun('urt' , rightNow+', true' , update_reloadTime);      
        } catch (error) {
            console.error(error);
        }
    }

    async function dashboard_init(action) {
        const _method = check3hourse(action);
        const _type = action ?  "_db" : _method;      // action來決定 false=自動判斷check3hourse 或 true=強制_db
        try {
            mloading(); 
                // await load_fun('_db',   'formcase,'       , bring_form);     // step_1 直接抓db(true/false 輸出json檔)，取得 formcase 內容後鋪設內容
                // await load_fun('_json', 'formcase, true'  , bring_form);     // step_1 先抓json，沒有then抓db(true/false 輸出json檔)，取得 formcase 內容後鋪設內容
            await load_fun(_type, 'formcase, true'  , bring_form);     // step_1 先抓json，沒有then抓db(true/false 輸出json檔)，取得 formcase 內容後鋪設內容
            await load_fun(_type, '_site, true'     , bring_site);     // step_2 先抓json，沒有then抓db(true/false 輸出json檔)，取得 _site    內容後鋪設內容
            await load_fun(_type, '_fab, true'      , bring_fab);      // step_3 先抓json，沒有then抓db(true/false 輸出json檔)，取得 _fab     內容後鋪設內容
            await load_fun(_type, 'highlight, true' , bring_light);    // step_4 先抓json，沒有then抓db(true/false 輸出json檔)，取得 highlight內容後鋪設內容

        } catch (error) {
            console.error(error);
        }
        await $("body").mLoading("hide");
    }
    $(function () {         // $(document).ready()
        $('[data-toggle="tooltip"]').tooltip(); // 在任何地方啟用工具提示框
        get_sysRole();
        dashboard_init(false);
    })