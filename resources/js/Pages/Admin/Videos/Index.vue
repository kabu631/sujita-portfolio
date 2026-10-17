<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Icon from '@/Components/Icon.vue';

const props = defineProps({
    videos: { type: Array, required: true },
});

const videoList = ref([...props.videos]);

function deleteVideo(video) {
    if (confirm(`Are you sure you want to delete "${video.title}"?`)) {
        router.delete(`/admin/videos/${video.id}`);
    }
}

// Drag & drop reordering
let draggedIndex = null;

function onDragStart(index) {
    draggedIndex = index;
}

function onDragOver(event) {
    event.preventDefault();
}

function onDrop(targetIndex) {
    if (draggedIndex === null || draggedIndex === targetIndex) return;

    const items = [...videoList.value];
    const [moved] = items.splice(draggedIndex, 1);
    items.splice(targetIndex, 0, moved);
    videoList.value = items;
    draggedIndex = null;

    // Send new ID order to backend
    router.post('/admin/videos/reorder', {
        ids: items.map((i) => i.id),
    }, { preserveScroll: true });
}
</script>

<template>
    <AdminLayout title="Music Videos">
        <Head title="Manage Music Videos" />

        <div class="admin-card">
            <div class="admin-card__header">
                <div>
                    <h2 class="admin-card__title">Music Video Catalog</h2>
                    <p class="admin-card__desc">Drag rows using the handle to change display order on the public site</p>
                </div>
                <Link href="/admin/videos/create" class="admin-btn admin-btn--primary">
                    <Icon name="plus" :size="16" />
                    <span>New Video</span>
                </Link>
            </div>

            <div v-if="videoList.length === 0" style="padding: 3rem; text-align: center; color: #777;">
                No music videos added yet. Click "New Video" to create your first entry.
            </div>

            <div v-else class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"></th>
                            <th>Cover</th>
                            <th>Title & Artist</th>
                            <th>Role / Director</th>
                            <th>Year</th>
                            <th>Featured</th>
                            <th>Status</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(video, index) in videoList"
                            :key="video.id"
                            draggable="true"
                            @dragstart="onDragStart(index)"
                            @dragover="onDragOver"
                            @drop="onDrop(index)"
                        >
                            <td>
                                <span class="drag-handle" title="Drag to reorder">
                                    <Icon name="grip" :size="16" />
                                </span>
                            </td>
                            <td>
                                <img
                                    :src="video.thumbnail_url || '/images/logo-full.jpg'"
                                    class="admin-thumb"
                                    alt="Cover"
                                />
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--cream);">{{ video.title }}</div>
                                <div style="font-size: 0.78rem; color: #888;">{{ video.artist || '—' }}</div>
                            </td>
                            <td>
                                <div>{{ video.role || 'Model' }}</div>
                                <div style="font-size: 0.78rem; color: #777;">{{ video.director ? 'Dir: ' + video.director : '' }}</div>
                            </td>
                            <td>{{ video.year || '—' }}</td>
                            <td>
                                <span v-if="video.is_featured" class="admin-status-badge" style="background: rgba(212, 175, 122, 0.2); color: var(--gold-light);">
                                    <Icon name="star" :size="12" />
                                    <span>Hero/Top</span>
                                </span>
                                <span v-else style="color: #666; font-size: 0.8rem;">—</span>
                            </td>
                            <td>
                                <span
                                    class="admin-status-badge"
                                    :class="video.is_published ? 'admin-status-badge--published' : 'admin-status-badge--draft'"
                                >
                                    {{ video.is_published ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 0.5rem;">
                                    <Link :href="`/admin/videos/${video.id}/edit`" class="admin-btn admin-btn--secondary admin-btn--sm">
                                        <Icon name="edit" :size="14" />
                                        <span>Edit</span>
                                    </Link>
                                    <button
                                        type="button"
                                        class="admin-btn admin-btn--danger admin-btn--sm"
                                        @click="deleteVideo(video)"
                                    >
                                        <Icon name="trash" :size="14" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
