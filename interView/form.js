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

    // 動態表單主fun -- JSON轉表單；依據不同的key_type進行切換型別 HARD CODED
    function make_question(session_key, key_class, item_a) {        // 接收參數：session, class, 單一問項
        var int_a = '';
        var dcff = '<div class="form-floating">';
        // 共用部分的操作1 label標籤
        function commonPart() {
            var labelSuffix = item_a.required ? '<sup class="text-danger"> *</sup>' : '';
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
                        '<input type="' + (item_a.type === 'date' ? 'date' : 'datetime-local') + '" name="' + item_a.name + '" class="form-control" id="' + item_a.name + '" value="" ' +
                        (item_a.required ? 'required' : '') + '>' + commonPart() + (item_a.valid ? validPart() : '') + '</div>';
                break;
            case 'textarea':
                int_a = dcff +
                    '<textarea name="' + item_a.name + '" id="' + item_a.name + '" class="form-control " style="height: 100px" placeholder="' + item_a.label + '"' 
                    + (item_a.required ? 'required' : '') + '>' + '</textarea>' + commonPart() + '</div>';

                int_a = '<div class="p-2">' + int_a + '</div>';
                break;
            case 'radio':
            case 'checkbox':
                int_a = '<div class=" border rounded p-2"><snap><b>*** ' + item_a.label + '：' + (item_a.required ? '<sup class="text-danger"> *</sup>' : '') + '</b></snap><br>';
                Object(item_a.options).forEach((option)=>{
                    let object_type = ((typeof option.value == 'object') ? option.label : option.value);   // for other's value
                    // int_a += '<div class="form-check bg-light rounded"><input type="' + item_a.type + '" name="' + item_a.name + (item_a.type == 'checkbox' ? '[]':'') + '" value="' + object_type + '" '
                    int_a += '<div class="form-check bg-light rounded"><input type="' + item_a.type + '" name="' + item_a.name + '[]' + '" value="' + object_type + '" '
                          + ' id="' + item_a.name + '_' + object_type + '" ' + (item_a.required ? ' required ' : '') + 'onchange="onchange_option(this.name)" ' 
                          + ' class="form-check-input ' + ((typeof option.value === 'object') ? ' other_item ' : '') + (option.value.only ? ' only_option ' : '') + '" >'
                          + '<label class="form-check-label" for="' + item_a.name + '_' + object_type + '">' + option.label + (typeof option.value === 'object' ? '：' : '') +'</label></div>';

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

        // 動態表單主fun -- JSON轉表單
        // step_0.前置工作、生成表頭
        if(form_json.form_title){ $('#form_title').empty().append(form_json.form_title);  }     // 文件標題
        if(form_json.dcc_no){     $('#dcc_no_head').empty().append(form_json.dcc_no); }         // DCC編號
        if(form_json.version){    $('#dcc_no_head').append('-' + form_json.version); }          // 文件版本
        let dcc_no_input = document.querySelector('#dcc_no');                                   // 
        if(dcc_no_input && form_json.dcc_no && form_json.version){ 
            dcc_no_input.value = form_json.dcc_no+'-'+form_json.version;
        }
        var form_doc = document.getElementById('item_list');                                    // 定義動態表單id位置
        if(form_item){                                                                          // confirm form_item is't empty
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
        }
    })

    // signature簽名板
    window.onload = function() {
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
                signatureImage.src = '../image/signin_empty.png';           // 預覽圖
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
                }
            });
        });
    };

