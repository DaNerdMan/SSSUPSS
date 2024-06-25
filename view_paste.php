<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once 'languages/' . ($_SESSION['lang'] ?? 'en') . '.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'] ?? 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSSUPSS - <?php echo $lang['paste_content']; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>SSSUPSS</h1>
            <h2><?php echo $lang['full_name']; ?></h2>
        </header>
        
        <main>
            <h3><?php echo $lang['paste_content']; ?></h3>
            <textarea id="pasteContent" readonly><?php echo htmlspecialchars($content); ?></textarea>
            <button onclick="copyToClipboard()"><?php echo $lang['copy_to_clipboard']; ?></button>
        </main>
        
        <footer>
            <p>&copy; 2024 SSSUPSS by Daniel Erdmann (madewithai.eu). <?php echo $lang['all_rights_reserved']; ?></p>
            <nav>
                <a href="index.php"><?php echo $lang['back_to_homepage']; ?></a>
            </nav>
        </footer>
    </div>
    <script>
    function copyToClipboard() {
        var copyText = document.getElementById("pasteContent");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        alert("<?php echo $lang['copied_to_clipboard']; ?>");
    }
    </script>
</body>
</html>