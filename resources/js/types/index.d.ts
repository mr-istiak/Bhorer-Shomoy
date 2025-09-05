import type { Box, LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
    isAdmin: boolean;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
};

interface BasePagination {}

export interface User extends BasePagination {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    role: string;
}

export interface Media extends BasePagination  {
    id: number;
    title: string;
    alt: string;
    path: string;
    size: number;
    mime_type: string;
    created_at: string;
    author: User;
    can_delete: boolean
}

export interface Category extends BasePagination  {
    id: number;
    name: string;
    bangla_name: string;
    slug: string;
    created_at: string;
    updated_at: string;
}

export interface Epaper extends BasePagination
{
    id: number;
    title: string;
    published_at: string;
    pdf_path: string;
    created_at: string;
    updated_at: string;
    pages: Epage[]
}

export interface Paginated<T> {
  data: T[];
  links?: string;
  per_page: number;
  total: number;
  current_page: number;
  last_page: number;
}

export interface ArticleBox {
    id?: number;
    epaper_id?: number;
    epage_id?: number;
    article_id?: number;
    epaper?: Epaper;
    epage?: Epage;
    article?: Article;
    type?: 'text' | 'image';
    bounding_box?: BoundingBox;
    extracted_content?: string;
    created_at?: string;
    updated_at?: string;
}

export interface Article {
    id?: number;
    epaper_id?: number;
    epaper?: Epaper;
    status?: 'generated' | 'under_review' | 'empty';
    text?: string;
    boxs?: ArticleBox[];
    created_at?: string;
    updated_at?: string;
}

export interface Epage {
    id: number;
    epaper_id: number;
    epaper: Epaper;
    page_number: number;
    image_path: string;
    article_boxes: ArticleBox[]
}

export type BoundingBox = {
    x: number;
    y: number;
    width: number;
    height: number;
}

export type BreadcrumbItemType = BreadcrumbItem;
