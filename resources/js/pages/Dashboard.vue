<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard as _dashboard } from '@/routes';
import { wrapRoute } from '@/utils/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, watch, computed } from 'vue';
import { Bar, Pie } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    ArcElement,
} from 'chart.js';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { DollarSign, Users, TrendingUp, Package } from 'lucide-vue-next';
import axios from 'axios';

// Registrar componentes de Chart.js
ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);

const dashboard = wrapRoute(_dashboard);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

// Estado para filtros
const periodoIngresos = ref('semana');
const periodoSuscripciones = ref('semana');

// Estado para datos
const ingresosData = ref<{ paquete: string; ingresos: number }[]>([]);
const suscripcionesData = ref<{ paquete: string; cantidad: number }[]>([]);
const loadingIngresos = ref(false);
const loadingSuscripciones = ref(false);

// Colores para los temas - colores vibrantes que funcionan con todos los temas
const chartColors = [
    'rgba(59, 130, 246, 0.8)',   // blue
    'rgba(16, 185, 129, 0.8)',   // green
    'rgba(245, 158, 11, 0.8)',   // amber
    'rgba(239, 68, 68, 0.8)',    // red
    'rgba(139, 92, 246, 0.8)',   // purple
    'rgba(236, 72, 153, 0.8)',   // pink
    'rgba(20, 184, 166, 0.8)',   // teal
    'rgba(249, 115, 22, 0.8)',   // orange
];

const chartColorsBorder = [
    'rgba(59, 130, 246, 1)',
    'rgba(16, 185, 129, 1)',
    'rgba(245, 158, 11, 1)',
    'rgba(239, 68, 68, 1)',
    'rgba(139, 92, 246, 1)',
    'rgba(236, 72, 153, 1)',
    'rgba(20, 184, 166, 1)',
    'rgba(249, 115, 22, 1)',
];

// Configuración del gráfico de barras
const barChartData = computed(() => ({
    labels: ingresosData.value.map(item => item.paquete),
    datasets: [
        {
            label: 'Ingresos (Bs)',
            data: ingresosData.value.map(item => item.ingresos),
            backgroundColor: chartColors.slice(0, ingresosData.value.length),
            borderColor: chartColorsBorder.slice(0, ingresosData.value.length),
            borderWidth: 2,
            borderRadius: 8,
        },
    ],
}));

const barChartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#fff',
            bodyColor: '#fff',
            padding: 12,
            cornerRadius: 8,
            callbacks: {
                label: (context: any) => `Bs ${context.raw.toFixed(2)}`,
            },
        },
    },
    scales: {
        x: {
            grid: {
                display: false,
            },
            ticks: {
                color: 'currentColor',
            },
        },
        y: {
            beginAtZero: true,
            grid: {
                color: 'rgba(128, 128, 128, 0.2)',
            },
            ticks: {
                color: 'currentColor',
                callback: (value: number) => `Bs ${value}`,
            },
        },
    },
}));

// Configuración del gráfico de pastel
const pieChartData = computed(() => ({
    labels: suscripcionesData.value.map(item => item.paquete),
    datasets: [
        {
            data: suscripcionesData.value.map(item => item.cantidad),
            backgroundColor: chartColors.slice(0, suscripcionesData.value.length),
            borderColor: chartColorsBorder.slice(0, suscripcionesData.value.length),
            borderWidth: 2,
        },
    ],
}));

const pieChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom' as const,
            labels: {
                padding: 20,
                usePointStyle: true,
                pointStyle: 'circle',
            },
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#fff',
            bodyColor: '#fff',
            padding: 12,
            cornerRadius: 8,
            callbacks: {
                label: (context: any) => `${context.label}: ${context.raw} suscripciones`,
            },
        },
    },
};

// Funciones para cargar datos
const cargarIngresos = async () => {
    loadingIngresos.value = true;
    try {
        const response = await axios.get('/dashboard/ingresos-por-paquete', {
            params: { periodo: periodoIngresos.value },
        });
        ingresosData.value = response.data;
    } catch (error) {
        console.error('Error cargando ingresos:', error);
    } finally {
        loadingIngresos.value = false;
    }
};

const cargarSuscripciones = async () => {
    loadingSuscripciones.value = true;
    try {
        const response = await axios.get('/dashboard/suscripciones-por-paquete', {
            params: { periodo: periodoSuscripciones.value },
        });
        suscripcionesData.value = response.data;
    } catch (error) {
        console.error('Error cargando suscripciones:', error);
    } finally {
        loadingSuscripciones.value = false;
    }
};

// Estadísticas calculadas
const totalIngresos = computed(() => 
    ingresosData.value.reduce((sum, item) => sum + item.ingresos, 0)
);

const totalSuscripciones = computed(() => 
    suscripcionesData.value.reduce((sum, item) => sum + item.cantidad, 0)
);

const paqueteMasPopular = computed(() => {
    if (suscripcionesData.value.length === 0) return 'N/A';
    const max = suscripcionesData.value.reduce((prev, curr) => 
        prev.cantidad > curr.cantidad ? prev : curr
    );
    return max.cantidad > 0 ? max.paquete : 'N/A';
});

