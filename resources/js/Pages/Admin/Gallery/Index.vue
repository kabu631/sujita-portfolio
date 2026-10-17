<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Icon from '@/Components/Icon.vue';

const props = defineProps({
    items: { type: Array, required: true },
    categories: { type: Object, required: true },
});

const galleryItems = ref([...props.items]);
const selectedCategoryFilter = ref('all');

// Bulk upload form
const uploadForm = useForm({
    images: [],
    category: Object.keys(props.categories)[0] || 'editorial',
});

const isDraggingFiles = ref(false);

function handleFiles(files) {
    uploadForm.images = Array.from(files);
}

function submitUpload() {
    if (uploadForm.images.length === 0) return;
    uploadForm.post('/admin/gallery', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            uploadForm.reset('images');
        },
    });
}

// Inline edit state
const editingItem = ref(null);
const editForm = useForm({
    caption: '',
    category: '',
    is_published: true,
});

function startEdit(item) {
    editingItem.value = item.id;
    editForm.caption = item.caption || '';
    editForm.category = item.category;
    editForm.is_published = Boolean(item.is_published);
}

function saveEdit(item) {
    editForm.put(`/admin/gallery/${item.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            editingItem.value = null;
        },
    });
}

function cancelEdit() {
    editingItem.value = null;
}

function deleteItem(item) {
    if (confirm('Delete this gallery photo?')) {
        router.delete(`/admin/gallery/${item.id}`, {
            preserveScroll: true,
        });
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

    const items = [...galleryItems.value];
    const [moved] = items.splice(draggedIndex, 1);
    items.splice(targetIndex, 0, moved);
    galleryItems.value = items;
    draggedIndex = null;

    router.post('/admin/gallery/reorder', {
        ids: items.map((i) => i.id),
    }, { preserveScroll: true });
}
</script>

<template>
    <AdminLayout title="Photo Gallery">
        <Head title="Manage Gallery" />

        <!-- Bulk Upload Section -->
        <div class="admin-card" style="margin-bottom: 2rem;">
            <div class="admin-card__header">
                <div>
                    <h2 class="admin-card__title">
                        <Icon name="upload" :size="18" />
                        <span>Upload New Photos</span>
                    </h2>
                    <p class="admin-card__desc">Select one or multiple images to add to a category</p>
                </div>
            </div>

            <form @submit.prevent="submitUpload" style="display: grid; gap: 1.25rem;">
                <div style="display: grid; grid-template-columns: 240px 1fr; gap: 1.5rem; align-items: start;">
                    <div class="admin-form-group">
                        <label class="admin-label">Assign Category</label>
                        <select v-model="uploadForm.category" class="admin-select">
                            <option v-for="(label, key) in categories" :key="key" :value="key">
                                {{ label }}
                            </option>
                        </select>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-label">Choose Files</label>
                        <div
                            style="border: 2px dashed rgba(212, 175, 122, 0.3); border-radius: 8px; padding: 1.75rem; text-align: center; background: #1c1a17; transition: all 0.2s; cursor: pointer;"
                            :style="{ borderColor: isDraggingFiles ? 'var(--gold)' : 'rgba(212, 175, 122, 0.3)' }"
                            @dragover.prevent="isDraggingFiles = true"
                            @dragleave="isDraggingFiles = false"
                            @drop.prevent="isDraggingFiles = false; handleFiles($event.dataTransfer.files)"
                        >
                            <input
                                id="gallery-bulk"
                                type="file"
                                accept="image/*"
                                multiple
                                style="display: none;"
                                @change="handleFiles($event.target.files)"
                            />
                            <label for="gallery-bulk" style="cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                                <Icon name="upload" :size="28" style="color: var(--gold);" />
                                <span style="font-size: 0.9rem; color: var(--cream);">
                                    <strong style="color: var(--gold-light);">Click to browse</strong> or drag & drop images here
                                </span>
                                <span style="font-size: 0.75rem; color: #888;">PNG, JPG, WebP up to 8MB each</span>
                            </label>
                        </div>
                        <div v-if="uploadForm.images.length > 0" style="margin-top: 0.75rem; font-size: 0.85rem; color: var(--gold-light);">
                            {{ uploadForm.images.length }} image(s) selected ready to upload.
                        </div>
                        <div v-if="uploadForm.errors.images" class="admin-error">{{ uploadForm.errors.images }}</div>
                    </div>
                </div>

                <div v-if="uploadForm.images.length > 0" style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="admin-btn admin-btn--primary" :disabled="uploadForm.processing">
                        <span v-if="uploadForm.processing">Uploading...</span>
                        <span v-else>Upload {{ uploadForm.images.length }} Photos</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Gallery Grid & Filter -->
        <div class="admin-card">
            <div class="admin-card__header">
                <div>
                    <h2 class="admin-card__title">Gallery Items ({{ items.length }})</h2>
                    <p class="admin-card__desc">Drag cards to reorder how they appear in the public masonry layout</p>
                </div>

                <!-- Filter -->
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <button
                        type="button"
                        class="admin-btn admin-btn--sm"
                        :class="selectedCategoryFilter === 'all' ? 'admin-btn--primary' : 'admin-btn--secondary'"
                        @click="selectedCategoryFilter = 'all'"
                    >
                        All ({{ items.length }})
                    </button>
                    <button
                        v-for="(label, key) in categories"
                        :key="key"
                        type="button"
                        class="admin-btn admin-btn--sm"
                        :class="selectedCategoryFilter === key ? 'admin-btn--primary' : 'admin-btn--secondary'"
                        @click="selectedCategoryFilter = key"
                    >
                        {{ label }}
                    </button>
                </div>
            </div>

            <div v-if="items.length === 0" style="padding: 3rem; text-align: center; color: #777;">
                No gallery photos found. Upload some photos above!
            </div>

            <div
                v-else
                style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1.5rem;"
            >
                <div
                    v-for="(item, index) in galleryItems"
                    :key="item.id"
                    v-show="selectedCategoryFilter === 'all' || item.category === selectedCategoryFilter"
                    draggable="true"
                    @dragstart="onDragStart(index)"
                    @dragover="onDragOver"
                    @drop="onDrop(index)"
                    style="background: #1c1a17; border: 1px solid rgba(212, 175, 122, 0.15); border-radius: 8px; overflow: hidden; display: flex; flex-direction: column;"
                >
                    <!-- Image with drag handle -->
                    <div style="position: relative; aspect-ratio: 4/5; background: #000; overflow: hidden;">
                        <img :src="item.image_url" alt="Gallery item" style="width: 100%; height: 100%; object-fit: cover;" />
                        <span
                            class="drag-handle"
                            style="position: absolute; top: 8px; left: 8px; background: rgba(0, 0, 0, 0.6); border-radius: 4px; padding: 4px;"
                            title="Drag to reorder"
                        >
                            <Icon name="grip" :size="16" style="color: #fff;" />
                        </span>
                        <span
                            class="admin-status-badge"
                            :class="item.is_published ? 'admin-status-badge--published' : 'admin-status-badge--draft'"
                            style="position: absolute; top: 8px; right: 8px;"
                        >
                            {{ item.is_published ? 'Visible' : 'Hidden' }}
                        </span>
                    </div>

                    <!-- Details / Inline Edit -->
                    <div style="padding: 1rem; flex: 1; display: flex; flex-direction: column; justify-content: space-between; gap: 0.75rem;">
                        <div v-if="editingItem !== item.id">
                            <div style="font-size: 0.7rem; letter-spacing: 0.12em; text-transform: uppercase; color: var(--gold);">
                                {{ categories[item.category] || item.category }}
                            </div>
                            <div style="font-size: 0.9rem; color: var(--cream); margin-top: 0.25rem;">
                                {{ item.caption || 'No caption' }}
                            </div>
                        </div>

                        <!-- Edit Form -->
                        <div v-else style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <input v-model="editForm.caption" type="text" class="admin-input" placeholder="Caption..." style="padding: 0.4rem 0.6rem; font-size: 0.8rem;" />
                            <select v-model="editForm.category" class="admin-select" style="padding: 0.4rem 0.6rem; font-size: 0.8rem;">
                                <option v-for="(label, key) in categories" :key="key" :value="key">
                                    {{ label }}
                                </option>
                            </select>
                            <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; color: #bbb;">
                                <input type="checkbox" v-model="editForm.is_published" style="accent-color: var(--gold);" />
                                <span>Published</span>
                            </label>
                        </div>

                        <!-- Actions -->
                        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255, 255, 255, 0.05); padding-top: 0.65rem;">
                            <div v-if="editingItem !== item.id" style="display: flex; gap: 0.4rem;">
                                <button type="button" class="admin-btn admin-btn--secondary admin-btn--sm" @click="startEdit(item)">
                                    <Icon name="edit" :size="12" />
                                    <span>Edit</span>
                                </button>
                                <button type="button" class="admin-btn admin-btn--danger admin-btn--sm" @click="deleteItem(item)">
                                    <Icon name="trash" :size="12" />
                                </button>
                            </div>
                            <div v-else style="display: flex; gap: 0.4rem; width: 100%;">
                                <button type="button" class="admin-btn admin-btn--primary admin-btn--sm" style="flex: 1;" @click="saveEdit(item)">
                                    Save
                                </button>
                                <button type="button" class="admin-btn admin-btn--secondary admin-btn--sm" @click="cancelEdit">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
