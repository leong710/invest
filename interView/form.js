
    $(function () {
        // 在任何地方啟用工具提示框
        $('[data-toggle="tooltip"]').tooltip();
        // 20230817 禁用Enter鍵表單自動提交 
        document.onkeydown = function(event) { 
            var target, code, tag; 
            if (!event) { 
                event = window.event;       //針對ie瀏覽器 
                target = event.srcElement; 
                code = event.keyCode; 
                if (code == 13) { 
                    tag = target.tagName; 
                    if (tag == "TEXTAREA") { return true; } 
                    else { return false; } 
                } 
            } else { 
                target = event.target;      //針對遵循w3c標準的瀏覽器，如Firefox 
                code = event.keyCode; 
                if (code == 13) { 
                    tag = target.tagName; 
                    if (tag == "INPUT") { return false; } 
                    else { return true; } 
                } 
            } 
        };
        // 監聽myModal被關閉時就執行--清除表格
        let searchUser_elm = document.getElementById('searchUser');
        searchUser_elm.addEventListener('hidden.bs.modal', function () {
            resetMain();                    // do something...清除欄位
            $('#modal_title').empty();      // 清除標題
        })
    })

    // 吐司顯示字條 // init toast
    function inside_toast(sinn){
        let toastLiveExample = document.getElementById('liveToast');
        let toast = new bootstrap.Toast(toastLiveExample);
        let toast_body = document.getElementById('toast-body');
        toast_body.innerHTML = sinn;
        toast.show();
    }

    // 20240506 local select生成
    function select_local(sortFab_id){
        $("#local_id").empty();
        $("#local_id").append('<option value="" hidden>-- [請選擇 廠別] --</option>');
        for (const [key, value] of Object.entries(locals)) {
            if(value['fab_id'] == sortFab_id){
                let lv = '<option value="'+value['id']+'" class="form-label" '+(value['flag'] == 'Off' ? 'disabled':'')+' >'+value['id']+'：'+value['local_title']+(value['flag'] == 'Off' ? ' -- disabled':'')+'</option>';
                $("#local_id").append(lv);
            }
        }
    }

    // 20240506 saveSubmit modal添加save功能
    function changeMode(mode){
        $('#modal_action, #submit_action').empty();         // 清除model標題和btn功能
        $('#modal_action').append(mode);                    // 炫染model標題

        if(mode == 'submit'){
            $('#idty').val('1');                            // 1 = 送出
            $('#saveSubmit .modal-header').removeClass('bg-success').addClass("bg-primary");    // 切換標題底色
            $('#modal_body').removeClass('unblock');        // 切換sign command
            let submit_btn = '<button type="submit" value="Submit" name="submit_document" class="btn btn-primary" ><i class="fa fa-paper-plane" aria-hidden="true"></i> 送出 (Submit)</button>';
            $('#submit_action').append(submit_btn);         // model_btn功能
            
        }else if(mode == 'save'){
            $('#idty').val('6');                            // 6 = 暫存
            $('#saveSubmit .modal-header').removeClass('bg-primary').addClass("bg-success");    // 切換標題底色
            $('#modal_body').addClass("unblock");           // 切換sign command
            let save_btn =   '<button type="submit" value="Save" name="save_document" class="btn btn-success" onclick="setFormBequired()" ><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> 儲存 (Save)</button>';
            $('#submit_action').append(save_btn);           // model_btn功能

        }
    }
    
    // tab_table的顯示關閉功能
    function op_tab(tab_value){
        $("#"+tab_value+"_btn .fa-chevron-circle-down").toggleClass("fa-chevron-circle-up");
        var tab_table = document.getElementById(tab_value+"_table");
        if (tab_table.style.display === "none") {
            tab_table.style.display = "table";
        } else {
            tab_table.style.display = "none";
        }
    }

