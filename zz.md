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
