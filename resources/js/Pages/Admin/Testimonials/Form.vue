<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Icon from '@/Components/Icon.vue';

const props = defineProps({
    testimonial: { type: Object, default: null },
});

const isEditing = Boolean(props.testimonial?.id);

const form = useForm({
    name: props.testimonial?.name || '',
    role: props.testimonial?.role || '',
    quote: props.testimonial?.quote || '',
    is_published: props.testimonial ? Boolean(props.testimonial.is_published) : true,
});

function submit() {
    if (isEditing) {
        form.put(`/admin/testimonials/${props.testimonial.id}`);
    } else {
        form.post('/admin/testimonials');
    }
}
</script>

<template>
    <AdminLayout :title="isEditing ? 'Edit Testimonial' : 'New Testimonial'">
        <Head :title="isEditing ? 'Edit Testimonial' : 'New Testimonial'" />

        <div style="max-width: 720px;">
            <div style="margin-bottom: 1.5rem;">
                <Link href="/admin/testimonials" class="admin-btn admin-btn--secondary admin-btn--sm">
                    <Icon name="arrow-left" :size="14" />
                    <span>Back to Testimonials</span>
                </Link>
            </div>

            <form @submit.prevent="submit" class="admin-card">
                <div class="admin-card__header">
                    <div>
                        <h2 class="admin-card__title">{{ isEditing ? 'Edit Testimonial' : 'New Testimonial' }}</h2>
                        <p class="admin-card__desc">Add praise or feedback from a director, producer or photographer</p>
                    </div>
                </div>

                <div style="display: grid; gap: 1.25rem;">
                    <div class="admin-form-group">
                        <label class="admin-label" for="name">Person Name *</label>
                        <input id="name" v-model="form.name" type="text" class="admin-input" required placeholder="e.g. Robin Sharma" />
                        <div v-if="form.errors.name" class="admin-error">{{ form.errors.name }}</div>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-label" for="role">Title / Organization / Project</label>
                        <input id="role" v-model="form.role" type="text" class="admin-input" placeholder="e.g. Music Video Director · 'Saanjh'" />
                        <div v-if="form.errors.role" class="admin-error">{{ form.errors.role }}</div>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-label" for="quote">Quote / Review *</label>
                        <textarea
                            id="quote"
                            v-model="form.quote"
                            class="admin-textarea"
                            rows="5"
                            required
                            placeholder="What did they say about working with Sujita?"
                        />
                        <div v-if="form.errors.quote" class="admin-error">{{ form.errors.quote }}</div>
                    </div>

                    <div class="admin-form-group">
                        <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer;">
                            <input type="checkbox" v-model="form.is_published" style="accent-color: var(--gold); width: 18px; height: 18px;" />
                            <span style="font-size: 0.88rem; font-weight: 500;">Published on website</span>
                        </label>
                    </div>
                </div>

                <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.08); display: flex; justify-content: flex-end; gap: 1rem;">
                    <Link href="/admin/testimonials" class="admin-btn admin-btn--secondary">
                        Cancel
                    </Link>
                    <button type="submit" class="admin-btn admin-btn--primary" :disabled="form.processing">
                        <span v-if="form.processing">Saving...</span>
                        <span v-else>{{ isEditing ? 'Update Quote' : 'Save Quote' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
