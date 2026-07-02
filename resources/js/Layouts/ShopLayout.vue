<script setup>
import Navbar from '@/Components/Shop/Navbar.vue'
import Footer from '@/Components/Shop/Footer.vue'
</script>

<template>
    <div class="min-h-screen flex flex-col bg-gray-50">

        <!-- Barre de navigation -->
        <Navbar />

        <!-- Contenu -->
        <main class="flex-1 max-w-7xl mx-auto w-full px-4 py-8">
            <slot />
        </main>

        <!-- Pied de page -->
        <Footer />

    </div>
</template>