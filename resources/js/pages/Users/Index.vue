<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { UserPlus, Eye, Pencil, Trash2 } from 'lucide-vue-next';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { confirmAlert, successAlert, errorAlert } from '@/composables/useSweetAlert';

const props = defineProps({
    users: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const role = ref(props.filters?.role || '');

let searchTimeout = null;

watch(search, (value) => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    searchTimeout = setTimeout(() => {
        const params = {};
        if (value) {
            params.search = value;
        }
        if (role.value) {
            params.role = role.value;
        }
        router.get('/users', params, { replace: true, preserveScroll: true });
    }, 300);
});

watch(role, (value) => {
    const params = {};
    if (search.value) {
        params.search = search.value;
    }
    if (value) {
        params.role = value;
    }
    router.get('/users', params, { replace: true, preserveScroll: true });
});

const deleteUser = async (id) => {
    const result = await confirmAlert({
        title: '¿Eliminar usuario?',
        text: 'Esta acción no se puede deshacer',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        router.delete(`/users/${id}`, {
            onSuccess: () => {
                successAlert({
                    title: '¡Eliminado!',
                    text: 'El usuario ha sido eliminado correctamente'
                });
            },
            onError: () => {
                errorAlert({
                    title: 'Error',
                    text: 'No se pudo eliminar el usuario'
                });
            }
        });
    }
};
</script>

<template>

    <Head title="Gestión de Usuarios" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Gestión de Usuarios
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-background shadow-sm sm:rounded-lg border border-border">
                    <div class="p-6 text-foreground">
                        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                            <div class="flex gap-4 w-full md:w-auto">
                                <Input v-model="search" placeholder="Buscar por nombre, email o CI..."
                                    class="w-full md:w-64" />
                                <Select v-model="role">
                                    <SelectTrigger class="w-[180px]">
                                        <SelectValue placeholder="Filtrar por rol" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">Todos</SelectItem>
                                        <SelectItem value="personal">Personal</SelectItem>
                                        <SelectItem value="cliente">Clientes</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <Button as-child>
                                <AppLink href="/users/create">
                                <UserPlus class="mr-2 h-4 w-4" />
                                Nuevo Usuario
                                </AppLink>
                            </Button>
                        </div>

                        <div class="rounded-md border" v-if="users && users.data">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Nombre</TableHead>
                                        <TableHead>Email</TableHead>
                                        <TableHead>Rol</TableHead>
                                        <TableHead>Estado</TableHead>
                                        <TableHead class="text-right">Acciones</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="user in users.data" :key="user.id">
                                        <TableCell class="font-medium">{{ user.name }}</TableCell>
                                        <TableCell>{{ user.email }}</TableCell>
                                        <TableCell>
                                            <Badge variant="outline" v-for="userRole in user.roles" :key="userRole.id"
                                                class="mr-1">
                                                {{ userRole.name }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            <Badge :variant="user.estado === 'activo' ? 'default' : 'destructive'">
                                                {{ user.estado }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <Button variant="outline" size="sm" as-child title="Ver">
                                                    <Link :href="`/users/${user.id}`">
                                                    <Eye class="h-4 w-4" />
                                                    </AppLink>
                                                </Button>
                                                <Button variant="outline" size="sm" as-child title="Editar">
                                                    <Link :href="`/users/${user.id}/edit`">
                                                    <Pencil class="h-4 w-4" />
                                                    </AppLink>
                                                </Button>
                                                <Button variant="destructive" size="sm" @click="deleteUser(user.id)"
                                                    title="Eliminar">
                                                    <Trash2 class="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4 flex justify-center" v-if="users && users.links && users.links.length > 3">
                            <div class="flex gap-1">
                                <Link v-for="(link, k) in users.links" :key="k" :href="link.url || '#'"
                                    v-html="link.label" class="px-3 py-1 border rounded text-sm"
                                    :class="{ 'bg-primary text-primary-foreground': link.active, 'opacity-50 cursor-not-allowed': !link.url }" />
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>