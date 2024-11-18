<?php
include("connect.php");
require '../PHPMailer/PHPMailerAutoload.php';

// Function to load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        throw new Exception('.env file not found at ' . $path);
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// Load environment variables
try {
    loadEnv(__DIR__ . '/.env');
} catch (Exception $e) {
    die($e->getMessage());
}

// Function to send email
function smtp_mailer($to, $subject, $msg) {
    $mail = new PHPMailer(); 
    $mail->IsSMTP(); 
    $mail->SMTPAuth = true; 
    $mail->SMTPSecure = 'tls'; 
    $mail->Host = $_ENV['SMTP_HOST'];
    $mail->Port = $_ENV['SMTP_PORT']; 
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';
    //$mail->SMTPDebug = 2; 
    $mail->Username = $_ENV['SMTP_USER'];
    $mail->Password = $_ENV['SMTP_PASS'];
    $mail->SetFrom($_ENV['SMTP_USER']);
    $mail->Subject = $subject;
    $mail->Body = $msg;
    $mail->AddAddress($to);
    $mail->SMTPOptions = array('ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => false
    ));
    if (!$mail->Send()) {
        echo $mail->ErrorInfo;
    } else {
        return 'Sent';
    }
}

$name = $_POST['name'];
$mobile = $_POST['mobile'];
$password = $_POST['password'];
$cpassword = $_POST['cpassword'];
$address = $_POST['address'];
$email = $_POST['email']; // New email field
$image = $_FILES['photo']['name'];
$tmp_name = $_FILES['photo']['tmp_name'];
$role = $_POST['role'];

if ($password == $cpassword) {
    move_uploaded_file($tmp_name, "../uploads/$image");
    $insert = mysqli_query($connect, "INSERT INTO user (name, mobile, password, address, email, photo, role, status, votes) VALUES ('$name', '$mobile', '$password', '$address', '$email', '$image', '$role', 0, 0)");
    if ($insert) {
        // Send confirmation email
        $subject = "Registration Confirmation";
        $message = "Thank you for registering!";
        smtp_mailer($email, $subject, $message);

        echo '
        <script>
        alert("Registration successful! A confirmation email has been sent to your email address.");
        window.location = "../";
        </script>
        ';
    } else {
        echo '
        <script>
        alert("Some error occurred!");
        window.location = "../routes/register.html";
        </script>
        ';
    }
} else {
    echo '
    <script>
    alert("Password and Confirm password do not match!");
    window.location = "../routes/register.html";
    </script>
    ';
}
?>