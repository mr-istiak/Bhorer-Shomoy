<script lang="ts">
import AuthBase from '@/layouts/AuthLayout.vue';

export default {
    layout: (h:any, page: any) => h(AuthBase, {
        title: 'Create an account',
        description: 'Enter your details below to create your account',
    }, {
        default: () => page
    }),
};
</script>

<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import PasswordInput from '@/components/PasswordInput.vue';
</script>

<template>
    <Head title="Register" />

    <Form
        method="post"
        :action="route('register')"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" name="name" placeholder="Full name" />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Email address</Label>
                <Input id="email" type="email" required :tabindex="2" autocomplete="email" name="email" placeholder="email@example.com" />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <Label for="password">Password</Label>
                <PasswordInput id="password" required :tabindex="3" autocomplete="new-password" name="password" placeholder="Password" />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation">Confirm password</Label>
                <PasswordInput
                    id="password_confirmation"
                    required
                    :tabindex="4"
                    autocomplete="new-password"
                    name="password_confirmation"
                    placeholder="Confirm password"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button type="submit" class="w-full mt-2" tabindex="5" :disabled="processing">
                <LoaderCircle v-if="processing" class="w-4 h-4 animate-spin" />
                Create account
            </Button>
        </div>

        <div class="text-sm text-center text-muted-foreground">
            Already have an account?
            <TextLink :href="route('login')" class="underline underline-offset-4" :tabindex="6">Log in</TextLink>
        </div>
    </Form>
</template>
