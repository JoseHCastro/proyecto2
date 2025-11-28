<script setup lang="ts">
import { dashboard as _dashboard, login as _login, register as _register } from '@/routes';
import { wrapRoute } from '@/utils/routes';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Dumbbell, Users, Calendar, Clock, MapPin, Phone, Mail, CheckCircle } from 'lucide-vue-next';
import ThemeSwitcher from '@/components/ThemeSwitcher.vue';
import AppFooter from '@/components/AppFooter.vue';

// Wrap routes to use full URLs
const dashboard = wrapRoute(_dashboard);
const login = wrapRoute(_login);
const register = wrapRoute(_register);

const props = withDefaults(
    defineProps<{
        canRegister: boolean;
        membresias?: any[];
        disciplinas?: any[];
        gimnasio?: any;
    }>(),
    {
        canRegister: true,
        membresias: () => [],
        disciplinas: () => [],
    },
);

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('es-BO', {
        style: 'currency',
        currency: 'BOB',
    }).format(price);
};

// Obtener disciplinas únicas de las sesiones de cada paquete
const getDisciplinasPaquete = (paquete: any) => {
    if (!paquete.sesiones || paquete.sesiones.length === 0) return [];

    const disciplinasMap = new Map();
    paquete.sesiones.forEach((sesion: any) => {
        if (sesion.disciplina) {
            disciplinasMap.set(sesion.disciplina.id, sesion.disciplina);
        }
    });

    return Array.from(disciplinasMap.values());
};

const descripcionGimnasio = computed(() => {
    return props.gimnasio?.descripcion || 'Entrena con los mejores profesionales, equipamiento de última generación y un ambiente motivador que te ayudará a alcanzar tus objetivos.';
});

const getDescripcionDisciplina = (disciplina: any) => {
    return disciplina.descripcion || 'Entrena con profesionales';
};
</script>

