<?php
$file = __DIR__ . '/logo.svg';
if (!is_file($file)) {
    http_response_code(404);
    exit;
}

$etag = '"' . md5_file($file) . '"';
$lastModified = gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT';

header('Content-Type: image/svg+xml; charset=utf-8');
header('Cache-Control: public, max-age=31536000, immutable');
header('ETag: ' . $etag);
header('Last-Modified: ' . $lastModified);

if ((isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) ||
    (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && trim($_SERVER['HTTP_IF_MODIFIED_SINCE']) === $lastModified)) {
    http_response_code(304);
    exit;
}

readfile($file);