// // searchUser function 
    // fun_0.清除searchUser_modal
    function resetMain(){
        $("#result").removeClass("border rounded bg-white");
        $('#result_table').empty();                                 // 搜尋清單
        // $('#modal_title').empty();                                  // 標題
        document.querySelector('#key_word').value = '';             // 搜尋key_word input
    }
    // fun_1.search Key_word
    function search_fun(){
        mloading("show");                                           // 啟用mLoading
        const uuid = '39aad298-a041-11ed-8ed4-2cfda183ef4f';        // hrdb
        let search = $('#key_word').val().trim();                   // search keyword取自user欄位
        let request = {
            functionname : 'search',                                // 操作功能
            uuid         : uuid,                                    // ppe
            search       : search                                   // 查詢對象key_word
        }
        $.ajax({
            url: 'http://tneship.cminl.oa/api/hrdb/index.php',      // 正式2024新版
            method: 'post',
            dataType: 'json',
            data: request,
            success: function(res){
                let res_r = res["result"];
                postList(res_r);                                    // 將結果轉給postList進行渲染
            },
            error (err){
                console.log("search error:", err);
                $("body").mLoading("hide");
                alert("查詢錯誤!!");
            }
        })

        $("body").mLoading("hide");                                 // 關閉mLoading
    }
    // fun_2.渲染功能
    function postList(res_r){
        // 清除表頭
        $('#result_table').empty();
        $("#result").addClass("bg-white");
        // 定義表格頭段
        let div_result_table = document.querySelector('.result table');
        let Rinner = "<thead><tr>"+
                        "<th>員工編號</th>"+"<th>員工姓名</th>"+"<th>職稱</th>"+"<th>user_ID</th>"+"<th>部門代號</th>"+"<th>部門名稱</th>"+"<th>select</th>"+
                    "</tr></thead>" + "<tbody id='tbody'>"+"</tbody>";
        // 鋪設表格頭段thead
        div_result_table.innerHTML += Rinner;
        // 定義表格中段tbody
        let div_result_tbody = document.querySelector('.result table tbody');
        $('#tbody').empty();
        for (let i=0; i < res_r.length; i++) {
            // 把user訊息包成json字串以便夾帶
                let user_json = {
                        'emp_id'    : res_r[i].emp_id.trim(),
                        'cname'     : res_r[i].cname.trim(),
                        'cstext'    : res_r[i].cstext.trim(),
                        'oftext'   : res_r[i].dept_no.trim() +'\/'+ res_r[i].oftext
                    };
            // let user_json = res_r[i].emp_id.trim() +','+ res_r[i].cname.trim() +','+ res_r[i].cstext.trim() + ',' + res_r[i].dept_no.trim() + '\/' + res_r[i].oftext;
            div_result_tbody.innerHTML += 
                '<tr>' +
                    '<td>' + res_r[i].emp_id.trim() +'</td>' +
                    '<td>' + res_r[i].cname.trim() + '</td>' +
                    '<td>' + res_r[i].cstext.trim() + '</td>' +
                    '<td>' + res_r[i].user.trim() + '</td>' +
                    '<td>' + res_r[i].dept_no.trim() + '</td>' +
                    '<td>' + res_r[i].oftext.trim() + '</td>' +
                    '<td>' + '<button type="button" class="btn btn-default btn-xs" id="'+res_r[i].emp_id+'" value=\''+ JSON.stringify(user_json) +'\' onclick="tagsInput_me(this.value)">'+
                    '<i class="fa-regular fa-circle"></i></button>' + '</td>' +
                '</tr>';
        }

    }
    // fun_3.點選、渲染模組
    function tagsInput_me(val) {
        if (val !== '') {
            let personal_inf = JSON.parse(val);
            let emp_id = personal_inf['emp_id'];        // 指定emp_id 
            let cname  = personal_inf['cname'];         // 指定cname 

            if(meeting_man_target == 'emp_id_btn'){     // 來自事故者基本資訊
                Object.keys(personal_inf).forEach((_key)=>{
                    let _key_elem = document.querySelector('#'+_key)
                    if(_key_elem){
                        _key_elem.value = personal_inf[_key]
                    }
                })
                searchUser_modal.hide();      // 關閉searchUser_modal

            }else{                                      // 來自會議title
                val = JSON.stringify({
                    "cname"  : cname,
                    "emp_id" : emp_id
                })
                window[meeting_man_target].push(val);
                $('#'+meeting_man_target+'_show').append('<div class="tag">' + cname + ' / '+ emp_id + '<span class="remove">x</span></div>');
                let tag_user = document.getElementById(emp_id);
                if(tag_user){ tag_user.value = ''; }
                let meeting_man_target_select = document.getElementById(meeting_man_target+'_select');
                if(meeting_man_target_select){
                    meeting_man_target_select.value = window[meeting_man_target];
                }
            }
        }
        // resetMain();
        // searchUser_modal.hide();      // 切到searchUser頁面
    }
    // fun_4.移除單項模組
    $('#meeting_man_a_show, #meeting_man_o_show, #meeting_man_s_show ').on('click', '.remove', function() {
        let this_parent     = $(this).parent().parent();                // 取得爺層的元素
        let this_parent_id  = this_parent[0].id.replace('_show', '');   // 取得爺層的id，並去除_show
        let tagIndex        = $(this).closest('.tag').index();          // 取得點擊index位置
        let tagg            = window[this_parent_id][tagIndex];         // 取得目標數值 emp_id,cname
        let emp_id          = tagg.substr(0, tagg.search(','));         // 指定 emp_id
            let tag_user        = document.getElementById(emp_id);
            if(tag_user){ 
                tag_user.value = tagg; 
            }
        window[this_parent_id].splice(tagIndex, 1);                     // 自陣列中移除
        $(this).closest('.tag').remove();                               // 自畫面中移除
        let _select = document.getElementById(this_parent_id+'_select');
        if(_select){
            _select.value = window[this_parent_id];
        }
    });
