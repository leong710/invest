
    // PDF的 uploadFile --暫存區 函数    // btn觸發    // 這裡只負責上到../doc_files/temp/內，轉存由下一步處理
    function uploadFile_pdf(key) {
        let formData = new FormData();
        let fileInput = document.getElementById(key + '_upload');                                                       // 取得row input檔名
        let uploadDir = '../doc_temp/';                                                                             // doc_pdf temp dir
        formData.append('file', fileInput.files[0]);
        formData.append('uploadDir', uploadDir);                                                                        // doc_pdf temp dir
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'upload.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);                               // 接收回傳
                let new_tobe_pdf = '<a target="_blank" class="btn text-success add_btn" id="doc_pdf_icon" data-toggle="tooltip" data-placement="bottom" ';
                new_tobe_pdf += ' title="'+response.fileName+'" href="'+response.filePath+'" ><i class="fa-solid fa-file-pdf fa-2x"></i></br>to be</a>'; 

                document.getElementById(key + '_tobe').innerHTML = new_tobe_pdf ;        // 套上btn
                document.getElementById(key).value = response.fileName;                  // pdf加上時間搓
                $('#del_tobe').prop('disabled', false);

            } else {
                alert('Upload failed. Please try again.');
            }
        };
        xhr.send(formData);
    }

    // PDF的 uplinkFile --暫存區 函数   // btn觸發
    function unlinkFile_pdf(key, tag) {
        let formData = new FormData();
        if(tag =='tobe'){
            var fileName = document.getElementById(key).value;                    // 取得confirm_sign_upload上的value=檔名
            var filePath = '../doc_temp/';

        }else if(tag =='asis'){
            var row_json = document.getElementById('row_json').innerText;                   // 取得row_json
            var row_obj = JSON.parse(row_json);                                             // row_json轉row_obj
            row_obj['confirm_sign_new'] = '';                                       // 將confirm_sign_new導入row_obj
            
            update_confirm_sign(row_obj, Callback_unlinkFile_pdf);
            return;

        }else{
            console.log('tag error: ', tag);
            return false;
        }
        let unlinkFile = filePath+fileName;
        
        formData.append('fileName'  , fileName);            // 純檔名
        formData.append('unlinkFile', unlinkFile);          // 路徑+檔名
        formData.append('row_json', row_json);          // 路徑封包

        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'unlink.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                // let response = JSON.parse(xhr.responseText);                            // 接收回傳
                document.getElementById(key+'_'+tag).innerHTML = '-- nothing --';        // 清除preview
                if(tag =='tobe'){
                    $('#'+key+' , #'+key+'_upload').val('');                                   // 清除選擇row檔案、confirm_sign欄位
                    $('#del_'+tag).prop('disabled', true);
                }
            } else {
                alert('unlink failed. Please try again.');
            }
        };
        xhr.send(formData);
    }

    function Callback_unlinkFile_pdf(result){
        if(result['error'] == undefined){
            document.getElementById('confirm_sign_asis').innerHTML = '-- nothing --';        // 清除preview
            $('#del_asis').prop('disabled', true);
        }
        result["window"] = 'auto';
        do_swal(result);
    }

    function Callback_submit_btn(result){
        result["window"] = 'auto_close';
        do_swal(result);
    }

    function do_swal(res){
        console.log(res);
        let this_content = swal_json['content'];
        this_content += (res['result']['content'] !== undefined) ? res['result']['content'] : '參數異常';
        
        swal_json['action'] = (res['result']['action'] !== undefined)  ? res['result']['action']  : 'warning';
  
        if(swal_json['action'] == 'success'){
            switch(res['window']) {
                case 'close':
                    swal(swal_json['fun'] ,this_content ,swal_json['action']).then(()=>{closeWindow(true)});    // 手動關閉 畫面+true=更新
                    break;
                case 'auto_close':
                    swal(swal_json['fun'] ,this_content ,swal_json['action'],{buttons: false, timer:2000}).then(()=>{closeWindow(true)});    // 2秒自動關閉 畫面+true=更新
                    break;
                case 'auto':
                    swal(swal_json['fun'] ,this_content ,swal_json['action'],{buttons: false, timer:2000});                                  // 2秒自動關閉
                    break;
                default:
                    swal(swal_json['fun'] ,this_content ,swal_json['action']);                                  // 手動關閉
            }

        }else if(swal_json['action'] == 'error'){
            swal(swal_json['fun'] ,this_content ,swal_json['action']);                                          // 手動關閉
        
        }else{
            swal(swal_json['fun'] ,'未知的錯誤!' ,'warning');                                                   // 手動關閉  

        }
        // history.back();  // location.href = this.url;
            // swal( TITLE , CONTENT , ACTION ).then(()=>{window.close();});           // 手動關閉畫面
            // swal( TITLE , CONTENT , ACTION ).then(()=>{history.back()});            // 手動關閉畫面+回上頁
            // swal( TITLE , CONTENT , ACTION ).then(()=>{closeWindow(true)});         // 手動關閉畫面 +true=更新、+false=不更新
            // swal( TITLE , CONTENT , ACTION , {buttons: false, timer:3000});         // 3秒自動關閉
            // swal( TITLE , CONTENT , ACTION , {buttons: false, timer:3000}).then(()=>{ location.href = url });   // 3秒自動關閉畫面+回指定頁面
            // swal( TITLE , CONTENT , ACTION , {buttons: false, timer:3000}).then(()=>{ closeWindow() });         // 3秒自動關閉畫面+false=不更新
    }

    // doc confirm_sign的 update 函数       // submit-btn觸發
    function update_confirm_sign(row_obj, myCallback) {

        row_obj["function"] = 'update_confirm_sign';         // 操作功能

        $.ajax({
            url      : 'update_api.php',
            method   : 'post',
            async    :  false,                               // ajax取得數據包後，可以return的重要參數
            dataType : 'json',
            data     : row_obj,
            success: function(res){
                myCallback(res);
                return true;
            },
            error: function(e){
                e['swal_action']  = 'error';
                e['swal_content'] = ' 套用失敗';
                myCallback(e);
                return false;
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
                update_confirm_sign(row_obj , Callback_submit_btn);                // myCallback
            }
        })
    })