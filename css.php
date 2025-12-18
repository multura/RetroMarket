<?php
// Разрешенные CSS файлы для безопасности
$allowed_files = [
    'holo-base-elements.css',
    'holo-kk-light-elements.css',
    'holo-base-widgets.css',
    'holo-kk-light-widgets.css'
];

$file_name = isset($_GET['file']) ? basename($_GET['file']) : '';

if (empty($file_name) || !in_array($file_name, $allowed_files, true)) {
    http_response_code(404);
    exit;
}

$file = __DIR__ . '/' . $file_name;

if (!is_file($file)) {
    http_response_code(404);
    exit;
}

$etag = '"' . md5_file($file) . '"';
$lastModified = gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT';

header('Content-Type: text/css; charset=utf-8');
header('Cache-Control: public, max-age=31536000, immutable');
header('ETag: ' . $etag);
header('Last-Modified: ' . $lastModified);

if ((isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) ||
    (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && trim($_SERVER['HTTP_IF_MODIFIED_SINCE']) === $lastModified)) {
    http_response_code(304);
    exit;
}

readfile($file);

