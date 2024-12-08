<?php
require 'db.php'; // Include database connection
$domain = 'localhost/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Invalid email address.";
    } else {
        $query = $db->prepare("SELECT * FROM users WHERE email = :email");
        $query->bindParam(':email', $email);
        $query->execute();
        if ($query->rowCount() === 1) {
            $token = bin2hex(random_bytes(16));
            $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));
            $db->prepare("UPDATE users SET reset_token = :token, token_expiry = :expiry WHERE email = :email")
                ->execute([':token' => $token, ':expiry' => $expiry, ':email' => $email]);
            // Send email with the reset link
            $reset_link = "$domain"."/reset_password.php?token=" . $token;
            mail($email, "Password Reset", "Click this link to reset your password: $reset_link");
        }
        $msg = "If the email exists, a reset link has been sent.";
    }
}
?>
