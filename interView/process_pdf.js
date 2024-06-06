
    // PDF的 uploadFile --暫存區 函数        // 這裡只負責上到../doc_pdf/temp/內，轉存由下一步處理
    function uploadFile_pdf(key) {
        let formData = new FormData();
        let fileInput = document.getElementById(key + '_upload');                                                       // 取得row input檔名
        let uploadDir = '../doc_pdf/temp/';                                                                             // doc_pdf temp dir
        formData.append('file', fileInput.files[0]);
        formData.append('uploadDir', uploadDir);                                                                        // doc_pdf temp dir
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'upload.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                let response = JSON.parse(xhr.responseText);                               // 接收回傳
                let new_tobe_pdf = '<button type="button" class="btn text-success add_btn" id="doc_pdf_icon" data-toggle="tooltip" data-placement="bottom" ';
                new_tobe_pdf += ' title="'+response.fileName+'" value="'+response.filePath+'" ';
                new_tobe_pdf += " onclick='openUrl(this.value)' ><i class='fa-solid fa-file-pdf fa-2x'></i></br>to be</button>"; 

                document.getElementById(key + '_tobe').innerHTML = new_tobe_pdf ;        // 套上btn
                document.getElementById(key).value = response.fileName;                  // pdf加上時間搓
            } else {
                alert('Upload failed. Please try again.');
            }
        };
        xhr.send(formData);
    }

    // PDF的 uplinkFile --暫存區 函数
    function unlinkFile_pdf(key) {
        let formData = new FormData();
        let fileName = document.getElementById('confirm_sign').value;                    // 取得confirm_sign_upload上的value=檔名
        let unlinkFile = '../doc_pdf/temp/'+fileName;
        formData.append('fileName'  , fileName);
        formData.append('unlinkFile', unlinkFile);
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'unlink.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                // let response = JSON.parse(xhr.responseText);                            // 接收回傳
                document.getElementById(key + '_tobe').innerHTML = '-- nothing --';        // 清除preview
                $('#'+key+' , #'+key+'_upload').val('');                                   // 清除選擇row檔案、confirm_sign欄位

            } else {
                alert('unlink failed. Please try again.');
            }
        };
        xhr.send(formData);
    }

    // doc confirm_sign的 update 函数       
    function update_confirm_sign(row_obj) {
        // 執筆檢查是否空值
        // Object.keys(row_obj).forEach(function(key){
        //     if(row_obj[key] == ''){
        //         return false;
        //     }
        // })

        row_obj["function"] = 'update_confirm_sign';         // 操作功能

        $.ajax({
            url      : 'update_confirm_sign.php',
            method   : 'post',
            async    :  false,                               // ajax取得數據包後，可以return的重要參數
            dataType : 'json',
            data     : row_obj,
            success: function(res){
                return res;
            },
            error: function(e){
                e['swal_action']  = 'error';
                e['swal_content'] = ' 套用失敗';
                return e;
            }
        });
    }

    $(function () {
        // 在任何地方啟用工具提示框
        $('[data-toggle="tooltip"]').tooltip();
    })

    $(document).ready(function () {

        let submit_btn = document.getElementById("submit");
        submit_btn.addEventListener('click', function(){
            let row_json = document.getElementById('row_json').innerText;                   // 取得row_json
            let row_obj = JSON.parse(row_json);                                             // row_json轉row_obj
            let confirm_sign_new = document.getElementById('confirm_sign').value;           // 取得新confirm_sign上傳名稱
                
            if((confirm_sign_new == "") && !confirm('temp未上傳文件!! 此動作會將原有的PDF移除，確定要這麼做?')){
                return;
            }else{
                row_obj['confirm_sign_new'] = confirm_sign_new;                             // 將confirm_sign_new導入row_obj
                result = update_confirm_sign(row_obj);
            }
    
            console.log('hello world!', result);

            // if(swal_json.length != 0){
            //     // // history.back();
            //         // location.href = this.url;
            //         // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action'], {buttons: false, timer:3000});         // 3秒
            //         // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{window.close();});           // 關閉畫面
    
            //     if(swal_json['action'] == 'success'){
            //         // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action'], {buttons: false, timer:2000}).then(()=>{ location.href = url }); // 秒自動關閉畫面
            //         // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{history.back()});          // 手動關閉畫面
            //         // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action'], {buttons: false, timer:2000}).then(()=>{closeWindow()}); // 秒自動關閉畫面
            //         swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{closeWindow(true)});        // 秒自動關閉畫面
    
            //     }else if(swal_json['action'] == 'error'){
            //         swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{history.back()});          // 手動關閉畫面
    
            //     }
            // }else{
            //     location.href = url;
            // }
    
        })




    })