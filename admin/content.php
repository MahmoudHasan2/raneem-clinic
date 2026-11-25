<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/uploads.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $fields = [
        'clinic_name','doctor_name','hero_badge','hero_title_1','hero_title_gold','hero_subtitle','hero_desc',
        'stat_years','stat_clients','stat_satisfaction','reel_url','hero_bg_type',
        'about_title','about_text_1','about_text_2',
        'video_title','video_desc',
        'phone','address','instagram_handle','instagram_url','whatsapp_url','email','working_hours',
        'footer_desc','footer_year_text',
    ];
    foreach ($fields as $f) {
        if (isset($_POST[$f])) {
            update_setting($f, trim($_POST[$f]));
        }
    }
    try {
        $img = handle_image_upload('about_image_file', 'site');
        if ($img) update_setting('about_image', $img);
        $heroImg = handle_image_upload('hero_bg_image_file', 'site');
        if ($heroImg) update_setting('hero_bg_image', $heroImg);
        $heroVid = handle_video_upload('hero_bg_video_file', 'site');
        if ($heroVid) update_setting('hero_bg_video', $heroVid);
    } catch (RuntimeException $ex) {
        $msg = $ex->getMessage();
    }
    if (!$msg) $msg = 'تم حفظ التعديلات بنجاح';
    // إعادة تحميل الإعدادات المخزنة مؤقتاً
    $s = [];
    $stmt = db()->query('SELECT setting_key, setting_value FROM site_settings');
    foreach ($stmt->fetchAll() as $row) $s[$row['setting_key']] = $row['setting_value'];
} else {
    $s = get_settings();
}
?>
<h3 class="mb-4">النصوص ومعلومات التواصل</h3>
<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data">
<input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

<div class="card">
    <div class="card-header">القسم الرئيسي (Hero) وفيديو انستغرام</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">اسم العيادة</label>
                <input type="text" name="clinic_name" class="form-control" value="<?= e($s['clinic_name'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">اسم الطبيبة</label>
                <input type="text" name="doctor_name" class="form-control" value="<?= e($s['doctor_name'] ?? '') ?>">
            </div>
            <div class="col-md-12">
                <label class="form-label">شارة أعلى العنوان</label>
                <input type="text" name="hero_badge" class="form-control" value="<?= e($s['hero_badge'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">العنوان الرئيسي (السطر الأول)</label>
                <input type="text" name="hero_title_1" class="form-control" value="<?= e($s['hero_title_1'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">العنوان الرئيسي (الكلمة الذهبية)</label>
                <input type="text" name="hero_title_gold" class="form-control" value="<?= e($s['hero_title_gold'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">العنوان الفرعي</label>
                <input type="text" name="hero_subtitle" class="form-control" value="<?= e($s['hero_subtitle'] ?? '') ?>">
            </div>
            <div class="col-md-12">
                <label class="form-label">الوصف تحت العنوان</label>
                <textarea name="hero_desc" class="form-control" rows="3"><?= e($s['hero_desc'] ?? '') ?></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">سنوات الخبرة</label>
                <input type="number" name="stat_years" class="form-control" value="<?= e($s['stat_years'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">عدد العميلات</label>
                <input type="number" name="stat_clients" class="form-control" value="<?= e($s['stat_clients'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">نسبة الرضا %</label>
                <input type="number" name="stat_satisfaction" class="form-control" value="<?= e($s['stat_satisfaction'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">نوع خلفية القسم الرئيسي</label>
                <select name="hero_bg_type" class="form-select">
                    <option value="image" <?= ($s['hero_bg_type'] ?? 'image') === 'image' ? 'selected' : '' ?>>صورة</option>
                    <option value="video" <?= ($s['hero_bg_type'] ?? '') === 'video' ? 'selected' : '' ?>>فيديو (mp4)</option>
                </select>
                <div class="form-text">ملاحظة: رابط ريلز انستغرام لا يعمل كخلفية لأن انستغرام يمنع تضمينه — استخدمي صورة أو ارفعي مقطع فيديو خاص بكِ بصيغة mp4.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label">صورة الخلفية (تُستخدم كصورة أو كغلاف للفيديو)</label>
                <input type="file" name="hero_bg_image_file" class="form-control" accept="image/*">
                <?php if (!empty($s['hero_bg_image'])): ?>
                    <img src="../<?= e($s['hero_bg_image']) ?>" class="mt-2 rounded" style="max-height:100px">
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <label class="form-label">فيديو الخلفية (mp4 فقط، يُفضّل أقل من 15 ميغا)</label>
                <input type="file" name="hero_bg_video_file" class="form-control" accept="video/mp4,video/webm">
                <?php if (!empty($s['hero_bg_video'])): ?>
                    <div class="form-text">يوجد فيديو مرفوع حالياً.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">قسم "من نحن"</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">عنوان القسم (يدعم &lt;br&gt; لسطر جديد)</label>
                <input type="text" name="about_title" class="form-control" value="<?= e($s['about_title'] ?? '') ?>">
            </div>
            <div class="col-md-12">
                <label class="form-label">الفقرة الأولى</label>
                <textarea name="about_text_1" class="form-control" rows="3"><?= e($s['about_text_1'] ?? '') ?></textarea>
            </div>
            <div class="col-md-12">
                <label class="form-label">الفقرة الثانية</label>
                <textarea name="about_text_2" class="form-control" rows="3"><?= e($s['about_text_2'] ?? '') ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">صورة قسم "من نحن"</label>
                <input type="file" name="about_image_file" class="form-control" accept="image/*">
                <?php if (!empty($s['about_image'])): ?>
                    <img src="../<?= e($s['about_image']) ?>" class="mt-2 rounded" style="max-height:120px">
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">قسم الفيديو</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">عنوان قسم الفيديو</label>
                <input type="text" name="video_title" class="form-control" value="<?= e($s['video_title'] ?? '') ?>">
            </div>
            <div class="col-md-12">
                <label class="form-label">وصف قسم الفيديو</label>
                <textarea name="video_desc" class="form-control" rows="2"><?= e($s['video_desc'] ?? '') ?></textarea>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">معلومات التواصل والفوتر</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">رقم الهاتف / واتساب</label>
                <input type="text" name="phone" class="form-control" value="<?= e($s['phone'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">العنوان</label>
                <input type="text" name="address" class="form-control" value="<?= e($s['address'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">معرّف انستغرام (نص فقط)</label>
                <input type="text" name="instagram_handle" class="form-control" value="<?= e($s['instagram_handle'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">رابط انستغرام</label>
                <input type="text" name="instagram_url" class="form-control" value="<?= e($s['instagram_url'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">رابط واتساب (wa.me/...)</label>
                <input type="text" name="whatsapp_url" class="form-control" value="<?= e($s['whatsapp_url'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control" value="<?= e($s['email'] ?? '') ?>">
            </div>
            <div class="col-md-12">
                <label class="form-label">ساعات العمل</label>
                <input type="text" name="working_hours" class="form-control" value="<?= e($s['working_hours'] ?? '') ?>">
            </div>
            <div class="col-md-12">
                <label class="form-label">وصف الفوتر</label>
                <textarea name="footer_desc" class="form-control" rows="2"><?= e($s['footer_desc'] ?? '') ?></textarea>
            </div>
            <div class="col-md-12">
                <label class="form-label">نص أسفل الفوتر (حقوق النشر)</label>
                <input type="text" name="footer_year_text" class="form-control" value="<?= e($s['footer_year_text'] ?? '') ?>">
            </div>
        </div>
    </div>
</div>

<button type="submit" class="btn btn-gold px-5">حفظ التعديلات</button>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