// // searchUser function 

    // Option其他選項遮蔽：On、Off
    function onchange_option(name){
        var opts = document.querySelectorAll('[name="'+name+'"].other_item')
        opts.forEach((opt)=>{
            let opt_id_o = document.querySelector('#'+opt.id+'_o');
            if(opt.checked){
                opt_id_o.classList.remove('unblock');
                opt_id_o.removeAttribute("disabled");
                opt_id_o.focus();

                if ($("#"+opt.id).hasClass("only_option")){         // 唯一選項
                    let only_opts = document.querySelectorAll('[name="'+opt.name+'"]')
                    only_opts.forEach(function(checkbox) {
                        // Check if the current checkbox is not the fourth one and is checked
                        if ((checkbox.id !== opt.id ) && checkbox.checked) {
                            // If so, uncheck it
                            checkbox.checked = false;
                            $("#"+checkbox.id+'_o').addClass('unblock');
                            opt_id_o.removeAttribute("disabled");
                        }
                    });
                }

            }else{
                // opt_id_o.value = "";
                opt_id_o.classList.add('unblock');
                opt_id_o.setAttribute("disabled", "disabled");
            }
        })
    }
    // a_pic的 uploadFile 函数
    function uploadFile(key) {
        let formData = new FormData();
        let fileInput = document.getElementById(key + '_row');                                                          // 取得row input檔名
        formData.append('file', fileInput.files[0]);
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'upload.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                let response = JSON.parse(xhr.responseText);                                                            // 接收回傳
                let preview_modal = '<a href="' + response.filePath + '" target="_blank" >';                            // 生成預覽按鈕a
                let src_img = '<img src="' + response.filePath + '" class="img-thumbnail" style="width: 50%;">';        // 生成img
                document.getElementById('preview_' + key).innerHTML = preview_modal + src_img +'</a>';                  // 套上a+img
                document.getElementById(key).value = response.fileName;                                                 // a_pic加上時間搓

            } else {
                alert('Upload failed. Please try again.');
            }
        };
        xhr.send(formData);
    }
    // a_pic的 uplinkFile 函数
    function unlinkFile(key) {
        let formData = new FormData();
        let unlinkFile = document.getElementById(key).value;                                                          // 取得a_pic加上時間搓
        formData.append('unlinkFile', unlinkFile);
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'unlink.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                // let response = JSON.parse(xhr.responseText);                                                            // 接收回傳
                document.getElementById('preview_' + key).innerHTML = '-- preView --';                                  // 清除preview
                document.getElementById(key + '_row').value = '';                                                       // 清除選擇row檔案
                document.getElementById(key).value = '';                                                                // 清除a_pic

            } else {
                alert('unlink failed. Please try again.');
            }
        };
        xhr.send(formData);
    }
    // signature簽名板
    async function signature_canva() {
        return new Promise((resolve) => { 
            var signaturePads = {};
            // Initialize Signature Pad for each canvas
            var canvases = document.querySelectorAll('canvas');
            canvases.forEach((canvas, index)=>{
                var signaturePad = new SignaturePad(canvas);
                signaturePads[canvas.id] = signaturePad;
            })

            // Attach event listeners to clear and save buttons
            var clearButtons = document.querySelectorAll('.clear-btn');
            clearButtons.forEach(function(button) {
                button.addEventListener('click', function(event) {
                    var padNumber = button.dataset.pad;
                    var signaturePad = signaturePads[padNumber + '_signaturePad'];
                    var signatureImage = document.getElementById(padNumber + '_signature-image');
                    $('#' + padNumber + '_signature-input').val('');            // base64儲存格
                    signaturePad.clear();                                       // 手寫盤
                    // signatureImage.src = '../image/signin_empty.png';           // 清除預覽圖並給預設值
                });
            });
            var saveButtons = document.querySelectorAll('.save-btn');
            saveButtons.forEach(function(button) {
                button.addEventListener('click', function(event) {
                    var padNumber = button.dataset.pad;
                    var signaturePad = signaturePads[padNumber + '_signaturePad'];
                    var signatureImage = document.getElementById(padNumber + '_signature-image');
                    if (signaturePad.isEmpty()) {
                        alert("Please provide a signature first.");
                    } else {
                        var dataURL = signaturePad.toDataURL();
                        signatureImage.src = dataURL;                           // 預覽圖
                        $('#' + padNumber + '_signature-input').val(dataURL);   // base64儲存格
                            // // 20240517_創建包含簽名和UUID的對象
                               // var uuid = 'a6c56412-0ce3-11ef-8582-2cfda183ef4f';
                               // var signatureData = {
                               //     uuid: uuid,
                               //     signature: dataURL
                               // };
                               // var signatureJSON = JSON.stringify(signatureData);              // 將對象轉換為JSON字串
                               // var encodedSignature = btoa(signatureJSON);                     // 將JSON字串進行base64編碼
                               // $('#' + padNumber + '_signature-input').val(encodedSignature);  // 將編碼後的字串儲存在輸入框中
                    }
                });
            });
            resolve(); // 文件載入成功，resolve
        });
    };

    async function eventListener(){
        return new Promise((resolve) => { 
            // 定義+監聽按鈕for與會人員...search btn id
            let search_btns = Array.from(document.querySelectorAll(".search_btn"));
            search_btns.forEach((s_btn)=>{
                s_btn.addEventListener('mousedown',function(){
                    // 標籤
                    let modal_title
                    if(this.id == 'meeting_man_a'){
                        modal_title = '事故當事者(或其委任代理人)'
                    }else if(this.id == 'meeting_man_o'){
                        modal_title = '其他與會人員'
                    }else if(this.id == 'meeting_man_s'){
                        modal_title = '環安人員'
                    }else if(this.id == 'emp_id_btn'){
                        modal_title = '事故者基本資料'
                    }
                    $('#modal_title').append(modal_title)
                    meeting_man_target = this.id;               // 搜尋meeting_man_target
                })
            })    

            // 20240507 -- 監聽 negative_opts 負向選項;
            let radios_all    = Array.from(document.querySelectorAll("input[type='radio'], input[type='checkbox']"));   // 取得所有radio和checkbox
            let negative_opts = Array.from(document.querySelectorAll(".negative"));         // 負向選項
            let get_negatives = Array.from(document.querySelectorAll(".get_negative"));     // 負向to對象
            let radios        = radios_all.filter(radio => !get_negatives.includes(radio)); // 將所有radio和checkbox清單中移除 負向to對象，避免干擾
            // 监听单选按钮组中的任何一个单选按钮的 change 事件
                radios.forEach((rdo) => {
                    rdo.addEventListener('change', () => {
                        // 检查当前单选按钮是否是负向选项
                        // if (negative_opts.includes(rdo)) {   // 原本是過濾點選事件是否為[負向選項]，取消原因是要讓非[負向選項]也可以進行滾算
                            // 计算 negative_arr 数组
                            negative_arr = negative_opts.filter(opt => opt.checked).map(opt => opt.id);
                        // }
                        // 根据 negative_arr 数组的长度设置 get_negatives 的状态
                        get_negatives.forEach((get_n) => {
                            let isChecked = negative_arr.length > 0;
                            get_n.checked = isChecked;
                            // $('#'+get_n.id+'_o').toggleClass('unblock', !isChecked);
                            $('#'+get_n.id+'_o').toggleClass('unblock', !isChecked).prop("disabled", !isChecked);
                        });
                    });
                });

            // 監聽工作起訖日欄位(id=a_work_e)，自動確認是否結束大於開始
            $('#a_work_s, #a_work_e').change(function() {
                let currentDate = new Date();                       // 取得今天日期
                let a_work_s = new Date($("#a_work_s").val());      // 取得起始
                let a_work_e = new Date($("#a_work_e").val());      // 取得訖止
                // 工作起始需不需要小於現在時間....需要確認
                if(this.id == 'a_work_s'){
                    $("#a_work_s").removeClass("is-valid is-invalid").addClass(a_work_s < currentDate ? "is-valid" : "is-invalid");
                }
                // 訖止時間需大於起始時間....
                $("#a_work_e").removeClass("is-valid is-invalid").addClass(a_work_s < a_work_e ? "is-valid" : "is-invalid");
            });
            
            // 監聽到職日欄位(id=hired)，自動計算年資並output(id=rload)
            // 監聽事故時間欄位(id=a_day)，自動確認是否結束大於開始
            $('#hired, #a_day ,#anis_day').change(function() {
                let currentDate = new Date();                               // 取得今天日期
                let hired       = new Date($("#hired").val());              // 取得到職日
                let a_day       = new Date($("#a_day").val());              // 取得事故日期
                let anis_day    = new Date($("#anis_day").val());           // 取得事故日期
                // 到職日不得大於現在時間....
                if(this.id == 'hired'){
                    // if(currentDate >= hired){
                    if(a_day >= hired){
                        // 計算年月日
                        // let difference  = currentDate - hired;        // 計算日期差距（毫秒單位）  ==> 開會當天-到職日 = 年資
                        let difference  = a_day - hired;        // 計算日期差距（毫秒單位）  ==> 事故日-到職日 = 年資
                        let years       = Math.floor(difference / (365.25 * 24 * 60 * 60 * 1000));
                        let months      = Math.floor((difference % (365.25 * 24 * 60 * 60 * 1000)) / (30.44 * 24 * 60 * 60 * 1000));
                        let days        = Math.floor((difference % (30.44 * 24 * 60 * 60 * 1000)) / (24 * 60 * 60 * 1000));
                        // 輸出结果
                        $("#rload").val("估算約：" + years + " 年 " + months + " 個月 " + days + " 天");
                    }else{
                        $("#rload").val('');
                    }
    
                    $("#hired").removeClass("is-valid is-invalid").addClass(hired < currentDate ? "is-valid" : "is-invalid");
                }
                // anis立案日不得大於現在時間....
                if(this.id == 'a_day'){
                    $("#a_day").removeClass("is-valid is-invalid").addClass(a_day < currentDate ? "is-valid" : "is-invalid");
                }
                // anis立案日不得大於現在時間....
                if(this.id == 'anis_day'){
                    $("#anis_day").removeClass("is-valid is-invalid").addClass(a_day <= anis_day && anis_day < currentDate ? "is-valid" : "is-invalid");
                }
                // 事故時間需大於到職日，並小於現在時間....
                if(!isNaN(hired)){          // 適用交通
                    $("#a_day").removeClass("is-valid is-invalid").addClass(hired <= a_day && a_day < currentDate ? "is-valid" : "is-invalid");
                }
                if(!isNaN(anis_day)){       // 適用廠內
                    $("#a_day").removeClass("is-valid is-invalid").addClass(a_day <= anis_day && a_day < currentDate ? "is-valid" : "is-invalid");
                }
            });
            resolve(); // 文件載入成功，resolve
        });
    }
    // 20240517 -- 暫存表單
    function setFormBequired(){
        console.log('setFormBequired...');
        var form = document.getElementById('item_list');                // 獲取表單元素
        var requiredElements = form.querySelectorAll('[required]');     // 獲取表單內所有含有 required 屬性的元素
        requiredElements.forEach(function(element) {                    // 遍歷這些元素，移除 required 屬性並添加 bequired 屬性
            element.removeAttribute('required');
            element.setAttribute('bequired', 'true');
        });
    }

