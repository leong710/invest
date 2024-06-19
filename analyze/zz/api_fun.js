
    async function load_api(fun, parm, myCallback) {        // parm = 參數
        return new Promise((resolve, reject) => {

            $.ajax({
                url      :'load_api.php',       // 正式2024新版
                method   :'post',
                async    : false,                                               // ajax取得數據包後，可以return的重要參數
                dataType :'json',
                data:{
                    fun  : fun,
                    parm : parm                                       // 傳送訊息
                },
                success: function(res){
                    console.log(res);
                    let result_obj = res['result_obj'];        // 擷取主要物件
                    resolve(myCallback(fun, result_obj))                 // resolve(true) = 表單載入成功，then 呼叫--myCallback
                },
                error: function(res){
                    console.log("load_api -- error：",res);
                    reject('fun load_'+fun+' failed. Please try again.'); // 載入失敗，reject
                }
            });
            return;
        });
    }