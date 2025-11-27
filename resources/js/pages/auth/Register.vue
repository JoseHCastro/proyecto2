<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login as _login } from '@/routes';
import { store as _store } from '@/routes/register';
import { wrapRoute } from '@/utils/routes';
import { Form, Head } from '@inertiajs/vue3';

// Wrap routes to use full URLs
const login = wrapRoute(_login);
const store = wrapRoute(_store);
</script>

<template>
    <AuthBase title="Crear una cuenta" description="Ingresa tus datos para crear tu cuenta">

        <Head title="Registro" />

        <Form v-bind="store.form()" :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Nombre Completo</Label>
                    <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" name="name"
                        placeholder="Nombre completo" />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Correo Electrónico</Label>
                    <Input id="email" type="email" required :tabindex="2" autocomplete="email" name="email"
                        placeholder="correo@ejemplo.com" />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="telefono">Teléfono</Label>
                    <Input id="telefono" type="tel" required :tabindex="3" autocomplete="tel" name="telefono"
                        placeholder="Número de teléfono" />
                    <InputError :message="errors.telefono" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Contraseña</Label>
                    <Input id="password" type="password" required :tabindex="4" autocomplete="new-password"
                        name="password" placeholder="Contraseña" />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirmar Contraseña</Label>
                    <Input id="password_confirmation" type="password" required :tabindex="5" autocomplete="new-password"
                        name="password_confirmation" placeholder="Confirmar contraseña" />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <Button type="submit" class="mt-2 w-full" tabindex="6" :disabled="processing"
                    data-test="register-user-button">
                    <Spinner v-if="processing" />
                    Crear cuenta
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                ¿Ya tienes una cuenta?
                <TextLink :href="login()" class="underline underline-offset-4" :tabindex="7">Iniciar sesión</TextLink>
            </div>
        </Form>
    </AuthBase>
</template>
