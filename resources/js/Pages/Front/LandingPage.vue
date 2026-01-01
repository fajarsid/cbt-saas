<template>
  <div class="landing-page">
    <!-- Navbar -->
    <nav :class="['navbar', { 'navbar-scrolled': isScrolled }]">
      <div class="container navbar-content">
        <a href="#" class="logo">
          <span class="logo-icon"></span>
          <span class="logo-text">CBT<span class="logo-accent">SaaS</span></span>
        </a>
        
        <div class="nav-links" :class="{ 'nav-open': mobileMenuOpen }">
          <a href="#fitur" @click="mobileMenuOpen = false">Fitur</a>
          <a href="#cara-kerja" @click="mobileMenuOpen = false">Cara Kerja</a>
          <a href="#harga" @click="mobileMenuOpen = false">Harga</a>
          <a href="#testimoni" @click="mobileMenuOpen = false">Testimoni</a>
          <a href="#faq" @click="mobileMenuOpen = false">FAQ</a>
        </div>

        <div class="nav-actions">
          <a href="/login" class="btn-login">Masuk</a>
          <a href="/register" class="btn-register">Daftar Gratis</a>
        </div>

        <button class="mobile-menu-btn" @click="mobileMenuOpen = !mobileMenuOpen">
          <span></span>
          <span></span>
          <span></span>
        </button>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-bg">
        <div class="hero-gradient"></div>
        <div class="hero-pattern"></div>
      </div>
      
      <div class="container hero-content">
        <div class="hero-badge">
          <span class="badge-icon">🚀</span>
          Platform ujian online gratis #1
        </div>
        
        <h1 class="hero-title">
          Platform <span class="text-gradient">Canggih</span> Ujian Online
          <br>Siap Pakai
        </h1>
        
        <p class="hero-subtitle">
          Buat ujian untuk segala keperluan dengan mudah, aman, dan terjangkau.
          Cocok untuk sekolah, bimbel, perusahaan, dan kepanitiaan.
        </p>

        <div class="hero-buttons">
          <a href="/register" class="btn-primary">
            <span>Mulai Gratis Sekarang</span>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
          </a>
          <a href="#fitur" class="btn-secondary">
            <span>Pelajari Lebih Lanjut</span>
          </a>
        </div>

        <div class="hero-stats">
          <div class="stat-avatars">
            <div class="avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">A</div>
            <div class="avatar" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">B</div>
            <div class="avatar" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">C</div>
            <div class="avatar-more">+200</div>
          </div>
          <p>Lebih dari <strong>10.000+ peserta</strong> telah puas melaksanakan ujian</p>
        </div>

        <div class="hero-features">
          <div class="hero-feature">
            <span>Buat Soal Otomatis dengan AI</span>
          </div>
          <div class="hero-feature">
            <span>Penilaian Instan & Akurat</span>
          </div>
          <div class="hero-feature">
            <span>Pengawasan Real-Time</span>
          </div>
          <div class="hero-feature">
            <span>Siap Untuk Semua Jenis Ujian</span>
          </div>
        </div>
      </div>

      <div class="hero-scroll-indicator">
        <div class="scroll-mouse">
          <div class="scroll-wheel"></div>
        </div>
        <span>Scroll untuk melihat lebih</span>
      </div>
    </section>

    <!-- Clients Section -->
    <section class="clients">
      <div class="container">
        <p class="clients-title">TELAH DIPERCAYA OLEH BERBAGAI LEMBAGA DAN INSTANSI</p>
        <div class="clients-marquee">
          <div class="clients-track">
            <div class="client-logo" v-for="i in 12" :key="i">
              <div class="client-placeholder">Client {{ i }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Steps Section -->
    <section id="cara-kerja" class="steps">
      <div class="container">
        <div class="section-header">
          <span class="section-badge">TIPS</span>
          <h2 class="section-title">4 Langkah Mudah Membuat Ujian!</h2>
        </div>

        <div class="steps-grid">
          <div class="step-card" v-for="(step, index) in steps" :key="index">
            <div class="step-number">{{ step.number }}</div>
            <div class="step-icon">{{ step.icon }}</div>
            <h3 class="step-title">{{ step.title }}</h3>
            <p class="step-desc">{{ step.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="fitur" class="features">
      <div class="container">
        <div class="section-header">
          <span class="section-badge">FITUR UTAMA</span>
          <h2 class="section-title">Satu Platform Lengkap untuk Segala Jenis Ujian</h2>
        </div>

        <div class="features-tabs">
          <button 
            v-for="(feature, index) in features" 
            :key="index"
            :class="['feature-tab', { active: activeFeature === index }]"
            @click="activeFeature = index"
          >
            {{ feature.title }}
          </button>
        </div>

        <div class="feature-content">
          <div class="feature-image">
            <div class="feature-image-placeholder">
              <span class="feature-emoji">{{ features[activeFeature].icon }}</span>
            </div>
          </div>
          <div class="feature-info">
            <h3>{{ features[activeFeature].title }}</h3>
            <p>{{ features[activeFeature].description }}</p>
            <ul class="feature-list">
              <li v-for="(item, i) in features[activeFeature].items" :key="i">
                <span class="check-icon">✓</span>
                {{ item }}
              </li>
            </ul>
            <a href="#" class="feature-link">
              Pelajari lebih lanjut
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12h14M12 5l7 7-7 7"/>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Use Cases Section -->
    <section class="use-cases">
      <div class="container">
        <div class="section-header">
          <span class="section-badge">STUDI KASUS</span>
          <h2 class="section-title">Penggunaan di Berbagai Skenario Nyata</h2>
        </div>

        <div class="cases-grid">
          <div class="case-card" v-for="(useCase, index) in useCases" :key="index">
            <div class="case-image">
              <span class="case-emoji">{{ useCase.icon }}</span>
            </div>
            <div class="case-content">
              <span class="case-label">Studi Kasus</span>
              <h3>{{ useCase.title }}</h3>
              <p>{{ useCase.description }}</p>
              <a href="#" class="case-link">Pelajari lebih lanjut →</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Solutions Section -->
    <section class="solutions">
      <div class="container">
        <div class="section-header">
          <span class="section-badge">SOLUSI CBT SAAS</span>
          <h2 class="section-title">Bagaimana CBT SaaS Membantu Menyelenggarakan Ujian Lebih Efisien?</h2>
        </div>

        <div class="solutions-grid">
          <div class="solution-card" v-for="(solution, index) in solutions" :key="index">
            <div class="solution-icon">{{ solution.icon }}</div>
            <h3>{{ solution.title }}</h3>
            <p>{{ solution.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Section -->
    <section id="harga" class="pricing">
      <div class="container">
        <div class="section-header">
          <span class="section-badge">HARGA LAYANAN</span>
          <h2 class="section-title">Mulai Gratis, Tingkatkan Sesuai Kebutuhan Anda</h2>
        </div>

        <div class="pricing-toggle">
          <button 
            :class="{ active: pricingType === 'regular' }"
            @click="pricingType = 'regular'"
          >Regular</button>
          <button 
            :class="{ active: pricingType === 'subscription' }"
            @click="pricingType = 'subscription'"
          >Langganan</button>
          <button 
            :class="{ active: pricingType === 'enterprise' }"
            @click="pricingType = 'enterprise'"
          >Khusus</button>
        </div>

        <div class="pricing-grid">
          <div class="pricing-card">
            <div class="pricing-header">
              <h3>Standard</h3>
              <div class="pricing-price">
                <span class="price">Gratis</span>
              </div>
              <p>Cocok untuk ujian harian dengan fitur andal</p>
            </div>
            <a href="/register" class="pricing-btn">Mulai Gratis Sekarang</a>
            <ul class="pricing-features">
              <li><span class="check">✓</span> Computer Based Test (CBT)</li>
              <li><span class="check">✓</span> Gratis 100 Kuota Ujian Per Bulan</li>
              <li><span class="check">✓</span> Hingga 50 Peserta Serentak</li>
              <li><span class="check">✓</span> SLA 95% Uptime</li>
            </ul>
          </div>

          <div class="pricing-card popular">
            <div class="popular-badge">Paling Populer</div>
            <div class="pricing-header">
              <h3>Professional</h3>
              <div class="pricing-price">
                <span class="currency">Rp</span>
                <span class="price">3.000</span>
                <span class="period">/ peserta</span>
              </div>
              <p>Min. 10 Peserta</p>
            </div>
            <a href="/register" class="pricing-btn primary">Mulai Sekarang</a>
            <ul class="pricing-features">
              <li><span class="check">✓</span> Computer Based Test (CBT)</li>
              <li><span class="check">✓</span> Computer Adaptive Test (CAT)</li>
              <li><span class="check">✓</span> Hingga 1000 Peserta Serentak</li>
              <li><span class="check">✓</span> SLA 99% Uptime</li>
            </ul>
          </div>

          <div class="pricing-card">
            <div class="pricing-header">
              <h3>Enterprise</h3>
              <div class="pricing-price">
                <span class="price">Hubungi</span>
              </div>
              <p>Solusi premium dengan dukungan prioritas</p>
            </div>
            <a href="#" class="pricing-btn">Kontak Kami</a>
            <ul class="pricing-features">
              <li><span class="check">✓</span> Semua Fitur Professional</li>
              <li><span class="check">✓</span> Dukungan Teknis Prioritas</li>
              <li><span class="check">✓</span> Tidak Ada Batasan Peserta</li>
              <li><span class="check">✓</span> SLA 99.9% Uptime</li>
            </ul>
          </div>
        </div>

        <p class="pricing-note">
          Setiap paket dirancang fleksibel dengan sistem batas peserta serentak.
          Tidak ada batas kuota total, dan ujian dapat berlangsung terus selama masa aktif.
        </p>
      </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimoni" class="testimonials">
      <div class="container">
        <div class="section-header">
          <span class="section-badge">DIPERCAYA RATUSAN INSTANSI</span>
          <h2 class="section-title">Testimoni Pengguna</h2>
          <p class="section-subtitle">Kata mereka yang telah merasakan kemudahan ujian bersama CBT SaaS</p>
        </div>

        <div class="testimonials-grid">
          <div class="testimonial-card" v-for="(testimonial, index) in testimonials" :key="index">
            <div class="testimonial-stars">
              <span v-for="i in 5" :key="i">⭐</span>
            </div>
            <p class="testimonial-text">"{{ testimonial.text }}"</p>
            <div class="testimonial-author">
              <div class="author-avatar" :style="{ background: testimonial.color }">
                {{ testimonial.name.charAt(0) }}
              </div>
              <div class="author-info">
                <h4>{{ testimonial.name }}</h4>
                <p>{{ testimonial.role }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="testimonials-cta">
          <a href="#" class="btn-community">
            <span>💬</span> Gabung Komunitas
          </a>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="faq">
      <div class="container">
        <div class="section-header">
          <span class="section-badge">FAQ</span>
          <h2 class="section-title">Pertanyaan Yang Sering Ditanyakan</h2>
        </div>

        <div class="faq-list">
          <div 
            class="faq-item" 
            v-for="(item, index) in faqs" 
            :key="index"
            :class="{ open: openFaq === index }"
          >
            <button class="faq-question" @click="openFaq = openFaq === index ? null : index">
              <span>{{ item.question }}</span>
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9l6 6 6-6"/>
              </svg>
            </button>
            <div class="faq-answer">
              <p>{{ item.answer }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
      <div class="cta-bg">
        <div class="cta-gradient"></div>
      </div>
      <div class="container cta-content">
        <span class="cta-badge">SIAP MEMULAI?</span>
        <h2>Buat Ujian Hanya Dalam Hitungan Menit</h2>
        <p>Rasakan sendiri betapa mudahnya membuat, mengelola, dan menganalisis hasil ujian dengan platform kami.</p>
        <div class="cta-buttons">
          <a href="/register" class="btn-primary">
            Coba Gratis Sekarang
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
          </a>
          <a href="#" class="btn-white">Kontak Kami</a>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
      <div class="container">
        <div class="footer-grid">
          <div class="footer-brand">
            <a href="#" class="logo">
              <span class="logo-icon"></span>
              <span class="logo-text">CBT<span class="logo-accent">SaaS</span></span>
            </a>
            <p>Solusi cepat Computer-Based Test yang lengkap untuk kebutuhan ujian, seleksi, dan perekrutan.</p>
            <div class="footer-badges">
              <div class="badge-item">🏛️ KOMINFO</div>
              <div class="badge-item">🔒 DMCA</div>
            </div>
          </div>

          <div class="footer-links">
            <h4>Layanan</h4>
            <a href="#">Harga Layanan</a>
            <a href="#">Pusat Bantuan</a>
            <a href="#">Dokumentasi API</a>
            <a href="#">Gabung Komunitas</a>
          </div>

          <div class="footer-links">
            <h4>Legal</h4>
            <a href="#">Service Level Agreement</a>
            <a href="#">Syarat dan Ketentuan</a>
            <a href="#">Kebijakan Privasi</a>
          </div>

          <div class="footer-contact">
            <h4>Hubungi Kami</h4>
            <p>📍 Indonesia</p>
            <p>📞 +62 xxx-xxxx-xxxx</p>
            <p>✉️ hello@cbtsaas.com</p>
            <div class="footer-hours">
              <p><strong>Jam Kerja:</strong></p>
              <p>09.00 - 16.00 WIB</p>
              <p class="small">(Ujian dapat dilakukan 24/7)</p>
            </div>
          </div>
        </div>

        <div class="footer-bottom">
          <p>© 2025 CBT SaaS. Hak cipta dilindungi sepenuhnya.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const isScrolled = ref(false);
const mobileMenuOpen = ref(false);
const activeFeature = ref(0);
const pricingType = ref('regular');
const openFaq = ref(null);

const handleScroll = () => {
  isScrolled.value = window.scrollY > 50;
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});

const steps = [
  { number: '01', icon: '📋', title: 'Susun Paket Soal', description: 'Atur pertanyaan, bobot nilai, dan konfigurasi ujian sesuai kebutuhan.' },
  { number: '02', icon: '📅', title: 'Jadwalkan Pelaksanaan', description: 'Tentukan tanggal, durasi, serta sesi ujian hanya dalam beberapa klik.' },
  { number: '03', icon: '👥', title: 'Tambahkan Peserta', description: 'Unggah data peserta sekaligus atau buka pendaftaran otomatis.' },
  { number: '04', icon: '📊', title: 'Pantau & Unduh Hasil', description: 'Lihat statistik ujian dan unduh laporan lengkap secara instan.' }
];

const features = [
  {
    title: 'CBT & CAT',
    icon: '💻',
    description: 'CBT (Computer Based Test) memungkinkan peserta mengerjakan ujian langsung melalui komputer dengan sistem yang stabil. CAT (Computer Adaptive Test) menyesuaikan tingkat kesulitan soal secara otomatis.',
    items: ['Antarmuka modern dan responsif', 'Mendukung berbagai tipe soal', 'Acak soal dan jawaban otomatis', 'Timer dan auto-submit']
  },
  {
    title: 'Esai & Upload',
    icon: '📝',
    description: 'Fitur ini mendukung ujian berbasis analisis dan tugas proyek yang membutuhkan jawaban esai atau file pendukung.',
    items: ['Editor teks lengkap', 'Upload file beragam format', 'Penilaian manual atau rubrik', 'Komentar dan feedback']
  },
  {
    title: 'Branding',
    icon: '🎨',
    description: 'Bangun kepercayaan peserta dengan tampilan platform yang konsisten dengan identitas lembaga Anda.',
    items: ['Custom logo dan warna', 'Domain sendiri (white label)', 'Template sertifikat', 'Email branding']
  },
  {
    title: 'Pembayaran QRIS',
    icon: '💳',
    description: 'Kelola seluruh proses pendaftaran ujian secara otomatis mulai dari pengisian data hingga verifikasi pembayaran.',
    items: ['Integrasi QRIS otomatis', 'Invoice digital', 'Laporan keuangan', 'Multi payment gateway']
  },
  {
    title: 'Penilaian IRT',
    icon: '📈',
    description: 'Nilai dihitung otomatis dengan berbagai metode termasuk IRT, Likert, dan rubrik evaluasi.',
    items: ['Item Response Theory (IRT)', 'Skala Likert', 'Custom scoring formula', 'Analisis butir soal']
  },
  {
    title: 'Monitoring',
    icon: '👁️',
    description: 'Pastikan setiap ujian berlangsung jujur dengan pengawasan kamera dan deteksi aktivitas mencurigakan.',
    items: ['Webcam proctoring', 'Screen monitoring', 'Deteksi tab switching', 'Laporan pelanggaran']
  },
  {
    title: 'AI Generator',
    icon: '🤖',
    description: 'Gunakan kecerdasan buatan untuk mempercepat pembuatan soal, variasi jawaban, dan pembahasan.',
    items: ['Generate soal otomatis', 'Buat pembahasan instan', 'Variasi jawaban', 'Analisis kesulitan']
  }
];

const useCases = [
  { icon: '', title: 'Bimbingan Belajar', description: 'Selenggarakan try-out dan evaluasi hasil belajar dengan sistem otomatis dan laporan instan.' },
  { icon: '💼', title: 'Rekrutmen', description: 'Asesmen kandidat lebih cepat dan objektif melalui ujian online yang aman dan terukur.' },
  { icon: '🏆', title: 'Kepanitiaan Olimpiade', description: 'Kelola seleksi, penilaian, dan publikasi hasil lomba dalam satu sistem terpadu.' },
  { icon: '🎓', title: 'Sekolah & Universitas', description: 'Laksanakan ujian harian, UTS, UAS, hingga akreditasi dengan sistem digital yang stabil.' }
];

const solutions = [
  { icon: '🚀', title: 'Langsung Siap Digunakan', description: 'Tak perlu instalasi atau setup teknis. Daftar, buat ujian, dan platform langsung siap digunakan.' },
  { icon: '♾️', title: 'Gratis Tanpa Batas', description: 'Gunakan semua fitur utama tanpa biaya dan tanpa batas waktu. Upgrade kapan pun sesuai kebutuhan.' },
  { icon: '✅', title: 'Penilaian Otomatis', description: 'Sistem menghitung hasil secara instan dengan dukungan metode penilaian IRT, Likert, dan custom.' },
  { icon: '🔒', title: 'Keamanan Terjamin', description: 'Pastikan ujian berlangsung jujur dengan deteksi kecurangan otomatis dan monitoring real-time.' },
  { icon: '🔗', title: 'Integrasi API', description: 'Hubungkan dengan LMS, SIAKAD, atau sistem HR. Data tersinkron otomatis dan aman.' },
  { icon: '⚡', title: 'Stabil untuk Ribuan', description: 'Platform tangguh dengan performa stabil, mampu menangani ribuan peserta bersamaan.' }
];

const testimonials = [
  { name: 'Ade Putri', role: 'Penyelenggara Ujian', text: 'Sistem platform ujian ini mudah dipahami, praktis digunakan, dan mendukung kelancaran pengelolaan soal maupun peserta.', color: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' },
  { name: 'Muhamad Arya', role: 'Penyelenggara Ujian', text: 'Semenjak ada CBT SaaS semua otomatis bahkan mendukung metode IRT. Proses rekapitulasi jadi jauh lebih cepat.', color: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)' },
  { name: 'Devrangga Hazza', role: 'Peserta Ujian', text: 'UI modern, UX rapi, serta keamanan berlapis. Sistem ini membuat ujian nyaman dan mudah dinavigasi.', color: 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)' }
];

const faqs = [
  { question: 'Apakah saya bisa menggunakan platform ini secara gratis?', answer: 'Ya, tentu bisa. Anda hanya perlu mendaftar akun dan secara otomatis akan mendapatkan akses ke Paket Gratis Selamanya tanpa biaya apa pun. Anda sudah bisa langsung menggunakan platform untuk menyelenggarakan ujian tanpa biaya tersembunyi.' },
  { question: 'Apa yang terjadi setelah masa berlaku paket berbayar habis?', answer: 'Setelah masa berlaku paket berbayar berakhir, akun Anda akan otomatis kembali ke Paket Gratis Selamanya. Anda tetap bisa menggunakan platform tanpa biaya, hanya saja fitur dan kapasitas akan menyesuaikan dengan batasan pada paket gratis.' },
  { question: 'Apakah CBT SaaS dapat digunakan pada handphone?', answer: 'Ya, peserta ujian dapat mengerjakan ujian melalui handphone, laptop atau komputer dengan browser Google Chrome, Chromium, Microsoft Edge, atau Safari.' },
  { question: 'Apakah ada spesifikasi perangkat khusus untuk peserta?', answer: 'Tidak ada spesifikasi khusus untuk perangkat ujian, namun peserta diwajibkan menggunakan browser modern seperti Google Chrome, Chromium, Microsoft Edge, atau Safari.' },
  { question: 'Bagaimana jika saya butuh bantuan teknis?', answer: 'Tim dukungan teknis kami siap membantu Anda. Buat tiket atau hubungi kami melalui email untuk mendapatkan bantuan. Bagi pengguna paket tertentu, tersedia juga dukungan live chat untuk respon yang lebih cepat.' }
];
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.landing-page {
  font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  color: #1a1a2e;
  line-height: 1.6;
  overflow-x: hidden;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
}

/* Navbar */
.navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  padding: 16px 0;
  transition: all 0.3s ease;
}

.navbar-scrolled {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.08);
  padding: 12px 0;
}

.navbar-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.logo {
  display: flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
  font-size: 24px;
  font-weight: 800;
}

.logo-icon {
  font-size: 28px;
}

.logo-text {
  color: #1a1a2e;
}

.logo-accent {
  background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.nav-links {
  display: flex;
  gap: 32px;
}

.nav-links a {
  text-decoration: none;
  color: #4a4a68;
  font-weight: 500;
  font-size: 15px;
  transition: color 0.2s;
}

.nav-links a:hover {
  color: #f97316;
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 16px;
}

.btn-login {
  text-decoration: none;
  color: #4a4a68;
  font-weight: 600;
  font-size: 15px;
  transition: color 0.2s;
}

.btn-login:hover {
  color: #f97316;
}

.btn-register {
  background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
  color: white;
  text-decoration: none;
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  transition: transform 0.2s, box-shadow 0.2s;
}

.btn-register:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(249, 115, 22, 0.3);
}

.mobile-menu-btn {
  display: none;
  flex-direction: column;
  gap: 5px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
}

.mobile-menu-btn span {
  width: 24px;
  height: 2px;
  background: #1a1a2e;
  border-radius: 2px;
  transition: all 0.3s;
}

/* Hero Section */
.hero {
  position: relative;
  min-height: 100vh;
  display: flex;
  align-items: center;
  padding: 120px 0 80px;
  overflow: hidden;
}

.hero-bg {
  position: absolute;
  inset: 0;
  z-index: -1;
}

.hero-gradient {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, #fff5eb 0%, #fff 30%, #f0f7ff 70%, #fff 100%);
}

.hero-pattern {
  position: absolute;
  inset: 0;
  background-image: 
    radial-gradient(circle at 20% 50%, rgba(249, 115, 22, 0.08) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(59, 130, 246, 0.08) 0%, transparent 50%),
    radial-gradient(circle at 40% 80%, rgba(168, 85, 247, 0.05) 0%, transparent 50%);
}

.hero-content {
  text-align: center;
  max-width: 900px;
  margin: 0 auto;
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, rgba(249, 115, 22, 0.1) 0%, rgba(234, 88, 12, 0.1) 100%);
  border: 1px solid rgba(249, 115, 22, 0.2);
  padding: 8px 16px;
  border-radius: 50px;
  font-size: 14px;
  font-weight: 600;
  color: #ea580c;
  margin-bottom: 24px;
  animation: fadeInUp 0.6s ease;
}

.badge-icon {
  font-size: 16px;
}

.hero-title {
  font-size: clamp(36px, 5vw, 64px);
  font-weight: 800;
  line-height: 1.1;
  margin-bottom: 24px;
  animation: fadeInUp 0.6s ease 0.1s both;
}

.text-gradient {
  background: linear-gradient(135deg, #f97316 0%, #ea580c 50%, #c2410c 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hero-subtitle {
  font-size: 18px;
  color: #64748b;
  max-width: 600px;
  margin: 0 auto 32px;
  animation: fadeInUp 0.6s ease 0.2s both;
}

.hero-buttons {
  display: flex;
  justify-content: center;
  gap: 16px;
  margin-bottom: 48px;
  animation: fadeInUp 0.6s ease 0.3s both;
}

.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
  color: white;
  text-decoration: none;
  padding: 16px 28px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 16px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3);
}

.btn-primary:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(249, 115, 22, 0.4);
}

.btn-secondary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: white;
  color: #1a1a2e;
  text-decoration: none;
  padding: 16px 28px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 16px;
  border: 2px solid #e2e8f0;
  transition: all 0.3s ease;
}

.btn-secondary:hover {
  border-color: #f97316;
  color: #f97316;
}

.hero-stats {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 16px;
  margin-bottom: 48px;
  animation: fadeInUp 0.6s ease 0.4s both;
}

.stat-avatars {
  display: flex;
  align-items: center;
}

.avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  font-size: 14px;
  margin-left: -12px;
  border: 3px solid white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.avatar:first-child {
  margin-left: 0;
}

.avatar-more {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #1a1a2e;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 700;
  margin-left: -12px;
  border: 3px solid white;
}

.hero-stats p {
  font-size: 14px;
  color: #64748b;
}

.hero-stats strong {
  color: #1a1a2e;
}

.hero-features {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 16px;
  animation: fadeInUp 0.6s ease 0.5s both;
}

.hero-feature {
  display: flex;
  align-items: center;
  gap: 8px;
  background: white;
  padding: 12px 20px;
  border-radius: 50px;
  font-size: 14px;
  font-weight: 500;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
  border: 1px solid #f1f5f9;
}

.feature-icon {
  font-size: 18px;
}

.hero-scroll-indicator {
  position: absolute;
  bottom: 40px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  animation: bounce 2s infinite;
}

.scroll-mouse {
  width: 24px;
  height: 36px;
  border: 2px solid #cbd5e1;
  border-radius: 12px;
  position: relative;
}

.scroll-wheel {
  width: 4px;
  height: 8px;
  background: #94a3b8;
  border-radius: 2px;
  position: absolute;
  top: 8px;
  left: 50%;
  transform: translateX(-50%);
  animation: scroll 1.5s infinite;
}

.hero-scroll-indicator span {
  font-size: 12px;
  color: #94a3b8;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes bounce {
  0%, 100% { transform: translateX(-50%) translateY(0); }
  50% { transform: translateX(-50%) translateY(-10px); }
}

@keyframes scroll {
  0% { opacity: 1; transform: translateX(-50%) translateY(0); }
  100% { opacity: 0; transform: translateX(-50%) translateY(10px); }
}

/* Clients Section */
.clients {
  padding: 60px 0;
  background: #f8fafc;
  overflow: hidden;
}

.clients-title {
  text-align: center;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 2px;
  color: #94a3b8;
  margin-bottom: 32px;
}

.clients-marquee {
  overflow: hidden;
  mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
}

.clients-track {
  display: flex;
  gap: 48px;
  animation: marquee 30s linear infinite;
}

.client-logo {
  flex-shrink: 0;
}

.client-placeholder {
  padding: 16px 32px;
  background: white;
  border-radius: 8px;
  color: #94a3b8;
  font-weight: 600;
  font-size: 14px;
  border: 1px solid #e2e8f0;
}

@keyframes marquee {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

/* Section Styles */
.section-header {
  text-align: center;
  margin-bottom: 48px;
}

.section-badge {
  display: inline-block;
  background: linear-gradient(135deg, rgba(249, 115, 22, 0.1) 0%, rgba(234, 88, 12, 0.1) 100%);
  color: #ea580c;
  padding: 6px 16px;
  border-radius: 50px;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 1px;
  margin-bottom: 16px;
}

.section-title {
  font-size: clamp(28px, 4vw, 42px);
  font-weight: 800;
  margin-bottom: 16px;
  color: #1a1a2e;
}

.section-subtitle {
  font-size: 18px;
  color: #64748b;
  max-width: 600px;
  margin: 0 auto;
}

/* Steps Section */
.steps {
  padding: 100px 0;
  background: white;
}

.steps-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
}

.step-card {
  background: linear-gradient(180deg, #fafafa 0%, #ffffff 100%);
  border: 1px solid #f1f5f9;
  border-radius: 20px;
  padding: 32px 24px;
  text-align: center;
  position: relative;
  transition: all 0.3s ease;
}

.step-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
  border-color: #fed7aa;
}

.step-number {
  position: absolute;
  top: -12px;
  left: 24px;
  background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
  color: white;
  padding: 4px 12px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 700;
}

.step-icon {
  font-size: 48px;
  margin-bottom: 16px;
}

.step-title {
  font-size: 18px;
  font-weight: 700;
  margin-bottom: 8px;
  color: #1a1a2e;
}

.step-desc {
  font-size: 14px;
  color: #64748b;
  line-height: 1.6;
}

/* Features Section */
.features {
  padding: 100px 0;
  background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
}

.features-tabs {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 8px;
  margin-bottom: 48px;
}

.feature-tab {
  padding: 12px 24px;
  border: 2px solid #e2e8f0;
  border-radius: 50px;
  background: white;
  font-size: 14px;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  transition: all 0.3s ease;
}

.feature-tab:hover {
  border-color: #fed7aa;
  color: #f97316;
}

.feature-tab.active {
  background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
  border-color: transparent;
  color: white;
}

.feature-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 64px;
  align-items: center;
}