// Watchers para recargar cuando cambia el periodo
watch(periodoIngresos, cargarIngresos);
watch(periodoSuscripciones, cargarSuscripciones);

// Cargar datos al montar
onMounted(() => {
    cargarIngresos();
    cargarSuscripciones();
});

const getPeriodoLabel = (periodo: string) => {
    switch (periodo) {
        case 'semana': return 'Última semana';
        case 'mes': return 'Último mes';
        case 'año': return 'Último año';
        default: return 'Última semana';
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <!-- Cards de resumen -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card class="border-l-4 border-l-blue-500">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Ingresos Totales</CardTitle>
                        <DollarSign class="h-4 w-4 text-blue-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">Bs {{ totalIngresos.toFixed(2) }}</div>
                        <p class="text-xs text-muted-foreground">{{ getPeriodoLabel(periodoIngresos) }}</p>
                    </CardContent>
                </Card>
                
                <Card class="border-l-4 border-l-green-500">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Suscripciones</CardTitle>
                        <Users class="h-4 w-4 text-green-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ totalSuscripciones }}</div>
                        <p class="text-xs text-muted-foreground">{{ getPeriodoLabel(periodoSuscripciones) }}</p>
                    </CardContent>
                </Card>
                
                <Card class="border-l-4 border-l-amber-500">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Paquetes Activos</CardTitle>
                        <Package class="h-4 w-4 text-amber-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ ingresosData.length }}</div>
                        <p class="text-xs text-muted-foreground">Paquetes con datos</p>
                    </CardContent>
                </Card>
                
                <Card class="border-l-4 border-l-purple-500">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Más Popular</CardTitle>
                        <TrendingUp class="h-4 w-4 text-purple-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-xl font-bold truncate">{{ paqueteMasPopular }}</div>
                        <p class="text-xs text-muted-foreground">Paquete con más suscripciones</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Gráficos -->
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Gráfico de Barras - Ingresos -->
                <Card class="col-span-1">
                    <CardHeader>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <CardTitle class="flex items-center gap-2">
                                    <DollarSign class="h-5 w-5 text-primary" />
                                    Ingresos por Paquete
                                </CardTitle>
                                <CardDescription>
                                    Ingresos totales en Bolivianos por cada paquete
                                </CardDescription>
                            </div>
                            <Select v-model="periodoIngresos">
                                <SelectTrigger class="w-[160px]">
                                    <SelectValue placeholder="Periodo" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="semana">Última semana</SelectItem>
                                    <SelectItem value="mes">Último mes</SelectItem>
                                    <SelectItem value="año">Último año</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="relative h-[300px] sm:h-[350px]">
                            <div v-if="loadingIngresos" class="absolute inset-0 flex items-center justify-center bg-background/50">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                            </div>
                            <Bar 
                                v-if="ingresosData.length > 0" 
                                :data="barChartData" 
                                :options="barChartOptions"
                            />
                            <div v-else-if="!loadingIngresos" class="flex items-center justify-center h-full text-muted-foreground">
                                <p>No hay datos de ingresos para este periodo</p>
                            </div>
                        </div>
                        <!-- Total de Ingresos -->
                        <div class="mt-4 pt-4 border-t border-border">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-muted-foreground">Total Ingresos:</span>
                                <span class="text-lg font-bold text-primary">Bs {{ totalIngresos.toFixed(2) }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Gráfico de Pastel - Suscripciones -->
                <Card class="col-span-1">
                    <CardHeader>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <CardTitle class="flex items-center gap-2">
                                    <Users class="h-5 w-5 text-primary" />
                                    Suscripciones por Paquete
                                </CardTitle>
                                <CardDescription>
                                    Distribución de suscripciones activas
                                </CardDescription>
                            </div>
                            <Select v-model="periodoSuscripciones">
                                <SelectTrigger class="w-[160px]">
                                    <SelectValue placeholder="Periodo" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="semana">Última semana</SelectItem>
                                    <SelectItem value="mes">Último mes</SelectItem>
                                    <SelectItem value="año">Último año</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="relative h-[300px] sm:h-[350px]">
                            <div v-if="loadingSuscripciones" class="absolute inset-0 flex items-center justify-center bg-background/50">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                            </div>
                            <Pie 
                                v-if="suscripcionesData.some(d => d.cantidad > 0)" 
                                :data="pieChartData" 
                                :options="pieChartOptions"
                            />
                            <div v-else-if="!loadingSuscripciones" class="flex items-center justify-center h-full text-muted-foreground">
                                <p>No hay suscripciones para este periodo</p>
                            </div>
                        </div>
                        <!-- Total de Suscripciones -->
                        <div class="mt-4 pt-4 border-t border-border">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-muted-foreground">Total Suscripciones:</span>
                                <span class="text-lg font-bold text-primary">{{ totalSuscripciones }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Los gráficos heredan los colores del tema automáticamente */
:deep(.chartjs-render-monitor) {
    animation: none !important;
}
</style>
