<?php
// Магазин приложений с темой Holo Ice Cream Sandwich Dark
// Сканирование APK файлов из папки apps

$base_dir = 'apps/';
$apps = [];
$folders = [];

// Пользовательские названия папок берём из JSON (ключ — относительный путь)
$folder_names_file = __DIR__ . '/folder_names.json';
$folder_names = [];
$folder_names_raw = @file_get_contents($folder_names_file);
if ($folder_names_raw !== false) {
    $decoded = json_decode($folder_names_raw, true);
    if (is_array($decoded)) {
        $folder_names = $decoded;
    }
}

// Текущая относительная директория внутри apps (безопасно очищаем ввод)
$rel_dir = isset($_GET['dir']) ? trim(str_replace(['..', "\\"], ['', '/'], $_GET['dir']), "/") : '';

// Вычисляем абсолютные пути и проверяем, что не выходим за пределы apps
$root_path = realpath($base_dir) ?: $base_dir; // на случай если каталога ещё нет
$current_dir_path = $rel_dir !== '' ? $base_dir . $rel_dir . '/' : $base_dir;
$current_real = realpath($current_dir_path);

if ($current_real !== false && strpos($current_real, realpath($base_dir)) === 0) {
    $apps_dir = rtrim($current_dir_path, '/') . '/';
} else {
    $rel_dir = '';
    $apps_dir = $base_dir;
}

// Сканируем папку apps для поиска APK файлов
if (is_dir($apps_dir)) {
    $files = scandir($apps_dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $full = $apps_dir . $file;
        if (is_dir($full)) {
            $rel_path = ltrim(($rel_dir !== '' ? $rel_dir . '/' : '') . $file, '/');
            $folders[] = [
                'name' => $file,
                'rel' => $rel_path,
                'display' => $folder_names[$rel_path] ?? $file
            ];
            continue;
        }
        if (pathinfo($file, PATHINFO_EXTENSION) === 'apk') {
            $rel_file = ($rel_dir !== '' ? $rel_dir . '/' : '') . $file;
            $web_path = $base_dir . $rel_file;
            $file_size = @filesize($full);
            $file_date = @filemtime($full);
            $apps[] = [
                'name' => pathinfo($file, PATHINFO_FILENAME),
                'filename' => $file,
                'size' => $file_size !== false ? formatFileSize($file_size) : '—',
                'date' => $file_date !== false ? date("d.m.Y H:i", $file_date) : '—',
                'path' => $web_path,
                'url' => safeUrlPath($web_path)
            ];
        }
    }
}

function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

// Кодирует путь для ссылки, сохраняя слеши
function safeUrlPath($path) {
    return str_replace('%2F', '/', rawurlencode($path));
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <title>RetroMarket</title>
    <style>
        .logo-img {
            height: 28px;
            width: auto;
            vertical-align: middle;
        }
    </style>
    
    <!-- Holo Web CSS библиотека - Ice Cream Sandwich Dark -->
    <link rel="stylesheet" type="text/css" href="css.php?file=holo-base-elements.css" />
    <link rel="stylesheet" type="text/css" href="css.php?file=holo-kk-light-elements.css" />
    <link rel="stylesheet" type="text/css" href="css.php?file=holo-base-widgets.css" />
    <link rel="stylesheet" type="text/css" href="css.php?file=holo-kk-light-widgets.css" />
    
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
</head>
<body>
    <!-- Верхняя панель действий -->
    <header class="holo-actionBar">
      <?php if ($rel_dir !== ''): ?>
        <button class="holo-title holo-up" onclick="window.open('index.php', '_self');">
            <img class="logo-img" src="logo.php" alt="RetroMarket">
        </button>
      <?php else: ?>
        <button class="holo-title">
            <img class="logo-img" src="logo.php" alt="RetroMarket">
        </button>
      <?php endif; ?>
      <?php if ($rel_dir !== ''): ?>
        <button onclick="alert('Количество приложений: <?php echo count($apps); ?>')" style="float:right;"><?php echo count($apps); ?></button>
      <?php endif; ?>
    </header>

    <div>
    <?php if ($rel_dir === ''): ?>
          <a href="../"><button>mltr</button></a>
    <?php endif; ?>
          <a href="dmca.html"><button>DMCA</button></a>
    </div>
    <!-- Основной контент -->

    <?php if (empty($apps) && empty($folders)): ?>
        <p>Что-то пусто тут...</p>
    <?php else: ?>
        <!-- Список папок и приложений в одном списке -->
        <ul class="holo-list" id="catalog">
            <?php foreach ($folders as $folder): ?>
                <li>
                    <button onclick="window.location.href='?dir=<?php echo urlencode($folder['rel']); ?>'" style="width: 100%; width: calc(100% + 32px); text-align: left;">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div style="flex: 1;">
                                <div style="font-weight: bold; margin-bottom: 4px;"><?php echo htmlspecialchars($folder['display']); ?></div>
                                <div style="font-size: 0.9em; color: #CCCCCC;">Папка</div>
                            </div>
                        </div>
                    </button>
                </li>
            <?php endforeach; ?>
            <?php foreach ($apps as $index => $app): ?>
                <li>
                    <button onclick="downloadApp('<?php echo htmlspecialchars($app['url']); ?>', '<?php echo htmlspecialchars($app['filename']); ?>')" style="width: 100%; width: calc(100% + 32px); text-align: left;">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div style="flex: 1;">
                                <div style="font-weight: bold; margin-bottom: 4px;"><?php echo htmlspecialchars($app['name']); ?></div>
                                <div style="font-size: 0.9em; color: #CCCCCC;">
                                    Размер: <?php echo $app['size']; ?>
                                </div>
                            </div>
                        </div>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- Нижняя панель действий -->
    <footer class="holo-actionBar">
        <button onclick="window.location.reload();" style="width: 100%; padding-left: 0; padding-right: 0;">
            Обновить
        </button>
    </footer>

    <!-- Holo Touch для улучшения работы с сенсорными устройствами -->
    <script type="text/javascript" src="holo-touch.js"></script>
    
    <script>
        function downloadApp(path, filename) {
            // Создаем временную ссылку для скачивания
            const link = document.createElement('a');
            link.href = path;
            link.download = filename;
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
</html>
