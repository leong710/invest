
    $(function () {
        // 在任何地方啟用工具提示框
        $('[data-toggle="tooltip"]').tooltip();
    })

    // PDF的 uploadFile 函数        // 這裡只負責上到../doc_pdf/temp/內，轉存由下一步處理
    function uploadFile_pdf(key) {
        let formData = new FormData();
        let fileInput = document.getElementById(key + '_new');                                                          // 取得row input檔名
        formData.append('file', fileInput.files[0]);
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'upload_pdf.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                let response = JSON.parse(xhr.responseText);                                                            // 接收回傳
                let new_preview_pdf = '<button type="button" class="btn text-danger add_btn" id="doc_pdf_icon" data-toggle="tooltip" data-placement="bottom" ';
                new_preview_pdf += ' title="'+response.fileName+'" value="'+response.filePath+'" ';
                new_preview_pdf += " onclick='openUrl(this.value)' ><i class='fa-solid fa-file-pdf fa-2x'></i></button>"; 

                document.getElementById(key + '_preview').innerHTML = new_preview_pdf ;                                 // 套上btn
                document.getElementById(key).value = response.fileName;                                                 // pdf加上時間搓
            } else {
                alert('Upload failed. Please try again.');
            }
        };
        xhr.send(formData);
    }
    // PDF的 uplinkFile 函数
    function unlinkFile_pdf(key) {
        let formData = new FormData();
        // let unlinkFile = document.getElementById(key).value;                                                          // 取得a_pic加上時間搓
        let unlinkFile = document.getElementById('doc_pdf_icon').value;                                                  // 取得doc_pdf_icon上含有路徑的value
        let fileName   = document.getElementById('doc_pdf_icon').title;                                              // 取得doc_pdf_icon上title的value=檔名
        formData.append('unlinkFile', unlinkFile);
        formData.append('fileName'  , fileName);
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'unlink_pdf.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                // let response = JSON.parse(xhr.responseText);                                                            // 接收回傳
                document.getElementById(key + '_preview').innerHTML = '-- nothing --';                                  // 清除preview
                $('#'+key+' , #'+key+'_new').val('');                                                                   // 清除選擇row檔案、confirm_sign欄位

            } else {
                alert('unlink failed. Please try again.');
            }
        };
        xhr.send(formData);
    }