# Sujita Khakural — Official Model & Artist Portfolio

A luxury, high-fashion portfolio and management web application built for model and digital creator **Sujita Khakural**, featuring editorial hero sliders, music video showcase, interactive lookbook gallery with lightbox, client testimonials, booking inquiries, and a comprehensive admin management portal.

---

## 🌟 Highlights & Features

- **4-Slide Editorial Hero Slider**:
  - Full-screen high-resolution imagery with subtle Ken Burns motion.
  - Automatic slide progression every 2 seconds with synchronized progress indicator bars.
  - Mobile swipe and interactive navigation arrows.
- **Cinematic Music Video Showcase**:
  - Embedded video player modal (YouTube & Vimeo).
  - Categorized music video listings with roles, years, and directors.
- **Photo Lookbook & Gallery**:
  - Category filters (Editorial, Portrait, Runway, Music Video, Behind the Scenes).
  - Masonry grid layout with full-screen lightbox photo viewer.
  - Responsive **View More** button on mobile to prevent excessive scrolling.
- **Reputation & Testimonials**:
  - Carousel of reviews from directors, fashion photographers, and producers.
- **Model Comp Card & Measurements**:
  - Detailed stats (Height, Bust, Waist, Shoe, Hair, Eyes) presented in a luxury comp card.
- **Contact & Booking Inquiries**:
  - Inquiry submission with honeypot spam protection and shoot date validation.
  - Social media integration (Instagram, Facebook, LinkedIn, TikTok, YouTube).
- **Admin Management Portal (`/admin-login` & `/admin`)**:
  - Direct management of site identity, bio, hero slides, and comp card measurements.
  - Social links editor with direct tab synchronization (`/admin/settings?tab=social`).
  - Music video management with drag-and-drop reordering.
  - Gallery photo uploads and caption editing.
  - Inquiry inbox with unread tracking and detail viewer.
  - Testimonial manager.

---

## 🛠️ Tech Stack

- **Backend**: [Laravel 12 / 13](https://laravel.com) running on PHP 8.3+
- **Frontend Architecture**: [Inertia.js v2 / v3](https://inertiajs.com) with [Vue 3](https://vuejs.org) (Composition API, `<script setup>`)
- **Styling**: Vanilla CSS design system with fluid typography, CSS custom properties, and micro-interactions
- **Build Tool**: [Vite](https://vitejs.dev)

---

## 🚀 Getting Started

### 1. Prerequisites
- PHP 8.3+
- Composer
- Node.js & npm
- MySQL or SQLite

### 2. Installation

```bash
# Clone the repository
git clone https://github.com/kabu631/sujita-portfolio.git
cd sujita-portfolio

# Install PHP dependencies
composer install

# Install NPM dependencies
npm install

# Copy environment configuration
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations and seeders
php artisan migrate --seed

# Alternatively, import the complete MySQL database dump:
# mysql -u root -p sujita_portfolio < database/sujita_portfolio.sql

# Create storage symlink
php artisan storage:link

# Build frontend production bundle
npm run build
```

### 3. Local Development

```bash
# Start Laravel development server
php artisan serve

# In a separate terminal, start Vite dev server
npm run dev
```

The application will be accessible at `http://127.0.0.1:8000`.

---

## 🔐 Admin Portal

- **URL**: `http://127.0.0.1:8000/admin-login` (or `/admin`)
- **Default Email**: `admin@sujita.com`
- **Default Password**: `Sujita@2026`

*Note: Credentials and account information can be changed anytime from `/admin/account`.*

---

## 🧪 Testing & Code Standards

```bash
# Run PHPUnit test suite
php artisan test

# Format code with Laravel Pint
vendor/bin/pint --format agent
```

---

## 📄 License

Proprietary. All rights reserved.