.feature-image-placeholder {
  background: linear-gradient(135deg, #fff5eb 0%, #fef3c7 100%);
  border-radius: 24px;
  padding: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #fed7aa;
}

.feature-emoji {
  font-size: 120px;
}

.feature-info h3 {
  font-size: 28px;
  font-weight: 800;
  margin-bottom: 16px;
  color: #1a1a2e;
}

.feature-info > p {
  font-size: 16px;
  color: #64748b;
  margin-bottom: 24px;
  line-height: 1.7;
}

.feature-list {
  list-style: none;
  margin-bottom: 24px;
}

.feature-list li {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 8px 0;
  font-size: 15px;
  color: #475569;
}

.check-icon {
  color: #22c55e;
  font-weight: 700;
}

.feature-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: #f97316;
  text-decoration: none;
  font-weight: 600;
  font-size: 15px;
  transition: gap 0.3s ease;
}

.feature-link:hover {
  gap: 12px;
}

/* Use Cases Section */
.use-cases {
  padding: 100px 0;
  background: white;
}

.cases-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 24px;
}

.case-card {
  display: flex;
  gap: 24px;
  background: linear-gradient(180deg, #fafafa 0%, #ffffff 100%);
  border: 1px solid #f1f5f9;
  border-radius: 20px;
  padding: 32px;
  transition: all 0.3s ease;
}

.case-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.06);
  border-color: #fed7aa;
}

