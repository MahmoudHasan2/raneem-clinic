<?php
/**
 * يرفع صورة من $_FILES[$field] إلى مجلد فرعي داخل /uploads ويرجّع المسار النسبي
 * يرجع null إذا لم يتم اختيار ملف
 */
function handle_image_upload(string $field, string $subdir): ?string {
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('حدث خطأ أثناء رفع الصورة');
    }
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $mime = mime_content_type($_FILES[$field]['tmp_name']);
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('صيغة الصورة غير مدعومة (يرجى استخدام jpg, png, webp)');
    }
    $ext = $allowed[$mime];
    $filename = bin2hex(random_bytes(8)) . '.' . $ext;
    $targetDir = UPLOAD_DIR . '/' . $subdir;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $target = $targetDir . '/' . $filename;
    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $target)) {
        throw new RuntimeException('تعذر حفظ الصورة على الخادم');
    }
    return UPLOAD_URL . '/' . $subdir . '/' . $filename;
}

/**
 * يرفع فيديو (mp4/webm) من $_FILES[$field] إلى مجلد فرعي داخل /uploads ويرجّع المسار النسبي
 * يرجع null إذا لم يتم اختيار ملف
 */
function handle_video_upload(string $field, string $subdir): ?string {
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('حدث خطأ أثناء رفع الفيديو');
    }
    $allowed = ['video/mp4' => 'mp4', 'video/webm' => 'webm'];
    $mime = mime_content_type($_FILES[$field]['tmp_name']);
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('صيغة الفيديو غير مدعومة (استخدمي mp4 أو webm فقط)');
    }
    $ext = $allowed[$mime];
    $filename = bin2hex(random_bytes(8)) . '.' . $ext;
    $targetDir = UPLOAD_DIR . '/' . $subdir;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $target = $targetDir . '/' . $filename;
    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $target)) {
        throw new RuntimeException('تعذر حفظ الفيديو على الخادم (تأكدي أن حجمه ضمن الحد المسموح في استضافتك)');
    }
    return UPLOAD_URL . '/' . $subdir . '/' . $filename;
}
