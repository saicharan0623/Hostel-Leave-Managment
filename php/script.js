const camera = document.getElementById('camera');
const captureButton = document.getElementById('capture');
const previewImage = document.getElementById('preview');
const canvas = document.createElement('canvas');
let imageData = ''; // Define imageData variable outside the click event listener

let isImageCaptured = false; // Flag to track if an image has been captured

navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => {
        camera.srcObject = stream;
    })
    .catch(error => {
        console.error('Error accessing camera:', error);
    });

captureButton.addEventListener('click', () => {

    const context = canvas.getContext('2d');
    canvas.width = camera.videoWidth;
    canvas.height = camera.videoHeight;
    context.drawImage(camera, 0, 0, canvas.width, canvas.height);

    imageData = canvas.toDataURL('image/jpeg'); // Update imageData
    console.log(imageData);

    // Update the flag to indicate that an image has been captured
    isImageCaptured = true;

    // Display the captured image as a preview
    previewImage.src = imageData;
    previewImage.style.display = 'block';
});

// Function to handle form submission
function ValidateForm() {
    console.log('inside validate form');
    if (isImageCaptured) {
        // An image has been captured, proceed with form submission
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'apply_leave.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function () {
            console.log('XHR state changed:', xhr.readyState);

            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    console.log('Image saved successfully.');
                    alert('Image Captured');
                    ////window.location.href = 'leavebutton.php';
                } else {
                    console.error('Image save failed.');
                }
            }
        };

        const data = 'imageData=' + encodeURIComponent(imageData);
        xhr.send(data);
    } else {
        // No image has been captured, display an alert
        alert('Please capture an image before submitting.');
    }
}
