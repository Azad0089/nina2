<?php
declare(strict_types=1);
/**
 * Local file uploads (default storage driver). Limits come from Admin > Settings.
 * Cloud storage (S3/FTP/Cloudflare) and FFmpeg transcoding are wired for later:
 * see the TODO markers below. Today every upload is saved as-is to /uploads.
 */

const UPLOAD_DIR_AVATARS = __DIR__ . '/../uploads/avatars';
const UPLOAD_DIR_COVERS  = __DIR__ . '/../uploads/covers';
const UPLOAD_DIR_MEDIA   = __DIR__ . '/../uploads/media';
const UPLOAD_DIR_BRAND   = __DIR__ . '/../uploads/branding';

function ensure_upload_dirs(): void {
    foreach ([UPLOAD_DIR_AVATARS, UPLOAD_DIR_COVERS, UPLOAD_DIR_MEDIA, UPLOAD_DIR_BRAND] as $d) {
        if (!is_dir($d)) { @mkdir($d, 0755, true); }
    }
}

/**
 * Save an uploaded file (from $_FILES[$field]) into $dir.
 * $kind: 'image' | 'video' | 'any' — controls which mime types are accepted.
 * Returns ['ok'=>true,'path'=>'uploads/media/xxx.jpg','kind'=>'image'] or ['ok'=>false,'message'=>...]
 */
function handle_upload(string $field, string $dir, string $kind = 'image'): array {
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return ['ok' => false, 'message' => 'No file was uploaded.'];
    }
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'message' => 'Upload failed (error code ' . $f['error'] . ').'];
    }

    $imageTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $videoTypes = ['video/mp4' => 'mp4', 'video/webm' => 'webm', 'video/quicktime' => 'mov'];

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = (string)$finfo->file($f['tmp_name']);

    $maxMb = $kind === 'video' ? setting_int('max_video_mb', 200) : setting_int('max_photo_mb', 15);
    if ($f['size'] > $maxMb * 1024 * 1024) {
        return ['ok' => false, 'message' => "File is larger than the {$maxMb}MB limit set by the site admin."];
    }

    $allowed = $kind === 'video' ? $videoTypes : ($kind === 'image' ? $imageTypes : $imageTypes + $videoTypes);
    if (!isset($allowed[$mime])) {
        return ['ok' => false, 'message' => 'Unsupported file type: ' . e($mime)];
    }
    $ext = $allowed[$mime];
    $detectedKind = isset($imageTypes[$mime]) ? 'image' : 'video';

    ensure_upload_dirs();
    $name = bin2hex(random_bytes(16)) . '.' . $ext;
    $dest = rtrim($dir, '/') . '/' . $name;
    if (!move_uploaded_file($f['tmp_name'], $dest)) {
        return ['ok' => false, 'message' => 'Could not save the uploaded file.'];
    }

    // TODO (cloud storage): if setting('storage_driver') !== 'local', push $dest to S3 / FTP /
    // Cloudflare R2 here and store the returned remote URL instead of the local path.
    // TODO (ffmpeg): if $detectedKind === 'video' && setting_bool('ffmpeg_enabled'), queue a
    // transcode job (see admin > settings > media) instead of / in addition to serving the
    // upload as-is. The upload always works without ffmpeg; transcoding is optional.

    $relative = 'uploads/' . basename($dir) . '/' . $name;
    return ['ok' => true, 'path' => $relative, 'kind' => $detectedKind];
}

/** Public URL for a stored upload path (works for local paths; cloud paths would already be absolute URLs). */
function media_url(?string $path): string {
    if (!$path) return '';
    if (preg_match('#^https?://#', $path)) return $path; // already a full (cloud) URL
    return url($path);
}
