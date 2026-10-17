<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import BrandMark from '@/Components/BrandMark.vue';
import Icon from '@/Components/Icon.vue';

const props = defineProps({
    settings: { type: Object, required: true },
    videos: { type: Array, default: () => [] },
    gallery: { type: Array, default: () => [] },
    testimonials: { type: Array, default: () => [] },
    categories: { type: Object, default: () => ({}) },
    projectTypes: { type: Array, default: () => [] },
});

// Intro loader state
const showLoader = ref(false);
const isLeavingLoader = ref(true);
const isHeroReady = ref(true);

onMounted(() => {
    // If user visited before in session, speed up or run smooth 1.6s intro
    const seen = sessionStorage.getItem('sk_intro_seen');
    const delay = seen ? 600 : 1800;

    setTimeout(() => {
        isLeavingLoader.value = true;
        setTimeout(() => {
            showLoader.value = false;
            isHeroReady.value = true;
            sessionStorage.setItem('sk_intro_seen', '1');
        }, 900);
    }, delay);
});

// Navigation state
const isScrolled = ref(false);
const mobileMenuOpen = ref(false);
const showFloatingTop = ref(false);
const activeSection = ref('');
const trackedSections = ['about', 'works', 'gallery', 'testimonials', 'booking'];

function handleScroll() {
    isScrolled.value = window.scrollY > 40;
    showFloatingTop.value = window.scrollY > 400;

    // Scrollspy calculation:
    const scrollBottom = window.innerHeight + window.scrollY;
    if (document.documentElement.scrollHeight - scrollBottom < 120) {
        activeSection.value = 'booking';
        return;
    }

    const offset = 180;
    let current = '';
    for (const id of trackedSections) {
        const el = document.getElementById(id);
        if (el) {
            const rect = el.getBoundingClientRect();
            if (rect.top <= offset && rect.bottom > offset) {
                current = id;
                break;
            }
        }
    }
    activeSection.value = current;
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
});
onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

// Modals
const activeVideo = ref(null);
const activeImageIndex = ref(null);

function openVideo(video) {
    if (video?.embed_url) {
        activeVideo.value = video;
        document.body.classList.add('is-locked');
    }
}

function closeVideo() {
    activeVideo.value = null;
    document.body.classList.remove('is-locked');
}

function openLightbox(index) {
    activeImageIndex.value = index;
    document.body.classList.add('is-locked');
}

function closeLightbox() {
    activeImageIndex.value = null;
    document.body.classList.remove('is-locked');
}

function prevImage() {
    if (activeImageIndex.value > 0) {
        activeImageIndex.value--;
    } else {
        activeImageIndex.value = filteredGallery.value.length - 1;
    }
}

function nextImage() {
    if (activeImageIndex.value < filteredGallery.value.length - 1) {
        activeImageIndex.value++;
    } else {
        activeImageIndex.value = 0;
    }
}

// Keyboard shortcuts for modals
function handleKeydown(e) {
    if (e.key === 'Escape') {
        closeVideo();
        closeLightbox();
    } else if (activeImageIndex.value !== null) {
        if (e.key === 'ArrowLeft') prevImage();
        if (e.key === 'ArrowRight') nextImage();
    }
}

onMounted(() => window.addEventListener('keydown', handleKeydown));
onUnmounted(() => window.removeEventListener('keydown', handleKeydown));

// Gallery Filtering & Mobile Expand
const selectedCategory = ref('all');
const isGalleryExpanded = ref(false);

const filteredGallery = computed(() => {
    if (selectedCategory.value === 'all') return props.gallery;
    return props.gallery.filter((item) => item.category === selectedCategory.value);
});

watch(selectedCategory, () => {
    isGalleryExpanded.value = false;
});

function toggleGalleryExpanded() {
    isGalleryExpanded.value = !isGalleryExpanded.value;
    if (!isGalleryExpanded.value) {
        const el = document.getElementById('gallery');
        if (el) {
            el.scrollIntoView({ behavior: 'smooth' });
        }
    }
}

// Testimonial Carousel (4 slides)
const currentTestimonialIndex = ref(0);
let testimonialTimer = null;

function nextTestimonial() {
    if (props.testimonials.length > 0) {
        currentTestimonialIndex.value = (currentTestimonialIndex.value + 1) % props.testimonials.length;
    }
}
function prevTestimonial() {
    if (props.testimonials.length > 0) {
        currentTestimonialIndex.value = (currentTestimonialIndex.value - 1 + props.testimonials.length) % props.testimonials.length;
    }
}

function startTestimonialAutoplay() {
    stopTestimonialAutoplay();
    if (props.testimonials.length > 1) {
        testimonialTimer = setInterval(() => {
            nextTestimonial();
        }, 5500);
    }
}

function stopTestimonialAutoplay() {
    if (testimonialTimer) {
        clearInterval(testimonialTimer);
        testimonialTimer = null;
    }
}

// 4 Hero Sliders
const activeHeroSlide = ref(0);
let heroTimer = null;
const heroTouchStartX = ref(0);
const heroTouchEndX = ref(0);

