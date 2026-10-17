<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Icon from '@/Components/Icon.vue';

const props = defineProps({
    video: { type: Object, default: null },
});

const isEditing = Boolean(props.video?.id);

const form = useForm({
    title: props.video?.title || '',
    artist: props.video?.artist || '',
    role: props.video?.role || 'Lead Model',
    director: props.video?.director || '',
    year: props.video?.year || new Date().getFullYear(),
    video_url: props.video?.video_url || '',
    description: props.video?.description || '',
    is_featured: props.video ? Boolean(props.video.is_featured) : false,
    is_published: props.video ? Boolean(props.video.is_published) : true,
    thumbnail: null,
    remove_thumbnail: false,
});

const thumbnailPreview = ref(props.video?.thumbnail_url || null);

function handleFileChange(event) {
    const file = event.target.files[0];
    if (file) {
        form.thumbnail = file;
        form.remove_thumbnail = false;
        const reader = new FileReader();
        reader.onload = (e) => {
            thumbnailPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

function removeThumbnail() {
    form.thumbnail = null;
    form.remove_thumbnail = true;
    thumbnailPreview.value = null;
}

function submit() {
    if (isEditing) {
        // Inertia multipart PUT works best via POST with _method spoofing or standard FormData
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(`/admin/videos/${props.video.id}`, {
            forceFormData: true,
        });
    } else {
        form.post('/admin/videos', {
            forceFormData: true,
        });
    }
}
</script>

<template>
    <AdminLayout :title="isEditing ? 'Edit Music Video' : 'Add Music Video'">
        <Head :title="isEditing ? 'Edit Video' : 'New Video'" />

        <div style="max-width: 860px;">
            <div style="margin-bottom: 1.5rem;">
                <Link href="/admin/videos" class="admin-btn admin-btn--secondary admin-btn--sm">
                    <Icon name="arrow-left" :size="14" />
                    <span>Back to Videos</span>
                </Link>
            </div>

            <form @submit.prevent="submit" class="admin-card">
                <div class="admin-card__header">
                    <div>
                        <h2 class="admin-card__title">{{ isEditing ? `Edit "${video.title}"` : 'New Music Video' }}</h2>
                        <p class="admin-card__desc">Provide details and link to the YouTube/Vimeo video</p>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                    <div class="admin-form-group" style="grid-column: span 2;">
                        <label class="admin-label" for="title">Video Title *</label>
                        <input id="title" v-model="form.title" type="text" class="admin-input" required placeholder="e.g. Saanjh" />
                        <div v-if="form.errors.title" class="admin-error">{{ form.errors.title }}</div>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-label" for="artist">Singer / Artist</label>
                        <input id="artist" v-model="form.artist" type="text" class="admin-input" placeholder="e.g. Featured Musician" />
                        <div v-if="form.errors.artist" class="admin-error">{{ form.errors.artist }}</div>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-label" for="role">Sujita's Role</label>
                        <input id="role" v-model="form.role" type="text" class="admin-input" placeholder="e.g. Lead Model, Female Protagonist" />
                        <div v-if="form.errors.role" class="admin-error">{{ form.errors.role }}</div>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-label" for="director">Director</label>
                        <input id="director" v-model="form.director" type="text" class="admin-input" placeholder="e.g. Director Name" />
                        <div v-if="form.errors.director" class="admin-error">{{ form.errors.director }}</div>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-label" for="year">Release Year</label>
                        <input id="year" v-model="form.year" type="number" class="admin-input" min="2000" max="2100" />
                        <div v-if="form.errors.year" class="admin-error">{{ form.errors.year }}</div>
                    </div>

                    <div class="admin-form-group" style="grid-column: span 2;">
                        <label class="admin-label" for="video_url">Video URL (YouTube or Vimeo)</label>
                        <input
                            id="video_url"
                            v-model="form.video_url"
                            type="url"
                            class="admin-input"
                            placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/..."
                        />
                        <p class="admin-help">Supports full YouTube, youtu.be, Shorts and Vimeo URLs. The site automatically creates responsive embeds.</p>
                        <div v-if="form.errors.video_url" class="admin-error">{{ form.errors.video_url }}</div>
                    </div>

                    <div class="admin-form-group" style="grid-column: span 2;">
                        <label class="admin-label">Custom Cover Image (Optional)</label>
                        <div style="display: flex; gap: 1.5rem; align-items: center; margin-top: 0.5rem;">
                            <div style="width: 160px; aspect-ratio: 16/9; border-radius: 8px; border: 1px dashed rgba(212, 175, 122, 0.3); overflow: hidden; background: #1c1a17; display: grid; place-items: center;">
                                <img
                                    v-if="thumbnailPreview"
                                    :src="thumbnailPreview"
                                    alt="Preview"
                                    style="width: 100%; height: 100%; object-fit: cover;"
                                />
                                <Icon v-else name="image" :size="32" style="color: #555;" />
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <input id="thumbnail" type="file" accept="image/*" style="display: none;" @change="handleFileChange" />
                                <label for="thumbnail" class="admin-btn admin-btn--secondary admin-btn--sm" style="cursor: pointer;">
                                    <Icon name="upload" :size="14" />
                                    <span>Upload Cover</span>
                                </label>
                                <button
                                    v-if="thumbnailPreview"
                                    type="button"
                                    class="admin-btn admin-btn--danger admin-btn--sm"
                                    @click="removeThumbnail"
                                >
                                    <Icon name="trash" :size="14" />
                                    <span>Remove Cover</span>
                                </button>
                                <p class="admin-help">If omitted and YouTube URL is provided, the official YouTube HD thumbnail is used automatically.</p>
                            </div>
                        </div>
                    </div>

                    <div class="admin-form-group" style="grid-column: span 2;">
                        <label class="admin-label" for="description">Story / Production Notes</label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            class="admin-textarea"
                            rows="4"
                            placeholder="Brief context about the song, mood, theme and Sujita's look..."
                        />
                        <div v-if="form.errors.description" class="admin-error">{{ form.errors.description }}</div>
                    </div>

                    <div class="admin-form-group" style="grid-column: span 2; display: flex; gap: 2rem; align-items: center; padding-top: 0.5rem;">
                        <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer;">
                            <input type="checkbox" v-model="form.is_featured" style="accent-color: var(--gold); width: 18px; height: 18px;" />
                            <span style="font-size: 0.88rem; font-weight: 500;">Feature on Home Hero / Top highlight</span>
                        </label>

                        <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer;">
                            <input type="checkbox" v-model="form.is_published" style="accent-color: var(--gold); width: 18px; height: 18px;" />
                            <span style="font-size: 0.88rem; font-weight: 500;">Published (visible on website)</span>
                        </label>
                    </div>
                </div>

                <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.08); display: flex; justify-content: flex-end; gap: 1rem;">
                    <Link href="/admin/videos" class="admin-btn admin-btn--secondary">
                        Cancel
                    </Link>
                    <button type="submit" class="admin-btn admin-btn--primary" :disabled="form.processing">
                        <span v-if="form.processing">Saving...</span>
                        <span v-else>{{ isEditing ? 'Update Video' : 'Create Video' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