// // step_1 表單生成 function 
    // 動態表單主fun -- JSON轉表單；依據不同的key_type進行切換型別 HARD CODED
    function make_question(session_key, key_class, item_a) {        // 接收參數：session, class, 單一問項
        let int_a = '';
        let dcff = '<div class="form-floating">';
        // 共用部分的操作1 label標籤
        function commonPart() {
            let labelSuffix = item_a.required ? '<sup class="text-danger"> *</sup>' : '';
            return '<label for="' + item_a.name + '" >' + item_a.label + '：' + labelSuffix +'</label>';
        }
        // 共用部分的操作2 驗證回饋
        function validPart() {
            return '<div class="invalid-feedback" id="' + item_a.name + '_feedback">數值填入錯誤 ~ </div>';
        }
        // 共用部分的操作3 info   有單文字和物件
        function infoPart() {
            let info_temp = '';
            if(typeof item_a.info !== 'object'){
                info_temp += ' >>> ' + item_a.info;
            }else{
                for (const [key_1, value_1] of Object.entries(item_a.info)) {
                    if(info_temp){
                        info_temp += '<br/>'
                    }
                    info_temp += key_1 + '.' + value_1
                }
            }
            return '<span class="info">' + info_temp + '</span>';
        }
        // 日期格式化函數
        function formatDate(date) {
            return date.toISOString().slice(0, item_a.type === 'date' ? 10 : 16);
        }
        // 主要fun：內層問項生成：根據字段類型生成相應的表單元素
        switch(item_a.type) {
            case 'text':
                int_a = '<input type="text" name="' + item_a.name + '" id="' + item_a.name + '" class="form-control mb-0" placeholder="' + item_a.label + '" '+ (item_a.required ? 'required' : '') + '>' + commonPart();
                if(item_a.name == 'emp_id'){
                    int_a = '<div class="input-group form-floating">' + int_a 
                        + '<button type="button" class="btn btn-outline-primary search_btn" id="emp_id_btn" data-toggle="tooltip" data-placement="bottom" title="以工號自動帶出其他資訊" '
                        + ' data-bs-target="#searchUser" data-bs-toggle="modal" >'+'<i class="fa-solid fa-magnifying-glass"></i> 搜尋</button>'+'</div>';
                }else{
                    int_a = dcff + int_a + '</div>';
                }
                break;
            case 'date':
            case 'datetime':
                int_a = dcff +
                        // '<input type="' + (item_a.type === 'date' ? 'date' : 'datetime-local') + '" name="' + item_a.name + '" class="form-control" id="' + item_a.name + '" value="' + formatDate(new Date()) + '" ' +
                        '<input type="' + (item_a.type === 'date' ? 'date' : 'datetime-local') + '" name="' + item_a.name + '" class="form-control " id="' + item_a.name + '" value="" ' +
                        (item_a.required ? 'required' : '') + '>' + commonPart() + (item_a.valid ? validPart() : '') + '</div>';
                break;
            case 'textarea':
                int_a = dcff +
                    '<textarea name="' + item_a.name + '" id="' + item_a.name + '" class="form-control " style="height: 100px" placeholder="' + item_a.label + '"' 
                    + (item_a.required ? 'required' : '') + '>' + '</textarea>' + commonPart() + '</div>';

                // int_a = '<div class="p-2">' + int_a + '</div>';
                break;
            case 'radio':
            case 'checkbox':
                int_a = '<div class=" border rounded p-2"><snap><b>*** ' + item_a.label + '：' + (item_a.required ? '<sup class="text-danger"> *</sup>' : '') + '</b></snap><br>';
                Object(item_a.options).forEach((option)=>{
                    // let object_type = ((typeof option.value == 'object') ? option.label : option.value);   // for other's value
                    let object_type = ((typeof option.value == 'object') ? option.label : option.value);   // for other's value
                    // int_a += '<div class="form-check bg-light rounded"><input type="' + item_a.type + '" name="' + item_a.name + (item_a.type == 'checkbox' ? '[]':'') + '" value="' + object_type + '" '
                    int_a += '<div class="form-check bg-light rounded"><input type="' + item_a.type + '" name="' + item_a.name + '[]' + '" value="' + object_type + '" '
                          + ' id="' + item_a.name + '_' + object_type + '" ' + (item_a.required ? ' required ' : '') + 'onchange="onchange_option(this.name)" ' 
                          + ' class="form-check-input ' + ((typeof option.value === 'object') ? ' other_item ' : '') + (option.value.only ? ' only_option ' : '') 
                            + ((item_a.negative != undefined && item_a.negative == object_type) ? 'negative ' : '') 
                            + ((item_a.get_negative != undefined && item_a.get_negative == object_type) ? 'get_negative ' : '') 
                          + '" >' + '<label class="form-check-label '
                            + ((item_a.negative != undefined && item_a.negative == object_type) ? 'negative ' : '') 
                            + ((item_a.get_negative != undefined && item_a.get_negative == object_type) ? 'get_negative ' : '') 
                          + '" for="' + item_a.name + '_' + object_type + '">' + option.label + (typeof option.value === 'object' ? '：' : '') 
                          + '</label></div>';

                    if (typeof option.value === 'object' && option.value.type == 'text') {
                        // int_a += '<input type="'+ option.value.type +'" name="' + option.value.name + (item_a.type == 'checkbox' ? '[]':'') + '" '
                        int_a += '<input type="'+ option.value.type +'" name="' + option.value.name + '[]' + '" '
                            + ' placeholder="' + option.value.label + '" id="' + item_a.name + '_' + option.label + '_o" class="form-control unblock" disabled >';
                    }
                }) 
                int_a += '</div>';
                break;
            case 'file':       // session_2 事故位置簡圖
                int_a = '<div class="col-6 col-md-6 a_pic" id="preview_' + item_a.name + '" > -- preView -- </div>'
                    + '<input type="hidden" name="' + item_a.name + '" id="' + item_a.name + '" ' + (item_a.required ? 'required' : '' ) + '>'
                if(!check_action){
                    int_a += '<div class="col-6 col-md-6 py-0 px-2"><div class="col-12 bg-white border rounded ">' + commonPart()
                        + '<div class="input-group "><input type="file" name="' + item_a.name + '_row" id="' + item_a.name + '_row" class="form-control mb-0" accept=".jpg,.png,.gif,.bmp" >'
                        + '<button type="button" class="btn btn-outline-success" onclick="uploadFile(\'' + item_a.name + '\')">Upload</button>' 
                        + '<button type="button" class="btn btn-outline-danger" onclick="unlinkFile(\'' + item_a.name + '\')">Delete</button>' 
                        + '</div></div>' + '</div>'
                }
                break;  
            case 'signature':   // 簽名模組
                int_a = '<div class="col-12 border rounded ">'
                    + '<snap class="p-0" ><b>*** ' + item_a.label + '：' + (item_a.required ? '<sup class="text-danger"> *</sup>' : '') + '</b></snap>'
                    + '<div class="row">' 

                    + '<div class="col-12 col-md-6 text-center"><img id="' + item_a.name + '_signature-image" src="../image/signin_empty.png" alt="Signature Image" class="img-thumbnail" >'
                    + '<br><input type="hidden" name="' + item_a.name + '" id="' + item_a.name + '_signature-input" ' + (item_a.required ? 'required' : '' ) + '>'
                    +'</div>' 
                if(!check_action){
                    int_a += '<div class="col-12 col-md-6 text-center">'
                        + '<canvas id="' + item_a.name + '_signaturePad" width="400" height="250" class=" border rounded p-2 bg-light signature"></canvas>'
                        + '<div class="py-1">'
                        + '<button type="button" class="btn btn-outline-info clear-btn" data-pad="' + item_a.name + '">Clear</button>'+'&nbsp'
                        + '<button type="button" class="btn btn-outline-success save-btn" data-pad="' + item_a.name + '">Save Signature</button>'
                        + '</div>' + '</div>'
                }
                int_a += '</div>' + '</div>'
                break;
        }
        // 有info就呼叫fun崁入
        int_a += (item_a.info) ? infoPart() : '';
        // 外層session包裝 // 將表單元素添加到特定的容器中
        if(key_class && item_a.type != 'signature'){
            int_a = '<div class="'+ key_class +'">' + int_a + '</div>';
        }else if(item_a.type == 'signature'){
            int_a = '<div class="col-12 p-2">' + int_a + '</div>';
        }
        // 渲染form
        $('#' + session_key +' .accordion-body').append(int_a);      
    }
    // 20240501 -- // 動態表單主fun -- JSON轉表單
    function bring_form(form_json){
        let form_item     = form_json.form_item;            // 抓item項目for form item
        // step_0.前置工作、生成表頭
        if(form_json.form_title){ $('#form_title').empty().append(form_json.form_title);  }     // 文件標題
        if(form_json.dcc_no){     $('#dcc_no_head').empty().append(form_json.dcc_no); }         // DCC編號
        if(form_json.version){    $('#dcc_no_head').append('-' + form_json.version); }          // 文件版本
        let dcc_no_input = document.querySelector('#dcc_no');                                   // 
        if(dcc_no_input && form_json.dcc_no && form_json.version){ 
            dcc_no_input.value = form_json.dcc_no+'-'+form_json.version;
        }
        // let form_doc = document.getElementById('item_list');                                    // 定義動態表單id位置
        if(form_item){                                                                          // confirm form_item is't empty
            // console.log('step_1-2 make_question(key_1, value_1.class, item_value) -- ');
            for (const [key_1, value_1] of Object.entries(form_item)) {
                // step_1.生成session_title
                let match;
                const regex = new RegExp('session', 'gi');
                if ((match = regex.exec(key_1)) !== null) {
                    let int_1 = '<div class="accordion-item">';                 // 使用手風琴模組
                    if (value_1.label.length != 0) {
                        int_1 += '<h5 class="accordion-header" id="' + key_1 + '_head">'+
                            '<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#' + key_1 + '" aria-expanded="true" aria-controls="' + key_1 + '">'+
                            '<b>※&nbsp' + key_1 + '&nbsp' + value_1.label + '：</b>'+ '</button></h5>';
                    }
                    int_1 += '<div id="' + key_1 + '"  class="accordion-collapse collapse show" aria-labelledby="' + key_1 + '_head" > '
                        + (value_1.info ? '&nbsp' + value_1.info : '') 
                        +'<div class="row accordion-body">'
                        +'</div></div></div>'
    
                    $('#item_list').append(int_1);
                }
                // step_2.生成問項...將每一筆繞出來
                Object(value_1.item).forEach((item_value)=>{
                    make_question(key_1, value_1.class, item_value);
                })
            }

            let int_end = '<div class="col-12 mt-3 py-0 rounded bg-success text-white text-center">-- 問卷底部 --</div>'
            $('#item_list').append(int_end);
            return true;
        } else {
            return false;
        }
    }

