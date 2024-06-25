<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

define('DATA_DIR', __DIR__ . '/data/');
define('MAX_CONTENT_LENGTH', 500000); // 500 KB
define('MAX_REQUESTS_PER_HOUR', 10);
define('CLEANUP_PROBABILITY', 0.1); // 10% Chance auf Bereinigung bei jeder Anfrage
define('MAX_EXPIRATION', 604800); // 1 Woche in Sekunden

// Spracheinstellungen
$availableLanguages = ['de', 'en', 'fr'];
$defaultLanguage = 'en';

if (isset($_GET['lang']) && in_array($_GET['lang'], $availableLanguages)) {
    $_SESSION['lang'] = $_GET['lang'];
} elseif (!isset($_SESSION['lang'])) {
    $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    $_SESSION['lang'] = in_array($browserLang, $availableLanguages) ? $browserLang : $defaultLanguage;
}

include_once 'languages/' . $_SESSION['lang'] . '.php';

// Überprüfe PHP-Version
if (version_compare(PHP_VERSION, '7.0.0', '<')) {
    die('PHP 7.0.0 oder höher wird benötigt. Ihre Version: ' . PHP_VERSION);
}

// Überprüfe ob notwendige Erweiterungen vorhanden sind
if (!extension_loaded('openssl')) {
    die('Die OpenSSL-Erweiterung ist nicht geladen');
}

if (!is_dir(DATA_DIR) || !is_writable(DATA_DIR)) {
    die('Das Datenverzeichnis existiert nicht oder ist nicht beschreibbar');
}

function checkRateLimit($fingerprint) {
    $rateLimitFile = DATA_DIR . 'rate_limit_' . md5($fingerprint);
    $currentTime = time();
    
    if (file_exists($rateLimitFile)) {
        $data = json_decode(file_get_contents($rateLimitFile), true);
        if ($currentTime - $data['timestamp'] > 3600) {
            $data = ['count' => 1, 'timestamp' => $currentTime];
        } elseif ($data['count'] >= MAX_REQUESTS_PER_HOUR) {
            return false;
        } else {
            $data['count']++;
        }
    } else {
        $data = ['count' => 1, 'timestamp' => $currentTime];
    }
    
    file_put_contents($rateLimitFile, json_encode($data));
    return true;
}

function encrypt($data, $key) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
    if ($encrypted === false) {
        throw new Exception('Verschlüsselung fehlgeschlagen');
    }
    return base64_encode($iv . $encrypted);
}

function decrypt($data, $key) {
    $data = base64_decode($data);
    $iv = substr($data, 0, openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = substr($data, openssl_cipher_iv_length('aes-256-cbc'));
    $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
    if ($decrypted === false) {
        throw new Exception('Entschlüsselung fehlgeschlagen');
    }
    return $decrypted;
}

function generateUniqueId() {
    return bin2hex(random_bytes(8));
}

function generateEncryptionKey() {
    return bin2hex(random_bytes(16));
}

function saveData($id, $content, $type, $expiration, $encryptionKey) {
    $data = [
        'content' => encrypt($content, $encryptionKey),
        'type' => $type,
        'expiration' => $expiration,
    ];
    return file_put_contents(DATA_DIR . $id, json_encode($data)) !== false;
}

function getData($id) {
    $file = DATA_DIR . $id;
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if ($data['expiration'] && $data['expiration'] < time()) {
            unlink($file);
            return null;
        }
        return $data;
    }
    return null;
}

function cleanupExpiredContent() {
    $files = glob(DATA_DIR . '*');
    $now = time();
    foreach ($files as $file) {
        if (is_file($file)) {
            $data = json_decode(file_get_contents($file), true);
            if (isset($data['expiration']) && $data['expiration'] > 0 && $data['expiration'] < $now) {
                unlink($file);
            }
        }
    }
}

