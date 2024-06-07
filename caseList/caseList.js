
// // // utility fun
    // Bootstrap Alarm function
    function alert(message, type) {
        var alertPlaceholder = document.getElementById("liveAlertPlaceholder")      // Bootstrap Alarm
        var wrapper = document.createElement('div')
        wrapper.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible" role="alert">' + message 
                            + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        alertPlaceholder.append(wrapper)
    }
    // fun3-3：吐司顯示字條 // init toast
    function inside_toast(sinn){
        var toastLiveExample = document.getElementById('liveToast');
        var toast = new bootstrap.Toast(toastLiveExample);
        var toast_body = document.getElementById('toast-body');
        toast_body.innerHTML = sinn;
        toast.show();
    }
    // 選擇local時，取得該local的low_level
    function select_local(local_id){
        Object(allLocals).forEach(function(aLocal){
            if(aLocal['id'] == local_id){
                low_level = JSON.parse(aLocal['low_level']);            // 引入所選local的low_level值
            }
        })
        // 預防已經先選了器材，進行防錯
        var select_cata_SN = document.getElementById('cata_SN').value;  // 取得器材的選項值
        if(select_cata_SN != null){                                     // 假如器材已經選擇了
            update_standard_lv(select_cata_SN);                         // 就執行取得low_level對應值
        }
    }
    // 選擇器材，並更新low_level值
    function update_standard_lv(cata_SN){
        var standard_lv = document.getElementById('standard_lv');       // 定義standard_lv主體
        // var low_level = JSON.parse('<=json_encode($low_level);?>');    // 引入所local的low_level值
        if(low_level[cata_SN] == null){
            standard_lv.value = 0;                                      // 預防low_level對應值是null
        }else{
            standard_lv.value = low_level[cata_SN];                  // 套用對應cata_SN的low_level值
        }
    }

    
    // 變更按鈕樣態
    function change_btn(target){
        var toggle_btn = document.getElementById(target+'_toggle_btn');
        var lot_num = document.getElementById('lot_num');

        if (lot_num.value == '') {
            // 输入字段为空
            toggle_btn.innerText = '永久';
            toggle_btn.classList.remove('btn-secondary');
            toggle_btn.classList.add('btn-warning', 'text-dark');
        } else {
            // 输入字段有值
            toggle_btn.innerText = '清除';
            toggle_btn.classList.remove('btn-warning', 'text-dark');
            toggle_btn.classList.add('btn-secondary');
        }
    }
    // 變更lot_num數值
    function chenge_lot_num(target){
        var lot_num = document.getElementById('lot_num');
        if(lot_num.value =='') {
            lot_num.value = '9999-12-31';
        }else{
            lot_num.value = '';
        }
        change_btn(target);
    };
    // 2023/12/13 step_1 將訊息推送到TN PPC(mapp)給對的人~ // 20240220_增加mapp推播訊息
    function push_mapp(user_emp_id, mg_msg){
        $.ajax({
            // url:'http://10.53.248.167/SendNotify',                   // 20230505 正式修正要去掉port 801
            url:'http://tneship.cminl.oa/api/pushmapp/index.php',       // 正式2024新版
            method:'post',
            async: false,                                               // ajax取得數據包後，可以return的重要參數
            dataType:'json',
            data:{
                uuid    : '752382f7-207b-11ee-a45f-2cfda183ef4f',       // ppe
                eid     : user_emp_id,                                  // 傳送對象
                message : mg_msg                                        // 傳送訊息
            },
            success: function(res){
                mapp_result_check = true; 
            },
            error: function(res){
                console.log("push_mapp -- error：",res);
                // ** 受到CORS阻擋，但實際上已完成發送... 所以全部填success
                mapp_result_check = false;
            }
        });
        return mapp_result_check;
    }

    $(function(){
        // 在任何地方啟用工具提示框
        $('[data-toggle="tooltip"]').tooltip();
        // swl function    
        if(swal_json.length != 0){
            if(swal_json['action'] == 'success'){
                var sinn = 'submit - ( '+swal_json['fun']+' : '+swal_json['content']+' ) <b>'+ swal_json['action'] +'</b>&nbsp!!';
                inside_toast(sinn);
                // 20240220_增加mapp推播訊息
                if(swal_json['fun'] == 'store_ptreceive' && ppe_pms.length !=0 ){
                    var mg_msg = swal_json['msg'];
                    Object(ppe_pms).forEach(function(user){
                        var user_emp_id = String(user['emp_id']).trim();            // 定義 user_emp_id + 去空白
                        push_mapp(user_emp_id, mg_msg);
                    })
                }
            }
            swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action'], {buttons: false, timer:3000});
        }
        // 20230131 新增保存日期為'永久'    20230714 升級合併'永久'、'清除'
        // 監聽lot_num是否有輸入值，跟著改變樣態
        $('#lot_num').on('input', function() {
            change_btn('edit');
        });
    });
    