// // editMode function 
    // edit鋪設渲染_表頭
    function reShow_info(){
        // 1.會議info
        let meeting_info1_arr = ['fab_id','local_id','case_title', 'a_dept', 'meeting_time', 'meeting_local','uuid'];
        meeting_info1_arr.forEach((meeting_info1)=>{
            if(document_row[meeting_info1]){
                document.querySelector('#'+meeting_info1).value = document_row[meeting_info1]; 
            }
        })
        // 2.與會人員
        let meeting_info2_arr = ['meeting_man_a', 'meeting_man_o', 'meeting_man_s'];
        meeting_info2_arr.forEach((meeting_man)=>{
            if(document_row[meeting_man].length >=1 ){
                meeting_man_target = meeting_man;                                 // let key => target
                meeting_man_val = JSON.parse('['+document_row[meeting_man]+']');  // 取出的字串藥先用 [ ] 包起來，再轉成JSON物件
                for(let i=0; i < meeting_man_val.length; i++){                    // 依照物件長度進行遶圈
                    tagsInput_me(JSON.stringify(meeting_man_val[i]));             // 轉成字串進行渲染
                }
            }
        })
    }

    function edit_show(){
        // edit step0.更換submit按鈕型態
            let update_btn = '<button type="submit" value="update" name="update_document" class="btn btn-primary" ><i class="fa fa-paper-plane" aria-hidden="true"></i> Update (Submit)</button>'
            $('#submit_action').empty();
            $('#submit_action').append(update_btn);
        
        // edit step1.呼叫fun鋪設渲染_表頭：'case_title','a_dept','meeting_time','meeting_local','meeting_man_a','meeting_man_o','meeting_man_s','uuid','id'
            reShow_info();

        // edit step2.特例呈現：'confirm_sign','ruling_sign','a_pic'
            let special_items = ['confirm_sign','ruling_sign','a_pic']
            special_items.forEach((special_item)=>{
                if(document_row[special_item] != null){
                    if(special_item == 'a_pic'){        // 路線圖檔
                        let a_pic_path = '../image/a_pic/'                                                                          // 指定pic路徑
                        let a_pic_val  = document_row[special_item];                                                                // 取得pic_value
                        let preview_modal = '<a href="' + a_pic_path + a_pic_val + '" target="_blank" >';                           // 生成預覽按鈕a
                        let src_img = '<img src="' + a_pic_path + a_pic_val + '" class="img-thumbnail" style="width: 50%;">';       // 生成img
                        let preview_item = document.getElementById('preview_' + special_item); 
                        if(preview_item){
                            preview_item.innerHTML = preview_modal + src_img +'</a>';                                               // 套上a+img
                        }            
                        let input_item = document.getElementById(special_item);
                        if(input_item){
                            input_item.value = a_pic_val;                                                                           // 欄位填上pic_value
                        }
    
                    }else{                              // 簽名
                        let base64_sign = document_row[special_item];
                        let signatureImage = document.getElementById(special_item+'_signature-image');
                        signatureImage.src = base64_sign;                           // 渲染預覽圖
                        $('#'+special_item+'_signature-input').val(base64_sign);    // 填上base64儲存格
    
                    }
                }
            })

        // edit step3.內容呈現
            let _content = document_row['_content']
            let match;
            Object.keys(_content).forEach(function(content_key){        // 將原陣列_content逐筆繞出來
                let option_value = _content[content_key];
                const regex = new RegExp('combo', 'gi');                // 建立比對文字'combo'
                if ((match = regex.exec(content_key)) === null) {       // 非combo選項，直接帶入value
                    $('#'+content_key).val(option_value); 
                }else{                                                  // combo選項，需要特例檢查，以便開啟其他輸入
                    option_value.forEach((item_value, index)=>{
                        if (['其他', '無', '1', '2', '3'].includes(option_value[index-1])) {     // ** 當你的上一個value，有涉及到'其他','無','否'，就將它的例外input_o打開，並帶入value
                            $('#' + content_key + '_' + option_value[index-1] + '_o').removeClass('unblock').removeAttr("disabled").val(item_value);
                        }else{                                                         // ** 如果沒有就直接帶入value  // checkbox和redio都適用
                            $('#' + content_key + '_' + item_value).prop('checked', true);
                        }
                    })
                }
            })

        // edit step9.鋪設logs紀錄
            var forTable = document.querySelector('.logs tbody');
            $('#logs_div').removeClass('unblock');                              // 解除隱藏
            for (var i = 0, len = json.length; i < len; i++) {
                json[i].remark = json[i].remark.replaceAll('_rn_', '<br>');     // *20231205 加入換行符號
                forTable.innerHTML += 
                    '<tr><td>' + json[i].step + '</td><td>' + json[i].cname + '</td><td>' + json[i].datetime + '</td><td>' + json[i].action + 
                        '</td><td style="text-align: left; word-break: break-all;">' + json[i].remark + '</td></tr>';
            }

        // let sinn = 'submit - ( '+swal_json['fun']+' : '+swal_json['content']+' ) <b>'+ swal_json['action'] +'</b>&nbsp!!';
        let sinn = action + '&nbsp模式開啟，表單套用成功&nbsp!!';
        inside_toast(sinn);
    }
// // editMode function 

// // "edit" "review" 下disabled特定元素
    // 获取表单元素
    const mainForm = document.getElementById('mainForm');
    // 定义一个函数来根据cherk_action的值设置表单元素的disabled属性
    function setFormDisabled(cherk_action) {
        
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
            $('#submit_btn, #delete_btn, #saveSubmit, #searchUser').empty();
        }

    }
    // setFormDisabled(true); // 当cherk_action为true时禁用表单元素
    // setFormDisabled(false); // 当cherk_action为false时启用表单元素
// // "edit" "review" 下disabled特定元素

    $(document).ready(function(){
        // 定義+監聽按鈕for與會人員...search btn id
        var search_btns = Array.from(document.querySelectorAll(".search_btn"));
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
        // 監聽工作起訖日欄位(id=a_work_e)，自動確認是否結束大於開始
        $('#a_work_s, #a_work_e').change(function() {
            var currentDate = new Date();                       // 取得今天日期
            var a_work_s = new Date($("#a_work_s").val());      // 取得起始
            var a_work_e = new Date($("#a_work_e").val());      // 取得訖止
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
            var currentDate = new Date();                       // 取得今天日期
            var hired    = new Date($("#hired").val());            // 取得到職日
            var a_day    = new Date($("#a_day").val());            // 取得事故日期
            var anis_day = new Date($("#anis_day").val());            // 取得事故日期
            // 到職日不得大於現在時間....
            if(this.id == 'hired'){
                if(currentDate >= hired){
                    var difference = currentDate - hired;        // 計算日期差距（毫秒單位）
                    // 計算年月日
                    var years = Math.floor(difference / (365.25 * 24 * 60 * 60 * 1000));
                    var months = Math.floor((difference % (365.25 * 24 * 60 * 60 * 1000)) / (30.44 * 24 * 60 * 60 * 1000));
                    var days = Math.floor((difference % (30.44 * 24 * 60 * 60 * 1000)) / (24 * 60 * 60 * 1000));
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

        if(action == "edit" || action == "review" ){
            edit_show();
        }
        
        // 调用函数，并传入cherk_action的值
        setFormDisabled(check_action); // 当cherk_action为false时启用表单元素

    })

