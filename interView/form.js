// a_pic的 uploadFile 函数
    function uploadFile(key) {
        let formData = new FormData();
        let fileInput = document.getElementById(key);
        formData.append('file', fileInput.files[0]);
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'upload.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                let response = JSON.parse(xhr.responseText);
                let preview_modal = '<a href="' + response.filePath + '" target="_blank" >';
                let src_img = '<img src="' + response.filePath + '" class="img-thumbnail" style="width: 50%;">';
                document.getElementById('preview_' + key).innerHTML = preview_modal + src_img +'</a>';

            } else {
                alert('Upload failed. Please try again.');
            }
        };
        xhr.send(formData);
    }

    $(document).ready(function(){
        // 監聽到職日欄位(id=hired)，自動計算年資並output(id=rload)
        $('#hired').change(function() {
            var selectedDate = new Date($(this).val());         // 取得到職日日期
            var currentDate = new Date();                       // 取得今天日期
            var difference = currentDate - selectedDate;        // 計算日期差距（毫秒單位）
            // 計算年月日
            var years = Math.floor(difference / (365.25 * 24 * 60 * 60 * 1000));
            var months = Math.floor((difference % (365.25 * 24 * 60 * 60 * 1000)) / (30.44 * 24 * 60 * 60 * 1000));
            var days = Math.floor((difference % (30.44 * 24 * 60 * 60 * 1000)) / (24 * 60 * 60 * 1000));
            // 輸出结果
            $("#rload").val("估算約：" + years + " 年 " + months + " 個月 " + days + " 天");
        });
        
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
    
        // 監聽事故時間欄位(id=a_day)，自動確認是否結束大於開始
        $('#hired, #a_day').change(function() {
            var currentDate = new Date();                       // 取得今天日期
            var hired = new Date($("#hired").val());            // 取得到職日
            var a_day = new Date($("#a_day").val());            // 取得事故日期
            // 到職日不得小於現在時間....
            if(this.id == 'hired'){
                $("#hired").removeClass("is-valid is-invalid").addClass(hired < currentDate ? "is-valid" : "is-invalid");
            }
            // 事故時間需大於到職日，並小於現在時間....
            $("#a_day").removeClass("is-valid is-invalid").addClass(hired <= a_day && a_day < currentDate ? "is-valid" : "is-invalid");
        });
        
    })