.case-image {
  flex-shrink: 0;
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #fff5eb 0%, #fed7aa 100%);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.case-emoji {
  font-size: 36px;
}

.case-content {
  flex: 1;
}

.case-label {
  font-size: 12px;
  font-weight: 600;
  color: #f97316;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.case-content h3 {
  font-size: 20px;
  font-weight: 700;
  margin: 8px 0;
  color: #1a1a2e;
}

.case-content p {
  font-size: 14px;
  color: #64748b;
  line-height: 1.6;
  margin-bottom: 12px;
}

.case-link {
  color: #f97316;
  text-decoration: none;
  font-weight: 600;
  font-size: 14px;
}

.case-link:hover {
  text-decoration: underline;
}

/* Solutions Section */
.solutions {
  padding: 100px 0;
  background: linear-gradient(180deg, #1a1a2e 0%, #0f172a 100%);
}

.solutions .section-badge {
  background: rgba(249, 115, 22, 0.2);
}

.solutions .section-title {
  color: white;
}

.solutions-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

.solution-card {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 20px;
  padding: 32px;
  transition: all 0.3s ease;
}

.solution-card:hover {
  background: rgba(255, 255, 255, 0.08);
  border-color: rgba(249, 115, 22, 0.3);
  transform: translateY(-4px);
}

.solution-icon {
  font-size: 40px;
  margin-bottom: 16px;
}

.solution-card h3 {
  font-size: 18px;
  font-weight: 700;
  color: white;
  margin-bottom: 8px;
}

.solution-card p {
  font-size: 14px;
  color: #94a3b8;
  line-height: 1.6;
}

/* Pricing Section */
.pricing {
  padding: 100px 0;
  background: white;
}

.pricing-toggle {
  display: flex;
  justify-content: center;
  gap: 8px;
  margin-bottom: 48px;
  background: #f1f5f9;
  padding: 6px;
  border-radius: 12px;
  width: fit-content;
  margin-left: auto;
  margin-right: auto;
}

.pricing-toggle button {
  padding: 12px 24px;
  border: none;
  background: transparent;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  transition: all 0.3s ease;
}

.pricing-toggle button.active {
  background: white;
  color: #1a1a2e;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.pricing-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
  max-width: 1000px;
  margin: 0 auto;
}

.pricing-card {
  background: white;
  border: 2px solid #e2e8f0;
  border-radius: 24px;
  padding: 32px;
  position: relative;
  transition: all 0.3s ease;
}

.pricing-card:hover {
  border-color: #fed7aa;
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
}

.pricing-card.popular {
  border-color: #f97316;
  transform: scale(1.05);
  box-shadow: 0 20px 50px rgba(249, 115, 22, 0.2);
}

.popular-badge {
  position: absolute;
  top: -12px;
  left: 50%;
  transform: translateX(-50%);
  background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
  color: white;
  padding: 6px 20px;
  border-radius: 50px;
  font-size: 12px;
  font-weight: 700;
}

.pricing-header {
  text-align: center;
  margin-bottom: 24px;
  padding-bottom: 24px;
  border-bottom: 1px solid #f1f5f9;
}

.pricing-header h3 {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a2e;
  margin-bottom: 12px;
}

.pricing-price {
  display: flex;
  align-items: baseline;
  justify-content: center;
  gap: 4px;
  margin-bottom: 8px;
}

.currency {
  font-size: 20px;
  font-weight: 600;
  color: #1a1a2e;
}

.price {
  font-size: 36px;
  font-weight: 800;
  color: #1a1a2e;
}

.period {
  font-size: 14px;
  color: #64748b;
}

.pricing-header > p {
  font-size: 14px;
  color: #64748b;
}

.pricing-btn {
  display: block;
  width: 100%;
  padding: 14px;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  background: white;
  color: #1a1a2e;
  font-size: 15px;
  font-weight: 600;
  text-decoration: none;
  text-align: center;
  margin-bottom: 24px;
  transition: all 0.3s ease;
}

.pricing-btn:hover {
  border-color: #f97316;
  color: #f97316;
}

.pricing-btn.primary {
  background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
  border-color: transparent;
  color: white;
}

.pricing-btn.primary:hover {
  box-shadow: 0 8px 20px rgba(249, 115, 22, 0.3);
}

.pricing-features {
  list-style: none;
}

.pricing-features li {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 0;
  font-size: 14px;
  color: #475569;
}

.pricing-features .check {
  color: #f97316;
  font-weight: 700;
}

.pricing-note {
  text-align: center;
  font-size: 14px;
  color: #64748b;
  max-width: 600px;
  margin: 48px auto 0;
}

/* Testimonials Section */
.testimonials {
  padding: 100px 0;
  background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
}

.testimonials-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

.testimonial-card {
  background: white;
  border: 1px solid #f1f5f9;
  border-radius: 20px;
  padding: 32px;
  transition: all 0.3s ease;
}

.testimonial-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.06);
}

