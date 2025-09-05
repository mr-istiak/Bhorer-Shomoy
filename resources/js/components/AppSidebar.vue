<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Newspaper, User, Eye, LayoutGrid, Image, FolderTree, NotebookTabs } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const page = usePage()

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
        icon: LayoutGrid,
    },
    {
        title: 'Posts',
        href: route('posts.index'),
        icon: Newspaper,
    },
    {
        title: 'Categories',
        href: route('categories.index'),
        icon: FolderTree
    },
    {
        title: 'Media',
        href: route('media.index'),
        icon: Image,
    },
];

if(page.props.auth.user) {
    if(page.props.auth.isAdmin) {
        mainNavItems.push({
            title: 'Users',
            href: route('users.index'),
            icon: User,
        });
    }
    if(page.props.auth.isAdmin || (page.props.auth.user.role === 'editor')) {
        mainNavItems.push({
            title: 'Epapers',
            href: route('epapers.index'),
            icon: NotebookTabs,
        });
    }
}

const footerNavItems: NavItem[] = [
    {
        title: 'View Site',
        href: 'https://bhorershomoy.com/',
        icon: Eye,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
