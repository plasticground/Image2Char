# Image2Char
Converting Image to Chars with PHP

### Headers

```
header('Allow: GET');
header('HTTP/1.1 200 OK');
header('Content-Type: text/plain; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
```

### Available GET Parameters
- img
- scale
- detail_level
- symbols
- brightness
- contrast
- grayscale
- mean_removal

#### img:

`Type: string (URL) | Supported formats: jpg, jpeg, png, tga, gif`

This parameter sets the source image. Alpha channels will be replaced with white.

#### scale:

`Type: int | Min: 16 | Default: 64 | Max: 1024`

This parameter sets the maximum image size in pixels on most sides.

#### detail_level

`Type: int | Min: 2 | Default: 4 | Max: 8`

This parameter sets the number of characters used from the preset set.

#### symbols

`Type: string`

This parameter sets characters and ignores **detail_level** parameter.

#### brightness

`Type: int | Min: -255 | Default: 0 | Max: 255`

This parameter sets the brightness of source image.

#### contrast

`Type: int | Min: -255 | Default: 0 | Max: 255`

This parameter sets the contrast of source image.

#### grayscale

`Type: int (bool) | Min: 0 | Default: 0 | Max: 1`

This parameter enable or disable the grayscale filter for source image.

#### mean_removal

`Type: int (bool) | Min: 0 | Default: 0 | Max: 1`

This parameter enable or disable the mean removal filter for source image.
