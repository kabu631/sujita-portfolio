<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Icon from '@/Components/Icon.vue';

const user = usePage().props.auth.user;

const form = useForm({
    name: user?.name || '',
    email: user?.email || '',
    current_password: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.put('/admin/account', {
        onSuccess: () => {
            form.reset('current_password', 'password', 'password_confirmation');
        },
    });
}
</script>

<template>
    <AdminLayout title="Account & Security">
        <Head title="Admin Account" />

        <div style="max-width: 640px;">
            <form @submit.prevent="submit" class="admin-card">
                <div class="admin-card__header">
                    <div>
                        <h2 class="admin-card__title">Profile Credentials</h2>
                        <p class="admin-card__desc">Update your admin login email or change your password</p>
                    </div>
                </div>

                <div style="display: grid; gap: 1.25rem;">
                    <div class="admin-form-group">
                        <label class="admin-label" for="acc-name">Name</label>
                        <input id="acc-name" v-model="form.name" type="text" class="admin-input" required />
                        <div v-if="form.errors.name" class="admin-error">{{ form.errors.name }}</div>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-label" for="acc-email">Login Email</label>
                        <input id="acc-email" v-model="form.email" type="email" class="admin-input" required />
                        <div v-if="form.errors.email" class="admin-error">{{ form.errors.email }}</div>
                    </div>

                    <div style="margin-top: 1rem; padding-top: 1.25rem; border-top: 1px solid rgba(255, 255, 255, 0.08);">
                        <h3 style="font-size: 0.95rem; font-weight: 600; color: var(--gold-light); margin-bottom: 0.5rem;">
                            Change Password (Optional)
                        </h3>
                        <p style="font-size: 0.8rem; color: #888; margin-bottom: 1.25rem;">
                            Leave blank if you do not wish to update your password
                        </p>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-label" for="current_password">Current Password</label>
                        <input id="current_password" v-model="form.current_password" type="password" class="admin-input" autocomplete="current-password" />
                        <div v-if="form.errors.current_password" class="admin-error">{{ form.errors.current_password }}</div>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-label" for="new_password">New Password</label>
                        <input id="new_password" v-model="form.password" type="password" class="admin-input" autocomplete="new-password" />
                        <div v-if="form.errors.password" class="admin-error">{{ form.errors.password }}</div>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-label" for="password_confirmation">Confirm New Password</label>
                        <input id="password_confirmation" v-model="form.password_confirmation" type="password" class="admin-input" autocomplete="new-password" />
                    </div>
                </div>

                <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.08); display: flex; justify-content: flex-end;">
                    <button type="submit" class="admin-btn admin-btn--primary" :disabled="form.processing">
                        <span v-if="form.processing">Saving...</span>
                        <span v-else>Update Account</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
