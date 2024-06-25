<?php
session_start();
include_once 'languages/' . ($_SESSION['lang'] ?? 'en') . '.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'] ?? 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSSUPSS - <?php echo $lang['disclaimer_title']; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>SSSUPSS</h1>
            <h2><?php echo $lang['full_name']; ?></h2>
        </header>
        
        <main>
            <h3><?php echo $lang['disclaimer_title']; ?></h3>
            <div class="content">
                <?php echo nl2br(htmlspecialchars($lang['disclaimer_content'])); ?>
            </div>
        </main>
        
        <footer>
            <p>&copy; 2024 SSSUPSS by Daniel Erdmann (madewithai.eu). <?php echo $lang['all_rights_reserved']; ?></p>
            <nav>
                <a href="index.php"><?php echo $lang['back_to_homepage']; ?></a>
            </nav>
        </footer>
    </div>
</body>
</html>