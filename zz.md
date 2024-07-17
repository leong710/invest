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

        24/07/05
            1. user回饋：
                a. 取消事故者自述 與 目擊者自述 之text輸入欄位功能；原因是DL沒有個人帳號可以登入填寫
                b. 原欄位改用pdf檔案上傳方式取代，點擊上傳位置在文件表單內；此動作需要改變底層資料庫與上傳檔案處理方式
                c. 使用者介面調整：
                    c-1. 聯絡窗口 請直接平舖在畫面上，不用再點擊後才popup呈現。
                    c-2. 空表單按鈕下方 請接著各廠燈號，燈號標準：紅燈--職災申報逾期； 黃燈--未結案案件>=1 (暫存算不算??); 綠燈--沒有未結案 與 未申報；
                    c-3. 點擊燈號可以跳到[訪談清單管理]對應廠區的清單
        24/07/08
            1. 重構上傳檔案處理與儲存方式：
                1-1.上傳：結案存查、事故照片asis--分別由各自的暫存資料夾進行暫放(doc_pdf/temp、image/temp)；tobe--統一放在同一個暫存資料夾(doc_temp)，等待文件儲存時進行轉移。遇同名則在檔名後面加上序號。
                1-2.儲存：結案存檔、事故照片asis--doc_pdf下依照 棟別/案件簡稱/西元年份/anis_no 分層儲存；tobe--簡化成 doc_files下分成 西元年份/anis_no進行原名儲存。遇同名則在檔名後面加上序號。
                1-3.移除：結案存檔、事故照片asis--doc_pdf/offLine下依照 棟別/案件簡稱/西元年份/anis_no 分層儲存；tobe--簡化成 doc_offLine下分成 西元年份/anis_no進行原名儲存。遇同名則在檔名後面加上目前的時間搓。
        24/07/09
            1. 新增、修正事故者自述 與 目擊者自述所需欄位：
                1-1.修正doc_json文稿，type="text" 改成 type="file";
                1-2.修正js 問項生成function、檔案上傳function;
                1-3.修正php function：store、update、upload、unlink、delete...等函數
                1-4.修正儲存比對校驗與紀錄功能
                1-5.優化文件中 事故照片、事故者自述與目擊者自述檔案移除功能：上傳暫存temp--直接unlink刪除；既有檔案--移到offLine垃圾桶(以防不慎誤刪)
        24/07/10
            1. 修正並優化入口首頁顯示方式，左側表單縮排、右側各廠燈號、下方聯絡窗口；
            2. 獨立建構dashBoard，並設定成入口首頁。
        24/07/11
            1. 優化 form、site、fab生成方式改成循環function
            2. 預先準備Notify和發報紀錄功能...(尚未完成)
        24/07/12
            1. 為降低db讀取頻次、維持效率，將formcase、site、fab讀取結果轉存成JSON檔，並記錄成reload time，往後載入時直接讀取JSON檔。
            2. 依據reload time與現在時間差異>=3小時，程式重新讀取db並更新JSON檔與reload time。
            3. 建構一[強制更新]按鈕，可以跳過時間判斷，直接讀取db並更新JSON和reload time。
        24/07/15 一
            1. 分析紅黃綠燈之燈號條件，實驗製作mysql查詢語法。
        24/07/16 二
            1. 優化查詢匯出功能：結案狀態 typw=radio(單選)改成checkbox(多選)，同時修改後端db查詢語法，以符合多選需求；
            2. 匯出功能：本次已將Excel匯出的前處理工作埋入查詢後post_result(渲染)功能中同步進行，並在Excel中預先加入凍結窗格和篩選。
        24/07/17 三
            1. user提出需求，要將所有問項一併匯出，以利後續進行分析：
                1-1. 修正與優化倒出功能，因搜尋條件可查多種不同表單，所以需要獨立彙整出總key_list，再進行逐一拆解與彙整之後並轉存Excel
                1-2. 修正查詢後得到空值，若再按下匯出會造成錯誤畫面，已加入判斷長度，並將匯出按鈕進行關閉或解除disabled

