
    $(function () {
        // 在任何地方啟用工具提示框
        $('[data-toggle="tooltip"]').tooltip();
    })

    function Callback_submit_btn(result){
        result["window"] = 'auto_close';
        do_swal(result);
    }

    function do_swal(res){

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
    }

    // doc _odd的 update 函数       
    function update_odd(row_obj, myCallback) {

        row_obj["function"] = 'update_odd';         // 操作功能

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


    $(document).ready(function () {

        const row_json = document.getElementById('row_json').innerText;   // 取得row_json
        const row_obj  = JSON.parse(row_json);                            // row_json轉row_obj
        if(row_obj["_odd"] !== undefined && row_obj["_odd"]["od_day"]){
            document.getElementById('od_day').value = row_obj["_odd"]["od_day"];
        }

        const submit_btn = document.getElementById("submit");
        submit_btn.addEventListener('click', function(){
            const od_day_new = document.getElementById('od_day').value;           // 取得新od_day上傳名稱
            if((od_day_new == "") && !confirm('未填職災申報完成日期!! 此動作會將原有日期(od_day)移除，確定要這麼做?')){
                return;

            }else{
                row_obj['_odd']['od_day'] = od_day_new;                         // 將od_day_new導入row_obj
                console.log('row_obj...', row_obj);
                update_odd(row_obj , Callback_submit_btn);                      // myCallback
            }
        })
    })