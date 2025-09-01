<?php
// Магазин приложений с темой Holo Ice Cream Sandwich Dark
// Сканирование APK файлов из папки apps

$apps_dir = 'apps/';
$apps = [];

// Сканируем папку apps для поиска APK файлов
if (is_dir($apps_dir)) {
    $files = scandir($apps_dir);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'apk') {
            $file_path = $apps_dir . $file;
            $file_size = filesize($file_path);
            $file_date = date("d.m.Y H:i", filemtime($file_path));
            
            $apps[] = [
                'name' => pathinfo($file, PATHINFO_FILENAME),
                'filename' => $file,
                'size' => formatFileSize($file_size),
                'date' => $file_date,
                'path' => $file_path
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
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <title>RetroMarket</title>
    
    <!-- Holo Web CSS библиотека - Ice Cream Sandwich Dark -->
    <link rel="stylesheet" type="text/css" href="holo-base-elements.css" />
    <link rel="stylesheet" type="text/css" href="holo-ics-dark-elements.css" />
    <link rel="stylesheet" type="text/css" href="holo-base-widgets.css" />
    <link rel="stylesheet" type="text/css" href="holo-ics-dark-widgets.css" />
    
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
</head>
<body>
    <!-- Верхняя панель действий -->
    <header class="holo-actionBar">
        <button class="holo-title">
            RetroMarket
        </button>
        <button onclick="alert('Количество приложений: <?php echo count($apps); ?>')" style="float:right;"><?php echo count($apps); ?></button>
    </header>

    <div>
          <a href="../"><button>Главная страница</button></a>
          <a href="dmca.html"><button>DMCA</button></a>
    </div>
    <!-- Основной контент -->
    <?php if (empty($apps)): ?>
        <p>Что-то пусто тут...</p>
    <?php else: ?>
        <!-- Список приложений в стиле Holo -->
        <ul class="holo-list">
            <?php foreach ($apps as $index => $app): ?>
                <li>
                    <button onclick="downloadApp('<?php echo htmlspecialchars($app['path']); ?>', '<?php echo htmlspecialchars($app['filename']); ?>')" style="width: 100%; width: calc(100% + 32px); text-align: left;">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div style="flex: 1;">
                                <div style="font-weight: bold; margin-bottom: 4px;"><?php echo htmlspecialchars($app['name']); ?></div>
                                <div style="font-size: 0.9em; color: #CCCCCC;">
                                    Размер: <?php echo $app['size']; ?> | Дата: <?php echo $app['date']; ?>
                                </div>
                                <div style="font-size: 0.8em; color: #999999;">
                                    <?php echo htmlspecialchars($app['filename']); ?>
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
