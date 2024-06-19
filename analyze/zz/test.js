async function load_fun(fun, parm, myCallback) {
    return new Promise((resolve, reject) => {
        let formData = new FormData();
        let fun_temp = (parm['_get_dccNo'] === true ) ? 'caseList' : fun;
        formData.append('fun', fun_temp);
            if (typeof parm === 'object') {
                for (const [_key, _value] of Object.entries(parm)){
                    formData.append(_key, _value);
                } 
            }else {
                formData.append('parm', parm);
            }
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'load_fun.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                let response = JSON.parse(xhr.responseText);
                let result_obj = response['result_obj'];
                resolve(myCallback(fun, result_obj)) 

            } else {
                alert('fun load_'+fun+' failed. Please try again.');
                reject('fun load_'+fun+' failed. Please try again.'); 
            }
        };
        xhr.send(formData);
    });
}

async function gain_bigData(fun, gain_obj){
    return new Promise((resolve) => { 
        let innerText  = '';
        let innerText2 = '';
        switch(fun){
            case 'form':
                $('#main table tbody').empty();
                $('#main table thead tr').empty().append('<th>'+'label / name'+'</th>')
                for (const [key, value] of Object.entries(gain_obj)) {
                    if (typeof value === 'object') {
                        for (const [o_key, o_value] of Object.entries(value)){
                            if (typeof o_value === 'object') {
                                o_value.item.forEach((o_value_item)=>{
                                    if (typeof o_value_item === 'object') {
                                        innerText2 = '<td>'+o_value_item.label +'</br>('+ o_value_item.name +')</td>';
                                    }else {
                                        innerText2 = '<td>'+o_value_item +'</br>('+ o_value_item +')</td>';
                                    }
                                    $('#main table tbody').append('<tr id="'+ o_value_item.name +'">'+innerText2+'</tr>');
                                    doc_keys[o_value_item.name] = { 'label' : o_value_item.label};  
                                }) 
                            }
                        } 
                    }
                }
                break;

            case 'document':
                $('#main table thead tr').append('<th>'+gain_obj.anis_no+'</th>');
                Object.keys(doc_keys).forEach((doc_key)=>{
                    value = (gain_obj[doc_key] !== undefined) ? gain_obj[doc_key] : gain_obj._content[doc_key];
                    if (typeof value === 'string' && value.match(/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/)) {
                        value = value.replace('T', ' ');
                    }
                    if (typeof value === 'object') {
                        innerText = '';
                        for (const [o_key, o_value] of Object.entries(value)){
                            innerText += (innerText == '') ? o_value : '</br>'+ o_value;
                        } 
                    }else{
                        innerText = (value !== undefined) ? value : '- NA -'; 
                    }
                    $('#main table tbody tr[id="'+ doc_key +'"]').append('<td>'+ innerText +'</td>');
                    if(doc_key.match("_combo_")){
                        if(doc_keys[doc_key]['value'] == undefined){
                            doc_keys[doc_key]['value'] = {};
                        }
                        if(doc_keys[doc_key]['value'][value] == undefined){
                            doc_keys[doc_key]['value'][value] = 1;
                        }else{
                            doc_keys[doc_key]['value'][value]++;
                        }
                    }
                })
                break;
                
            default :
                reject();
                break;
        }

        resolve();
    });
}
// step2:
async function caseList(fun, gain_obj){
    return new Promise((resolve) => { 
        Object(gain_obj).forEach((_doc)=>{
            let uuid = _doc['uuid']
            load_fun('document', uuid, gain_bigData); 
        })
        resolve(); 
    });
}
// step1:
async function get_dccNo(fun, gain_obj){
    return new Promise((resolve) => { 
        if(doc_keys.length == 0 && gain_obj['dcc_no'] !== undefined) {
            let dcc_no = gain_obj['dcc_no']
            load_fun('form', dcc_no, gain_bigData); 
        }
        resolve(); 
    });
}
// step3:
async function analyze(fun, gain_obj){
    return new Promise((resolve) => { 
        $('#main table thead tr').append('<th>'+'- 統計 -'+'</th>');
        Object.keys(doc_keys).forEach((doc_key)=>{
            let key_value = doc_keys[doc_key]['value'];
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
        resolve();
    });
}

async function loadData(query_obj) {
    try {
        query_obj['_get_dccNo'] = true;
        await load_fun('get_dccNo', query_obj, get_dccNo);
        
        delete query_obj['_get_dccNo'];
        await load_fun('caseList', query_obj, caseList);

        await analyze(doc_keys);

    } catch (error) {
        console.error(error);
    }
}