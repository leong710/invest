
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
                        // if (['其他', '無'].includes(option_value[index-1])) {     // ** 當你的上一個value，有涉及到'其他','無','否'，就將它的例外input_o打開，並帶入value
                        if (['其他', '無', '否', 'Other', '1', '2', '3'].includes(option_value[index-1])) {     // ** 當你的上一個value，有涉及到'其他','無','否'，就將它的例外input_o打開，並帶入value
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

        // edit step10.鋪設Editions紀錄
            if(editions_arr.length >0){
                var editTable = document.querySelector('.editions tbody');
                $('#editions_div').removeClass('unblock');                              // 解除隱藏
                for (var i = 0; i < editions_arr.length;  i++) {
                    var content_item =''
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


