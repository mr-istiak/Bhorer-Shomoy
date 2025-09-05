<script lang="ts">
import AuthBase from '@/layouts/AuthLayout.vue';

export default {
    layout: (h:any, page: any) => h(AuthBase, {
        title: 'Confirm your password',
        description: 'This is a secure area of the application. Please confirm your password before continuing.',
    }, {
        default: () => page
    }),
};
</script>

<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
</script>

<template>
    <Head title="Confirm password" />

    <Form method="post" :action="route('password.confirm')" reset-on-success v-slot="{ errors, processing }">
        <div class="space-y-6">
            <div class="grid gap-2">
                <Label htmlFor="password">Password</Label>
                <Input
                    id="password"
                    type="password"
                    name="password"
                    class="block w-full mt-1"
                    required
                    autocomplete="current-password"
                    autofocus
                />

                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center">
                <Button class="w-full" :disabled="processing">
                    <LoaderCircle v-if="processing" class="w-4 h-4 animate-spin" />
                    Confirm Password
                </Button>
            </div>
        </div>
    </Form>
</template>