<template>

    <Head title="Elevation Gym - Tu gimnasio de confianza" />

    <div class="min-h-screen bg-background">
        <!-- Navbar -->
        <nav
            class="sticky top-0 z-50 border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
            <div class="container mx-auto flex h-16 items-center justify-between px-4">
                <AppLink href="/" class="flex items-center gap-2">
                <Dumbbell class="h-8 w-8 text-primary" />
                <span class="text-2xl font-bold text-foreground">Elevation Gym</span>
                </AppLink>

                <div class="flex items-center gap-4">
                    <ThemeSwitcher />

                    <Link v-if="$page.props.auth.user" :href="dashboard()">
                    <Button variant="default">
                        Dashboard
                    </Button>
                    </AppLink>
                    <template v-else>
                        <Link :href="login()">
                        <Button variant="ghost">
                            Iniciar Sesión
                        </Button>
                        </AppLink>
                        <Link v-if="canRegister" :href="register()">
                        <Button variant="default">
                            Registrarse
                        </Button>
                        </AppLink>
                    </template>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative overflow-hidden bg-gradient-to-br from-primary/10 via-background to-background py-20">
            <div class="container mx-auto px-4">
                <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
                    <div class="space-y-6">
                        <Badge variant="secondary" class="w-fit">
                            <Dumbbell class="mr-2 h-3 w-3" />
                            Gimnasio Profesional
                        </Badge>
                        <h1 class="text-4xl font-bold tracking-tight text-foreground sm:text-5xl md:text-6xl">
                            Eleva tu <span class="text-primary">Potencial</span> al Máximo
                        </h1>
                        <p class="text-lg text-muted-foreground">
                            {{ descripcionGimnasio }}
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <Link v-if="!$page.props.auth.user" :href="register()">
                            <Button size="lg" class="gap-2">
                                <Users class="h-5 w-5" />
                                Únete Ahora
                            </Button>
                            </AppLink>
                            <a href="#membresias">
                                <Button size="lg" variant="outline">
                                    Ver Planes
                                </Button>
                            </a>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="aspect-square overflow-hidden rounded-2xl">
                            <img src="assets/image.png" alt="Elevation Gym" class="h-full w-full object-cover" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Disciplinas Section -->
        <section v-if="disciplinas.length > 0" class="py-20">
            <div class="container mx-auto px-4">
                <div class="mb-12 text-center">
                    <h2 class="mb-4 text-3xl font-bold text-foreground">Nuestras Disciplinas</h2>
                    <p class="text-muted-foreground">Entrena en múltiples disciplinas con instructores certificados</p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <Card v-for="disciplina in disciplinas" :key="disciplina.id"
                        class="overflow-hidden transition-all hover:shadow-lg">
                        <CardHeader class="bg-primary/5">
                            <CardTitle class="flex items-center gap-2">
                                <Dumbbell class="h-5 w-5 text-primary" />
                                {{ disciplina.nombre }}
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="pt-4">
                            <p class="text-sm text-muted-foreground">{{ getDescripcionDisciplina(disciplina) }}</p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>

        <!-- Membresías Section -->
        <section id="membresias" class="bg-muted/30 py-20">
            <div class="container mx-auto px-4">
                <div class="mb-12 text-center">
                    <h2 class="mb-4 text-3xl font-bold text-foreground">Planes y Membresías</h2>
                    <p class="text-muted-foreground">Elige el plan que mejor se adapte a tus necesidades</p>
                </div>

                <div v-if="membresias.length > 0" class="space-y-12">
                    <div v-for="membresia in membresias" :key="membresia.id">
                        <template v-if="membresia.paquetes && membresia.paquetes.length > 0">
                            <div class="mb-6 text-center">
                                <h3 class="text-2xl font-bold text-foreground">{{ membresia.nombre }}</h3>
                                <p v-if="membresia.descripcion" class="text-muted-foreground">{{ membresia.descripcion
                                }}</p>
                            </div>

                            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                                <Card v-for="paquete in membresia.paquetes" :key="paquete.id"
                                    class="relative overflow-hidden transition-all hover:shadow-xl hover:scale-105">
                                    <div
                                        class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-primary to-primary/50" />

                                    <CardHeader>
                                        <CardTitle class="text-2xl">{{ paquete.nombre }}</CardTitle>
                                        <CardDescription v-if="paquete.descripcion">
                                            {{ paquete.descripcion }}
                                        </CardDescription>
                                    </CardHeader>

                                    <CardContent class="space-y-4">
                                        <!-- Precio -->
                                        <div class="text-center">
                                            <div class="text-4xl font-bold text-primary">
                                                {{ formatPrice(paquete.precio) }}
                                            </div>
                                            <p class="text-sm text-muted-foreground">por mes</p>
                                        </div>

                                        <!-- Duración -->
                                        <div v-if="paquete.duracion_dias" class="flex items-center gap-2 text-sm">
                                            <Clock class="h-4 w-4 text-primary" />
                                            <span>{{ paquete.duracion_dias }} días de acceso</span>
                                        </div>

                                        <!-- Disciplinas incluidas -->
                                        <div v-if="getDisciplinasPaquete(paquete).length > 0">
                                            <p class="mb-2 text-sm font-semibold">Disciplinas incluidas:</p>
                                            <div class="flex flex-wrap gap-2">
                                                <Badge v-for="disciplina in getDisciplinasPaquete(paquete)"
                                                    :key="disciplina.id" variant="secondary">
                                                    {{ disciplina.nombre }}
                                                </Badge>
                                            </div>
                                        </div>

                                        <!-- Beneficios -->
                                        <div class="space-y-2 border-t pt-4">
                                            <div class="flex items-start gap-2">
                                                <CheckCircle class="mt-0.5 h-4 w-4 text-primary" />
                                                <span class="text-sm">Acceso a todas las instalaciones</span>
                                            </div>
                                            <div class="flex items-start gap-2">
                                                <CheckCircle class="mt-0.5 h-4 w-4 text-primary" />
                                                <span class="text-sm">Instructores certificados</span>
                                            </div>
                                            <div class="flex items-start gap-2">
                                                <CheckCircle class="mt-0.5 h-4 w-4 text-primary" />
                                                <span class="text-sm">Evaluación física inicial</span>
                                            </div>
                                        </div>

                                        <Link v-if="!$page.props.auth.user" :href="register()">
                                        <Button class="w-full">
                                            Elegir Plan
                                        </Button>
                                        </AppLink>
                                    </CardContent>
                                </Card>
                            </div>
                        </template>
                    </div>
                </div>

                <div v-else class="text-center">
                    <p class="text-muted-foreground">No hay planes disponibles en este momento.</p>
                </div>
            </div>
        </section>

        <!-- Características Section -->
        <section class="py-20">
            <div class="container mx-auto px-4">
                <div class="mb-12 text-center">
                    <h2 class="mb-4 text-3xl font-bold text-foreground">¿Por qué Elevation Gym?</h2>
                    <p class="text-muted-foreground">Entrenamiento de calidad con las mejores instalaciones</p>
                </div>

                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader>
                            <Users class="mb-2 h-10 w-10 text-primary" />
                            <CardTitle>Entrenadores Profesionales</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground">
                                Personal certificado y con experiencia para guiarte en cada paso
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <Dumbbell class="mb-2 h-10 w-10 text-primary" />
                            <CardTitle>Equipamiento Moderno</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground">
                                Máquinas y equipos de última generación para tu entrenamiento
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <Calendar class="mb-2 h-10 w-10 text-primary" />
                            <CardTitle>Horarios Flexibles</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground">
                                Entrena cuando mejor te convenga con nuestros amplios horarios
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CheckCircle class="mb-2 h-10 w-10 text-primary" />
                            <CardTitle>Seguimiento Personalizado</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground">
                                Registro de asistencias y medición de progreso constante
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>

        <!-- Contacto Section -->
        <section v-if="gimnasio" class="bg-muted/30 py-20">
            <div class="container mx-auto px-4">
                <div class="mb-12 text-center">
                    <h2 class="mb-4 text-3xl font-bold text-foreground">Contáctanos</h2>
                    <p class="text-muted-foreground">Estamos aquí para ayudarte a comenzar tu transformación</p>
                </div>

                <div class="mx-auto max-w-3xl">
                    <Card>
                        <CardContent class="grid gap-6 p-6 md:grid-cols-3">
                            <div v-if="gimnasio.direccion" class="flex flex-col items-center gap-2 text-center">
                                <MapPin class="h-8 w-8 text-primary" />
                                <div>
                                    <p class="font-semibold">Dirección</p>
                                    <p class="text-sm text-muted-foreground">{{ gimnasio.direccion }}</p>
                                </div>
                            </div>

                            <div v-if="gimnasio.telefono" class="flex flex-col items-center gap-2 text-center">
                                <Phone class="h-8 w-8 text-primary" />
                                <div>
                                    <p class="font-semibold">Teléfono</p>
                                    <p class="text-sm text-muted-foreground">{{ gimnasio.telefono }}</p>
                                </div>
                            </div>

                            <div v-if="gimnasio.email" class="flex flex-col items-center gap-2 text-center">
                                <Mail class="h-8 w-8 text-primary" />
                                <div>
                                    <p class="font-semibold">Email</p>
                                    <p class="text-sm text-muted-foreground">{{ gimnasio.email }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <AppFooter />
    </div>
</template>