.testimonial-stars {
  margin-bottom: 16px;
}

.testimonial-stars span {
  font-size: 16px;
}

.testimonial-text {
  font-size: 15px;
  color: #475569;
  line-height: 1.7;
  margin-bottom: 24px;
}

.testimonial-author {
  display: flex;
  align-items: center;
  gap: 12px;
}

.author-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  font-size: 18px;
}

.author-info h4 {
  font-size: 15px;
  font-weight: 700;
  color: #1a1a2e;
}

.author-info p {
  font-size: 13px;
  color: #64748b;
}

.testimonials-cta {
  text-align: center;
  margin-top: 48px;
}

.btn-community {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #0ea5e9;
  color: white;
  text-decoration: none;
  padding: 14px 28px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 15px;
  transition: all 0.3s ease;
}

.btn-community:hover {
  background: #0284c7;
  transform: translateY(-2px);
}

/* FAQ Section */
.faq {
  padding: 100px 0;
  background: white;
}

.faq-list {
  max-width: 800px;
  margin: 0 auto;
}

.faq-item {
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  margin-bottom: 12px;
  overflow: hidden;
  transition: all 0.3s ease;
}

.faq-item:hover {
  border-color: #fed7aa;
}

.faq-item.open {
  border-color: #f97316;
  box-shadow: 0 8px 20px rgba(249, 115, 22, 0.1);
}

