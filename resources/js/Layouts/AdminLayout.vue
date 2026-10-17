<script setup>
import { computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import BrandMark from '@/Components/BrandMark.vue';
import Icon from '@/Components/Icon.vue';

const props = defineProps({
    title: { type: String, default: '' },
});

const page = usePage();
const authUser = computed(() => page.props.auth?.user);
const flash = computed(() => page.props.flash || {});
const unreadInquiries = computed(() => page.props.unreadInquiries || 0);

const navItems = [
    { name: 'Dashboard', href: '/admin', icon: 'grid' },
    { name: 'Site Settings', href: '/admin/settings', icon: 'settings' },
    { name: 'Social Links', href: '/admin/settings?tab=social', icon: 'globe' },
    { name: 'Music Videos', href: '/admin/videos', icon: 'film' },
    { name: 'Gallery', href: '/admin/gallery', icon: 'image' },
    { name: 'Testimonials', href: '/admin/testimonials', icon: 'quote' },
    { name: 'Inquiries', href: '/admin/inquiries', icon: 'inbox', badge: unreadInquiries },
    { name: 'Account', href: '/admin/account', icon: 'user' },
];

function isCurrentRoute(item) {
    const currentUrl = page.url;
    if (item.href === '/admin') return currentUrl === '/admin';
    if (item.href.includes('?tab=')) {
        return currentUrl === item.href;
    }
    const basePath = item.href.split('?')[0];
    if (basePath === '/admin/settings' && currentUrl.includes('tab=social')) {
        return false;
    }
    return currentUrl.startsWith(basePath);
}

function logout() {
    router.post('/admin/logout');
}
</script>

<template>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-sidebar__header">
                <BrandMark :size="38" :ring="true" />
                <div>
                    <span class="admin-sidebar__brand-name">Sujita Khakural</span>
                    <span class="admin-sidebar__brand-sub">Admin Portal</span>
                </div>
            </div>

            <nav class="admin-sidebar__nav">
                <Link
                    v-for="item in navItems"
                    :key="item.name"
                    :href="item.href"
                    class="admin-nav-item"
                    :class="{ 'is-active': isCurrentRoute(item) }"
                >
                    <Icon :name="item.icon" :size="18" />
                    <span>{{ item.name }}</span>
                    <span v-if="item.badge && item.badge.value > 0" class="admin-nav-badge">
                        {{ item.badge.value }}
                    </span>
                </Link>
            </nav>

            <div class="admin-sidebar__footer">
                <Link href="/" target="_blank" class="admin-nav-item">
                    <Icon name="external" :size="16" />
                    <span>View Live Site</span>
                </Link>
                <button type="button" @click="logout" class="admin-nav-item" style="color: #e0796b;">
                    <Icon name="logout" :size="16" />
                    <span>Sign Out</span>
                </button>
            </div>
        </aside>

        <!-- Main -->
        <main class="admin-main">
            <header class="admin-topbar">
                <h1 class="admin-topbar__title">{{ title }}</h1>
                <div class="admin-topbar__actions">
                    <span style="font-size: 0.85rem; color: #a8a096;">
                        Signed in as <strong style="color: var(--cream);">{{ authUser?.name }}</strong>
                    </span>
                    <Link href="/" target="_blank" class="admin-view-site">
                        <span>Live Site</span>
                        <Icon name="external" :size="14" />
                    </Link>
                </div>
            </header>

            <div class="admin-content">
                <!-- Flash alerts -->
                <div v-if="flash.success" class="admin-alert admin-alert--success">
                    <Icon name="check" :size="18" />
                    <span>{{ flash.success }}</span>
                </div>
                <div v-if="flash.error" class="admin-alert admin-alert--error">
                    <Icon name="close" :size="18" />
                    <span>{{ flash.error }}</span>
                </div>

                <slot />
            </div>
        </main>
    </div>
</template>
