# CargoTrace — Vercel Deployment Guide

## Panduan Deploy Laravel CargoTrace ke Vercel

### Persiapan yang sudah dilakukan:
- ✅ `vercel.json` — konfigurasi routing & PHP runtime
- ✅ `api/index.php` — serverless entry point
- ✅ `.gitignore` — `public/build` sudah di-uncommit agar assets Vite ter-deploy

### Yang perlu kamu lakukan:

#### 1. Daftar database MySQL gratis
Karena Vercel tidak menyediakan MySQL, gunakan salah satu:
- **Aiven** (aiven.io) — MySQL gratis 30 hari
- **TiDB Cloud** (tidbcloud.com) — MySQL-compatible, free forever
- **PlanetScale** (planetscale.com) — MySQL-compatible

#### 2. Daftar Vercel & deploy
Lihat panduan lengkap di artifact.