// // step_2 文件填入 function 
    // edit 副函數：鋪設渲染_表頭
    function reShow_info(document_row){
        // 1.會議info
        let meeting_info1_arr = ['anis_no','fab_id', 'local_id', 'case_title', 'a_dept', 'meeting_time', 'meeting_local', 'uuid' ,'meeting_man_d'];
        meeting_info1_arr.forEach((meeting_info1)=>{
            if(document_row[meeting_info1]){
                document.querySelector('#'+meeting_info1).value = document_row[meeting_info1]; 
                if( meeting_info1 == "fab_id"){
                    select_local(document_row[meeting_info1]);          // 使用fab_id.value呼叫select_local生成select option
                }
            }
        })
        // 2.與會人員
        let meeting_info2_arr = ['meeting_man_a', 'meeting_man_o', 'meeting_man_s'];
        meeting_info2_arr.forEach((meeting_man)=>{
            // console.log(meeting_man, Object.keys(document_row[meeting_man]).length);
            if(Object.keys(document_row[meeting_man]).length >=1 ){
                meeting_man_target = meeting_man;                                 // let key => target
                meeting_man_val = JSON.parse('['+document_row[meeting_man]+']');  // 取出的字串藥先用 [ ] 包起來，再轉成JSON物件

                for(let i=0; i < meeting_man_val.length; i++){                    // 依照物件長度進行遶圈
                    tagsInput_me(JSON.stringify(meeting_man_val[i]));             // 轉成字串進行渲染
                }
            }
        })
    }
    // edit 主函數
    function edit_show(document_row){
        // console.log('step_2-1 edit_show(document_row)：', '填入表單');
        // edit step0.更換submit按鈕型態
            let update_btn = '<button type="submit" value="update" name="update_document" class="btn btn-primary" ><i class="fa fa-paper-plane" aria-hidden="true"></i> Update (Submit)</button>'
            $('#submit_action').empty();
            $('#submit_action').append(update_btn);
        
        // edit step1.呼叫fun鋪設渲染_表頭：'case_title','a_dept','meeting_time','meeting_local','meeting_man_a','meeting_man_o','meeting_man_s','uuid','id'
            // console.log('step_2-1-1 reShow_info(document_row) -- 渲染表頭');
            reShow_info(document_row);

        // edit step2.特例呈現：'confirm_sign','ruling_sign','a_pic'
            // console.log('step_2-1-2 special_items.forEach((special_item)=> -- 特例呈現');
            let special_items = ['confirm_sign','ruling_sign','a_pic']
            special_items.forEach((special_item)=>{
                if(document_row[special_item] != null){
                    if(special_item == 'a_pic'){        // 路線圖檔
                        let a_pic_path     = '../image/a_pic/'                                                                      // 指定pic路徑
                        let a_pic_val      = document_row[special_item];                                                            // 取得pic_value
                        let preview_modal  = '<a href="'  + a_pic_path + a_pic_val + '" target="_blank" >';                         // 生成預覽按鈕a
                        let src_img        = '<img src="' + a_pic_path + a_pic_val + '" class="img-thumbnail" style="width: 50%;">';// 生成img
                        let preview_item   = document.getElementById('preview_' + special_item); 
                        if(preview_item){
                            preview_item.innerHTML = preview_modal + src_img +'</a>';                                               // 套上a+img
                        }            
                        let input_item     = document.getElementById(special_item);
                        if(input_item){
                            input_item.value = a_pic_val;                                                                           // 欄位填上pic_value
                        }
                    }else{                              // 簽名
                        let base64_sign    = document_row[special_item];
                        let signatureImage = document.getElementById(special_item+'_signature-image');
                        signatureImage.src = base64_sign;                           // 渲染預覽圖
                        $('#'+special_item+'_signature-input').val(base64_sign);    // 填上base64儲存格
                    }
                }
            })

        // edit step3.內容呈現
            // console.log('step_2-1-3 document_row["_content"] -- 內容呈現');
            let _content = document_row['_content']
            let match;
            Object.keys(_content).forEach(function(content_key){        // 將原陣列_content逐筆繞出來
                let option_value = _content[content_key];
                    // console.log('_key, _value : ', content_key, option_value);
                const regex = new RegExp('combo', 'gi');                // 建立比對文字'combo'
                if ((match = regex.exec(content_key)) === null) {       // 非combo選項，直接帶入value
                    $('#'+content_key).val(option_value); 
                    
                }else{                                                  // combo選項，需要特例檢查，以便開啟其他輸入
                    if(option_value !== null){                          // 預防空值null
                        option_value.forEach((item_value, index)=>{
                            // if (['其他', '無', 'Other', '1', '2', '3'].includes(option_value[index-1])) {       // ** 當你的上一個value，有涉及到'其他','無','否'，就將它的例外input_o打開，並帶入value
                            if (['其他', '無', '否', '有', 'Other'].includes(option_value[index-1])) {     // ** 當你的上一個value，有涉及到'其他','無','否'，就將它的例外input_o打開，並帶入value
                                $('#' + content_key + '_' + option_value[index-1] + '_o').removeClass('unblock').removeAttr("disabled").val(item_value);
                            }else{                                                         // ** 如果沒有就直接帶入value  // checkbox和redio都適用
                                $('#' + content_key + '_' + item_value).prop('checked', true);
                            }
                        })
                    }
                }
            })

        // edit step9.鋪設logs紀錄
            let json         = JSON.parse(document_row["logs"]);
            let forTable = document.querySelector('.logs tbody');
            $('#logs_div').removeClass('unblock');                              // 解除隱藏
            for (let i = 0, len = json.length; i < len; i++) {
                json[i].remark = json[i].remark.replaceAll('_rn_', '<br>');     // *20231205 加入換行符號
                forTable.innerHTML += 
                    '<tr><td>' + json[i].step + '</td><td>' + json[i].cname + '</td><td>' + json[i].datetime + '</td><td>' + json[i].action + 
                        '</td><td style="text-align: left; word-break: break-all;">' + json[i].remark + '</td></tr>';
            }

        // edit step10.鋪設Editions紀錄
            let editions_arr = JSON.parse(document_row["editions"]);
            if(editions_arr != null && editions_arr.length >0){
                let editTable = document.querySelector('.editions tbody');
                $('#editions_div').removeClass('unblock');                              // 解除隱藏
                for (let i = 0; i < editions_arr.length;  i++) {
                    let content_item =''
                    for (const [u_key, u_value] of Object.entries(editions_arr[i].update_document)) {
                        if(typeof u_value == 'object'){
                            content_item += '<b>◎&nbsp'+u_key+'：</b></br>';
                            for (const [u_item_key, u_item_value] of Object.entries(u_value)) {
                                content_item += '&nbsp&nbsp&nbsp-&nbsp'+u_item_key+'：'+u_item_value+'</br>';
                            }
                        }else{
                            content_item += '<b>◎&nbsp'+u_key+'：</b>'+u_value+'</br>';
                        }
                    }
                    editTable.innerHTML += '<tr><td>' + (i+1) + '</td><td>' + editions_arr[i].updated_cname + '</td><td>' + editions_arr[i].updated_at 
                            + '</td><td style="text-align: left; word-break: break-all;">' + content_item + '</td></tr>';
                }
            }

        // let sinn = 'submit - ( '+swal_json['fun']+' : '+swal_json['content']+' ) <b>'+ swal_json['action'] +'</b>&nbsp!!';
        let sinn = action + '&nbsp模式開啟，表單套用成功&nbsp!!';
        inside_toast(sinn);
        return true;
    }