.faq-question {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  background: white;
  border: none;
  cursor: pointer;
  text-align: left;
  font-size: 16px;
  font-weight: 600;
  color: #1a1a2e;
}

.faq-question svg {
  flex-shrink: 0;
  color: #94a3b8;
  transition: transform 0.3s ease;
}

.faq-item.open .faq-question svg {
  transform: rotate(180deg);
  color: #f97316;
}

.faq-answer {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.3s ease;
}

.faq-item.open .faq-answer {
  max-height: 300px;
}

.faq-answer p {
  padding: 0 24px 24px;
  font-size: 15px;
  color: #64748b;
  line-height: 1.7;
}

/* CTA Section */
.cta {
  position: relative;
  padding: 100px 0;
  overflow: hidden;
}

.cta-bg {
  position: absolute;
  inset: 0;
  z-index: -1;
}

.cta-gradient {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, #1a1a2e 0%, #0f172a 100%);
}

.cta-content {
  text-align: center;
  position: relative;
}

.cta-badge {
  display: inline-block;
  background: rgba(249, 115, 22, 0.2);
  color: #fb923c;
  padding: 6px 16px;
  border-radius: 50px;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 1px;
  margin-bottom: 24px;
}

.cta h2 {
  font-size: clamp(28px, 4vw, 42px);
  font-weight: 800;
  color: white;
  margin-bottom: 16px;
}

