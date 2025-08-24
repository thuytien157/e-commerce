<template>
    <a-layout-sider breakpoint="lg" :collapsed="collapsed" @collapse="onCollapse" @breakpoint="onBreakpoint"
        class="custom-ant-sider" :width="200">
        <div class="logo-admin">
            <img src="/images/logo/logo.svg" alt="Logo" />
        </div>

        <a-menu v-model:selectedKeys="selectedKeys" v-model:openKeys="openKeys" theme="white" mode="inline">
            <template v-for="item in menuItems" :key="item.key">
                <a-menu-item v-if="!item.children" :key="item.key">
                    <router-link :to="item.to">
                        <component :is="item.icon" />
                        <span>{{ item.label }}</span>
                    </router-link>
                </a-menu-item>

                <a-sub-menu v-else :key="item.key">
                    <template #title>
                        <span>
                            <component :is="item.icon" />
                            <span>{{ item.label }}</span>
                        </span>
                    </template>
                    <a-menu-item v-for="subItem in item.children" :key="subItem.key">
                        <router-link :to="subItem.to">
                            <component :is="subItem.icon" v-if="subItem.icon" />
                            <span>{{ subItem.label }}</span>
                        </router-link>
                    </a-menu-item>
                </a-sub-menu>
            </template>
        </a-menu>
    </a-layout-sider>
</template>

<script setup>
import { ref, watch } from "vue";
import { useRoute } from "vue-router";
import {
    DashboardOutlined,
    AppstoreOutlined,
    ShoppingOutlined,
    GoldOutlined,
    HistoryOutlined,
    FieldTimeOutlined,
    UserOutlined,
} from "@ant-design/icons-vue";

const route = useRoute();
const selectedKeys = ref([]);
const openKeys = ref([]);
const collapsed = ref(false);

const menuItems = [
    {
        key: "/admin/dashboard",
        to: "/admin/dashboard",
        label: "Thống kê",
        icon: DashboardOutlined,
        permission: "view_dashboard",
    },
    {
        key: "categories-management",
        to: "/admin/category",
        label: "Danh mục",
        icon: AppstoreOutlined,
    },
    {
        key: "products-management",
        to: "/admin/product",
        label: "Sản phẩm",
        icon: GoldOutlined,
    },
    {
        key: "attributes-management",
        to: "/admin/attribute",
        label: "Thuộc tính",
        icon: HistoryOutlined,
    },
    {
        key: "reviews-management",
        to: "/admin/review",
        label: "Đánh giá",
        icon: FieldTimeOutlined,
    },
    {
        key: "orders-management",
        to: "/admin/order",
        label: "Đơn hàng",
        icon: ShoppingOutlined,
    },
    {
        key: "users-management",
        to: "/admin/user",
        label: "Người dùng",
        icon: UserOutlined,
    },
];

const onCollapse = (collapsedValue) => {
    collapsed.value = collapsedValue;
};

const onBreakpoint = (broken) => {
    console.log(broken);
};

watch(
    () => route.path,
    (newPath) => {
        selectedKeys.value = [newPath];

        const parentKey = menuItems.find((item) =>
            item.children?.some((child) => child.to === newPath)
        )?.key;
        if (parentKey) {
            openKeys.value = [parentKey];
        } else {
            openKeys.value = [];
        }
    },
    { immediate: true }
);
</script>

<style scoped>
.custom-ant-sider {
    background-color: #fff;
    border-right: 1px solid #eee;
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
}

.ant-layout-sider-collapsed .logo-admin {
    width: 50px;
    height: 32px;
    margin: 16px 0;
    overflow: hidden;
    text-align: center;
}

.logo-admin {
    width: 120px;
    margin: 20px 20px 10px 20px;
    transition: all 0.2s ease-in-out;
}

.logo-admin img {
    max-width: 100%;
}

.ant-menu a {
    color: #000000;
}

.ant-menu-title-content .router-link-active {
    color: blue;
}

.ant-menu-white.ant-menu-inline .ant-menu-item-selected::after,
.ant-menu-white.ant-menu-inline .ant-menu-submenu-selected::after {
    content: "";
    display: block;
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    width: 3px;
    background-color: #d9363e;
}

/* .ant-menu-white .ant-menu-item:hover,
.ant-menu-white .ant-menu-submenu-title:hover {
    background-color: #feeceb;
    color: #b71c1c;
} */
</style>