const heroSlides = computed(() => [
    {
        id: 1,
        eyebrow: props.settings.hero_slide_1_eyebrow || props.settings.hero_eyebrow || 'Model — Music Videos — Editorial',
        subtitle: props.settings.hero_slide_1_sub || props.settings.hero_subtitle || 'Bringing confidence, elegance and personality to every frame — from the runway to the music video set.',
        image: props.settings.hero_image || '/storage/seed/sujita-1.jpg',
        badge: 'High Fashion',
        position: 'center 12%',
        mobilePosition: 'center 10%',
    },
    {
        id: 2,
        eyebrow: props.settings.hero_slide_2_eyebrow || 'Lead Actress — Cinematic Music Videos',
        subtitle: props.settings.hero_slide_2_sub || 'Starring as the lead muse in chart-topping visual anthems and collaborative directorial masterworks.',
        image: props.settings.hero_slide_2_image || '/storage/seed/sujita-2.jpg',
        badge: 'Cinematic Muse',
        position: 'center 14%',
        mobilePosition: 'center 10%',
    },
    {
        id: 3,
        eyebrow: props.settings.hero_slide_3_eyebrow || 'High Fashion — Runway & Lookbook',
        subtitle: props.settings.hero_slide_3_sub || 'Grace in motion — commanding attention with haute couture presence, designer elegance, and poise.',
        image: props.settings.hero_slide_3_image || '/storage/seed/sujita-3.jpg',
        badge: 'Runway Muse',
        position: 'center 12%',
        mobilePosition: 'center 10%',
    },
    {
        id: 4,
        eyebrow: props.settings.hero_slide_4_eyebrow || 'Digital Creator — Global Campaigns',
        subtitle: props.settings.hero_slide_4_sub || 'Based in Virginia, USA and available worldwide for international commercial campaigns and editorials.',
        image: props.settings.hero_slide_4_image || '/storage/seed/sujita-4.jpg',
        badge: 'Editorial Muse',
        position: 'center 15%',
        mobilePosition: 'center 12%',
    },
]);

function setHeroSlide(index) {
    activeHeroSlide.value = (index + heroSlides.value.length) % heroSlides.value.length;
    resetHeroTimer();
}

function nextHeroSlide() {
    setHeroSlide(activeHeroSlide.value + 1);
}

function prevHeroSlide() {
    setHeroSlide(activeHeroSlide.value - 1);
}

function startHeroTimer() {
    stopHeroTimer();
    heroTimer = setInterval(() => {
        nextHeroSlide();
    }, 2000);
}

function stopHeroTimer() {
    if (heroTimer) {
        clearInterval(heroTimer);
        heroTimer = null;
    }
}

function resetHeroTimer() {
    stopHeroTimer();
    startHeroTimer();
}

function handleHeroTouchStart(e) {
    if (e.touches && e.touches[0]) {
        heroTouchStartX.value = e.touches[0].clientX;
    }
}

function handleHeroTouchEnd(e) {
    if (e.changedTouches && e.changedTouches[0]) {
        heroTouchEndX.value = e.changedTouches[0].clientX;
        const diff = heroTouchStartX.value - heroTouchEndX.value;
        if (Math.abs(diff) > 40) {
            if (diff > 0) {
                nextHeroSlide();
            } else {
                prevHeroSlide();
            }
        }
    }
}

onMounted(() => {
    startTestimonialAutoplay();
    startHeroTimer();
});
onUnmounted(() => {
    stopTestimonialAutoplay();
    stopHeroTimer();
});

// Featured Video vs Regular Videos
const featuredVideo = computed(() => {
    return props.videos.find((v) => v.is_featured) || props.videos[0] || null;
});

const remainingVideos = computed(() => {
    if (!featuredVideo.value) return props.videos;
    return props.videos.filter((v) => v.id !== featuredVideo.value.id);
});

// Booking Form
const form = useForm({
    name: '',
    email: '',
    phone: '',
    company: '',
    project_type: props.projectTypes[0] || 'Music Video',
    shoot_date: '',
    message: '',
    website: '', // honeypot
});

const formSubmitted = ref(false);
const dateError = ref('');

