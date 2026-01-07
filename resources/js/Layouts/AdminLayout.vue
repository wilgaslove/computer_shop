<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 text-white p-4">
      <h2 class="font-bold text-lg mb-4">Admin</h2>

      <nav class="space-y-2">
        <Link href="/admin/dashboard">Dashboard</Link>
        <Link href="/products">Produits</Link>
        <Link href="/categories">Catégories</Link>
      </nav>
    </aside>

    <!-- Content -->
    <main class="flex-1 p-6 bg-gray-100">
      <slot />
    </main>
  </div>
</template>