// // // add mode function
    function add_module(to_module){     // 啟用新增模式
        $('#modal_action, #modal_button, #modal_delect_btn, #edit_ptstock_info').empty();     // 清除model功能
        $('#reset_btn').click();                                                            // reset清除表單
        var add_btn = '<input type="submit" name="'+to_module+'_store" class="btn btn-primary" value="新增">';
        $('#modal_action').append('新增');                      // model標題
        $('#modal_button').append(add_btn);                     // 儲存鈕
        var reset_btn = document.getElementById('reset_btn');   // 指定清除按鈕
        reset_btn.classList.remove('unblock');                  // 新增模式 = 解除
        document.querySelector('#edit_'+to_module+' .modal-header').classList.remove('edit_mode_bgc');
        document.querySelector('#edit_'+to_module+' .modal-header').classList.add('add_mode_bgc');
    }
// // // edit mode function
    // fun-1.鋪編輯畫面
    function edit_module(to_module, row_id){
        $('#modal_action, #modal_button, #modal_delect_btn, #edit_ptreceive_info, #shopping_cart_tbody').empty();     // 清除model功能
        $('#reset_btn').click();                                                            // reset清除表單
        var reset_btn = document.getElementById('reset_btn');   // 指定清除按鈕
        reset_btn.classList.add('unblock');                     // 編輯模式 = 隱藏
        document.querySelector('#edit_'+to_module+' .modal-header').classList.remove('add_mode_bgc');
        document.querySelector('#edit_'+to_module+' .modal-header').classList.add('edit_mode_bgc');
        // remark: to_module = 來源與目的 site、fab、local
        // step1.將原排程陣列逐筆繞出來
        Object(window[to_module]).forEach(function(row){          
            if(row['id'] == row_id){
                // step2.鋪畫面到module
                Object(window[to_module+'_item']).forEach(function(item_key){
                    if(item_key == 'id'){
                        document.querySelector('#'+to_module+'_delete_id').value = row['id'];       // 鋪上delete_id = this id.no for delete form
                        document.querySelector('#'+to_module+'_edit_id').value = row['id'];         // 鋪上edit_id = this id.no for edit form
                    }else if(item_key == 'ppty'){
                        document.querySelector('#edit_'+to_module+' #'+item_key+'_'+row[item_key]).checked = true;
                    }else if(item_key == 'item'){
                        var item_arr = JSON.parse(row['item']);
                        Object.keys(item_arr).forEach(ikey => {
                            var item_amount = item_arr[ikey];
                            add_item(ikey, item_amount, 'off');
                        })
                    }else{
                        document.querySelector('#edit_'+to_module+' #'+item_key).value = row[item_key]; 
                    }
                })

                // 鋪上最後更新
                let to_module_info = '最後更新：'+row['updated_at']+' / by '+row['updated_cname'];
                document.querySelector('#edit_'+to_module+'_info').innerHTML = to_module_info;

                var upd_btn = '<input type="submit" name="'+to_module+'_update" value="送出" class="btn btn-primary">';
                var del_btn = '<input type="submit" name="'+to_module+'_delete" value="刪除" class="btn btn-sm btn-xs btn-danger" onclick="return confirm(`確認刪除？`)">';
                $('#modal_action').append('編輯');          // model標題
                $('#modal_delect_btn').append(del_btn);     // 刪除鈕
                $('#modal_button').append(upd_btn);         // 儲存鈕
                return;
            }
        })
    }
