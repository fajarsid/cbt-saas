<template>
    <!-- Impersonation Banner -->
    <div v-if="$page.props.impersonating" class="bg-warning text-dark py-2 px-4 d-flex justify-content-between align-items-center" style="position: fixed; top: 0; left: 0; right: 0; z-index: 9999;">
        <span>
            <i class="fa fa-exclamation-triangle me-2"></i>
            Anda sedang login sebagai: <strong>{{ $page.props.impersonating.tenant_name }}</strong>
        </span>
        <button @click="stopImpersonate" class="btn btn-dark btn-sm">
            <i class="fa fa-sign-out-alt me-2"></i> Kembali ke Admin
        </button>
    </div>

    <nav class="navbar navbar-dark navbar-theme-primary px-4 col-12 d-lg-none" :style="$page.props.impersonating ? 'margin-top: 44px' : ''">
        <a class="navbar-brand me-lg-5" href="/">
            <img class="navbar-brand-dark" src=""/>
            <img class="navbar-brand-light" src=""/>
        </a>
        <div class="d-flex align-items-center">
            <button class="navbar-toggler d-lg-none collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <!-- sidebar -->
    <Sidebar :style="$page.props.impersonating ? 'margin-top: 44px' : ''" />

    <main class="content" :style="$page.props.impersonating ? 'margin-top: 44px' : ''">

        <!-- navbar -->
        <Navbar />

        <!-- content -->
        <slot />

    </main>

</template>

<script>
    //import navbar
    import Navbar from "../Components/Navbar.vue";

    //import sidebar
    import Sidebar from '../Components/Sidebar.vue';

    //import router
    import { router } from '@inertiajs/vue3';

    export default {

        //register components
        components: {
            Navbar,
            Sidebar
        },

        methods: {
            stopImpersonate() {
                router.post('/admin/stop-impersonate');
            }
        }
    }

</script>

<style>

</style>
