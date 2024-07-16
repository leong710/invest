-- mysql table = _document ，其主要欄位 
-- 1. fab_id 表示該紀錄歸屬哪一個廠區
-- 2. _odd 儲存的是VARCHAR格式的JSON文字串，內容如下：{"due_day":"2024-08-05","od_day":""}，其中due_day代表最後期限，od_day代表完成的日期
-- 3. idty 表單狀態，分別是 1=簽核中、6=暫存、3=作廢、10=結案

-- 另一個 table = _fab ，主要欄位
-- 1. id  = _document.fab_id
-- 2. fab_title 廠區名稱

-- 我的查詢需求是要找出每一個fab的燈號
-- 其燈號條件如下：
-- 綠燈：沒有暫存、沒有簽核中、沒有未完成日期(就是od_day不能是空值)
-- 黃燈：只要有 暫存 或 簽核中 或是 沒有未完成日期(但是today減due_day要>0且<=5)
-- 紅燈：只要od_day沒有日期，且today減due_day要<=0

-- 可以幫我製作出查詢語法嗎?

SELECT 
    f.fab_title,
    CASE
        WHEN COUNT(CASE 
            WHEN d.idty IN (1, 6) 
              OR (JSON_UNQUOTE(JSON_EXTRACT(d._odd, '$.od_day')) = '' 
              AND d._odd <> '[]' )
            THEN 1 
            END) = 0 
        THEN '綠燈'
        WHEN COUNT(CASE 
            WHEN d.idty IN (1, 6) 
              OR JSON_UNQUOTE(JSON_EXTRACT(d._odd, '$.od_day')) = '' 
                AND DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(d._odd, '$.due_day'))) > 0 
                AND DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(d._odd, '$.due_day'))) <= 5 
              OR d._odd = '[]' 
                AND d.idty NOT IN (1, 6)
            THEN 1 
            END) > 0 
        THEN '黃燈'
        WHEN COUNT(CASE 
            WHEN JSON_UNQUOTE(JSON_EXTRACT(d._odd, '$.od_day')) = '' 
              AND DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(d._odd, '$.due_day'))) <= 0 
            THEN 1 
            END) > 0 
        THEN '紅燈'
        ELSE '無資料'
    END AS 信號燈
FROM _fab f
LEFT JOIN _document d ON f.id = d.fab_id
GROUP BY f.fab_title;

-- 我的修正
SELECT f.id, f.fab_title, DATEDIFF(JSON_UNQUOTE(JSON_EXTRACT(d._odd, '$.due_day')), CURDATE()) AS '_audit', 
    CASE
        WHEN COUNT(CASE 
                    WHEN d.idty IN (1, 6, 10) 
                        AND (d._odd <> '[]' AND JSON_UNQUOTE(JSON_EXTRACT(d._odd, '$.od_day')) = '')
                    THEN 1 
                    END) = 0 
            THEN 'success'      -- G

        WHEN COUNT(CASE 
                    WHEN d.idty IN (1, 6, 10) 
                        AND (d._odd <> '[]' AND JSON_UNQUOTE(JSON_EXTRACT(d._odd, '$.od_day')) = '' 
                            AND DATEDIFF(JSON_UNQUOTE(JSON_EXTRACT(d._odd, '$.due_day')), CURDATE()) > 1 
                            -- AND DATEDIFF(JSON_UNQUOTE(JSON_EXTRACT(d._odd, '$.due_day')), CURDATE()) <= 5 
                            )
                        THEN 1 
                    END) > 0 
            THEN 'warning'      -- Y

        WHEN COUNT(CASE 
                    WHEN d._odd <> '[]' AND JSON_UNQUOTE(JSON_EXTRACT(d._odd, '$.od_day')) = '' 
                        AND DATEDIFF(JSON_UNQUOTE(JSON_EXTRACT(d._odd, '$.due_day')), CURDATE()) <= 1 
                    THEN 1 
                END) > 0 
            THEN 'danger'       -- R

        ELSE 'secondary'
    END AS 'light'
FROM _fab f
LEFT JOIN _document d ON f.id = d.fab_id
WHERE f.flag = 'On'
GROUP BY f.id 


-- Notify清單
SELECT _d.id, _d.fab_id, _d.idty, _d._odd , DATEDIFF(JSON_UNQUOTE(JSON_EXTRACT(_d._odd, '$.due_day')), CURDATE()) AS '_dueDay'
FROM `_document` _d
WHERE DATEDIFF(JSON_UNQUOTE(JSON_EXTRACT(_d._odd, '$.due_day')), CURDATE()) <= 5;