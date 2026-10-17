<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import BrandMark from '@/Components/BrandMark.vue';
import Icon from '@/Components/Icon.vue';

const form = useForm({
    email: 'admin@sujita.com',
    password: '',
    remember: true,
});

function submit() {
    form.post('/admin-login', {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Admin Login" />

    <div class="admin-auth-wrap">
        <div class="admin-auth-card">
            <div style="display: flex; justify-content: center; margin-bottom: 1.5rem;">
                <BrandMark :size="72" :ring="true" />
            </div>

            <h1 style="font-family: var(--serif); font-size: 1.8rem; font-weight: 500; color: var(--cream); margin-bottom: 0.25rem;">
                Management Portal
            </h1>
            <p style="font-size: 0.85rem; color: #888; margin-bottom: 2rem;">
                Sign in to manage portfolio content, media & bookings
            </p>

            <form @submit.prevent="submit" style="text-align: left;">
                <div class="admin-form-group">
                    <label class="admin-label" for="email">Email Address</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="admin-input"
                        required
                        autofocus
                    />
                    <div v-if="form.errors.email" class="admin-error">{{ form.errors.email }}</div>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label" for="password">Password</label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="admin-input"
                        required
                    />
                    <div v-if="form.errors.password" class="admin-error">{{ form.errors.password }}</div>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.75rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: #aaa; cursor: pointer;">
                        <input type="checkbox" v-model="form.remember" style="accent-color: var(--gold);" />
                        <span>Remember me</span>
                    </label>
                </div>

                <button
                    type="submit"
                    class="admin-btn admin-btn--primary"
                    style="width: 100%; padding: 0.85rem;"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Signing in...</span>
                    <span v-else>Access Dashboard</span>
                </button>
            </form>

            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.08); font-size: 0.8rem; color: #666;">
                Demo Credentials: <span style="color: var(--gold-light);">admin@sujita.com</span> / <span style="color: var(--gold-light);">Sujita@2026</span>
            </div>
        </div>
    </div>
</template>