try {
    if (mt_rand() / mt_getrandmax() < CLEANUP_PROBABILITY) {
        cleanupExpiredContent();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            $fingerprint = $_POST['fingerprint'] ?? '';
            if (!$fingerprint || !checkRateLimit($fingerprint)) {
                http_response_code(429);
                echo json_encode(['error' => $lang['too_many_requests']]);
                exit;
            }
            
            $id = generateUniqueId();
            $content = $_POST['content'];
            $type = $_POST['action'];
            $expiration = min(intval($_POST['expiration'] ?? 0) * 3600, MAX_EXPIRATION);
            $encryptionKey = $_POST['encryption_key'];

            if (strlen($content) > MAX_CONTENT_LENGTH) {
                http_response_code(400);
                echo json_encode(['error' => $lang['content_too_long']]);
                exit;
            }

            if ($type === 'url' && !filter_var($content, FILTER_VALIDATE_URL)) {
                http_response_code(400);
                echo json_encode(['error' => $lang['invalid_url']]);
                exit;
            }

            $expirationTimestamp = time() + $expiration;
            
            if (saveData($id, $content, $type, $expirationTimestamp, $encryptionKey)) {
                $viewLink = "https://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?id=$id&key=$encryptionKey";
                $deleteLink = "https://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?delete=$id&key=$encryptionKey";

                echo json_encode(['viewLink' => $viewLink, 'deleteLink' => $deleteLink]);
            } else {
                throw new Exception($lang['error_saving_data']);
            }
            exit;
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['id']) && isset($_GET['key'])) {
            $data = getData($_GET['id']);
            if ($data) {
                $decryptedContent = decrypt($data['content'], $_GET['key']);
                if ($data['type'] === 'url') {
                    header("Location: $decryptedContent");
                    exit;
                } elseif ($data['type'] === 'paste') {
                    $content = $decryptedContent;
                    include 'view_paste.php';
                    exit;
                }
            } else {
                echo $lang['content_not_found'];
                exit;
            }
        } elseif (isset($_GET['delete']) && isset($_GET['key'])) {
            $file = DATA_DIR . $_GET['delete'];
            if (file_exists($file)) {
                $data = json_decode(file_get_contents($file), true);
                try {
                    // Versuche, den Inhalt zu entschlüsseln
                    decrypt($data['content'], $_GET['key']);
                    // Wenn die Entschlüsselung erfolgreich war, lösche die Datei
                    if (unlink($file)) {
                        echo $lang['content_deleted'];
                    } else {
                        echo $lang['error_deleting_file'];
                    }
                } catch (Exception $e) {
                    // Wenn die Entschlüsselung fehlschlägt, verweigere das Löschen
                    echo $lang['invalid_delete_key'];
                }
                exit;
            } else {
                echo $lang['content_not_found'];
                exit;
            }
        }
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo $lang['error_occurred'];
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSSUPSS - <?php echo $lang['full_name']; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>SSSUPSS</h1>
            <h2><?php echo $lang['full_name']; ?></h2>
        </header>
        
        <main>
            <section class="info">
                <h3><?php echo $lang['how_it_works']; ?></h3>
                <ol>
                    <li><?php echo $lang['encryption_info']; ?></li>
                    <li><?php echo $lang['storage_info']; ?></li>
                    <li><?php echo $lang['link_info']; ?></li>
                    <li><?php echo $lang['url_redirect_info']; ?></li>
                    <li><?php echo $lang['auto_delete_info']; ?></li>
                    <li><?php echo $lang['cleanup_info']; ?></li>
                </ol>
                <p><?php echo $lang['key_info']; ?></p>
            </section>
            
            <form id="contentForm">
                <input type="hidden" id="fingerprint" name="fingerprint">
                <textarea id="content" name="content" placeholder="<?php echo $lang['content_placeholder']; ?>" required></textarea>
                <div class="form-group">
                    <div id="encryption-key-container">
                        <label for="encryption_key"><?php echo $lang['encryption_key']; ?></label>
                        <input type="text" id="encryption_key" name="encryption_key" readonly>
                        <button type="button" onclick="generateNewKey()"><?php echo $lang['generate_new_key']; ?></button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="expiration"><?php echo $lang['expiration']; ?></label>
                    <select id="expiration" name="expiration">
                        <option value="1"><?php echo $lang['1_hour']; ?></option>
                        <option value="24"><?php echo $lang['24_hours']; ?></option>
                        <option value="168"><?php echo $lang['1_week']; ?></option>
                    </select>
                </div>
                <div class="button-group">
                    <button type="button" onclick="submitForm('url')"><?php echo $lang['create_secret_url']; ?></button>
                    <button type="button" onclick="submitForm('paste')"><?php echo $lang['create_paste']; ?></button>
                </div>
            </form>
            <div id="result" style="display: none;">
                <h3><?php echo $lang['result']; ?></h3>
                <div class="form-group">
                    <label for="viewLink"><?php echo $lang['view_link']; ?></label>
                    <input type="text" id="viewLink" readonly>
                    <button onclick="copyToClipboard('viewLink')"><?php echo $lang['copy_to_clipboard']; ?></button>
                </div>
                <div class="form-group">
                    <label for="deleteLink"><?php echo $lang['delete_link']; ?></label>
                    <input type="text" id="deleteLink" readonly>
                    <button onclick="copyToClipboard('deleteLink')"><?php echo $lang['copy_to_clipboard']; ?></button>
                </div>
            </div>
        </main>
        
        <footer>
            <p>&copy; 2024 SSSUPSS by Daniel Erdmann (madewithai.eu). <?php echo $lang['all_rights_reserved']; ?></p>
            <nav>
                <a href="terms.php"><?php echo $lang['terms_of_service']; ?></a> |
                <a href="privacy.php"><?php echo $lang['privacy_policy']; ?></a> |
                <a href="disclaimer.php"><?php echo $lang['disclaimer']; ?></a>
            </nav>
            <div class="language-selector">
                <a href="?lang=de">Deutsch</a> |
                <a href="?lang=en">English</a> |
                <a href="?lang=fr">Français</a>
            </div>
        </footer>
    </div>
    <script src="js/fingerprint2.min.js"></script>
    <script src="js/script.js"></script>
    <script>
    function copyToClipboard(elementId) {
        var copyText = document.getElementById(elementId);
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        alert("<?php echo $lang['copied_to_clipboard']; ?>");
    }
    </script>
</body>
</html>