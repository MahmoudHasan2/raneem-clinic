-- ============================================================
-- قاعدة بيانات عيادة رنيم (Raneem Clinic) - نظام الموقع والداشبورد
-- استوردي هذا الملف من phpMyAdmin في Hostinger (قسم Databases)
-- ============================================================

SET NAMES utf8mb4;

-- المستخدمين (دخول الداشبورد)
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- المستخدم الافتراضي: اسم المستخدم admin / كلمة السر admin123
-- !! غيّري كلمة السر فوراً من داخل الداشبورد (الإعدادات) بعد أول دخول !!
INSERT INTO admin_users (username, password_hash) VALUES
('admin', 'PASTE_PASSWORD_HASH_HERE');

-- إعدادات ومحتوى الموقع (نصوص، أرقام تواصل، رابط الفيديو...)
CREATE TABLE IF NOT EXISTS site_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value LONGTEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO site_settings (setting_key, setting_value) VALUES
('clinic_name', 'Raneem Clinic'),
('doctor_name', 'د. رنيم داوود'),
('hero_badge', 'أخصائية تجميل وجلدية معتمدة'),
('hero_title_1', 'جمالك يبدأ بـ'),
('hero_title_gold', 'بشرة صحية'),
('hero_subtitle', 'د. رنيم داوود'),
('hero_desc', 'نقدم لكِ أحدث تقنيات العناية بالبشرة والتجميل بأيدٍ خبيرة ومواد طبية عالمية. اكتشفي جمالك الحقيقي مع عيادة رنيم المتخصصة.'),
('stat_years', '10'),
('stat_clients', '5000'),
('stat_satisfaction', '98'),
('reel_url', 'https://www.instagram.com/reel/DZqWIYGRPDK/embed/'),
('about_title', 'عيادة رنيم<br>للتجميل والجلدية'),
('about_text_1', 'عيادة رنيم هي وجهتك الأولى للعناية بالبشرة والتجميل في الناصرة. بقيادة الدكتورة رنيم داوود، أخصائية التجميل والجلدية، نقدم لكِ تجربة فريدة تجمع بين الخبرة الطبية واللمسة الفنية.'),
('about_text_2', 'نؤمن بأن كل امرأة تستحق أن تشعر بالثقة والجمال. لذلك نستخدم أحدث التقنيات والمواد الطبية المعتمدة عالمياً لضمان نتائج مبهرة وآمنة.'),
('about_image', ''),
('video_title', 'شاهدي تجربة العناية في عيادتنا'),
('video_desc', 'استمتعي بجولة افتراضية داخل عيادتنا واكتشفي الأجواء الفاخرة والتقنيات الحديثة التي نستخدمها'),
('phone', '+963 986 009 927'),
('address', 'الناصرة / ساحة السيتي باص'),
('instagram_handle', '@dr.ranim_daoud'),
('instagram_url', 'https://www.instagram.com/dr.ranim_daoud'),
('whatsapp_url', 'https://wa.me/4915100000000'),
('email', 'info@raneemclinic.com'),
('working_hours', 'السبت - الخميس: 10:00 ص - 8:00 م'),
('footer_desc', 'عيادة رنيم للتجميل والجلدية، وجهتك الأولى للعناية بالبشرة في الناصرة. نقدم أحدث التقنيات بأيدٍ خبيرة ومواد طبية عالمية.'),
('footer_year_text', 'جميع الحقوق محفوظة © 2026 Raneem Clinic | د. رنيم داوود');

-- الخدمات (تظهر في قسم "خدماتنا")
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(100) NOT NULL DEFAULT 'fas fa-spa',
    title VARCHAR(150) NOT NULL,
    description TEXT,
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO services (icon, title, description, sort_order) VALUES
('fas fa-syringe', 'البوتوكس والفيلر', 'تقنيات حقن متقدمة للتخلص من التجاعيد واستعادة نضارة الوجه بشكل طبيعي وآمن', 1),
('fas fa-laser-pointer', 'الليزر التجميلي', 'إزالة الشعر بالليزر، علاج التصبغات، وإزالة الوشم بأحدث أجهزة الليزر العالمية', 2),
('fas fa-face-smile-beam', 'تقشير البشرة', 'تقشير كيميائي وطبي عميق لإزالة الخلايا الميتة وتجديد البشرة بشكل صحي', 3),
('fas fa-droplet', 'الفيلر والخيوط', 'تكبير الشفاه، شد الوجه بالخيوط التجميلية، ونحت الذقن بأعلى معايير الجودة', 4),
('fas fa-hand-sparkles', 'العناية بالبشرة', 'جلسات تنظيف عميق، ترطيب، علاج حب الشباب، وبرامج مخصصة لكل نوع بشرة', 5),
('fas fa-wand-magic-sparkles', 'الميزوثيرابي', 'حقن الفيتامينات والمعادن المباشر للبشرة والشعر لإعادة الحيوية والنضارة', 6);

