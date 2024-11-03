<?php
include 'config.php';

// Function to sanitize form input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Initialize alert message and type variables
$response_message = "";
$response_status = "error"; // Default to error

// Check if the reCAPTCHA response is set
if (isset($_POST['g-recaptcha-response'])) {
    $recaptchaResponse = $_POST['g-recaptcha-response'];
    
    // Verify the reCAPTCHA response with Google servers
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret'   => RECAPTCHA_SECRET_KEY,
        'response' => $recaptchaResponse
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $result   = json_decode($response);

    if ($result->success) {
        // Verification successful, process the form submission
        $name    = sanitize_input($_POST['name']);
        $email   = sanitize_input($_POST['email']);
        $phone   = sanitize_input($_POST['phone']);
        $message = sanitize_input($_POST['message']);

        // Email recipient
        $to = 'willrichardson182@gmail.com';

        // Email subject
        $subject = 'Elena - You have received a new message';

        // Email body
        $body = "Name: $name\nEmail: $email\nPhone Number: $phone\nMessage: $message";

        // Additional headers
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        // Send the email with error handling
        if (mail($to, $subject, $body, $headers)) {
            $response_message = "Form submitted successfully! Thank you. We aim to reply within 48 working hours.";
            $response_status = "success";
        } else {
            $response_message = "Error: Unable to send email. Please give us a call instead!";
        }
    } else {
        $response_message = "Google reCAPTCHA verification failed. Please try again.";
    }
} else {
    $response_message = "reCAPTCHA response is missing.";
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode([
    'status'  => $response_status,
    'message' => $response_message
]);
?>