const minDate = computed(() => {
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd}`;
});

function validateDate() {
    if (!form.shoot_date) {
        dateError.value = '';
        return true;
    }
    if (form.shoot_date < minDate.value) {
        dateError.value = 'Target shoot date cannot be in the past.';
        return false;
    }
    dateError.value = '';
    return true;
}

function submitBooking() {
    if (!validateDate()) {
        return;
    }
    form.post('/inquiries', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            dateError.value = '';
            formSubmitted.value = true;
            setTimeout(() => {
                formSubmitted.value = false;
            }, 6000);
        },
    });
}

function scrollToSection(id) {
    mobileMenuOpen.value = false;
    const el = document.getElementById(id);
    if (el) {
        el.scrollIntoView({ behavior: 'smooth' });
    }
}

function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function handleImageError(event) {
    const img = event.target;
    if (!img || img.dataset.retried) return;
    img.dataset.retried = '1';
    const src = img.getAttribute('src') || '';
    if (window.location.pathname.includes('/sujita-portfolio') && !src.includes('/sujita-portfolio/')) {
        const clean = src.replace(/^\.\//, '').replace(/^\//, '');
        img.src = '/sujita-portfolio/' + clean;
        return;
    }
    if (src.startsWith('./')) {
        img.src = src.replace(/^\.\//, '/');
    } else if (!src.startsWith('http') && !src.startsWith('/')) {
        img.src = '/' + src;
    }
}
</script>

<template>
    <Head>
        <title>{{ settings.meta_title || settings.site_name }}</title>
        <meta name="description" :content="settings.meta_description" />
    </Head>

    <!-- Intro Curtain Loader -->
    <div v-if="showLoader" class="loader" :class="{ 'is-leaving': isLeavingLoader }">
        <div class="loader__panel loader__panel--top"></div>
        <div class="loader__panel loader__panel--bottom"></div>
        <div class="loader__inner">
            <BrandMark :size="100" :ring="true" :draw="true" :src="settings.logo" />
            <span class="loader__name">{{ settings.site_name }}</span>
            <div class="loader__bar"><span></span></div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="nav" :class="{ 'is-scrolled': isScrolled }">
        <div class="container nav__inner">
            <a href="#hero" class="nav__brand" @click.prevent="scrollToTop">
                <BrandMark :size="40" :ring="true" :src="settings.logo" />
                <div class="nav__brand-text">
                    <span class="nav__brand-name">{{ settings.site_name }}</span>
                    <span class="nav__brand-sub">Model & Actress</span>
                </div>
            </a>

            <nav class="nav__links">
                <a href="#about" class="nav__link" :class="{ 'is-active': activeSection === 'about' }" @click.prevent="scrollToSection('about')">About</a>
                <a href="#works" class="nav__link" :class="{ 'is-active': activeSection === 'works' }" @click.prevent="scrollToSection('works')">Music Videos</a>
                <a href="#gallery" class="nav__link" :class="{ 'is-active': activeSection === 'gallery' }" @click.prevent="scrollToSection('gallery')">Portfolio</a>
                <a href="#testimonials" class="nav__link" :class="{ 'is-active': activeSection === 'testimonials' }" @click.prevent="scrollToSection('testimonials')">Praise</a>
                <a href="#booking" class="nav__link" :class="{ 'is-active': activeSection === 'booking' }" @click.prevent="scrollToSection('booking')">Contact</a>
            </nav>

            <div class="nav__actions">
                <a href="#booking" class="btn btn--gold btn--sm" @click.prevent="scrollToSection('booking')">
                    Book Now
                </a>
                <button
                    type="button"
                    class="nav__toggle"
                    aria-label="Toggle Navigation"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                >
                    <Icon :name="mobileMenuOpen ? 'close' : 'menu'" :size="22" />
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Drawer -->
    <Transition name="drawer">
        <div v-if="mobileMenuOpen" class="drawer">
            <div class="drawer__top">
                <BrandMark :size="38" :ring="true" :src="settings.logo" />
                <button type="button" @click="mobileMenuOpen = false" class="nav__toggle" style="display: grid;">
                    <Icon name="close" :size="22" />
                </button>
            </div>

            <nav class="drawer__links">
                <a
                    v-for="(item, i) in [
                        { id: 'about', label: 'About & Stats' },
                        { id: 'works', label: 'Music Videos' },
                        { id: 'gallery', label: 'Portfolio Gallery' },
                        { id: 'testimonials', label: 'Reviews' },
                        { id: 'booking', label: 'Booking Inquiry' },
                    ]"
                    :key="item.id"
                    :href="`#${item.id}`"
                    class="drawer__link"
                    :style="{ '--i': i }"
                    @click.prevent="scrollToSection(item.id)"
                >
                    <small>0{{ i + 1 }}</small>
                    <span>{{ item.label }}</span>
                </a>
            </nav>

            <div class="drawer__foot">
                <div class="socials">
                    <a v-if="settings.facebook" :href="settings.facebook" target="_blank" rel="noopener" title="Facebook"><Icon name="facebook" :size="18" /></a>
                    <a v-if="settings.linkedin" :href="settings.linkedin" target="_blank" rel="noopener" title="LinkedIn"><Icon name="linkedin" :size="18" /></a>
                    <a v-if="settings.instagram" :href="settings.instagram" target="_blank" rel="noopener" title="Instagram"><Icon name="instagram" :size="18" /></a>
                    <a v-if="settings.tiktok" :href="settings.tiktok" target="_blank" rel="noopener" title="TikTok"><Icon name="tiktok" :size="18" /></a>
                    <a v-if="settings.youtube" :href="settings.youtube" target="_blank" rel="noopener" title="YouTube"><Icon name="youtube" :size="18" /></a>
                </div>
            </div>
        </div>
    </Transition>

    <!-- HERO SECTION (4-Slide Editorial Slider) -->
    <section
        id="hero"
        class="hero"
        :class="{ 'is-ready': isHeroReady }"
        @mouseenter="stopHeroTimer"
        @mouseleave="startHeroTimer"
        @touchstart="handleHeroTouchStart"
        @touchend="handleHeroTouchEnd"
    >
        <!-- 4 Background Slide Layers -->
        <div class="hero__slider">
            <div
                v-for="(slide, index) in heroSlides"
                :key="slide.id"
                class="hero__slide"
                :class="{ 'is-active': activeHeroSlide === index }"
            >
                <!-- Right / Ambient Blurred Layer -->
                <div class="hero__slide-blur" aria-hidden="true">
                    <img
                        :src="slide.image"
                        alt=""
                        :style="{
                            '--pos': slide.position,
                            '--pos-m': slide.mobilePosition
                        }"
                        @error="handleImageError"
                    />
                    <div class="hero__slide-blur-veil"></div>
                </div>

                <!-- Left Clear Person Portrait -->
                <div class="hero__slide-clear">
                    <img
                        :src="slide.image"
                        :alt="`${settings.site_name} - Slide ${index + 1}`"
                        :style="{
                            '--pos': slide.position,
                            '--pos-m': slide.mobilePosition
                        }"
                        :loading="index === 0 ? 'eager' : 'lazy'"
                        @error="handleImageError"
                    />
                </div>
            </div>
        </div>

        <div class="hero__veil"></div>

        <div class="container hero__inner">
            <div class="hero__content">
                <Transition name="fade" mode="out-in">
                    <span :key="`eyebrow-${activeHeroSlide}`" class="hero__eyebrow">
                        {{ heroSlides[activeHeroSlide].eyebrow }}
                    </span>
                </Transition>

                <h1 class="hero__title">
                    <span class="hero__line">
                        <span
                            v-for="(char, i) in (settings.hero_first_name || 'Sujita').split('')"
                            :key="`fn-${i}`"
                            class="char"
                            :style="{ '--i': i, '--base': '200ms' }"
                        >{{ char }}</span>
                    </span>
                    <span class="hero__line hero__line--italic">
                        <span
                            v-for="(char, i) in (settings.hero_last_name || 'Khakural').split('')"
                            :key="`ln-${i}`"
                            class="char"
                            :style="{ '--i': i, '--base': '450ms' }"
                        >{{ char }}</span>
                    </span>
                </h1>

                <Transition name="fade" mode="out-in">
                    <p :key="`sub-${activeHeroSlide}`" class="hero__subtitle hero__fade" style="--d: 850ms;">
                        {{ heroSlides[activeHeroSlide].subtitle }}
                    </p>
                </Transition>

                <div class="hero__actions hero__fade" style="--d: 1050ms;">
                    <a href="#booking" class="btn btn--gold" @click.prevent="scrollToSection('booking')">
                        <span>Book for Project</span>
                        <Icon name="arrow-up-right" :size="16" />
                    </a>
                    <button
                        v-if="featuredVideo && featuredVideo.embed_url"
                        type="button"
                        class="btn btn--ghost"
                        @click="openVideo(featuredVideo)"
                    >
                        <span class="btn__play"><Icon name="play" :size="12" /></span>
                        <span>Watch Highlight</span>
                    </button>
                    <a href="#works" class="btn btn--ghost" @click.prevent="scrollToSection('works')">
                        <span>View Reel</span>
                    </a>
                </div>

                <!-- 4 Hero Slider Indicators & Controls -->
                <div class="hero__slider-controls hero__fade" style="--d: 1150ms;">
                    <div class="hero__slider-meta">
                        <span class="hero__slider-cur">0{{ activeHeroSlide + 1 }}</span>
                        <span class="hero__slider-divider">/</span>
                        <span class="hero__slider-total">04</span>
                    </div>

                    <div class="hero__slider-bars">
                        <button
                            v-for="(slide, index) in heroSlides"
                            :key="`bar-${slide.id}`"
                            type="button"
                            class="hero__slider-bar"
                            :class="{ 'is-active': activeHeroSlide === index }"
                            @click="setHeroSlide(index)"
                            :title="`Go to slide 0${index + 1}`"
                            :aria-label="`Slide ${index + 1}`"
                        >
                            <span class="hero__slider-bar-fill"></span>
                        </button>
                    </div>

                    <div class="hero__slider-arrows">
                        <button
                            type="button"
                            class="hero__slider-arrow"
                            @click="prevHeroSlide"
                            aria-label="Previous slide"
                            title="Previous slide"
                        >
                            <Icon name="arrow-left" :size="16" />
                        </button>
                        <button
                            type="button"
                            class="hero__slider-arrow"
                            @click="nextHeroSlide"
                            aria-label="Next slide"
                            title="Next slide"
                        >
                            <Icon name="arrow-right" :size="16" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="hero__aside">
            <div class="hero__socials">
                <a v-if="settings.facebook" :href="settings.facebook" target="_blank" rel="noopener" title="Facebook"><Icon name="facebook" :size="18" /></a>
                <a v-if="settings.linkedin" :href="settings.linkedin" target="_blank" rel="noopener" title="LinkedIn"><Icon name="linkedin" :size="18" /></a>
                <a v-if="settings.instagram" :href="settings.instagram" target="_blank" rel="noopener" title="Instagram"><Icon name="instagram" :size="18" /></a>
                <a v-if="settings.tiktok" :href="settings.tiktok" target="_blank" rel="noopener" title="TikTok"><Icon name="tiktok" :size="18" /></a>
                <a v-if="settings.youtube" :href="settings.youtube" target="_blank" rel="noopener" title="YouTube"><Icon name="youtube" :size="18" /></a>
            </div>
            <div class="hero__vertical">{{ settings.location || 'Virginia · USA' }}</div>
        </div>

        <div class="hero__scroll">
            <span>Scroll</span>
            <div class="hero__scroll-line"></div>
        </div>
    </section>

    <!-- MARQUEE TICKER -->
    <aside class="marquee" aria-hidden="true">
        <div class="marquee__track">
            <div class="marquee__group" v-for="g in 2" :key="g">
                <span class="marquee__item"><span class="marquee__text">Music Video Muse</span> <Icon name="sparkle" :size="20" class="marquee__star" /></span>
                <span class="marquee__item"><span class="marquee__text">Runway Model</span> <Icon name="sparkle" :size="20" class="marquee__star" /></span>
                <span class="marquee__item"><span class="marquee__text">Visual Storyteller</span> <Icon name="sparkle" :size="20" class="marquee__star" /></span>
                <span class="marquee__item"><span class="marquee__text">Commercial & Editorial</span> <Icon name="sparkle" :size="20" class="marquee__star" /></span>
                <span class="marquee__item"><span class="marquee__text">Virginia · Worldwide</span> <Icon name="sparkle" :size="20" class="marquee__star" /></span>
            </div>
        </div>
    </aside>

    <!-- ABOUT & COMP CARD SECTION -->
    <section id="about" class="section about">
        <div class="container">
            <div class="about__grid">
                <!-- Visual with spinning badge -->
                <div class="about__visual" v-reveal="{ from: 'left' }">
                    <div class="about__frame"></div>
                    <div class="about__photo">
                        <img :src="settings.about_image || '/images/logo-full.jpg'" :alt="settings.site_name" @error="handleImageError" />
                    </div>

                    <div class="about__badge">
                        <svg class="about__badge-text" viewBox="0 0 100 100">
                            <path id="badge-circle" d="M 50, 50 m -37, 0 a 37,37 0 1,1 74,0 a 37,37 0 1,1 -74,0" fill="none" />
                            <text xml:space="preserve"><textPath href="#badge-circle" textLength="228" lengthAdjust="spacing">OFFICIAL SUJITA KHAKURAL • OFFICIAL SUJITA KHAKURAL • </textPath></text>
                        </svg>
                        <BrandMark :size="42" :ring="false" />
                    </div>
                </div>

                <!-- Text & Comp Card -->
                <div class="about__body" v-reveal="{ from: 'right', delay: 150 }">
                    <div class="section-label">
                        <span class="section-label__num">01</span>
                        <span>Biography</span>
                    </div>

                    <h2 class="display">
                        {{ settings.about_heading || 'Elegance, in motion.' }}
                    </h2>

                    <div class="about__text lede" style="white-space: pre-line;">
                        {{ settings.about_text }}
                    </div>

                    <div class="about__meta">
                        <span v-if="settings.location">
                            <Icon name="pin" :size="16" />
                            <span>{{ settings.location }}</span>
                        </span>
                        <span v-if="settings.availability">
                            <Icon name="globe" :size="16" />
                            <span>{{ settings.availability }}</span>
                        </span>
                    </div>

                    <!-- Comp Card Measurements Grid -->
                    <div class="stats" v-reveal="{ delay: 200 }">
                        <div v-if="settings.m_height" class="stat">
                            <span class="stat__label">Height</span>
                            <span class="stat__value">{{ settings.m_height }}</span>
                        </div>
                        <div v-if="settings.m_bust" class="stat">
                            <span class="stat__label">Bust</span>
                            <span class="stat__value">{{ settings.m_bust }}</span>
                        </div>
                        <div v-if="settings.m_waist" class="stat">
                            <span class="stat__label">Waist</span>
                            <span class="stat__value">{{ settings.m_waist }}</span>
                        </div>
                        <div v-if="settings.m_shoe" class="stat">
                            <span class="stat__label">Shoe</span>
                            <span class="stat__value">{{ settings.m_shoe }}</span>
                        </div>
                        <div v-if="settings.m_hair" class="stat">
                            <span class="stat__label">Hair</span>
                            <span class="stat__value">{{ settings.m_hair }}</span>
                        </div>
                        <div v-if="settings.m_eyes" class="stat">
                            <span class="stat__label">Eyes</span>
                            <span class="stat__value">{{ settings.m_eyes }}</span>
                        </div>
                        <div v-if="settings.m_hips" class="stat">
                            <span class="stat__label">Hips</span>
                            <span class="stat__value">{{ settings.m_hips }}</span>
                        </div>
                        <div v-if="settings.m_dress" class="stat">
                            <span class="stat__label">Dress</span>
                            <span class="stat__value">{{ settings.m_dress }}</span>
                        </div>
                    </div>

                    <div class="about__signature">
                        {{ settings.site_name }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MUSIC VIDEOS SECTION -->
    <section id="works" class="section works">
        <div class="container">
            <div class="section-head" v-reveal>
                <div class="section-head__title">
                    <div class="section-label">
                        <span class="section-label__num">02</span>
                        <span>Film & Screen</span>
                    </div>
                    <h2 class="display">
                        Music <em>Videos</em>
                    </h2>
                </div>
                <div class="section-head__aside">
                    Featured lead roles, music video performances and cinematic collaborations with directors and artists worldwide.
                </div>
            </div>

            <!-- Featured Video Hero Banner -->
            <div
                v-if="featuredVideo"
                class="feature"
                v-reveal="{ from: 'zoom' }"
                @click="openVideo(featuredVideo)"
                style="cursor: pointer;"
            >
                <img :src="featuredVideo.thumbnail_url || '/images/logo-full.jpg'" :alt="featuredVideo.title" class="feature__img" @error="handleImageError" />
                <div class="feature__body">
                    <span class="tag">Featured Music Video</span>
                    <h3 class="feature__title">{{ featuredVideo.title }}</h3>
                    <div class="feature__meta">
                        <span v-if="featuredVideo.artist">{{ featuredVideo.artist }}</span>
                        <span>{{ featuredVideo.role || 'Lead Model' }}</span>
                        <span v-if="featuredVideo.director">Dir. {{ featuredVideo.director }}</span>
                        <span v-if="featuredVideo.year">{{ featuredVideo.year }}</span>
                    </div>
                    <p v-if="featuredVideo.description" class="feature__desc">
                        {{ featuredVideo.description }}
                    </p>
                </div>
                <button type="button" class="play-btn" aria-label="Play Video">
                    <Icon name="play" :size="30" />
                </button>
            </div>

            <!-- Additional Music Videos Grid -->
            <div v-if="remainingVideos.length > 0" class="video-grid" style="margin-top: 2rem;">
                <div
                    v-for="(video, i) in remainingVideos"
                    :key="video.id"
                    class="video-card"
                    v-reveal="{ delay: i * 80 }"
                    @click="openVideo(video)"
                    style="cursor: pointer;"
                >
                    <div class="video-card__thumb">
                        <img :src="video.thumbnail_url || '/images/logo-full.jpg'" :alt="video.title" @error="handleImageError" />
                        <div class="video-card__play">
                            <Icon name="play" :size="22" />
                        </div>
                        <span v-if="video.role" class="tag tag--outline video-card__badge">
                            {{ video.role }}
                        </span>
                        <span class="video-card__index">0{{ i + 2 }}</span>
                    </div>
                    <div class="video-card__info">
                        <h4 class="video-card__title">{{ video.title }}</h4>
                        <div class="video-card__meta">
                            <span v-if="video.artist">{{ video.artist }}</span>
                            <span v-if="video.year">{{ video.year }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PHOTO GALLERY / PORTFOLIO -->
    <section id="gallery" class="section gallery-section">
        <div class="container">
            <div class="section-head" v-reveal>
                <div class="section-head__title">
                    <div class="section-label">
                        <span class="section-label__num">03</span>
                        <span>Visual Stories</span>
                    </div>
                    <h2 class="display">
                        Photo <em>Gallery</em>
                    </h2>
                </div>

                <!-- Category Filters -->
                <div class="filters">
                    <button
                        type="button"
                        class="filter"
                        :class="{ 'is-active': selectedCategory === 'all' }"
                        @click="selectedCategory = 'all'"
                    >
                        <span>All</span>
                        <sup>{{ gallery.length }}</sup>
                    </button>
                    <button
                        v-for="(label, key) in categories"
                        :key="key"
                        type="button"
                        class="filter"
                        :class="{ 'is-active': selectedCategory === key }"
                        @click="selectedCategory = key"
                    >
                        <span>{{ label }}</span>
                        <sup>{{ gallery.filter(g => g.category === key).length }}</sup>
                    </button>
                </div>
            </div>

            <!-- Masonry Grid -->
            <div v-if="filteredGallery.length === 0" class="empty-note">
                No photos currently in this category.
            </div>
            <div v-else>
                <div class="masonry" :class="{ 'is-expanded': isGalleryExpanded }">
                    <div
                        v-for="(item, index) in filteredGallery"
                        :key="item.id"
                        class="masonry__item"
                        :style="{ '--i': index }"
                        @click="openLightbox(index)"
                    >
                        <img :src="item.src" :alt="item.caption || 'Model portrait'" loading="lazy" @error="handleImageError" />
                        <div class="masonry__overlay">
                            <span class="masonry__cat">{{ item.label }}</span>
                            <h4 v-if="item.caption" class="masonry__caption">{{ item.caption }}</h4>
                        </div>
                    </div>
                </div>

                <!-- View More for Mobile View -->
                <div v-if="filteredGallery.length > 6" class="gallery-more">
                    <button
                        type="button"
                        class="gallery-more__btn"
                        @click="toggleGalleryExpanded"
                        :aria-expanded="isGalleryExpanded"
                    >
                        <span>{{ isGalleryExpanded ? 'Show Less' : 'View More' }}</span>
                        <span v-if="!isGalleryExpanded" class="gallery-more__pill">+{{ filteredGallery.length - 6 }}</span>
                        <Icon :name="isGalleryExpanded ? 'chevron-up' : 'chevron-down'" :size="15" />
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS SECTION -->
    <section v-if="testimonials.length > 0" id="testimonials" class="section voices">
        <div class="container">
            <div class="voices__inner" v-reveal>
                <div class="voices__head">
                    <div class="section-label" style="justify-content: center;">
                        <span class="section-label__num">04</span>
                        <span>Reputation</span>
                    </div>
                    <h2 class="display" style="margin: 0;">
                        Words of <em>Praise</em>
                    </h2>
                </div>

                <Icon name="quote" :size="42" class="voices__mark" />

                <div
                    class="voices__stage"
                    @mouseenter="stopTestimonialAutoplay"
                    @mouseleave="startTestimonialAutoplay"
                >
                    <Transition name="quote" mode="out-in">
                        <div :key="currentTestimonialIndex" class="voices__quote">
                            <blockquote class="voices__text">
                                "{{ testimonials[currentTestimonialIndex].quote }}"
                            </blockquote>
                            <div class="voices__author">
                                <span class="voices__name">{{ testimonials[currentTestimonialIndex].name }}</span>
                                <span class="voices__role">{{ testimonials[currentTestimonialIndex].role }}</span>
                            </div>
                        </div>
                    </Transition>
                </div>

                <div class="voices__nav">
                    <button type="button" class="voices__arrow" aria-label="Previous quote" @click="prevTestimonial">
                        <Icon name="arrow-left" :size="18" />
                    </button>
                    <div class="voices__dots">
                        <button
                            v-for="(_, i) in testimonials"
                            :key="i"
                            type="button"
                            class="voices__dot"
                            :class="{ 'is-active': currentTestimonialIndex === i }"
                            @click="currentTestimonialIndex = i"
                            :aria-label="`Go to slide ${i + 1}`"
                        />
                    </div>
                    <button type="button" class="voices__arrow" aria-label="Next quote" @click="nextTestimonial">
                        <Icon name="arrow-right" :size="18" />
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- BOOKING & CONTACT SECTION -->
    <section id="booking" class="section booking">
        <div class="container">
            <div class="booking__grid">
                <!-- Left: Contact Information -->
                <div class="booking__intro" v-reveal="{ from: 'left' }">
                    <div class="section-label">
                        <span class="section-label__num">05</span>
                        <span>Inquiries</span>
                    </div>

                    <h2 class="display">
                        Let's create <em>something</em> memorable.
                    </h2>

                    <p class="lede">
                        {{ settings.booking_text || 'Casting for a music video, campaign or editorial? Share a few details about your project and I will get back to you within 48 hours.' }}
                    </p>

                    <ul class="contact-list">
                        <li v-if="settings.contact_email">
                            <span class="contact-list__icon"><Icon name="mail" :size="18" /></span>
                            <div>
                                <span class="contact-list__label">Direct Email</span>
                                <a :href="`mailto:${settings.contact_email}`">{{ settings.contact_email }}</a>
                            </div>
                        </li>
                        <li v-if="settings.contact_phone">
                            <span class="contact-list__icon"><Icon name="phone" :size="18" /></span>
                            <div>
                                <span class="contact-list__label">Phone / WhatsApp</span>
                                <span>{{ settings.contact_phone }}</span>
                            </div>
                        </li>
                        <li v-if="settings.agency">
                            <span class="contact-list__icon"><Icon name="briefcase" :size="18" /></span>
                            <div>
                                <span class="contact-list__label">Representation</span>
                                <span>{{ settings.agency }}</span>
                            </div>
                        </li>
                        <li>
                            <span class="contact-list__icon"><Icon name="pin" :size="18" /></span>
                            <div>
                                <span class="contact-list__label">Based in</span>
                                <span>{{ settings.location || 'Manassas, Virginia, USA' }}</span>
                            </div>
                        </li>
                        <li v-if="settings.facebook">
                            <span class="contact-list__icon"><Icon name="facebook" :size="18" /></span>
                            <div>
                                <span class="contact-list__label">Facebook</span>
                                <a :href="settings.facebook" target="_blank" rel="noopener">Connect on Facebook</a>
                            </div>
                        </li>
                        <li v-if="settings.linkedin">
                            <span class="contact-list__icon"><Icon name="linkedin" :size="18" /></span>
                            <div>
                                <span class="contact-list__label">LinkedIn</span>
                                <a :href="settings.linkedin" target="_blank" rel="noopener">Connect on LinkedIn</a>
                            </div>
                        </li>
                    </ul>

                    <div style="margin-top: 1.4rem;">
                        <span class="contact-list__label" style="margin-bottom: 0.75rem;">Follow & Connect</span>
                        <div class="socials">
                            <a v-if="settings.facebook" :href="settings.facebook" target="_blank" rel="noopener" title="Facebook"><Icon name="facebook" :size="20" /></a>
                            <a v-if="settings.linkedin" :href="settings.linkedin" target="_blank" rel="noopener" title="LinkedIn"><Icon name="linkedin" :size="20" /></a>
                            <a v-if="settings.instagram" :href="settings.instagram" target="_blank" rel="noopener" title="Instagram"><Icon name="instagram" :size="20" /></a>
                            <a v-if="settings.tiktok" :href="settings.tiktok" target="_blank" rel="noopener" title="TikTok"><Icon name="tiktok" :size="20" /></a>
                            <a v-if="settings.youtube" :href="settings.youtube" target="_blank" rel="noopener" title="YouTube"><Icon name="youtube" :size="20" /></a>
                        </div>
                    </div>
                </div>

                <!-- Right: Booking Form Card -->
                <div class="form-card" v-reveal="{ from: 'right', delay: 150 }">
                    <Transition name="fade" mode="out-in">
                        <!-- Success Message -->
                        <div v-if="formSubmitted" class="form-success">
                            <div class="form-success__icon">
                                <Icon name="check" :size="36" />
                            </div>
                            <h3>Inquiry Received</h3>
                            <p>Thank you for reaching out! Sujita will review your project details and get back to you shortly.</p>
                        </div>

                        <!-- Form -->
                        <form v-else @submit.prevent="submitBooking">
                            <!-- Honeypot -->
                            <div class="honeypot">
                                <input type="text" v-model="form.website" tabindex="-1" autocomplete="off" />
                            </div>

                            <div class="form-grid">
                                <div class="field" :class="{ 'has-error': form.errors.name }">
                                    <input
                                        id="f-name"
                                        v-model="form.name"
                                        type="text"
                                        class="field__input"
                                        placeholder=" "
                                        required
                                    />
                                    <label class="field__label" for="f-name">Your Name *</label>
                                    <span class="field__line"></span>
                                    <span v-if="form.errors.name" class="field__error">{{ form.errors.name }}</span>
                                </div>

                                <div class="field" :class="{ 'has-error': form.errors.email }">
                                    <input
                                        id="f-email"
                                        v-model="form.email"
                                        type="email"
                                        class="field__input"
                                        placeholder=" "
                                        required
                                    />
                                    <label class="field__label" for="f-email">Email Address *</label>
                                    <span class="field__line"></span>
                                    <span v-if="form.errors.email" class="field__error">{{ form.errors.email }}</span>
                                </div>

                                <div class="field" :class="{ 'has-error': form.errors.phone }">
                                    <input
                                        id="f-phone"
                                        v-model="form.phone"
                                        type="tel"
                                        class="field__input"
                                        placeholder=" "
                                    />
                                    <label class="field__label" for="f-phone">Phone / WhatsApp</label>
                                    <span class="field__line"></span>
                                </div>

                                <div class="field" :class="{ 'has-error': form.errors.company }">
                                    <input
                                        id="f-company"
                                        v-model="form.company"
                                        type="text"
                                        class="field__input"
                                        placeholder=" "
                                    />
                                    <label class="field__label" for="f-company">Production / Brand</label>
                                    <span class="field__line"></span>
                                </div>

                                <div class="field field--static">
                                    <select id="f-type" v-model="form.project_type" class="field__input">
                                        <option v-for="type in projectTypes" :key="type" :value="type">
                                            {{ type }}
                                        </option>
                                    </select>
                                    <label class="field__label" for="f-type">Project Scope</label>
                                    <span class="field__line"></span>
                                </div>

                                <div class="field field--static" :class="{ 'has-error': form.errors.shoot_date || dateError }">
                                    <input
                                        id="f-date"
                                        v-model="form.shoot_date"
                                        type="date"
                                        class="field__input"
                                        :min="minDate"
                                        @change="validateDate"
                                        @input="validateDate"
                                    />
                                    <label class="field__label" for="f-date">Target Shoot Date</label>
                                    <span class="field__line"></span>
                                    <span v-if="dateError" class="field__error">{{ dateError }}</span>
                                    <span v-else-if="form.errors.shoot_date" class="field__error">{{ form.errors.shoot_date }}</span>
                                </div>

                                <div class="field span-2" :class="{ 'has-error': form.errors.message }">
                                    <textarea
                                        id="f-msg"
                                        v-model="form.message"
                                        class="field__input"
                                        placeholder=" "
                                        rows="4"
                                        required
                                    ></textarea>
                                    <label class="field__label" for="f-msg">Project Vision, Location & Details *</label>
                                    <span class="field__line"></span>
                                    <span v-if="form.errors.message" class="field__error">{{ form.errors.message }}</span>
                                </div>
                            </div>

                            <div class="form-foot">
                                <span class="form-foot__note">Available for domestic & international travel</span>
                                <button type="submit" class="btn btn--gold" :disabled="form.processing">
                                    <span v-if="form.processing">Sending...</span>
                                    <span v-else>Submit Inquiry</span>
                                    <Icon name="arrow-up-right" :size="16" />
                                </button>
                            </div>
                        </form>
                    </Transition>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="footer__top">
                <a href="#hero" class="nav__brand" @click.prevent="scrollToTop">
                    <BrandMark :size="48" :ring="true" :src="settings.logo" />
                    <div class="nav__brand-text">
                        <span class="nav__brand-name">{{ settings.site_name }}</span>
                        <span class="nav__brand-sub">Official Model Portfolio</span>
                    </div>
                </a>

                <nav class="footer__nav">
                    <a href="#about" class="nav__link" @click.prevent="scrollToSection('about')">About</a>
                    <a href="#works" class="nav__link" @click.prevent="scrollToSection('works')">Music Videos</a>
                    <a href="#gallery" class="nav__link" @click.prevent="scrollToSection('gallery')">Gallery</a>
                    <a href="#booking" class="nav__link" @click.prevent="scrollToSection('booking')">Contact</a>
                </nav>
            </div>

            <!-- Big responsive wordmark ensuring full name is always 100% visible -->
            <div class="footer__wordmark-wrap">
                <svg
                    class="footer__wordmark-svg"
                    viewBox="0 0 1750 230"
                    preserveAspectRatio="xMidYMid meet"
                    role="img"
                    :aria-label="settings.site_name"
                >
                    <text
                        x="50%"
                        y="58%"
                        text-anchor="middle"
                        dominant-baseline="middle"
                        class="footer__wordmark-text"
                    >
                        {{ settings.site_name.toUpperCase() }}
                    </text>
                </svg>
            </div>

            <div class="footer__bottom">
                <div>
                    © {{ new Date().getFullYear() }} {{ settings.site_name }}. All rights reserved.
                </div>
                <div style="display: flex; gap: 1.5rem; align-items: center;">
                    <span>Virginia, USA</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- VIDEO MODAL -->
    <Transition name="fade">
        <div v-if="activeVideo" class="overlay" @click.self="closeVideo">
            <button type="button" class="overlay__close" @click="closeVideo" aria-label="Close video">
                <Icon name="close" :size="24" />
            </button>
            <div class="video-modal__frame">
                <iframe
                    :src="activeVideo.embed_url"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                ></iframe>
            </div>
            <div class="video-modal__caption">
                <strong>{{ activeVideo.title }}</strong>
                <small v-if="activeVideo.artist">{{ activeVideo.artist }} · {{ activeVideo.role }}</small>
            </div>
        </div>
    </Transition>

    <!-- LIGHTBOX MODAL -->
    <Transition name="zoom">
        <div v-if="activeImageIndex !== null" class="overlay" @click.self="closeLightbox">
            <button type="button" class="overlay__close" @click="closeLightbox" aria-label="Close image">
                <Icon name="close" :size="24" />
            </button>

            <button type="button" class="lightbox__arrow lightbox__arrow--prev" @click="prevImage" aria-label="Previous image">
                <Icon name="chevron-left" :size="26" />
            </button>
            <button type="button" class="lightbox__arrow lightbox__arrow--next" @click="nextImage" aria-label="Next image">
                <Icon name="chevron-right" :size="26" />
            </button>

            <figure class="lightbox__figure">
                <img :src="filteredGallery[activeImageIndex].src" :alt="filteredGallery[activeImageIndex].caption || 'Model portrait'" class="lightbox__img" @error="handleImageError" />
                <figcaption class="lightbox__caption">
                    <span v-if="filteredGallery[activeImageIndex].caption">{{ filteredGallery[activeImageIndex].caption }}</span>
                    <span class="lightbox__count">
                        {{ activeImageIndex + 1 }} / {{ filteredGallery.length }}
                    </span>
                </figcaption>
            </figure>
        </div>
    </Transition>
    <!-- Floating Back to Top on scroll -->
    <Transition name="fade">
        <button
            v-if="showFloatingTop"
            type="button"
            class="floating-top"
            @click="scrollToTop"
            aria-label="Back to top"
            title="Back to top"
        >
            <span class="floating-top__circle">
                <Icon name="arrow-up" :size="20" />
            </span>
        </button>
    </Transition>
</template>