.cta p {
  font-size: 18px;
  color: #94a3b8;
  max-width: 500px;
  margin: 0 auto 32px;
}

.cta-buttons {
  display: flex;
  justify-content: center;
  gap: 16px;
}

.btn-white {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: white;
  color: #1a1a2e;
  text-decoration: none;
  padding: 16px 28px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 16px;
  transition: all 0.3s ease;
}

.btn-white:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(255, 255, 255, 0.2);
}

/* Footer */
.footer {
  background: #0f172a;
  padding: 80px 0 32px;
}

.footer-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1.5fr;
  gap: 48px;
  margin-bottom: 48px;
}

.footer-brand .logo {
  margin-bottom: 16px;
}

.footer-brand .logo-text {
  color: white;
}

.footer-brand > p {
  font-size: 14px;
  color: #94a3b8;
  line-height: 1.7;
  margin-bottom: 24px;
}

.footer-badges {
  display: flex;
  gap: 12px;
}

.badge-item {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 12px;
  color: #94a3b8;
}

.footer-links h4,
.footer-contact h4 {
  font-size: 14px;
  font-weight: 700;
  color: white;
  margin-bottom: 20px;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.footer-links a {
  display: block;
  color: #94a3b8;
  text-decoration: none;
  font-size: 14px;
  padding: 6px 0;
  transition: color 0.2s;
}

.footer-links a:hover {
  color: #f97316;
}

.footer-contact p {
  font-size: 14px;
  color: #94a3b8;
  padding: 4px 0;
}

.footer-hours {
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-hours .small {
  font-size: 12px;
  color: #64748b;
}

.footer-bottom {
  padding-top: 32px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  text-align: center;
}

.footer-bottom p {
  font-size: 14px;
  color: #64748b;
}

/* Responsive */
@media (max-width: 1024px) {
  .steps-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .feature-content {
    grid-template-columns: 1fr;
    gap: 40px;
  }

  .solutions-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .pricing-grid {
    grid-template-columns: 1fr;
    max-width: 400px;
  }

  .pricing-card.popular {
    transform: none;
  }

  .testimonials-grid {
    grid-template-columns: 1fr;
  }

  .footer-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .nav-links {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: white;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 24px;
    transform: translateX(100%);
    transition: transform 0.3s ease;
  }

  .nav-links.nav-open {
    transform: translateX(0);
  }

  .nav-links a {
    font-size: 24px;
  }

  .nav-actions {
    display: none;
  }

  .mobile-menu-btn {
    display: flex;
  }

  .hero-buttons {
    flex-direction: column;
  }

  .hero-features {
    flex-direction: column;
    align-items: center;
  }

  .steps-grid {
    grid-template-columns: 1fr;
  }

  .cases-grid {
    grid-template-columns: 1fr;
  }

  .case-card {
    flex-direction: column;
    text-align: center;
  }

  .solutions-grid {
    grid-template-columns: 1fr;
  }

  .cta-buttons {
    flex-direction: column;
  }

  .footer-grid {
    grid-template-columns: 1fr;
    gap: 32px;
  }
}
</style>