<?php
require_once __DIR__ . '/includes/db.php';
$pdo = db();
$services = $pdo->query('SELECT * FROM services ORDER BY sort_order ASC, id ASC')->fetchAll();
$gallery = $pdo->query('SELECT * FROM gallery ORDER BY sort_order ASC, id ASC')->fetchAll();
$testimonials = $pdo->query('SELECT * FROM testimonials ORDER BY sort_order ASC, id ASC')->fetchAll();
$s = get_settings();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($s['clinic_name'] ?? 'Raneem Clinic') ?> | <?= e($s['doctor_name'] ?? '') ?> - أخصائية تجميل وجلدية</title>
    <meta name="description" content="عيادة رنيم للتجميل والجلدية - <?= e($s['doctor_name'] ?? '') ?>. أحدث تقنيات العناية بالبشرة والتجميل في الناصرة، سوريا.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,500&family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        /* خلفية الهيرو (صورة أو فيديو) — تحل محل تضمين ريلز انستغرام الذي يمنعه انستغرام غالباً */
        .hero-bg-container { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; overflow: hidden; background: #000; }
        .hero-bg-media { position: absolute; top: 50%; left: 50%; width: 100%; height: 100%; min-width: 100%; min-height: 100%; transform: translate(-50%, -50%); object-fit: cover; }
    </style>
<base target="_blank">
</head>
<body>

    <!-- Loader -->
    <div class="loader-overlay" id="loader">
        <div class="loader-logo">RC</div>
        <div class="loader-sub"><?= e($s['clinic_name'] ?? '') ?></div>
    </div>

    <div class="cursor-glow" id="cursorGlow"></div>
    

    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="#" class="nav-logo">
                <div class="nav-logo-icon">RC</div>
                <div class="nav-logo-text">
                    <span class="nav-logo-title">RANEEM CLINIC</span>
                    <span class="nav-logo-sub"><?= e($s['doctor_name'] ?? '') ?></span>
                </div>
            </a>
            <ul class="nav-links" id="navLinks">
                <li><a href="#home">الرئيسية</a></li>
                <li><a href="#services">خدماتنا</a></li>
                <li><a href="#about">من نحن</a></li>
                <li><a href="#gallery">معرض الأعمال</a></li>
                <li><a href="#testimonials">آراء العملاء</a></li>
                <li><a href="#booking" class="nav-cta">احجز موعدك</a></li>
            </ul>
            <div class="mobile-toggle" id="mobileToggle">
                <span></span><span></span><span></span>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero" id="home">
        <div class="hero-bg-container">
            <?php if (($s['hero_bg_type'] ?? 'image') === 'video' && !empty($s['hero_bg_video'])): ?>
                <video class="hero-bg-media" autoplay muted loop playsinline
                    <?php if (!empty($s['hero_bg_image'])): ?>poster="<?= e($s['hero_bg_image']) ?>"<?php endif; ?>>
                    <source src="<?= e($s['hero_bg_video']) ?>" type="video/mp4">
                </video>
            <?php else: ?>
                <img class="hero-bg-media"
                     src="<?= e($s['hero_bg_image'] ?: 'https://images.unsplash.com/photo-1616394584738-fc6e612e71b9?w=1920&q=80') ?>"
                     alt="<?= e($s['clinic_name'] ?? '') ?>">
            <?php endif; ?>
        </div>

        <div class="hero-overlay"></div>
        <div class="hero-gold-line"></div>
        <div class="hero-gold-line"></div>

        <div class="hero-content">
            <div class="hero-badge" data-aos="fade-up">
                <i class="fas fa-certificate"></i>
                <span><?= e($s['hero_badge'] ?? '') ?></span>
            </div>

            <h1 class="hero-title" data-aos="fade-up" data-aos-delay="100">
                <?= e($s['hero_title_1'] ?? '') ?><br>
                <span class="gold-text"><?= e($s['hero_title_gold'] ?? '') ?></span>
            </h1>

            <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="200"><?= e($s['hero_subtitle'] ?? '') ?></p>

            <p class="hero-desc" data-aos="fade-up" data-aos-delay="300"><?= nl2br(e($s['hero_desc'] ?? '')) ?></p>

            <div class="hero-buttons" data-aos="fade-up" data-aos-delay="400">
                <a href="#booking" class="btn-primary"><i class="fas fa-calendar-check"></i> احجزي موعدك الآن</a>
                <a href="#services" class="btn-secondary"><i class="fas fa-spa"></i> استكشفي خدماتنا</a>
            </div>

            <div class="hero-stats" data-aos="fade-up" data-aos-delay="500">
                <div class="hero-stat">
                    <span class="hero-stat-number" data-count="<?= (int)($s['stat_years'] ?? 0) ?>">0</span>
                    <span class="hero-stat-label">سنوات خبرة</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number" data-count="<?= (int)($s['stat_clients'] ?? 0) ?>">0</span>
                    <span class="hero-stat-label">عميلة راضية</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number" data-count="<?= (int)($s['stat_satisfaction'] ?? 0) ?>">0</span>
                    <span class="hero-stat-label">% نسبة الرضا</span>
                </div>
            </div>
        </div>

        <div class="scroll-indicator" onclick="document.getElementById('services').scrollIntoView({behavior:'smooth'})">
            <span>Scroll</span>
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <!-- Services -->
    <section class="services" id="services">
        <div class="section-header" data-aos="fade-up">
            <span class="section-label">خدماتنا</span>
            <h2 class="section-title">حلول تجميلية شاملة لبشرتك</h2>
            <p class="section-desc">نقدم مجموعة واسعة من الخدمات التجميلية والعلاجية باستخدام أحدث التقنيات العالمية</p>
        </div>
        <div class="services-grid">
            <?php foreach ($services as $i => $svc): ?>
            <div class="service-card" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
                <div class="service-icon"><i class="<?= e($svc['icon']) ?>"></i></div>
                <h3 class="service-title"><?= e($svc['title']) ?></h3>
                <p class="service-desc"><?= e($svc['description']) ?></p>
                <a href="#booking" class="service-link">احجزي الآن <i class="fas fa-arrow-left"></i></a>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- About -->
    <section class="about" id="about">
        <div class="about-container">
            <div class="about-images" data-aos="fade-right">
                <div class="about-img-accent"></div>
                <img src="<?= e($s['about_image'] ?: 'https://images.unsplash.com/photo-1594824476967-48c8b964273f?w=800&q=80') ?>" alt="<?= e($s['doctor_name'] ?? '') ?>" class="about-img-main">
                <div class="about-img-accent-2"></div>
            </div>

            <div class="about-content" data-aos="fade-left">
                <span class="section-label">من نحن</span>
                <h2 class="section-title" style="text-align: right;"><?= $s['about_title'] ?? '' /* trusted admin html */ ?></h2>
                <p style="color: var(--text-light); line-height: 2; margin: 20px 0;"><?= nl2br(e($s['about_text_1'] ?? '')) ?></p>
                <p style="color: var(--text-light); line-height: 2; margin-bottom: 20px;"><?= nl2br(e($s['about_text_2'] ?? '')) ?></p>

                <div class="about-features">
                    <div class="about-feature">
                        <i class="fas fa-certificate"></i>
                        <div><h4>شهادات معتمدة</h4><p>خبرة طبية موثقة</p></div>
                    </div>
                    <div class="about-feature">
                        <i class="fas fa-shield-halved"></i>
                        <div><h4>معايير أمان عالية</h4><p>تعقيم وتعقيم كامل</p></div>
                    </div>
                    <div class="about-feature">
                        <i class="fas fa-gem"></i>
                        <div><h4>مواد عالمية</h4><p>منتجات أصلية 100%</p></div>
                    </div>
                    <div class="about-feature">
                        <i class="fas fa-heart"></i>
                        <div><h4>متابعة مستمرة</h4><p>رعاية بعد الجلسة</p></div>
                    </div>
                </div>

                <div class="about-signature">
                    <div class="signature-name">Dr. Raneem Daoud</div>
                    <div class="signature-title">أخصائية تجميل وجلدية</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Video -->
    <section class="video-section">
        <div class="video-bg"></div>
        <div class="video-container">
            <button class="video-play-btn" id="videoPlayBtn" data-aos="zoom-in"><i class="fas fa-play"></i></button>
            <h2 class="video-title" data-aos="fade-up"><?= e($s['video_title'] ?? '') ?></h2>
            <p class="video-desc" data-aos="fade-up" data-aos-delay="100"><?= e($s['video_desc'] ?? '') ?></p>
        </div>
    </section>

    <!-- Gallery -->
    <section class="gallery" id="gallery">
        <div class="section-header" data-aos="fade-up">
            <span class="section-label">معرض الأعمال</span>
            <h2 class="section-title">لحظات من عيادتنا</h2>
            <p class="section-desc">صور من جلساتنا ونتائج عملائنا المذهلة</p>
        </div>
        <div class="gallery-grid">
            <?php foreach ($gallery as $i => $g): ?>
            <div class="gallery-item" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
                <img src="<?= e($g['image_path']) ?>" alt="<?= e($g['title']) ?>">
                <div class="gallery-overlay">
                    <i class="fas fa-expand"></i>
                    <h4><?= e($g['title']) ?></h4>
                    <span><?= e($g['subtitle']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials" id="testimonials">
        <div class="section-header" data-aos="fade-up">
            <span class="section-label">آراء العملاء</span>
            <h2 class="section-title">ماذا تقول عملائنا عنا</h2>
        </div>
        <div class="testimonials-slider">
            <?php foreach ($testimonials as $i => $t): ?>
            <div class="testimonial-slide <?= $i === 0 ? 'active' : '' ?>">
                <img src="<?= e($t['avatar_path']) ?>" alt="عميلة" class="testimonial-avatar">
                <div class="testimonial-stars">
                    <?php for ($k = 0; $k < (int)$t['stars']; $k++): ?><i class="fas fa-star"></i><?php endfor; ?>
                </div>
                <p class="testimonial-text">"<?= e($t['testimonial_text']) ?>"</p>
                <div class="testimonial-name"><?= e($t['name']) ?></div>
                <div class="testimonial-role"><?= e($t['role']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="testimonial-nav">
            <button onclick="prevSlide()"><i class="fas fa-arrow-right"></i></button>
            <button onclick="nextSlide()"><i class="fas fa-arrow-left"></i></button>
        </div>
        <div class="testimonial-dots">
            <?php foreach ($testimonials as $i => $t): ?>
            <button class="testimonial-dot <?= $i === 0 ? 'active' : '' ?>" onclick="goToSlide(<?= $i ?>)"></button>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Booking -->
    <section class="booking" id="booking">
        <div class="booking-container">
            <div class="booking-info" data-aos="fade-right">
                <span class="section-label" style="text-align: right; display: block;">احجزي موعدك</span>
                <h3>ابدئي رحلة جمالك معنا</h3>
                <p>املئي النموذج وسنتواصل معكِ في أقرب وقت لتأكيد موعدك. نحن هنا للإجابة على جميع استفساراتك.</p>

                <div class="booking-contact-item">
                    <i class="fas fa-phone"></i>
                    <div><h4>اتصلي بنا</h4><span><?= e($s['phone'] ?? '') ?></span></div>
                </div>
                <div class="booking-contact-item">
                    <i class="fas fa-location-dot"></i>
                    <div><h4>العنوان</h4><span><?= e($s['address'] ?? '') ?></span></div>
                </div>
                <div class="booking-contact-item">
                    <i class="fab fa-instagram"></i>
                    <div><h4>تابعينا</h4><span><?= e($s['instagram_handle'] ?? '') ?></span></div>
                </div>
                <div class="booking-contact-item">
                    <i class="fas fa-clock"></i>
                    <div><h4>ساعات العمل</h4><span><?= e($s['working_hours'] ?? '') ?></span></div>
                </div>
            </div>

            <div class="booking-form" data-aos="fade-left">
                <form id="bookingForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label>الاسم الكامل</label>
                            <input type="text" placeholder="أدخلي اسمك" required>
                        </div>
                        <div class="form-group">
                            <label>رقم الهاتف</label>
                            <input type="tel" placeholder="+963 ..." required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>نوع الخدمة</label>
                            <select required>
                                <option value="">اختيار الخدمة</option>
                                <?php foreach ($services as $svc): ?>
                                <option value="<?= e($svc['title']) ?>"><?= e($svc['title']) ?></option>
                                <?php endforeach; ?>
                                <option value="other">خدمة أخرى</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>التاريخ المفضل</label>
                            <input type="date" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>ملاحظات إضافية</label>
                        <textarea placeholder="أخبرينا بمزيد من التفاصيل..."></textarea>
                    </div>
                    <button type="submit" class="btn-submit"><i class="fas fa-paper-plane"></i> تأكيد الحجز</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-brand">
                <div class="footer-logo">RANEEM CLINIC</div>
                <p class="footer-desc"><?= nl2br(e($s['footer_desc'] ?? '')) ?></p>
                <div class="footer-social">
                    <a href="<?= e($s['instagram_url'] ?? '#') ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="<?= e($s['whatsapp_url'] ?? '#') ?>" target="_blank"><i class="fab fa-whatsapp"></i></a>
                    <a href="tel:<?= e($s['phone'] ?? '') ?>"><i class="fas fa-phone"></i></a>
                </div>
            </div>
            <div>
                <h4 class="footer-title">روابط سريعة</h4>
                <ul class="footer-links">
                    <li><a href="#home">الرئيسية</a></li>
                    <li><a href="#services">خدماتنا</a></li>
                    <li><a href="#about">من نحن</a></li>
                    <li><a href="#gallery">معرض الأعمال</a></li>
                    <li><a href="#booking">احجز موعد</a></li>
                </ul>
            </div>
            <div>
                <h4 class="footer-title">خدماتنا</h4>
                <ul class="footer-links">
                    <?php foreach (array_slice($services, 0, 5) as $svc): ?>
                    <li><a href="#services"><?= e($svc['title']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div>
                <h4 class="footer-title">تواصل معنا</h4>
                <ul class="footer-contact">
                    <li><i class="fas fa-phone"></i> <?= e($s['phone'] ?? '') ?></li>
                    <li><i class="fas fa-location-dot"></i> <?= e($s['address'] ?? '') ?></li>
                    <li><i class="fas fa-envelope"></i> <?= e($s['email'] ?? '') ?></li>
                    <li><i class="fas fa-clock"></i> <?= e($s['working_hours'] ?? '') ?></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p><?= e($s['footer_year_text'] ?? '') ?></p>
        </div>
    </footer>

    <!-- Floating Buttons -->
    <div class="floating-buttons">
        <a href="<?= e($s['whatsapp_url'] ?? '#') ?>" target="_blank" class="floating-btn whatsapp">
            <i class="fab fa-whatsapp"></i><span class="tooltip">تواصلي عبر واتساب</span>
        </a>
        <a href="tel:<?= e($s['phone'] ?? '') ?>" class="floating-btn phone">
            <i class="fas fa-phone"></i><span class="tooltip">اتصلي الآن</span>
        </a>
    </div>

    <button class="back-to-top" id="backToTop"><i class="fas fa-arrow-up"></i></button>

    <div class="video-modal" id="videoModal">
        <div class="video-modal-content">
            <button class="video-modal-close" id="videoModalClose"><i class="fas fa-times"></i></button>
            <div class="video-placeholder">
                <i class="fas fa-play-circle"></i>
                <p>سيتم إضافة الفيديو قريباً</p>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/site.js"></script>
</body>
</html>
