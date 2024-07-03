    -- 2024/05
        2024/05/06 依據user需求添加： 
            1. 表單分類 > anis_no/ANIS表單編號欄位，變更資料庫欄位、crud function
            2. 與會人員 > 增加其他非INX與會人員彈性欄位，變更資料庫欄位、crud function；後續會移除廠內事故訪談表單session1後面4個重複欄位。
            3. 廠區清單管理 > 增加 local管理(crud模組)、篩選模組，變更資料庫欄位、crud function
            4. 廠區清單管理 > local_id/廠別添加 所有ANIS現有廠處單位，共118筆。
            5. form表單模組增加save儲存功能

        2024/05/31 
            1. 左側增加session-group，可以點選後快速跳到對應的session範圍，並根據畫面捲動到某個session時，對應的session-group按鈕會亮起。
            2. 增加另開浮動視窗功能，防止對user的使用干擾，增加判斷主畫面或popup畫面功能=>隱藏NAV、替換回上頁=window.close()、追蹤關閉視窗時判斷主視窗是否reloade更新
            3. 更新優化暫存功能update_document()，更新比對判斷與回存功能for _focus、_content。

    -- 2024/06
        2024/06/03 增加與優化功能
            1. 增加 anis_no監聽輸入英文字轉大寫、限制為'ANIS'、確保文字總長21字元，增加驗證提示。
            2. 優化setFormDisabled() 表單開啟狀態會決定input...等項目的disabled與否，誤把未選擇的other_value給解除，增加判定條件予以排除；同時優化onchange_option()，維持該disabled項目
            3. 調整年齡層問項的value：20~未滿25 => 20u-25d，以確保edit_show可以正確選到option項目。
            4. 優化edit_show() step_3 針對other_item的賦值和顯示功能。

        2024/06/12 增加與優化
            1. 增加pdf結案存檔模組...update_confirm_sign
                a. 增加上傳、刪除子function，並崁入儲存、更新function中呼叫
                b. index+DB增加欄位。
                c. 增加編輯模組(上傳、抽換、刪除)
            2. 稱加職災申報欄位...update_odd
                a. 增加判斷子function，並崁入儲存、更新function中呼叫
                b. index+DB增加欄位。
                c. 增加編輯模組
            3. 優化並整合：
                a. update模組：update_confirm_sign.php + update_odd.php => update_api.php (含外掛function)
                b. JS fun：load_form(dcc_no) + load_document(uuid) => load_fun(fun, parm, myCallback) 增加callback function功能~

        2024/06/14 增加與優化
            1.產出生活工傷與廠內交傷表單--對應功能correspond對應選項功能function
            2.建構chooseBoth以上皆是功能
            3.廠外交傷、生活工傷、廠內交傷表單--已反饋給user進行問項整合中
            4.hrdb table[staff]新增欄位：性別、國別、國籍 => 待後續應用；search modal新增以上欄位
            5.hrdb table[dept]新增北廠環安清單(8N050500及轄下單位)，=> 供員工膯入判斷(jnESH & tnESH)及後續應用 
            6.user提問：表單上由hrdb所載入的人員資料，是否會因為該員離職後而消失? 實作驗證：不會，因為人員資料已填入表單並儲存，但要注意的是，編輯時若不甚刪除，則無法再由hrdb套入的問題!

        2024/06/17 增加：
            1. 生活工傷、廠內交傷 表單需綁定對應的：事故類別、事故分類、災害類型=>JSON檔增加'lock'對應項目、JS 增加function lock_opt處理鎖定、並在setFormDisabled中增加排除項目'lock'

        2024/06/19 增加：
            1. 試作統計功能初步完成，但暫無匯出Excel功能。
        2024/06/20 優化：
            1. 將3表單進行sorting與分類成各session，並用樞紐進行分析出各表單所引用的問項。以上供user進行參考與引用for設備事故、人傷事故。
        2024/06/27 優化與增加：
            1. user提供 設備事故、人傷事故 表單問項，根據需求長出對應json與表單。
            2. 優化新表單對應功能correspond，新增reflesh_correspond()，主要用在生長lock_opt之後的對應項目生成。
            3. 因表單增加後index過於擁擠，進行優化。
        2024/06/28 增加與優化：
            1. 設備事故表單 更名為 設備/其他事故表單，並將事故間接原因之直接原因、間接原因下的輸入欄位升級成select選項。
            2. 在make_question()下的switch checkbox，增加select生成功能，以因應 直接原因、間接原因的選項需求。
        24/07/02
            1. 試作開發查詢匯出功能，功能欄位參考ANIS系統與部分欄位...
        24/07/03
            1. 新增session_1人員基本資料[籍別、性別]來自hrdb查詢渲染。
            2. autoinput標籤的eventListener。