// // // 20231114_綁定編輯完成事件    // contenteditable="true" table可編輯、儲存功能
    var rows = document.getElementsByTagName("td");
        Array.from(rows).forEach(function(row) {
            row.addEventListener("blur", handleBlur);               // 監聽進入編輯狀態 或失焦
            row.addEventListener("keydown", handleKeyDown);         // 監聽Enter => run失焦
            row.addEventListener("click", handleCellClick);         // 監聽點擊時取得原始值
        });
    // tableFun_1.綁定失焦事件
    function handleBlur(e) {                                    // 綁定失焦事件
       if(e.sourceCapabilities){
           var originalValue = parseFloat(this.getAttribute("data-original-value").replace(/[^\d.-]/g, ""));
           this.innerHTML = originalValue;
       } 
    }
    // tableFun_2.綁定按鍵事件
    function handleKeyDown(e) {                                 // 綁定按鍵事件
        if (e.keyCode == 13) {                                  // 如果按下的是 Enter 键
            e.preventDefault();

            var rowId = parseFloat(this.id);
            var rowName = this.getAttribute("name");
            var newValue = parseFloat(this.innerHTML.replace(/[^\d.-]/g, ""));
            var originalValue = parseFloat(this.getAttribute("data-original-value").replace(/[^\d.-]/g, ""));
 
            newValue = isNaN(newValue) ? 0 : newValue;
            originalValue = isNaN(originalValue) ? 0 : originalValue;
            
            if (newValue !== originalValue) {
                var request = {
                    "rowId"     : rowId,
                    "rowName"   : rowName,
                    "newValue"  : newValue
                }
                updateCellValue(this, newValue, request);           // 呼叫 tableFun_4.API更新
                this.blur();
            }
        } else if (e.keyCode == 27) {                           // 如果按下的是 Esc 键
            var originalValue = parseFloat(this.getAttribute("data-original-value").replace(/[^\d.-]/g, ""));
            this.innerHTML = originalValue;
            this.blur();
        }
    }
    // tableFun_3.綁定單元格編輯開始事件
    function handleCellClick(e) {                               // 綁定單元格編輯開始事件
        this.setAttribute("data-original-value", this.textContent.trim());      // 獲取當前單元格的原始值並設置到屬性中
    }
    // tableFun_4.API更新現量
    function updateCellValue(cell, newValue, _request) {
        cell.innerHTML = newValue;
        $.ajax({
            url:'api.php',
            method:'post',
            async: false,                                           // ajax取得數據包後，可以return的重要參數
            dataType:'json',
            data:{
                function: 'update_amount',           // 操作功能
                _id     : _request['rowId'],
                _rowName: _request['rowName'],
                _amount : _request['newValue']
            },
            success: function(res){
                swal_action = 'success';
                update_catchValue(_request);                        // 呼叫 tableFun_5.更新pno_Catch中的數值
            },
            error: function(e){
                swal_action = 'error';
                console.log("error");
            }
        });
        
        var sinn = '寫入 - ( '+_request['rowName']+' : '+_request['newValue']+' ) <b>'+ swal_action +'</b>&nbsp!!';
        inside_toast(sinn);

    }
    // tableFun_5.更新Catch中的數值
    function update_catchValue(_request){
        for(var i=0; i < ptstock.length ; i++){                             // stock array 採用迴圈繞出來
            if(ptstock[i]['id'] == _request['rowId']){                      // 找到id = rowId
                ptstock[i][_request['rowName']] = _request['newValue'];     // 覆蓋進stock中
                return;                                                   // 找到+完成後=返回
            }
        }
    }


// // // shopping_cart
    $(document).ready(function () {
        
        // dataTable 2 https://ithelp.ithome.com.tw/articles/10272439
        $('#stock_list').DataTable({
            "autoWidth": false,
            // 排序
            // "order": [[ 4, "asc" ]],
            // 顯示長度
            "pageLength": 25,
            // 中文化
            "language":{
                url: "../../libs/dataTables/dataTable_zh.json"
            }
        });


        // 假如index找不到當下存在已完成的表單，就alarm它!
        // if (check_yh_list_num == '0') {
        //     let message  = '*** '+ thisYear +' '+ half +'年度 PPE儲存量確認開始了! 請務必在指定時間前完成確認 ~ <i class="fa-solid fa-right-long"></i>&nbsp&nbsp&nbsp';
        //         message += '<button type="button" style="background-color: transparent;" data-bs-toggle="modal" data-bs-target="#checkList">'
        //                     +'<b><i class="fa-solid fa-clipboard-list" aria-hidden="true"></i>&nbsp打開點檢表</button></b>';
        //     alert( message, 'danger')
        // }
        if (_inplan && (sys_role <= 2) && (check_yh_list_num == '0')) {
            let message  = '*** <b>'+case_title+'</b> 開放填寫時間：<b><u>'+ start_time +'</u></b>&nbsp至&nbsp<b><u>'+ end_time +'</u></b>&nbsp請各廠窗口務必在指定時間前完成填寫&nbsp~&nbsp';
            alert( message, 'warning')
        }
    })