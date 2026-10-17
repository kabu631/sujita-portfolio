<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Icon from '@/Components/Icon.vue';

const props = defineProps({
    testimonials: { type: Array, required: true },
});

const list = ref([...props.testimonials]);

function deleteTestimonial(t) {
    if (confirm(`Delete testimonial from "${t.name}"?`)) {
        router.delete(`/admin/testimonials/${t.id}`, { preserveScroll: true });
    }
}

// Drag & drop
let draggedIndex = null;

function onDragStart(index) {
    draggedIndex = index;
}

function onDragOver(event) {
    event.preventDefault();
}

function onDrop(targetIndex) {
    if (draggedIndex === null || draggedIndex === targetIndex) return;

    const items = [...list.value];
    const [moved] = items.splice(draggedIndex, 1);
    items.splice(targetIndex, 0, moved);
    list.value = items;
    draggedIndex = null;

    router.post('/admin/testimonials/reorder', {
        ids: items.map((i) => i.id),
    }, { preserveScroll: true });
}
</script>

<template>
    <AdminLayout title="Testimonials & Reviews">
        <Head title="Manage Testimonials" />

        <div class="admin-card">
            <div class="admin-card__header">
                <div>
                    <h2 class="admin-card__title">Director & Client Quotes</h2>
                    <p class="admin-card__desc">Drag rows to change display order in the testimonial carousel</p>
                </div>
                <Link href="/admin/testimonials/create" class="admin-btn admin-btn--primary">
                    <Icon name="plus" :size="16" />
                    <span>New Quote</span>
                </Link>
            </div>

            <div v-if="list.length === 0" style="padding: 3rem; text-align: center; color: #777;">
                No testimonials yet. Click "New Quote" to add feedback from directors and clients.
            </div>

            <div v-else class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"></th>
                            <th>Author & Role</th>
                            <th>Quote Snippet</th>
                            <th>Status</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(t, index) in list"
                            :key="t.id"
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
                                <div style="font-weight: 600; color: var(--cream);">{{ t.name }}</div>
                                <div style="font-size: 0.78rem; color: #888;">{{ t.role || '—' }}</div>
                            </td>
                            <td style="max-width: 450px;">
                                <div style="font-style: italic; color: #bbb; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    "{{ t.quote }}"
                                </div>
                            </td>
                            <td>
                                <span
                                    class="admin-status-badge"
                                    :class="t.is_published ? 'admin-status-badge--published' : 'admin-status-badge--draft'"
                                >
                                    {{ t.is_published ? 'Active' : 'Draft' }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 0.5rem;">
                                    <Link :href="`/admin/testimonials/${t.id}/edit`" class="admin-btn admin-btn--secondary admin-btn--sm">
                                        <Icon name="edit" :size="14" />
                                        <span>Edit</span>
                                    </Link>
                                    <button
                                        type="button"
                                        class="admin-btn admin-btn--danger admin-btn--sm"
                                        @click="deleteTestimonial(t)"
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
