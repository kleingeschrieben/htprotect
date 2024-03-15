<?php
$htpasswdPath = '../htpasswd/.htpasswd';

$message = ""; // Nachricht für den Benutzer

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentUsername = $_SERVER['PHP_AUTH_USER'];
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Überprüfe, ob die Felder nicht leer sind und die Passwörter übereinstimmen
    if (empty($newPassword) || empty($confirmPassword)) {
        $message = "Bitte fülle alle Felder aus.";
    } elseif ($newPassword !== $confirmPassword) {
        $message = "Die Passwörter stimmen nicht überein.";
    } else {
        changePassword($currentUsername, $newPassword, $htpasswdPath);
        $message = "Passwort erfolgreich geändert.";
    }
}

function changePassword($username, $newPassword, $htpasswdPath) {
    $users = file($htpasswdPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $newContents = "";

    foreach ($users as $user) {
        list($storedUsername,) = explode(':', $user, 2);
        if ($username == $storedUsername) {
            $encryptedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $newContents .= "$storedUsername:$encryptedPassword\n";
        } else {
            $newContents .= "$user\n";
        }
    }

    file_put_contents($htpasswdPath, $newContents);
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Passwort ändern</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        h2 {
            color: #444;
            text-align: center;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: auto;
        }
        input[type="password"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #ddd;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .message {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<h2>Passwort ändern</h2>
<div class="message"><?= htmlspecialchars($message) ?></div>
<form action="" method="post">
    <input type="password" name="password" placeholder="Neues Passwort" required><br>
    <input type="password" name="confirmPassword" placeholder="Passwort wiederholen" required><br>
    <input type="submit" value="Passwort ändern">
</form>

</body>
</html>