-- معرض الأعمال
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255) NOT NULL,
    title VARCHAR(150),
    subtitle VARCHAR(150),
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO gallery (image_path, title, subtitle, sort_order) VALUES
('https://images.unsplash.com/photo-1560066984-138dadb4c035?w=800&q=80', 'صالون التجميل الفاخر', 'أجواء مريحة وهادئة', 1),
('https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=800&q=80', 'جلسة العناية بالبشرة', 'تقنيات متقدمة', 2),
('https://images.unsplash.com/photo-1616394584738-fc6e612e71b9?w=800&q=80', 'نتائج مذهلة', 'بشرة نضرة وشابة', 3),
('https://images.unsplash.com/photo-1519823551278-64ac92734fb1?w=800&q=80', 'أحدث الأجهزة', 'تكنولوجيا عالمية', 4),
('https://images.unsplash.com/photo-1604654894610-df63bc536371?w=800&q=80', 'منتجات عالمية', 'جودة مضمونة', 5);

-- آراء العملاء
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    role VARCHAR(150),
    testimonial_text TEXT,
    stars TINYINT DEFAULT 5,
    avatar_path VARCHAR(255),
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO testimonials (name, role, testimonial_text, stars, avatar_path, sort_order) VALUES
('سارة أحمد', 'عميلة دائمة', 'تجربة رائعة مع الدكتورة رنيم! النتائج فاقت توقعاتي تماماً. بشرتي أصبحت أكثر نضارة وشباباً. أنصح كل صديقة بزيارة العيادة.', 5, 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=200&q=80', 1),
('نور حسن', 'عميلة جديدة', 'أفضل عيادة تجميل في الناصرة بلا منازع. الدكتورة رنيم محترفة جداً والنتائج طبيعية ومذهلة. العيادة نظيفة وفاخرة والموظفين لطفاء للغاية.', 5, 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&q=80', 2),
('ليلى محمد', 'عميلة منذ عامين', 'جربت عدة عيادات قبل رنيم كلينك، لكن هنا وجدت الاحترافية الحقيقية. نتائج الفيلر رائعة وطبيعية. شكراً دكتورة رنيم على إطلالتي الجديدة!', 5, 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&q=80', 3);

-- ============================================================
-- نظام الإدارة والمحاسبة (الداشبورد)
-- ============================================================

-- الزبائن
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    phone VARCHAR(50),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- الموظفين
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    position VARCHAR(150),
    phone VARCHAR(50),
    monthly_salary DECIMAL(12,2) DEFAULT 0,
    hire_date DATE,
    status ENUM('active','inactive') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- سجل صرف الرواتب
CREATE TABLE IF NOT EXISTS salary_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    pay_month VARCHAR(7) NOT NULL, -- format YYYY-MM
    paid_date DATE NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- مصاريف المواد ومصاريف المركز
CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(200) NOT NULL,
    category VARCHAR(100) NOT NULL DEFAULT 'مواد',
    quantity DECIMAL(10,2) DEFAULT 1,
    unit_price DECIMAL(12,2) DEFAULT 0,
    total_amount DECIMAL(12,2) NOT NULL,
    expense_date DATE NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- عمليات الزبائن (تصنيف الخدمة المقدمة والسعر)
CREATE TABLE IF NOT EXISTS operations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    operation_type VARCHAR(100) NOT NULL, -- تجميل / مناكير / بديكير / حمام / تصفيف شعر / قص شعر / أظافر / رموش / جلسات ليزر
    description VARCHAR(255),
    price DECIMAL(12,2) NOT NULL DEFAULT 0,
    cost DECIMAL(12,2) NOT NULL DEFAULT 0, -- تكلفة المواد المستخدمة بالعملية (اختياري)
    employee_id INT NULL,
    operation_date DATE NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- فهارس لتسريع التقارير
CREATE INDEX idx_operations_date ON operations(operation_date);
CREATE INDEX idx_expenses_date ON expenses(expense_date);
CREATE INDEX idx_salary_month ON salary_payments(pay_month);