// // 20240430 -- step_1 由dcc_no取得表單form_json，再由bring_form(form_json)生成表單
    async function load_form(dcc_no) {
        // console.log('step_1 load_form(dcc_no)：', dcc_no);
        return new Promise((resolve, reject) => {
            let formData = new FormData();
            formData.append('dcc_no', dcc_no);
            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'load_form.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);    // 接收回傳
                    let form_json = response['form_json'];          // 取得所要的部分
                    resolve(bring_form(form_json))                  // step_1-1 呼叫--生成表單 // resolve(true) = 表單載入成功，
                } else {
                    alert('fun load_form failed. Please try again.');
                    reject('fun load_form failed. Please try again.'); // 表單載入失敗，reject
                }
            };
            xhr.send(formData);
        });
    }
// // 20240430 -- step_2 由uuid取得document_row，再由edit_show(document_row)填入表單
    async function load_document(uuid) {
        return new Promise((resolve, reject) => {
            let formData = new FormData();
            formData.append('uuid', uuid);
            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'load_document.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);    // 接收回傳
                    let document_row = response['_document_row']    // 取得所要的部分
                    resolve(edit_show(document_row));               // step_2-1 呼叫--填入表單 // resolve(true) = 表單填入成功，
                } else {
                    alert('fun load_document failed. Please try again.');
                    reject('fun load_document failed. Please try again.'); // 文件載入失敗，reject
                }
            };
            xhr.send(formData);
        });
    }
