AOS.init({
    duration: 800,
    easing: 'ease-out-cubic',
    once: false,
    mirror: true,
    offset: 100
});

window.addEventListener('load', () => {
    setTimeout(() => {
        const l = document.getElementById('loader');
        if (l) l.classList.add('hidden');
    }, 2000);
});

const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
    if (window.scrollY > 50) navbar.classList.add('scrolled');
    else navbar.classList.remove('scrolled');
});

const mobileToggle = document.getElementById('mobileToggle');
const navLinks = document.getElementById('navLinks');
mobileToggle.addEventListener('click', () => {
    mobileToggle.classList.toggle('active');
    navLinks.classList.toggle('active');
});
document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', () => {
        mobileToggle.classList.remove('active');
        navLinks.classList.remove('active');
    });
});

const backToTop = document.getElementById('backToTop');
window.addEventListener('scroll', () => {
    if (window.scrollY > 500) backToTop.classList.add('visible');
    else backToTop.classList.remove('visible');
});
backToTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

const cursorGlow = document.getElementById('cursorGlow');
document.addEventListener('mousemove', (e) => {
    cursorGlow.style.left = e.clientX + 'px';
    cursorGlow.style.top = e.clientY + 'px';
});

const canvas = document.getElementById('particles-canvas');
const ctx = canvas.getContext('2d');
let particles = [];
function resizeCanvas() { canvas.width = window.innerWidth; canvas.height = window.innerHeight; }
resizeCanvas();
window.addEventListener('resize', resizeCanvas);

class Particle {
    constructor() {
        this.x = Math.random() * canvas.width;
        this.y = Math.random() * canvas.height;
        this.size = Math.random() * 2 + 0.5;
        this.speedX = Math.random() * 0.5 - 0.25;
        this.speedY = Math.random() * 0.5 - 0.25;
        this.opacity = Math.random() * 0.5 + 0.1;
    }
    update() {
        this.x += this.speedX; this.y += this.speedY;
        if (this.x > canvas.width) this.x = 0;
        if (this.x < 0) this.x = canvas.width;
        if (this.y > canvas.height) this.y = 0;
        if (this.y < 0) this.y = canvas.height;
    }
    draw() {
        ctx.fillStyle = `rgba(197, 160, 89, ${this.opacity})`;
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fill();
    }
}
function initParticles() {
    particles = [];
    const particleCount = window.innerWidth < 768 ? 25 : 50;
    for (let i = 0; i < particleCount; i++) particles.push(new Particle());
}
initParticles();
function animateParticles() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    particles.forEach(p => { p.update(); p.draw(); });
    particles.forEach((a, i) => {
        particles.slice(i + 1).forEach(b => {
            const dx = a.x - b.x, dy = a.y - b.y;
            const distance = Math.sqrt(dx * dx + dy * dy);
            if (distance < 150) {
                ctx.strokeStyle = `rgba(197, 160, 89, ${0.1 * (1 - distance / 150)})`;
                ctx.lineWidth = 0.5;
                ctx.beginPath();
                ctx.moveTo(a.x, a.y);
                ctx.lineTo(b.x, b.y);
                ctx.stroke();
            }
        });
    });
    requestAnimationFrame(animateParticles);
}
animateParticles();

const counters = document.querySelectorAll('.hero-stat-number');
const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const counter = entry.target;
            const target = parseInt(counter.getAttribute('data-count'));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;
            const updateCounter = () => {
                current += step;
                if (current < target) {
                    counter.textContent = Math.floor(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target + (target === 98 ? '%' : '+');
                }
            };
            updateCounter();
            counterObserver.unobserve(counter);
        }
    });
}, { threshold: 0.5 });
counters.forEach(counter => counterObserver.observe(counter));

let currentSlide = 0;
const slides = document.querySelectorAll('.testimonial-slide');
const dots = document.querySelectorAll('.testimonial-dot');
function showSlide(index) {
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    slides[index].classList.add('active');
    dots[index].classList.add('active');
}
function nextSlide() { currentSlide = (currentSlide + 1) % slides.length; showSlide(currentSlide); }
function prevSlide() { currentSlide = (currentSlide - 1 + slides.length) % slides.length; showSlide(currentSlide); }
function goToSlide(index) { currentSlide = index; showSlide(currentSlide); }
if (slides.length) setInterval(nextSlide, 5000);

const videoModal = document.getElementById('videoModal');
const videoPlayBtn = document.getElementById('videoPlayBtn');
const videoModalClose = document.getElementById('videoModalClose');
videoPlayBtn.addEventListener('click', () => { videoModal.classList.add('active'); document.body.style.overflow = 'hidden'; });
videoModalClose.addEventListener('click', () => { videoModal.classList.remove('active'); document.body.style.overflow = ''; });
videoModal.addEventListener('click', (e) => { if (e.target === videoModal) { videoModal.classList.remove('active'); document.body.style.overflow = ''; } });

document.getElementById('bookingForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const btn = e.target.querySelector('.btn-submit');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check"></i> تم إرسال طلبك بنجاح!';
    btn.style.background = '#25D366';
    setTimeout(() => { btn.innerHTML = originalText; btn.style.background = ''; e.target.reset(); }, 3000);
});

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});

document.querySelectorAll('.gallery-item').forEach(item => {
    item.addEventListener('click', () => {
        const img = item.querySelector('img');
        const overlay = item.querySelector('.gallery-overlay');
        const title = overlay.querySelector('h4').textContent;
        const lightbox = document.createElement('div');
        lightbox.style.cssText = `
            position: fixed; inset: 0; background: rgba(0,0,0,0.92); z-index: 99999;
            display: flex; align-items: center; justify-content: center; padding: 20px;
            animation: fadeIn 0.3s ease;
        `;
        lightbox.innerHTML = `
            <img src="${img.src}" style="max-width: 90%; max-height: 90%; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.5);">
            <button style="position: absolute; top: 20px; left: 20px; background: none; border: none; color: white; font-size: 2rem; cursor: pointer; transition: all 0.3s;"><i class="fas fa-times"></i></button>
            <div style="position: absolute; bottom: 40px; color: white; font-size: 1.3rem; font-weight: 700; text-shadow: 0 2px 10px rgba(0,0,0,0.5);">${title}</div>
        `;
        document.body.appendChild(lightbox);
        document.body.style.overflow = 'hidden';
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox || e.target.closest('button')) {
                lightbox.remove();
                document.body.style.overflow = '';
            }
        });
    });
});

document.querySelectorAll('.service-card').forEach(card => {
    card.addEventListener('mousemove', (e) => {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left, y = e.clientY - rect.top;
        const centerX = rect.width / 2, centerY = rect.height / 2;
        const rotateX = (y - centerY) / 25, rotateY = (centerX - x) / 25;
        card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-12px)`;
    });
    card.addEventListener('mouseleave', () => { card.style.transform = ''; });
});

const heroSubtitle = document.querySelector('.hero-subtitle');
if (heroSubtitle) {
    const text = heroSubtitle.textContent;
    heroSubtitle.textContent = '';
    let i = 0;
    const typeWriter = () => {
        if (i < text.length) {
            heroSubtitle.textContent += text.charAt(i);
            i++;
            setTimeout(typeWriter, 100);
        }
    };
    setTimeout(typeWriter, 1500);
}
