<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>動態表單</title>
</head>
<body>

<div id="form-container"></div>

<script>
// JSON 配置
var formData = {
  "fields": [
    {
      "type": "text",
      "label": "姓名",
      "name": "name",
      "required": true
    },
    {
      "type": "email",
      "label": "電子郵件",
      "name": "email",
      "required": true
    },
    {
      "type": "select",
      "label": "性別",
      "name": "gender",
      "options": [
        {"label": "男", "value": "male"},
        {"label": "女", "value": "female"}
      ],
      "required": true
    },
    {
      "type": "select",
      "label": "喜歡的類型",
      "name": "interests",
      "options": [
        {"label": "音樂", "value": "music"},
        {"label": "運動", "value": "sports"},
        {"label": "閱讀", "value": "reading"},
        {"label": "其他", "value": "other"}
      ]
    },
    {
      "type": "text",
      "label": "其他",
      "name": "other_interests",
      "depends_on": {"field": "interests", "value": "other"}
    }
  ]
};

// 動態生成表單
var formContainer = document.getElementById('form-container');
var form = document.createElement('form');

formData.fields.forEach(function(fieldData) {
  var fieldElement;
  
  if (fieldData.type === 'select') {
    // 如果字段類型為下拉選擇框，則創建一個<select>元素
    fieldElement = document.createElement('select');
    
    // 添加選項
    fieldData.options.forEach(function(option) {
      var optionElement = document.createElement('option');
      optionElement.textContent = option.label;
      optionElement.value = option.value;
      fieldElement.appendChild(optionElement);
    });
  } else {
    // 如果字段類型不是下拉選擇框，則創建一個<input>元素
    fieldElement = document.createElement('input');
    fieldElement.type = fieldData.type;
  }
  
  // 設置字段名稱、占位符和必填屬性
  fieldElement.name = fieldData.name;
  fieldElement.placeholder = fieldData.label;
  if (fieldData.required) {
    fieldElement.required = true;
  }
  
  // 添加依賴關係
  if (fieldData.depends_on) {
    fieldElement.style.display = 'none';
    var dependencyField = form.querySelector('[name="' + fieldData.depends_on.field + '"]');
    dependencyField.addEventListener('change', function() {
      if (dependencyField.value === fieldData.depends_on.value) {
        fieldElement.style.display = 'block';
      } else {
        fieldElement.style.display = 'none';
      }
    });
  }
  
  // 創建標籤元素，將字段添加到標籤中
  var label = document.createElement('label');
  label.textContent = fieldData.label;
  label.appendChild(fieldElement);
  
  // 將字段標籤添加到表單中
  form.appendChild(label);
});

// 將表單添加到頁面中
formContainer.appendChild(form);
</script>

</body>
</html>
