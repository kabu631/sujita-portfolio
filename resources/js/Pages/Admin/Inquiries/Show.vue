<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Icon from '@/Components/Icon.vue';

const props = defineProps({
    inquiry: { type: Object, required: true },
});

function markUnread() {
    router.patch(`/admin/inquiries/${props.inquiry.id}/unread`);
}

function deleteInquiry() {
    if (confirm('Delete this inquiry?')) {
        router.delete(`/admin/inquiries/${props.inquiry.id}`);
    }
}
</script>

<template>
    <AdminLayout :title="`Inquiry from ${inquiry.name}`">
        <Head :title="`Inquiry: ${inquiry.name}`" />

        <div style="max-width: 820px;">
            <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                <Link href="/admin/inquiries" class="admin-btn admin-btn--secondary admin-btn--sm">
                    <Icon name="arrow-left" :size="14" />
                    <span>Back to Inquiries</span>
                </Link>

                <div style="display: flex; gap: 0.5rem;">
                    <button type="button" class="admin-btn admin-btn--secondary admin-btn--sm" @click="markUnread">
                        <Icon name="inbox" :size="14" />
                        <span>Mark as Unread</span>
                    </button>
                    <button type="button" class="admin-btn admin-btn--danger admin-btn--sm" @click="deleteInquiry">
                        <Icon name="trash" :size="14" />
                        <span>Delete</span>
                    </button>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card__header">
                    <div>
                        <h2 class="admin-card__title">{{ inquiry.name }}</h2>
                        <p class="admin-card__desc">
                            Received on {{ new Date(inquiry.created_at).toLocaleString() }}
                        </p>
                    </div>
                    <a
                        :href="`mailto:${inquiry.email}?subject=Re: Your inquiry for Sujita Khakural`"
                        class="admin-btn admin-btn--primary"
                    >
                        <Icon name="mail" :size="16" />
                        <span>Reply by Email</span>
                    </a>
                </div>

                <!-- Info Grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                    <div>
                        <div class="admin-label">Email</div>
                        <a :href="`mailto:${inquiry.email}`" style="color: var(--gold-light); text-decoration: underline;">
                            {{ inquiry.email }}
                        </a>
                    </div>
                    <div>
                        <div class="admin-label">Phone</div>
                        <div style="color: var(--cream);">{{ inquiry.phone || 'Not provided' }}</div>
                    </div>
                    <div>
                        <div class="admin-label">Company / Brand</div>
                        <div style="color: var(--cream);">{{ inquiry.company || 'Not provided' }}</div>
                    </div>
                    <div>
                        <div class="admin-label">Project Type</div>
                        <div style="color: var(--cream); font-weight: 600;">{{ inquiry.project_type || 'General' }}</div>
                    </div>
                    <div>
                        <div class="admin-label">Target Shoot Date</div>
                        <div style="color: var(--cream);">{{ inquiry.shoot_date || 'Flexible / TBD' }}</div>
                    </div>
                </div>

                <!-- Message -->
                <div>
                    <div class="admin-label">Project Message</div>
                    <div
                        style="background: #1c1a17; border: 1px solid rgba(212, 175, 122, 0.15); border-radius: 8px; padding: 1.5rem; color: #ede5da; line-height: 1.7; white-space: pre-wrap;"
                    >
                        {{ inquiry.message }}
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
