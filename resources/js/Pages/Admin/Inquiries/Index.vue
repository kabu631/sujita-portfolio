<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Icon from '@/Components/Icon.vue';

const props = defineProps({
    inquiries: { type: Object, required: true },
    filters: { type: Object, default: () => ({ filter: 'all', search: '' }) },
});

const search = ref(props.filters.search || '');
const currentFilter = ref(props.filters.filter || 'all');

let searchTimeout = null;
watch(search, (newVal) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 350);
});

function setFilter(type) {
    currentFilter.value = type;
    applyFilters();
}

function applyFilters() {
    router.get('/admin/inquiries', {
        filter: currentFilter.value,
        search: search.value,
    }, { preserveState: true, replace: true });
}

function deleteInquiry(id) {
    if (confirm('Permanently delete this inquiry?')) {
        router.delete(`/admin/inquiries/${id}`, { preserveScroll: true });
    }
}
</script>

<template>
    <AdminLayout title="Booking Inquiries">
        <Head title="Booking Inquiries" />

        <div class="admin-card">
            <div class="admin-card__header" style="flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h2 class="admin-card__title">
                        <Icon name="inbox" :size="20" />
                        <span>Client Messages & Bookings ({{ inquiries.total }})</span>
                    </h2>
                    <p class="admin-card__desc">Review shoot requests submitted via the public contact form</p>
                </div>

                <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
                    <!-- Filter buttons -->
                    <div style="display: flex; gap: 0.35rem;">
                        <button
                            type="button"
                            class="admin-btn admin-btn--sm"
                            :class="currentFilter === 'all' ? 'admin-btn--primary' : 'admin-btn--secondary'"
                            @click="setFilter('all')"
                        >
                            All
                        </button>
                        <button
                            type="button"
                            class="admin-btn admin-btn--sm"
                            :class="currentFilter === 'unread' ? 'admin-btn--primary' : 'admin-btn--secondary'"
                            @click="setFilter('unread')"
                        >
                            Unread
                        </button>
                    </div>

                    <!-- Search input -->
                    <div style="position: relative; width: 220px;">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search client/email..."
                            class="admin-input"
                            style="padding: 0.45rem 0.75rem 0.45rem 2.2rem; font-size: 0.8rem;"
                        />
                        <Icon name="search" :size="14" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #777;" />
                    </div>
                </div>
            </div>

            <div v-if="inquiries.data.length === 0" style="padding: 3rem; text-align: center; color: #777;">
                No inquiries match your criteria.
            </div>

            <div v-else class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Sender</th>
                            <th>Project Type</th>
                            <th>Shoot Date</th>
                            <th>Received</th>
                            <th>Status</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="inq in inquiries.data" :key="inq.id">
                            <td>
                                <div style="font-weight: 600; color: var(--cream);">{{ inq.name }}</div>
                                <div style="font-size: 0.78rem; color: #888;">
                                    {{ inq.email }} <span v-if="inq.company">· {{ inq.company }}</span>
                                </div>
                            </td>
                            <td>{{ inq.project_type || 'General' }}</td>
                            <td>{{ inq.shoot_date || '—' }}</td>
                            <td style="font-size: 0.8rem; color: #888;">
                                {{ new Date(inq.created_at).toLocaleDateString() }}
                            </td>
                            <td>
                                <span v-if="!inq.read_at" class="admin-status-badge admin-status-badge--unread">
                                    New
                                </span>
                                <span v-else class="admin-status-badge admin-status-badge--read">
                                    Read
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 0.5rem;">
                                    <Link :href="`/admin/inquiries/${inq.id}`" class="admin-btn admin-btn--secondary admin-btn--sm">
                                        <Icon name="eye" :size="14" />
                                        <span>View</span>
                                    </Link>
                                    <button
                                        type="button"
                                        class="admin-btn admin-btn--danger admin-btn--sm"
                                        @click="deleteInquiry(inq.id)"
                                    >
                                        <Icon name="trash" :size="14" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="inquiries.links && inquiries.links.length > 3"
                style="display: flex; justify-content: center; gap: 0.4rem; margin-top: 1.5rem;"
            >
                <Link
                    v-for="(link, i) in inquiries.links"
                    :key="i"
                    :href="link.url || '#'"
                    class="admin-btn admin-btn--sm"
                    :class="{
                        'admin-btn--primary': link.active,
                        'admin-btn--secondary': !link.active,
                        'disabled': !link.url,
                    }"
                    v-html="link.label"
                />
            </div>
        </div>
    </AdminLayout>
</template>
