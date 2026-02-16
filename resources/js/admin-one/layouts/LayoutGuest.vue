<script setup>
import { usePage } from '@inertiajs/vue3'

const page = usePage()
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-100 via-white to-emerald-50 text-slate-800 dark:from-slate-950 dark:via-slate-900 dark:to-slate-800 dark:text-slate-100">
    <div class="mx-auto flex min-h-screen w-full max-w-6xl items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
      <div class="grid w-full max-w-5xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900 lg:grid-cols-2">
        <div class="hidden bg-emerald-600 p-10 text-white lg:flex lg:flex-col lg:justify-between">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-100">TP PKK Pecalungan</p>
            <h1 class="mt-4 text-3xl font-semibold leading-tight">
              Sistem Monitoring Kegiatan Wilayah
            </h1>
            <p class="mt-4 text-sm text-emerald-50/90">
              Login untuk mengelola data kegiatan desa dan kecamatan secara terstruktur.
            </p>
          </div>
          <p class="text-xs text-emerald-100/90">
            {{ page.props.appName ?? 'Laravel' }}
          </p>
        </div>

        <div class="p-6 sm:p-10">
          <div class="mx-auto w-full max-w-md">
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Masuk</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
              Gunakan akun yang sudah didaftarkan oleh super-admin.
            </p>

            <div class="mt-8">
              <slot />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
