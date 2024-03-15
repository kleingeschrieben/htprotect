# Benutzerverwaltungsanwendung

Diese Benutzerverwaltungsanwendung ermöglicht es, Benutzerzugänge zu bestimmten Bereichen einer Website sicher zu verwalten. Sie basiert auf der Verwendung von .htaccess und .htpasswd für die Authentifizierung und bietet eine webbasierte Oberfläche für die Verwaltung von Benutzerkonten. Diese Lösung ist ideal für kleine Projekte oder als Einstiegspunkt in sicherheitsbewusste Webentwicklungen.

## Funktionen

- **Benutzer hinzufügen**: Ermöglicht das Hinzufügen neuer Benutzer mit Benutzernamen und Passwort.
- **Benutzer löschen**: Ermöglicht das Entfernen existierender Benutzer aus der .htpasswd-Datei.
- **Passwörter ändern**: Ermöglicht das Aktualisieren der Passwörter vorhandener Benutzer.
- **Basic HTTP Authentifizierung**: Schützt spezifische Bereiche der Website.

## Voraussetzungen

- Apache HTTP Server mit .htaccess-Unterstützung
- PHP 7.4 oder neuer

## Installation und Konfiguration

### Konfigurationsdatei bearbeiten

Die `config.php` existiert bereits im Projektverzeichnis mit voreingestellten Anmeldeinformationen:

- Benutzername: `admin`
- Passwort: `admin`

Um die Anwendung mit Ihren eigenen Anmeldeinformationen zu konfigurieren:

1. Öffnen Sie `config.php` in einem Texteditor Ihrer Wahl.
2. Ändern Sie die Werte für `ADMIN_USER` und `ADMIN_PASSWORD` entsprechend.

    ```php
    <?php
    // Konfigurationsdaten
    define('ADMIN_USER', 'IhrBenutzername'); // Ersetzen Sie 'IhrBenutzername'
    define('ADMIN_PASSWORD', 'IhrPasswort'); // Ersetzen Sie 'IhrPasswort'
    ```

3. Speichern Sie die Änderungen.

### Apache und .htaccess konfigurieren

Stellen Sie sicher, dass Ihr Apache-Server so konfiguriert ist, dass .htaccess-Dateien verwendet werden können und dass die Authentifizierung über .htpasswd funktioniert. Dies kann je nach Hosting-Umgebung variieren.

## Benutzung

Nach der Konfiguration können Sie die Anwendung über Ihren Webbrowser aufrufen. Verwenden Sie die in `config.php` festgelegten Anmeldeinformationen, um Zugriff auf die Benutzerverwaltungsfunktionen zu erhalten.

## Entwicklung

Diese Anwendung ist so gestaltet, dass sie leicht erweitert und an spezifische Bedürfnisse angepasst werden kann. Sie ist eine gute Basis für die Entwicklung weiterführender Sicherheits- und Verwaltungsfunktionen.

## Support

Bei Fragen oder Problemen erstellen Sie bitte ein Issue im GitHub-Repository des Projekts.

## Lizenz

Die Benutzerverwaltungsanwendung ist Open-Source und unter der MIT-Lizenz veröffentlicht.
