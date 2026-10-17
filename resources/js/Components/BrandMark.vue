<script setup>
import { useId } from 'vue';

/**
 * The SK monogram — vector recreation of the brand logo.
 * `draw` animates the ring stroke and letters in (used by the intro loader).
 */
defineProps({
    size: { type: [Number, String], default: 48 },
    ring: { type: Boolean, default: true },
    draw: { type: Boolean, default: false },
    src: { type: String, default: null }, // optional custom logo uploaded in admin
});

const gid = `skg-${useId()}`;
</script>

<template>
    <img v-if="src" :src="src" alt="Logo" class="brand-mark brand-mark--img" :style="{ width: `${size}px`, height: `${size}px` }" />
    <svg
        v-else
        class="brand-mark"
        :class="{ 'brand-mark--draw': draw }"
        :width="size"
        :height="size"
        viewBox="0 0 200 200"
        role="img"
        aria-label="SK monogram"
    >
        <defs>
            <linearGradient :id="gid" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%" stop-color="#f6e6c6" />
                <stop offset="40%" stop-color="#d4af7a" />
                <stop offset="72%" stop-color="#a8834f" />
                <stop offset="100%" stop-color="#ecd3a4" />
            </linearGradient>
        </defs>
        <circle
            v-if="ring"
            class="brand-mark__ring"
            cx="100"
            cy="100"
            r="94"
            fill="none"
            :stroke="`url(#${gid})`"
            stroke-width="2.2"
            pathLength="1"
        />
        <g class="brand-mark__letters" :fill="`url(#${gid})`" font-family="'Cormorant Garamond', Didot, 'Bodoni MT', Georgia, serif" font-weight="500">
            <text x="82" y="133" font-size="104" text-anchor="middle">S</text>
            <text x="122" y="133" font-size="104" text-anchor="middle">K</text>
        </g>
    </svg>
</template>
