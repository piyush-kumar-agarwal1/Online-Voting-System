<?php
session_start();
include('connect.php');
require '../PHPMailer/PHPMailerAutoload.php';

// Function to load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        throw new Exception('.env file not found');
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
loadEnv(__DIR__ . '/.env');

// Function to send email
function smtp_mailer($to, $subject, $msg) {
    $mail = new PHPMailer(); 
    $mail->IsSMTP();
    $mail->SMTPAuth = true; 
    $mail->SMTPSecure = 'tls'; 
    $mail->Host = $_ENV['SMTP_HOST'];
    $mail->Port = $_ENV['SMTP_PORT']; 
    $mail->Username = $_ENV['SMTP_USER'];
    $mail->Password = $_ENV['SMTP_PASS'];
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->AddAddress($to);
    $mail->SetFrom($_ENV['SMTP_USER']);
    $mail->Subject = $subject;
    $mail->Body = $msg;
    $mail->SMTPOptions = array('ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => false
    ));
    if (!$mail->Send()) {
        return 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        return 'Message sent!';
    }
}

$votes = $_POST['gvotes'];
$total_votes = $votes + 1;
$gid = $_POST['gid'];
$uid = $_SESSION['userdata']['id'];

$update_votes = mysqli_query($connect, "UPDATE user SET votes='$total_votes' WHERE id='$gid' ");
$update_user_status = mysqli_query($connect, "UPDATE user SET status=1 WHERE id='$uid'");

if ($update_votes && $update_user_status) {
    $groups = mysqli_query($connect, "SELECT * FROM user WHERE role=2");
    $groupsdata = mysqli_fetch_all($groups, MYSQLI_ASSOC);
    $_SESSION['userdata']['status'] = 1;
    $_SESSION['groupsdata'] = $groupsdata;

    // Send confirmation email
    $voter_email = $_SESSION['userdata']['email']; // Assuming the voter's email is stored in the session
    $subject = "Vote Confirmation";
    $message = "Thank you for voting!";
    $email_result = smtp_mailer($voter_email, $subject, $message);

    if ($email_result == 'Sent') {
        echo '
        <script>
        alert("Voting successful! A confirmation email has been sent to your email address.");
        window.location = "../routes/dashboard.php";
        </script>
        ';
    } else {
        echo '
        <script>
        alert("Voting successful, but email could not be sent: ' . $email_result . '");
        window.location = "../routes/dashboard.php";
        </script>
        ';
    }
} else {
    echo '
    <script>
    alert("Some error occurred!");
    window.location = "../routes/dashboard.php";
    </script>
    ';
}
