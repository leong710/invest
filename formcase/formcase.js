        
        // 功能封存，改由process處理;
        function uploadFile(key) {
            let formData = new FormData();
            let fileInput = document.getElementById(key);
            formData.append('file', fileInput.files[0]);
            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'upload.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);                                // 接收回傳
                    document.getElementById('formcase_' + key).value = response.fileName;       // dcc_no

                } else {
                    alert('Upload failed. Please try again.');
                }
            };
            xhr.send(formData);
        }
        function unlinkFile(key) {
            let formData = new FormData();
            let unlinkFile = document.getElementById('formcase_'+key).value;                      // 取dcc_no file.name
            formData.append('unlinkFile', unlinkFile);
            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'unlink.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    // let response = JSON.parse(xhr.responseText);                               // 接收回傳
                    document.getElementById(key).value = '';                                      // 清除選擇檔案
                    document.getElementById('formcase_'+key).value = '';                          // 清除md5

                } else {
                    alert('unlink failed. Please try again.');
                }
            };
            xhr.send(formData);
        }

    function add_module(to_module){     // 啟用新增模式
        $('#modal_action, #modal_button, #modal_delect_btn, #formcase_info').empty();       // 清除model功能
        $('#reset_btn').click();                                                                    // reset清除表單
        var add_btn = '<input type="submit" name="submit_formcase" value="新增" class="btn btn-primary">';
        $('#modal_action').append('新增');                                                  // model標題
        $('#modal_button').append(add_btn);                                                 // 儲存鈕
        var reset_btn = document.getElementById('reset_btn');                               // 指定清除按鈕
        reset_btn.classList.remove('unblock');                                              // 新增模式 = 解除
        document.querySelector("#edit_modal .modal-header").classList.remove('edit_mode_bgc');
        document.querySelector("#edit_modal .modal-header").classList.add('add_mode_bgc');
    }
    // fun-1.鋪編輯畫面
    function edit_module(to_module, row_id){
        $('#modal_action, #modal_button, #modal_delect_btn, #formcase_info').empty();       // 清除model功能
        $('#reset_btn').click();                                                            // reset清除表單
        var reset_btn = document.getElementById('reset_btn');                               // 指定清除按鈕
        reset_btn.classList.add('unblock');                                                 // 編輯模式 = 隱藏
        document.querySelector("#edit_modal .modal-header").classList.remove('add_mode_bgc');
        document.querySelector("#edit_modal .modal-header").classList.add('edit_mode_bgc');
        // step1.將原排程陣列逐筆繞出來
        Object(window[to_module]).forEach(function(row){          
            if(row['id'] == row_id){
                // step2.鋪畫面到module
                Object(window[to_module+'_item']).forEach(function(item_key){
                    if(item_key == 'id'){
                        document.querySelector('#'+to_module+'_delete_id').value = row['id'];       // 鋪上delete_id = this id.no for delete form
                        document.querySelector('#'+to_module+'_edit_id').value = row['id'];         // 鋪上edit_id = this id.no for edit form

                    }else if(item_key == 'flag'){
                        document.querySelector('#edit_modal #'+to_module+'_'+row[item_key]).checked = true;
                        
                    }else{
                        document.querySelector('#edit_modal #'+to_module+'_'+item_key).value = row[item_key]; 
                    }
                })

                // 鋪上最後更新
                let to_module_info = '最後更新：'+row['updated_at']+' / by '+row['updated_user'];
                document.querySelector('#'+to_module+'_info').innerHTML = to_module_info;

                // step3-3.開啟 彈出畫面模組 for user編輯
                // edit_myTodo_btn.click();
                var add_btn = '<input type="submit" name="update_formcase" value="儲存" class="btn btn-primary">';
                var del_btn = '<input type="submit" name="delete_formcase" value="刪除" class="btn btn-sm btn-xs btn-danger" onclick="return confirm(`確認刪除？`)">';
                $('#modal_action').append('編輯');          // model標題
                $('#modal_delect_btn').append(del_btn);     // 刪除鈕
                $('#modal_button').append(add_btn);         // 儲存鈕
                return;
            }
        })
    }



    $(document).ready(function(){
        // dataTable 2 https://ithelp.ithome.com.tw/articles/10272439
        $('#formcase_list').DataTable({
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
        
        // flag 切換上架/下架開關 20230712
        let flagBtns = [...document.querySelectorAll('.flagBtn')];
        for(let flagBtn of flagBtns){
            flagBtn.onclick = e => {
                let swal_content = e.target.name+'_id:'+e.target.id+'=';
                $.ajax({
                    url:'api.php',
                    method:'post',
                    async: false,                               // ajax取得數據包後，可以return的重要參數
                    dataType:'json',
                    data:{
                        function: 'cheng_flag',                 // 操作功能
                        table: e.target.name,
                        id: e.target.id,
                        flag: e.target.value
                    },
                    success: function(res){
                        let res_r = res["result"];
                        let res_r_flag = res_r["flag"];
                        if(res_r_flag == 'Off'){
                            e.target.classList.remove('btn-success');
                            e.target.classList.add('btn-warning');
                            e.target.value = 'Off';
                            e.target.innerText = 'Off';
                        }else{
                            e.target.classList.remove('btn-warning');
                            e.target.classList.add('btn-success');
                            e.target.value = 'On';
                            e.target.innerText = 'On';
                        }
                        swal_action = 'success';
                        swal_content += res_r_flag+' 套用成功';
                        swal('change_flag' ,swal_content ,swal_action, {buttons: false, timer:1000});

                    },
                    error: function(e){
                        swal_action = 'error';
                        swal_content += res_r_flag+' 套用失敗';
                        console.log("error");
                        swal('change_flag' ,swal_content ,swal_action);
                    }
                });
            }
        }
    })