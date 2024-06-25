# SSSUPSS - Super Simple Secure URL and Paste Sharing Service

[English](#english) | [Deutsch](#deutsch) | [Français](#français)

## English

### Introduction

SSSUPSS (Super Simple Secure URL and Paste Sharing Service) is a web application that allows users to securely share URLs and text snippets. 
It uses strong encryption to ensure the privacy and security of shared content.

### Features

1. **URL Obfuscation**: Create encrypted links to hide the target URL.
2. **Paste Sharing**: Share text snippets securely.
3. **End-to-End Encryption**: All content is encrypted using AES-256-CBC.
4. **Auto-Expiry**: Shared content can be set to expire after a specified time.
5. **Secure Deletion**: Content is automatically deleted after expiration.
6. **No Registration Required**: Use the service without creating an account.
7. **Multi-language Support**: Available in English, German, and French.

### How It Works

1. The user enters a URL or text content.
2. The content is encrypted with a randomly generated key using AES-256-CBC.
3. The encrypted content is stored on the server with a unique identifier.
4. Two links are generated:
   - View Link: Contains the ID and encryption key for accessing the content.
   - Delete Link: Allows manual deletion of the content.
5. When accessing the View Link, the content is decrypted and displayed (or redirected for URLs).
6. Content is automatically deleted after expiration or when accessed via the Delete Link.

### Installation

1. Clone the repository to your web server.
2. Ensure PHP 7.0 or higher is installed with the OpenSSL extension.
3. Create a `data` directory in the project root and set appropriate write permissions (chmod 750).
4. Configure your web server to use HTTPS for secure connections.

### Usage

1. Access the main page through a web browser.
2. Enter the URL or text you want to share.
3. Select an expiration time.
4. Click "Create Secret URL" or "Create Paste".
5. Share the generated View Link with others.

### Security Considerations

- The encryption key is never stored on the server.
- Use HTTPS to prevent man-in-the-middle attacks.
- Regularly update the server and dependencies.

### License

This project is licensed under the MIT License with the additional requirement of attribution. See the [LICENSE](LICENSE) file for details.

---

## Deutsch

### Einführung

SSSUPSS (Super Simple Secure URL and Paste Sharing Service) ist eine Webanwendung, die es Benutzern ermöglicht, URLs und Textschnipsel sicher zu teilen.
Es verwendet starke Verschlüsselung, um die Privatsphäre und Sicherheit der geteilten Inhalte zu gewährleisten.

### Funktionen

1. **URL-Verschleierung**: Erstellen Sie verschlüsselte Links, um die Ziel-URL zu verbergen.
2. **Paste-Sharing**: Teilen Sie Textschnipsel sicher.
3. **Ende-zu-Ende-Verschlüsselung**: Alle Inhalte werden mit AES-256-CBC verschlüsselt.
4. **Automatisches Verfallsdatum**: Geteilte Inhalte können nach einer bestimmten Zeit automatisch verfallen.
5. **Sichere Löschung**: Inhalte werden nach Ablauf automatisch gelöscht.
6. **Keine Registrierung erforderlich**: Nutzung des Dienstes ohne Kontoerstellung.
7. **Mehrsprachige Unterstützung**: Verfügbar in Englisch, Deutsch und Französisch.

### Funktionsweise

1. Der Benutzer gibt eine URL oder einen Textinhalt ein.
2. Der Inhalt wird mit einem zufällig generierten Schlüssel mittels AES-256-CBC verschlüsselt.
3. Der verschlüsselte Inhalt wird mit einer eindeutigen Kennung auf dem Server gespeichert.
4. Zwei Links werden generiert:
   - Ansichts-Link: Enthält die ID und den Verschlüsselungsschlüssel für den Zugriff auf den Inhalt.
   - Lösch-Link: Ermöglicht die manuelle Löschung des Inhalts.
5. Beim Zugriff auf den Ansichts-Link wird der Inhalt entschlüsselt und angezeigt (oder bei URLs weitergeleitet).
6. Inhalte werden nach Ablauf oder bei Zugriff über den Lösch-Link automatisch gelöscht.

### Installation

1. Klonen Sie das Repository auf Ihren Webserver.
2. Stellen Sie sicher, dass PHP 7.0 oder höher mit der OpenSSL-Erweiterung installiert ist.
3. Erstellen Sie ein `data`-Verzeichnis im Projektroot und setzen Sie entsprechende Schreibrechte (chmod 750).
4. Konfigurieren Sie Ihren Webserver für die Verwendung von HTTPS für sichere Verbindungen.

### Verwendung

1. Greifen Sie über einen Webbrowser auf die Hauptseite zu.
2. Geben Sie die URL oder den Text ein, den Sie teilen möchten.
3. Wählen Sie eine Ablaufzeit aus.
4. Klicken Sie auf "Geheime URL erstellen" oder "Paste erstellen".
5. Teilen Sie den generierten Ansichts-Link mit anderen.

### Sicherheitsüberlegungen

- Der Verschlüsselungsschlüssel wird nie auf dem Server gespeichert.
- Verwenden Sie HTTPS, um Man-in-the-Middle-Angriffe zu verhindern.
- Aktualisieren Sie regelmäßig den Server und die Abhängigkeiten.

### Lizenz

Dieses Projekt ist unter der MIT-Lizenz mit der zusätzlichen Anforderung der Namensnennung lizenziert. Siehe die [LICENSE](LICENSE)-Datei für Details.

---

## Français

### Introduction

SSSUPSS (Super Simple Secure URL and Paste Sharing Service) est une application web qui permet aux utilisateurs de partager des URL et des extraits de texte en toute sécurité.
Il utilise un chiffrement fort pour garantir la confidentialité et la sécurité du contenu partagé.

### Caractéristiques

1. **Dissimulation d'URL** : Créez des liens chiffrés pour masquer l'URL cible.
2. **Partage de Paste** : Partagez des extraits de texte en toute sécurité.
3. **Chiffrement de bout en bout** : Tout le contenu est chiffré en utilisant AES-256-CBC.
4. **Expiration automatique** : Le contenu partagé peut être configuré pour expirer après un temps spécifié.
5. **Suppression sécurisée** : Le contenu est automatiquement supprimé après expiration.
6. **Pas d'inscription requise** : Utilisez le service sans créer de compte.
7. **Support multilingue** : Disponible en anglais, allemand et français.

### Fonctionnement

1. L'utilisateur entre une URL ou un contenu textuel.
2. Le contenu est chiffré avec une clé générée aléatoirement en utilisant AES-256-CBC.
3. Le contenu chiffré est stocké sur le serveur avec un identifiant unique.
4. Deux liens sont générés :
   - Lien de vue : Contient l'ID et la clé de chiffrement pour accéder au contenu.
   - Lien de suppression : Permet la suppression manuelle du contenu.
5. Lors de l'accès au lien de vue, le contenu est déchiffré et affiché (ou redirigé pour les URL).
6. Le contenu est automatiquement supprimé après expiration ou lorsqu'il est accédé via le lien de suppression.

### Installation

1. Clonez le dépôt sur votre serveur web.
2. Assurez-vous que PHP 7.0 ou supérieur est installé avec l'extension OpenSSL.
3. Créez un répertoire `data` à la racine du projet et définissez les permissions d'écriture appropriées (chmod 750).
4. Configurez votre serveur web pour utiliser HTTPS pour des connexions sécurisées.

### Utilisation

1. Accédez à la page principale via un navigateur web.
2. Entrez l'URL ou le texte que vous souhaitez partager.
3. Sélectionnez un temps d'expiration.
4. Cliquez sur "Créer une URL secrète" ou "Créer un paste".
5. Partagez le lien de vue généré avec d'autres.

### Considérations de sécurité

- La clé de chiffrement n'est jamais stockée sur le serveur.
- Utilisez HTTPS pour prévenir les attaques de l'homme du milieu.
- Mettez régulièrement à jour le serveur et les dépendances.

### Licence

Ce projet est sous licence MIT avec l'exigence supplémentaire d'attribution. Voir le fichier [LICENSE](LICENSE) pour plus de détails.