<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Icon from '@/Components/Icon.vue';

const props = defineProps({
    stats: { type: Object, required: true },
    recentInquiries: { type: Array, default: () => [] },
    latestVideos: { type: Array, default: () => [] },
});
</script>

<template>
    <AdminLayout title="Overview & Quick Actions">
        <Head title="Admin Dashboard" />

        <!-- Stat cards -->
        <div class="admin-stat-grid">
            <Link href="/admin/videos" class="admin-stat-card">
                <div>
                    <div class="admin-stat-val">{{ stats.videos }}</div>
                    <div class="admin-stat-lbl">Music Videos</div>
                </div>
                <div class="admin-stat-icon">
                    <Icon name="film" :size="24" />
                </div>
            </Link>

            <Link href="/admin/gallery" class="admin-stat-card">
                <div>
                    <div class="admin-stat-val">{{ stats.gallery }}</div>
                    <div class="admin-stat-lbl">Gallery Photos</div>
                </div>
                <div class="admin-stat-icon">
                    <Icon name="image" :size="24" />
                </div>
            </Link>

            <Link href="/admin/inquiries" class="admin-stat-card">
                <div>
                    <div class="admin-stat-val" :style="{ color: stats.unread > 0 ? '#e0796b' : 'var(--gold-light)' }">
                        {{ stats.unread }} / {{ stats.inquiries }}
                    </div>
                    <div class="admin-stat-lbl">Unread Inquiries</div>
                </div>
                <div class="admin-stat-icon">
                    <Icon name="inbox" :size="24" />
                </div>
            </Link>

            <Link href="/admin/testimonials" class="admin-stat-card">
                <div>
                    <div class="admin-stat-val">{{ stats.testimonials }}</div>
                    <div class="admin-stat-lbl">Testimonials</div>
                </div>
                <div class="admin-stat-icon">
                    <Icon name="quote" :size="24" />
                </div>
            </Link>
        </div>

        <!-- Quick actions -->
        <div class="admin-card" style="margin-bottom: 2rem;">
            <div class="admin-card__header">
                <div>
                    <h2 class="admin-card__title">Quick Management</h2>
                    <p class="admin-card__desc">Easily jump into managing your portfolio sections</p>
                </div>
            </div>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <Link href="/admin/videos/create" class="admin-btn admin-btn--primary">
                    <Icon name="plus" :size="16" />
                    <span>Add Music Video</span>
                </Link>
                <Link href="/admin/gallery" class="admin-btn admin-btn--secondary">
                    <Icon name="upload" :size="16" />
                    <span>Upload Gallery Photos</span>
                </Link>
                <Link href="/admin/settings" class="admin-btn admin-btn--secondary">
                    <Icon name="settings" :size="16" />
                    <span>Edit Bio & Measurements</span>
                </Link>
            </div>
        </div>

        <!-- Two columns: Recent Inquiries & Latest Videos -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(420px, 1fr)); gap: 1.75rem;">
            <!-- Recent Inquiries -->
            <div class="admin-card">
                <div class="admin-card__header">
                    <div>
                        <h2 class="admin-card__title">
                            <Icon name="mail" :size="18" />
                            <span>Recent Booking Inquiries</span>
                        </h2>
                    </div>
                    <Link href="/admin/inquiries" class="admin-btn admin-btn--secondary admin-btn--sm">
                        View All
                    </Link>
                </div>

                <div v-if="recentInquiries.length === 0" style="padding: 2rem; text-align: center; color: #666;">
                    No inquiries received yet.
                </div>
                <div v-else class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Client / Sender</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="inq in recentInquiries" :key="inq.id">
                                <td>
                                    <div style="font-weight: 600; color: var(--cream);">{{ inq.name }}</div>
                                    <div style="font-size: 0.78rem; color: #888;">{{ inq.email }}</div>
                                </td>
                                <td>{{ inq.project_type || '—' }}</td>
                                <td>
                                    <span v-if="!inq.read_at" class="admin-status-badge admin-status-badge--unread">New</span>
                                    <span v-else class="admin-status-badge admin-status-badge--read">Read</span>
                                </td>
                                <td style="text-align: right;">
                                    <Link :href="`/admin/inquiries/${inq.id}`" class="admin-btn admin-btn--secondary admin-btn--sm">
                                        View
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Latest Videos -->
            <div class="admin-card">
                <div class="admin-card__header">
                    <div>
                        <h2 class="admin-card__title">
                            <Icon name="film" :size="18" />
                            <span>Music Videos on File</span>
                        </h2>
                    </div>
                    <Link href="/admin/videos" class="admin-btn admin-btn--secondary admin-btn--sm">
                        Manage All
                    </Link>
                </div>

                <div v-if="latestVideos.length === 0" style="padding: 2rem; text-align: center; color: #666;">
                    No music videos created yet.
                </div>
                <div v-else class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Cover</th>
                                <th>Title & Artist</th>
                                <th>Year</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="v in latestVideos" :key="v.id">
                                <td>
                                    <img :src="v.thumbnail_url || '/images/logo-full.jpg'" class="admin-thumb" alt="Thumb" />
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--cream);">{{ v.title }}</div>
                                    <div style="font-size: 0.78rem; color: #888;">{{ v.artist || '—' }}</div>
                                </td>
                                <td>{{ v.year || '—' }}</td>
                                <td style="text-align: right;">
                                    <Link :href="`/admin/videos/${v.id}/edit`" class="admin-btn admin-btn--secondary admin-btn--sm">
                                        Edit
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
