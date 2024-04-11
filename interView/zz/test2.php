<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Signature Pad Demo</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature-pad.min.css">
</head>
<body>
  <h1>Signature Pad Demo</h1>
  <canvas id="signature-pad" width="400" height="200"></canvas>
  <button id="clear-button">Clear</button>
  <button id="save-button">Save Signature</button>
  <img id="signature-image" src="" alt="Signature Image">
  <textarea id="signature-textarea" rows="4" cols="50"></textarea>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
  <script src="script.js"></script>
</body>
</html>


<script>

    window.onload = function() {
        var canvas = document.getElementById('signature-pad');
        var signaturePad = new SignaturePad(canvas);

        var clearButton = document.getElementById('clear-button');
        var saveButton = document.getElementById('save-button');
        var signatureImage = document.getElementById('signature-image');
        var signatureTextarea = document.getElementById('signature-textarea');

        clearButton.addEventListener('click', function(event) {
            signaturePad.clear();
        });
        saveButton.addEventListener('click', function(event) {
            if (signaturePad.isEmpty()) {
                alert("Please provide a signature first.");
            } else {
            // Convert the signature to a data URL
            var dataURL = signaturePad.toDataURL();
            // You can also send the dataURL to your server for further processing
            signatureImage.src = dataURL;
            // Set the data URL as the value of the textarea
            signatureTextarea.value = dataURL;
            }
        });
    };

    // window.onload = function() {
    //     var canvas = document.getElementById('signature-pad');
    //     var signaturePad = new SignaturePad(canvas);

    //     var clearButton = document.getElementById('clear-button');
    //     var saveButton = document.getElementById('save-button');
    //     var signatureImage = document.getElementById('signature-image');

    //     clearButton.addEventListener('click', function(event) {
    //         signaturePad.clear();
    //     });

    //     saveButton.addEventListener('click', function(event) {
    //         if (signaturePad.isEmpty()) {
    //         alert("Please provide a signature first.");
    //         } else {
    //         var dataURL = signaturePad.toDataURL();
    //         signatureImage.src = dataURL;
    //         // You can also send the dataURL to your server for further processing
    //         }
    //     });
    // };

    // window.onload = function() {
    //     var canvas = document.getElementById('signature-pad');
    //     var signaturePad = new SignaturePad(canvas);

    //     var clearButton = document.getElementById('clear-button');
    //     var saveButton = document.getElementById('save-button');
    //     var signatureImage = document.getElementById('signature-image');

    //     clearButton.addEventListener('click', function(event) {
    //         signaturePad.clear();
    //     });

    //     saveButton.addEventListener('click', function(event) {
    //         if (signaturePad.isEmpty()) {
    //             alert("Please provide a signature first.");
    //         } else {
    //             // Convert the signature to a data URL
    //             var dataURL = signaturePad.toDataURL();

    //             // Create a FormData object to send the data to the server
    //             var formData = new FormData();
    //             formData.append('signature', dataURL);

    //             // Send the data to the server using fetch or XMLHttpRequest
    //             fetch('/save-signature', {
    //                 method: 'POST',
    //                 body: formData
    //             })
    //             .then(response => response.json())
    //             .then(data => {
    //                 // Handle the response from the server
    //                 if (data.success) {
    //                     alert("Signature saved successfully!");
    //                 } else {
    //                     alert("Failed to save signature.");
    //                 }
    //             })
    //             .catch(error => {
    //                 console.error('Error:', error);
    //                 alert("An error occurred while saving signature.");
    //             });
    //         }
    //     });
    // };

</script>