// // 20240430 -- step_3 依cherk_action = true/false 啟閉表單特定元素
    async function setFormDisabled(cherk_action) {
        // console.log('step_3 setFormDisabled(cherk_action)：', cherk_action);
        return new Promise((resolve) => {  
            // 获取表单元素
            const mainForm = document.getElementById('mainForm');  
            // 获取表单中的所有可输入元素
            const formElements = mainForm.querySelectorAll('input, select, textarea, button');
            // 遍历每个表单元素，并根据cherk_action的值设置其disabled属性
            formElements.forEach(function(element) {
                // 例外處理：检查元素的类名是否包含"accordion-button"於以排除
                if (!element.classList.contains("accordion-button")) {
                    element.disabled = cherk_action;
                }
            });
            // 对于radio和checkbox，也需要分别处理
            const radioButtons = mainForm.querySelectorAll('input[type="radio"]');
            radioButtons.forEach(function(radio) {
                radio.disabled = cherk_action;
            });
            const checkboxes = mainForm.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(function(checkbox) {
                checkbox.disabled = cherk_action;
            });
            // 例外處理：特別將與會人員的x關閉
            const spans = mainForm.querySelectorAll('span[class="remove"]');
            spans.forEach(function(span) {
                span.disabled = cherk_action;
            });
            // 唯讀模式下，移除特定對象elements
            if(cherk_action){
                // $('#submit_btn, #delete_btn, #saveSubmit, #searchUser').empty();
                $('#submit_btn, #delete_btn, #submit_action, #searchUser').empty();
            }
            resolve(); // 文件載入成功，resolve
        });
    }
// // 20240502 -- step_4 依cherk_action = true/false 啟閉表單特定元素


// 20240502 -- (document).ready(()=> await 依序執行step 1 2 3
    async function loadData() {
        try {
                await load_form(dcc_no);                    // step_1 load_form(dcc_no);             // 20240501 -- 改由後端取得 form_a 內容
                await signature_canva();                    // step_1-1 signature_canva();           // 
                await eventListener();                      // step_1-2 eventListener();             // 
            if(action == "edit" || action == "review" ){
                await load_document(uuid);                  // step_2 load_document(uuid);           // 20240501 -- 改由後端取得 _document內容
            }
                await setFormDisabled(check_action);        // step_3 setFormDisabled(cherk_action); // 依cherk_action = true/false 啟閉表單特定元素

        } catch (error) {
            console.error(error);
        }
    }

    $(document).ready(function(){
        
        // 20240502 -- 調用 loadData 函數來載入數據 await 依序執行step 1 2 3
        loadData();

    })

