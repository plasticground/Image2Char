<?php

header('Allow: GET');
header('HTTP/1.1 200 OK');
header('Content-Type: text/plain; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

$file = $_GET['img'] ?? '';

try {
    if (empty($file) || is_file($file)) {
        die();
    } else {
        $extension = pathinfo($file)['extension'] ?? '';

        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $img = imagecreatefromjpeg($file);
                break;
            case 'png':
                $img = imagecreatefrompng($file);
                break;
            case 'tga':
                $img = imagecreatefromtga($file);
                break;
            case 'gif':
                $img = imagecreatefromgif($file);
                break;
            default:
                die();
        }
    }
} catch (Throwable $e) {
    echo 'Something went wrong';
    die();
}

const WHITE_COLOR = 16777215;

const MIN_SCALE = 16,
    DEFAULT_SCALE = 64,
    MAX_SCALE = 1024;

const MIN_DETAIL_LEVEL = 2,
    DEFAULT_DETAIL_LEVEL = 4,
    MAX_DETAIL_LEVEL = 8;

const MIN_BRIGHTNESS = -255,
    DEFAULT_BRIGHTNESS = 0,
    MAX_BRIGHTNESS = 255;

const MIN_CONTRAST = -255,
    DEFAULT_CONTRAST = 0,
    MAX_CONTRAST = 255;

const MIN_GRAYSCALE = 0,
    DEFAULT_GRAYSCALE = 0,
    MAX_GRAYSCALE = 1;

const MIN_MEAN_REMOVAL = 0,
    DEFAULT_MEAN_REMOVAL = 0,
    MAX_MEAN_REMOVAL = 1;

const SYMBOLS = ['─', '/', '#', 'L', '@', '░', '▒', '█'];

function getSetting($setting, int $min, int $max): int
{
    $setting = (int) $setting;

    if ($setting > $max) {
        $setting = $max;
    } elseif ($setting < $min) {
        $setting = $min;
    }

    return $setting;
}

if (($countSymbols = mb_strlen($_GET['symbols'] ?? '')) > 1) {
    $symbols = mb_str_split($_GET['symbols']);
    $detailLevel = $countSymbols;
} else {
    $countSymbols = count(SYMBOLS);
    $symbols = SYMBOLS;
    $detailLevel = getSetting($_GET['detail_level'] ?? DEFAULT_DETAIL_LEVEL, MIN_DETAIL_LEVEL, MAX_DETAIL_LEVEL);
}

$scale = getSetting($_GET['scale'] ?? DEFAULT_SCALE, MIN_SCALE, MAX_SCALE);
$brightness = getSetting($_GET['brightness'] ?? DEFAULT_BRIGHTNESS, MIN_BRIGHTNESS, MAX_BRIGHTNESS);
$contrast = getSetting($_GET['contrast'] ?? DEFAULT_CONTRAST, MIN_CONTRAST, MAX_CONTRAST);
$grayscale = getSetting($_GET['grayscale'] ?? DEFAULT_GRAYSCALE, MIN_GRAYSCALE, MAX_GRAYSCALE);
$meanRemoval = getSetting($_GET['mean_removal'] ?? DEFAULT_MEAN_REMOVAL, MIN_MEAN_REMOVAL, MAX_MEAN_REMOVAL);

if (($x = imagesx($img)) >= ($y = imagesy($img))) {
    $rescaleX = $scale;
    $rescaleY = $scale * $y / $x;
} else {
    $rescaleX = $scale * $x / $y;
    $rescaleY = $scale;
}

if (in_array($extension, ['png', 'gif', 'tga'])) {
    $bg = imagecreatetruecolor($x, $x);
    $white = imagecolorallocate($bg, 255, 255, 255);
    imagefill($bg, 0, 0, $white);

    imagecopyresampled(
        $bg, $img,
        0, 0, 0, 0,
        $x, $y,
        $x, $y
    );

    $img = $bg;
}

$img = imagescale($img, $rescaleX, $rescaleY);

if ($grayscale) {
    imagefilter($img, IMG_FILTER_GRAYSCALE);
}

if ($meanRemoval) {
    imagefilter($img, IMG_FILTER_MEAN_REMOVAL);
}

if ($brightness !== 0) {
    imagefilter($img, IMG_FILTER_BRIGHTNESS, $brightness);
}

if ($contrast !== 0) {
    imagefilter($img, IMG_FILTER_CONTRAST, $contrast);
}

$totalX = imagesx($img);
$totalY = imagesy($img);
$cursorX = 0;
$cursorY = 0;
$middleSymbol = (int) ($countSymbols / 2);

while ($cursorY < $totalY) {
    while ($cursorX < $totalX) {
        $symbol = imagecolorat($img, $cursorX, $cursorY) / WHITE_COLOR * $detailLevel;

        echo $symbols[(int) $symbol] ?? $symbols[$middleSymbol];

        ++$cursorX;
    }

    echo PHP_EOL;

    $cursorX = 0;
    ++$cursorY;
}

imagedestroy($img);

die();



