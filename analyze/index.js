
    $(function () {
        // 在任何地方啟用工具提示框
        $('[data-toggle="tooltip"]').tooltip();
        // 20230817 禁用Enter鍵表單自動提交 
        document.onkeydown = function(event) { 
            var target, code, tag; 
            if (!event) { 
                event = window.event;       //針對ie瀏覽器 
                target = event.srcElement; 
                code = event.keyCode; 
                if (code == 13) { 
                    tag = target.tagName; 
                    if (tag == "TEXTAREA") { return true; } 
                    else { return false; } 
                } 
            } else { 
                target = event.target;      //針對遵循w3c標準的瀏覽器，如Firefox 
                code = event.keyCode; 
                if (code == 13) { 
                    tag = target.tagName; 
                    if (tag == "INPUT") { return false; } 
                    else { return true; } 
                } 
            } 
        };
    })

    // 吐司顯示字條 // init toast
    function inside_toast(sinn){
        let toastLiveExample = document.getElementById('liveToast');
        let toast = new bootstrap.Toast(toastLiveExample);
        let toast_body = document.getElementById('toast-body');
        toast_body.innerHTML = sinn;
        toast.show();
    }


    async function eventListener(){
        return new Promise((resolve) => { 
            document.getElementById('search_btn').addEventListener('click', function() {
                const queryItem = document.getElementById('query_item');
                const selects = queryItem.getElementsByTagName('select');
                const queryItem_obj = {};
                for (let select of selects) {
                    if(select.value ==''){
                        alert(select.name+' is empty!!');
                        return;
                    }else{
                        queryItem_obj[select.name] = select.value;
                    }
                }
                loadData(queryItem_obj);
            });
            resolve(); // 文件載入成功，resolve
        });
    }

    async function load_fun(fun, parm, myCallback) {        // parm = 參數
        return new Promise((resolve, reject) => {
            let formData = new FormData();
            formData.append('fun', fun);
            formData.append('parm', parm);                  // 後端依照fun進行parm參數的採用
            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'load_fun.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);    // 接收回傳
                    let result_obj = response['result_obj'];        // 擷取主要物件
                    resolve(myCallback(fun, result_obj))                 // resolve(true) = 表單載入成功，then 呼叫--myCallback
                                                                    // myCallback：form = bring_form() 、document = edit_show() 、locals = ? 還沒寫好
                } else {
                    alert('fun load_'+fun+' failed. Please try again.');
                    reject('fun load_'+fun+' failed. Please try again.'); // 載入失敗，reject
                }
            };
            xhr.send(formData);
        });
    }

    async function gain_bigData(fun, gain_obj){
        return new Promise((resolve) => { 

            // let innerText1 = '';
            let innerText2 = '';
            switch(fun){
                case 'form':
                    // console.log('gain_obj:' , fun , gain_obj);
                    for (const [key, value] of Object.entries(gain_obj)) {
                        if (typeof value === 'object') {
                            for (const [o_key, o_value] of Object.entries(value)){
                                if (typeof o_value === 'object') {
                                    // innerText1 = '<td>'+ o_key + ' / ' + o_value.label + '</td>';
                                    o_value.item.forEach((o_value_item)=>{
                                        if (typeof o_value_item === 'object') {
                                            innerText2 = '<td>'+o_value_item.label +'</br>('+ o_value_item.name +')</td>';
                                        }else {
                                            innerText2 = '<td>'+o_value_item +'</br>('+ o_value_item +')</td>';
                                        }
                                        // $('#main table tbody').append('<tr id="'+ o_value_item.name +'">'+innerText1 + innerText2+'</tr>');
                                        $('#main table tbody').append('<tr id="'+ o_value_item.name +'">'+innerText2+'</tr>');
                                        doc_keys[o_value_item.name] = { 'label' : o_value_item.label};       // ** 建立表單key：主要是document中有可能填寫不完整，而造成缺項
                                        // innerText1 = '<td></td>';
                                    }) 
                                }else {
                                    console.log('not-object_2:', o_key, o_value);
                                }
                            } 
                        }else {
                            console.log('not-object_1: ',key, value)
                        }
                    }
                    break;
    
                case 'document':
                    // console.log('gain_obj:' , fun , gain_obj);
                    // 表頭標題
                    $('#main table thead tr').append('<th>'+gain_obj.anis_no+'</th>')

                    Object.keys(doc_keys).forEach((doc_key)=>{
                        value = (gain_obj[doc_key] !== undefined) ? gain_obj[doc_key] : gain_obj._content[doc_key];
                        // 文字日期轉換
                        if (typeof value === 'string' && value.match(/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/)) {
                            value = value.replace('T', ' ');
                        }
                        // 
                        if (typeof value === 'object') {
                            innerText = '';
                            for (const [o_key, o_value] of Object.entries(value)){
                                innerText += (innerText == '') ? o_value : '</br>'+ o_value;
                            } 
                        }else{
                            innerText = (value !== undefined) ? value : '- NA -'; 
                        }
                        $('#main table tbody tr[id="'+ doc_key +'"]').append('<td>'+ innerText +'</td>');
                        // 建立統計資料
                        if(doc_key.match("_combo_")){
                            if(doc_keys[doc_key]['value'] == undefined){
                                doc_keys[doc_key]['value'] = [];
                            }
                            if(doc_keys[doc_key]['value'][value] == undefined){
                                doc_keys[doc_key]['value'][value] = 1;
                            }else{
                                doc_keys[doc_key]['value'][value]++;
                            }
                        }
                    })
                    break;
                    
                case 'analyze':
                    console.log(gain_obj);

                    if(doc_keys.length == 0){
                        // await load_fun('form', dcc_no, gain_bigData); // step_1 load_form(dcc_no);             // 20240501 -- 改由後端取得 form_a 內容
                    }


                    $('#main table thead tr').append('<th>'+'- 統計 -'+'</th>')
                    Object.keys(doc_keys).forEach((doc_key)=>{
                        key_value = doc_keys[doc_key]['value'];
                        console.log(doc_key, key_value);

                        if(key_value !== undefined){
                            if (typeof key_value === 'object') {
                                innerText = '';
                                for (const [o_key, o_value] of Object.entries(key_value)){
                                    innerText += (innerText == '') ? o_key + ' : ' + o_value : '</br>' + o_key + ' : ' + o_value;
                                } 
                            }else {
                                innerText = (value !== undefined) ? value : '- NA -'; 
                            }
                        }else{
                            innerText = '';
                        }
                        $('#main table tbody tr[id="'+ doc_key +'"]').append('<td>'+ innerText +'</td>');
                    })
                    break;
    
                default :
            }

            resolve(); // 文件載入成功，resolve
        });
    }

// 20240502 -- (document).ready(()=> await 依序執行step 1 2 3
    async function loadData(query_obj) {
        try {
                // await load_fun('form', dcc_no, gain_bigData); // step_1 load_form(dcc_no);             // 20240501 -- 改由後端取得 form_a 內容
                // await load_fun('document', uuid, gain_bigData);// step_2 load_document(uuid);           // 20240501 -- 改由後端取得 _document內容
                // await load_fun('analyze', '4acceeee-2d2f-11ef-ba23-2cfda183ef4f', gain_bigData);// step_2 load_document(uuid);           // 20240501 -- 改由後端取得 _document內容
                
                load_fun('analyze', query_obj, gain_bigData);
                
                // gain_bigData('gain_doc', doc_keys);

        } catch (error) {
            console.error(error);
        }
    }


    $(document).ready(function(){

        // 20240502 -- 調用 loadData 函數來載入數據 await 依序執行step 1 2 3
        // loadData();

        eventListener()
    })

