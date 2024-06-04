
    // PDF的 uploadFile 函数
    function uploadFile(key) {
        let formData = new FormData();
        let fileInput = document.getElementById(key + '_new');                                                          // 取得row input檔名
        formData.append('file', fileInput.files[0]);
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'upload_pdf.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                let response = JSON.parse(xhr.responseText);                                                            // 接收回傳
                let preview_modal = '<a href="' + response.filePath + '" target="_blank" >';                            // 生成預覽按鈕a
                let src_img = '<img src="' + response.filePath + '" class="img-thumbnail" style="width: 50%;">';        // 生成img

                let fab_title  = document.getElementById('fab_title').innerText;
                let short_name = document.getElementById('short_name').innerText;
                let case_year  = document.getElementById('case_year').innerText;

                let new_preview_pdf = '<button type="button" class="btn text-danger add_btn" ';
                new_preview_pdf += ' value="../doc_pdf/'+fab_title+'/'+short_name+'/'+case_year+'/'+response.filePath+'" ';
                new_preview_pdf += " onclick='openUrl(this.value)' ><i class='fa-solid fa-file-pdf fa-2x'></i></button>"; 

                document.getElementById('preview_' + key).innerHTML = preview_modal + src_img +'</a>';                  // 套上a+img
                document.getElementById(key).value = response.fileName;                                                 // a_pic加上時間搓

            } else {
                alert('Upload failed. Please try again.');
            }
        };
        xhr.send(formData);
    }
    // PDF的 uplinkFile 函数
    function unlinkFile(key) {
        let formData = new FormData();
        let unlinkFile = document.getElementById(key).value;                                                          // 取得a_pic加上時間搓
        formData.append('unlinkFile', unlinkFile);
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'unlink_pdf.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                // let response = JSON.parse(xhr.responseText);                                                            // 接收回傳
                document.getElementById('preview_' + key).innerHTML = '-- preView --';                                  // 清除preview
                document.getElementById(key + '_new').value = '';                                                       // 清除選擇row檔案
                document.getElementById(key).value = '';                                                                // 清除a_pic

            } else {
                alert('unlink failed. Please try again.');
            }
        };
        xhr.send(formData);
    }