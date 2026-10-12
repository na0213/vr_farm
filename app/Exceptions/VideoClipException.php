<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * 動画を変換できなかったとき。メッセージは管理画面にそのまま出す。
 */
class VideoClipException extends RuntimeException {}
