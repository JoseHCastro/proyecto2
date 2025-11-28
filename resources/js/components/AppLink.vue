<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { toUrl } from '@/utils/routes';

interface Props {
    href: string;
    method?: 'get' | 'post' | 'put' | 'patch' | 'delete';
    as?: string;
    data?: object;
    preserveScroll?: boolean;
    preserveState?: boolean;
    replace?: boolean;
    only?: string[];
    headers?: object;
}

const props = withDefaults(defineProps<Props>(), {
    method: 'get',
});

// Compute the full URL with base path prefix
const fullHref = computed(() => {
    // Only transform relative paths starting with /
    if (props.href.startsWith('/')) {
        return toUrl(props.href);
    }
    // Return as-is for external URLs or anchors
    return props.href;
});
</script>

<template>
    <Link
        :href="fullHref"
        :method="method"
        :as="as"
        :data="data"
        :preserve-scroll="preserveScroll"
        :preserve-state="preserveState"
        :replace="replace"
        :only="only"
        :headers="headers"
    >
        <slot />
    </Link>
</template>
