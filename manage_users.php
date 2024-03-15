<?php
require_once 'config.php'; // Lade die Konfigurationsdaten

$message = ''; // Nachricht für den Benutzer

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="Admin Bereich"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Authentifizierung erforderlich.';
    exit;
} else {
    if ($_SERVER['PHP_AUTH_USER'] !== ADMIN_USER || $_SERVER['PHP_AUTH_PW'] !== ADMIN_PASSWORD) {
        header('HTTP/1.0 401 Unauthorized');
        echo 'Ungültige Anmeldeinformationen.';
        exit;
    }
}

$htpasswdPath = '../htpasswd/.htpasswd'; // Stelle sicher, dass dieser Pfad korrekt ist

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['addUser']) && !empty($_POST['username']) && !empty($_POST['password'])) {
        $result = addUser($_POST['username'], $_POST['password'], $htpasswdPath);
        $message = $result ? "Benutzer {$_POST['username']} hinzugefügt." : "Benutzer {$_POST['username']} existiert bereits.";
    } elseif (isset($_POST['deleteUser'])) {
        $result = deleteUser($_POST['username'], $htpasswdPath);
        $message = $result ? "Benutzer {$_POST['username']} gelöscht." : "Benutzer {$_POST['username']} konnte nicht gefunden werden.";
    } elseif (isset($_POST['changePassword']) && !empty($_POST['newPassword'])) {
        $result = changePassword($_POST['username'], $_POST['newPassword'], $htpasswdPath);
        $message = $result ? "Passwort von {$_POST['username']} geändert." : "Fehler beim Ändern des Passworts von {$_POST['username']}.";
    }
    // Lade die Seite neu, um die aktualisierte Benutzerliste anzuzeigen
    header("Location: " . $_SERVER['PHP_SELF'] . "?message=" . urlencode($message));
    exit;
}

function addUser($username, $password, $htpasswdPath) {
    if (userExists($username, $htpasswdPath)) {
        return false;
    }
    $options = ['cost' => 12,];
    $encryptedPassword = password_hash($password, PASSWORD_BCRYPT, $options);
    $entry = "$username:$encryptedPassword\n";
    file_put_contents($htpasswdPath, $entry, FILE_APPEND);
    return true;
}

function deleteUser($username, $htpasswdPath) {
    $contents = file_get_contents($htpasswdPath);
    $lines = explode("\n", $contents);
    $newContents = "";
    $found = false;

    foreach ($lines as $line) {
        if (strpos($line, "$username:") !== 0) {
            $newContents .= $line . "\n";
        } else {
            $found = true;
        }
    }

    if ($found) {
        file_put_contents($htpasswdPath, trim($newContents));
        return true;
    } else {
        return false;
    }
}

function changePassword($username, $newPassword, $htpasswdPath) {
    if (!userExists($username, $htpasswdPath)) {
        return false;
    }
    $encryptedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
    $users = file($htpasswdPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $newContents = "";

    foreach ($users as $user) {
        list($storedUsername,) = explode(':', $user, 2);
        if ($username === $storedUsername) {
            $newContents .= "$storedUsername:$encryptedPassword\n";
        } else {
            $newContents .= "$user\n";
        }
    }

    file_put_contents($htpasswdPath, $newContents);
    return true;
}

function userExists($username, $htpasswdPath) {
    $contents = file_get_contents($htpasswdPath);
    $lines = explode("\n", $contents);

    foreach ($lines as $line) {
        if (strpos($line, "$username:") === 0) {
            return true;
        }
    }

    return false;
}

if (isset($_GET['message'])) {
    $message = urldecode($_GET['message']);
}

$users = file($htpasswdPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Benutzerverwaltung</title>
    <!-- Styles sind unverändert und wurden hier ausgelassen für Kürze. Füge die CSS-Styles von vorher hier ein. -->
</head>
<body>
    <meta charset="UTF-8">
    <title>Benutzerverwaltung</title>
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
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background-color: #fff;
            margin-top: 20px;
        }
        th, td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        form {
            margin: 0;
        }
        input[type="text"], input[type="password"] {
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            margin: 5px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .form-inline {
            display: flex;
            align-items: center;
        }
        .form-inline input[type="password"] {
            margin-left: 10px;
            margin-right: 5px;
        }
    </style>
</head>
<body>

<h2>Benutzer hinzufügen</h2>
<form method="post">
    <input type="text" id="username" name="username" placeholder="Benutzername" required>
    <input type="password" id="password" name="password" placeholder="Passwort" required>
    <input type="submit" name="addUser" value="Benutzer hinzufügen">
</form>

<h2>Benutzerliste</h2>
<table>
    <tr>
        <th>Benutzername</th>
        <th>Passwort ändern</th>
		<th>Löschen</th>
    </tr>
    <?php foreach ($users as $user): ?>
    <?php list($username,) = explode(':', $user, 2); ?>
        <tr>
            <td><?= htmlspecialchars($username) ?></td>
            <td class="form-inline">

                <form method="post" style="display: inline;">
                    <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">
                    <input type="password" name="newPassword" required placeholder="Neues Passwort">
                    <input type="submit" name="changePassword" value="Passwort ändern">
                </form>
            </td>
			<td>
                <form method="post" style="display: inline;">
                    <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">
                    <input type="submit" name="deleteUser" value="Löschen">
                </form>
			</td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
