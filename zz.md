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