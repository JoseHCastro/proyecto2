<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useUrl } from '@/composables/useUrl';
import { computed } from 'vue';

const props = defineProps<{
    href: string;
    method?: 'get' | 'post' | 'put' | 'patch' | 'delete';
    as?: string;
    data?: object;
    preserveScroll?: boolean;
    preserveState?: boolean;
    replace?: boolean;
    only?: string[];
    except?: string[];
    onCancelToken?: (cancelToken: any) => void;
    onBefore?: () => void;
    onStart?: () => void;
    onProgress?: (progress: any) => void;
    onFinish?: () => void;
    onCancel?: () => void;
    onSuccess?: () => void;
    onError?: (errors: any) => void;
}>();

const { url } = useUrl();

const computedHref = computed(() => url(props.href));
</script>

<template>
    <Link
        :href="computedHref"
        :method="method"
        :as="as"
        :data="data"
        :preserve-scroll="preserveScroll"
        :preserve-state="preserveState"
        :replace="replace"
        :only="only"
        :except="except"
    >
        <slot />
    </Link>
</template>
