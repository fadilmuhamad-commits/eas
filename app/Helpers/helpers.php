<?php
function hexToRgb($hex)
{
  $hex = ltrim($hex, '#');
  if (!ctype_xdigit($hex) || strlen($hex) != 6) {
    throw new InvalidArgumentException('Invalid hex color code');
  }

  $r = hexdec(substr($hex, 0, 2));
  $g = hexdec(substr($hex, 2, 2));
  $b = hexdec(substr($hex, 4, 2));

  return [$r, $g, $b];
}
