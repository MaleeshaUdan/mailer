<?php
// Include the PHPMailer files
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// Set the SMTP parameters securely
$smtp_host = "your-smtp-host";
$smtp_port = 465; // outgoing port
$smtp_username = "your-smtp-username";
$smtp_password = "your-smtp-password";

// MySQL database configuration
$database_host = "your-database-host";
$database_name = "your-database-name";
$database_username = "your-database-username";
$database_password = "your-database-password";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the submitted form data and perform input validation
    $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $subject = filter_input(INPUT_POST, "subject", FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, "message", FILTER_SANITIZE_STRING);

    // Create a new PHPMailer instance
    $mail = new PHPMailer();

    // Set the mailer to use SMTP
    $mail->isSMTP();

    // Set the SMTP server settings securely
    $mail->Host = $smtp_host;
    $mail->Port = $smtp_port;
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_username;
    $mail->Password = $smtp_password;
    $mail->SMTPSecure = 'ssl'; // Use 'ssl' if your SMTP server requires SSL/TLS encryption

    // Set the sender and recipient email addresses
    $mail->setFrom($smtp_username, 'Vanni Vogue');
    $mail->addAddress($email, $name); // Reply email sent to the submitter
    $mail->addAddress('example@gmailcom', 'Vanni Vogue Copy'); // Copy email sent to vannivogue@vau.ac.lk

    // Set the email subject and body
    $mail->Subject = 'Thank you for contacting Us';
    
    $mail->isHTML(true);
    $mail->Body = '<html>
    <head>
        <title>Thank you for contacting Vanni Vogue</title>
        <style>
            /* Define your styles here */
            body {
                background-color: #f7f7f7;
                color: #333333;
                font-family: Arial, sans-serif;
                font-size: 8px;
            }
            
            .container {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                background-color: #ffffff;
                border-radius: 5px;
                box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
            }
            
            h1 {
                color: #5555ff;
                font-size: 28px;
                margin-bottom: 20px;
            }
            
            p {
                font-size: 18px;
                margin-bottom: 20px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Thank you for contacting </h1>
            <p>Dear ' . htmlspecialchars($name) . ',</p>
            <p>Thank you for reaching out to us. We appreciate your interest and will get back to you as soon as possible.</p>
            <p>Here are the details of your message:</p>
            <p><strong>Name:</strong> ' . htmlspecialchars($name) . '</p>
            <p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>
            <p><strong>Subject:</strong> ' . htmlspecialchars($subject) . '</p>
            <p><strong>Message:</strong> ' . htmlspecialchars($message) . '</p>
            <p>Best regards,<br></p>
        </div>
    </body>
    </html>';

    // Send the email
    if ($mail->send()) {
        // Create a new PDO instance for database connection
        $dsn = "mysql:host=$database_host;dbname=$database_name";
        $pdo = new PDO($dsn, $database_username, $database_password);

        // Prepare the SQL statement
        $statement = $pdo->prepare("INSERT INTO messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)");

        // Bind the parameters
        $statement->bindParam(':name', $name);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':subject', $subject);
        $statement->bindParam(':message', $message);

        // Execute the SQL statement
        $statement->execute();

        echo '
        <!doctype html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Message Sent</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        </head>
        <body>
            <div class="container mt-5">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Message Sent!</strong> Thank you for contacting us. We will get back to you shortly.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <a class="btn btn-primary" href="#">OK</a>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        </body>
        </html>';
    } else {
        echo "Error sending email.";
    }
}
?>
