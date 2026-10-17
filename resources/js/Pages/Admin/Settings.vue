<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Icon from '@/Components/Icon.vue';

const props = defineProps({
    groups: { type: Array, required: true },
    values: { type: Object, required: true },
});

const groupIcons = {
    brand: 'sparkle',
    hero: 'film',
    about: 'user',
    measurements: 'grid',
    contact: 'mail',
    social: 'globe',
};

function getInitialTab() {
    if (typeof window !== 'undefined') {
        const params = new URLSearchParams(window.location.search);
        const tab = params.get('tab');
        if (tab && props.groups.some((g) => g.key === tab)) {
            return tab;
        }
    }
    return props.groups[0]?.key || 'brand';
}

const activeTab = ref(getInitialTab());

function selectTab(key) {
    activeTab.value = key;
    if (typeof window !== 'undefined') {
        const url = new URL(window.location);
        url.searchParams.set('tab', key);
        window.history.replaceState({}, '', url);
    }
}

// Initialize form data from props.values
const initialData = {};
props.groups.forEach((g) => {
    g.fields.forEach((f) => {
        initialData[f.key] = props.values[f.key] ?? '';
    });
});

const form = useForm({
    ...initialData,
});

const imagePreviews = ref({});

function handleFileChange(event, key) {
    const file = event.target.files[0];
    if (file) {
        form[key] = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreviews.value[key] = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

function removeImage(key) {
    form[key] = null;
    form[`remove_${key}`] = true;
    imagePreviews.value[key] = null;
}

function submit() {
    form.post('/admin/settings', {
        preserveScroll: true,
        forceFormData: true,
    });
}
</script>

<template>
    <AdminLayout title="Site Settings & Bio">
        <Head title="Site Settings" />

        <div style="display: grid; grid-template-columns: 240px 1fr; gap: 2rem; align-items: start;">
            <!-- Tabs Sidebar -->
            <div class="admin-card" style="padding: 0.85rem; position: sticky; top: 90px;">
                <nav style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <button
                        v-for="group in groups"
                        :key="group.key"
                        type="button"
                        class="admin-nav-item"
                        :class="{ 'is-active': activeTab === group.key }"
                        style="width: 100%; text-align: left; border: none; cursor: pointer;"
                        @click="selectTab(group.key)"
                    >
                        <Icon v-if="groupIcons[group.key]" :name="groupIcons[group.key]" :size="16" />
                        <span>{{ group.label }}</span>
                    </button>
                </nav>
            </div>

            <!-- Form Content -->
            <form @submit.prevent="submit">
                <div
                    v-for="group in groups"
                    :key="group.key"
                    v-show="activeTab === group.key"
                    class="admin-card"
                >
                    <div class="admin-card__header">
                        <div>
                            <h2 class="admin-card__title">{{ group.label }}</h2>
                            <p v-if="group.description" class="admin-card__desc">{{ group.description }}</p>
                        </div>
                        <button type="submit" class="admin-btn admin-btn--primary" :disabled="form.processing">
                            <span v-if="form.processing">Saving...</span>
                            <span v-else>Save Changes</span>
                        </button>
                    </div>

                    <div style="display: grid; gap: 1.5rem;">
                        <div v-for="field in group.fields" :key="field.key" class="admin-form-group">
                            <label class="admin-label" :for="field.key" style="display: flex; align-items: center; gap: 0.5rem;">
                                <Icon
                                    v-if="['instagram', 'facebook', 'linkedin', 'tiktok', 'youtube'].includes(field.key)"
                                    :name="field.key"
                                    :size="16"
                                    style="color: var(--gold);"
                                />
                                <span>{{ field.label }}</span>
                            </label>

                            <!-- Textarea -->
                            <textarea
                                v-if="field.type === 'textarea'"
                                :id="field.key"
                                v-model="form[field.key]"
                                class="admin-textarea"
                                rows="4"
                            />

                            <!-- Image Upload -->
                            <div v-else-if="field.type === 'image'">
                                <div style="display: flex; gap: 1.5rem; align-items: center; margin-bottom: 0.75rem;">
                                    <div
                                        style="width: 110px; height: 110px; border-radius: 8px; border: 1px dashed rgba(212, 175, 122, 0.3); display: grid; place-items: center; overflow: hidden; background: #1c1a17;"
                                    >
                                        <img
                                            v-if="imagePreviews[field.key] || form[field.key]"
                                            :src="imagePreviews[field.key] || form[field.key]"
                                            alt="Preview"
                                            style="width: 100%; height: 100%; object-fit: cover;"
                                        />
                                        <Icon v-else name="image" :size="32" style="color: #555;" />
                                    </div>
                                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                        <input
                                            type="file"
                                            :id="field.key"
                                            accept="image/*"
                                            style="display: none;"
                                            @change="(e) => handleFileChange(e, field.key)"
                                        />
                                        <label :for="field.key" class="admin-btn admin-btn--secondary admin-btn--sm" style="cursor: pointer;">
                                            <Icon name="upload" :size="14" />
                                            <span>Upload New Image</span>
                                        </label>
                                        <button
                                            v-if="form[field.key] || imagePreviews[field.key]"
                                            type="button"
                                            class="admin-btn admin-btn--danger admin-btn--sm"
                                            @click="removeImage(field.key)"
                                        >
                                            <Icon name="trash" :size="14" />
                                            <span>Remove</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Regular Inputs: text, email, url -->
                            <input
                                v-else
                                :id="field.key"
                                v-model="form[field.key]"
                                :type="field.type"
                                class="admin-input"
                            />

                            <p v-if="field.help" class="admin-help">{{ field.help }}</p>
                            <p v-if="form.errors[field.key]" class="admin-error">{{ form.errors[field.key] }}</p>
                        </div>
                    </div>

                    <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.08); display: flex; justify-content: flex-end;">
                        <button type="submit" class="admin-btn admin-btn--primary" :disabled="form.processing">
                            <span v-if="form.processing">Saving...</span>
                            <span v-else>Save Changes</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
