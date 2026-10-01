<?php
if (!defined('POV_ROOT')) {
  define('POV_ROOT', dirname(__DIR__));
}
$pov_base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if (substr($pov_base, -5) === '/page') {
  $pov_base = dirname($pov_base);
}
if ($pov_base === '/' || $pov_base === '\\') {
  $pov_base = '';
}
function pov_url(string $path = ''): string {
  global $pov_base;
  $path = ltrim(str_replace('\\', '/', $path), '/');
  $query = '';
  if (strpos($path, '?') !== false) {
    [$path, $query] = explode('?', $path, 2);
    $query = '?' . $query;
  }
  $base = $pov_base ? $pov_base : '';
  // Encode spaces so CSS/JS/img URLs work under folders like "pov india"
  $base = str_replace(' ', '%20', $base);
  $parts = $path === '' ? [] : explode('/', $path);
  $parts = array_map('rawurlencode', $parts);
  $encoded = implode('/', $parts);
  return ($base ? $base : '') . '/' . $encoded . $query;
}

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
function pov_active(string $page, string $current): string {
  return $page === $current ? 'active' : '';
}

function pov_img_url(?string $img, string $fallback = 'assets/img/listings/realestate_01.webp'): string {
  $img = trim((string)$img);
  if ($img === '') {
    return pov_url($fallback);
  }
  if (preg_match('#^(https?:)?//#i', $img) || str_starts_with($img, 'data:image')) {
    return $img;
  }
  if (str_starts_with($img, 'assets/')) {
    return pov_url($img);
  }
  return pov_url('assets/img/listings/' . ltrim($img, '/'));
}

function pov_avatar_url(?string $avatar, string $fallback = 'assets/img/avatars/ryan.webp'): string {
  $avatar = trim((string)$avatar);
  if ($avatar === '') {
    return pov_url($fallback);
  }
  if (preg_match('#^(https?:)?//#i', $avatar) || str_starts_with($avatar, 'data:image')) {
    return $avatar;
  }
  if (str_starts_with($avatar, 'assets/')) {
    return pov_url($avatar);
  }
  return pov_url('assets/img/avatars/' . ltrim($avatar, '/'));
}

