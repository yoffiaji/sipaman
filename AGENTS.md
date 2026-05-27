# AGENTS.md — Laravel PIRT Karanganyar Project Guide

This file is the coding guide for AI agents working in this repository. It is **not** a task prompt and must not contain temporary case instructions. Use it to understand the project structure, functional flow, file responsibilities, and safe coding rules before editing code.

## 1. Project overview

This project is a Laravel-based PIRT information system for Karanganyar. It serves three main areas:

1. **Public website**
   - Public visitors can view verified PIRT products.
   - Public visitors can browse UMKM/pelaku usaha.
   - Public landing page content comes from the database.

2. **User / pelaku usaha area**
   - Product owners can log in using NIB or email.
   - User accounts are usually created by the import/verification flow or by admin/super admin.
   - Users can manage limited product display data such as store/product details and product images.

3. **Admin / super admin area**
   - Admins manage imported PIRT product data, verification status, product categories, landing page content, and logs.
   - Super admins additionally manage users, system settings, and audit trails.

The codebase uses both Blade web controllers and JSON API controllers. When adding logic, keep business rules reusable so the web and API paths do not drift apart.

## 2. Tech stack and commands

### Backend

- PHP `^8.3`
- Laravel `^13.0`
- Laravel Sanctum
- Maatwebsite/Laravel-Excel `^3.1`
- Eloquent models
- Blade views

### Frontend asset pipeline

- Vite
- Tailwind CSS v4
- `laravel-vite-plugin`

### Important commands

Use these when relevant:

```bash
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan test
npm install
npm run build
```

Development command from `composer.json`:

```bash
composer run dev
```

The `dev` script runs Laravel server, queue listener, log pail, and Vite together. Do not change this script unless the task explicitly requires development tooling changes.

## 3. Golden rules for agents

1. Inspect routes, controller, request, service, model, migration, seeder, and Blade/API response before editing a feature.
2. Do not guess file responsibilities. Use the file map in this document first.
3. Keep controllers thin. Put business logic in services and validation in FormRequests.
4. Reuse existing services. Do not duplicate logic between web controllers and API controllers.
5. Keep role authorization enforced in routes/middleware/policies, not only by hiding UI buttons.
6. Do not mass-rename routes, tables, model classes, namespaces, or column names just because visible branding changes.
7. When schema changes, update migration, model fillable/casts/relations, FormRequest, service/controller, seeders, views/API output, and tests/checks together.
8. Keep admin-facing messages in clear Indonesian.
9. Never log raw passwords, tokens, session values, `.env` secrets, or sensitive credentials.
10. Before deleting any file, prove it is unused by checking routes, imports, references, views, and API clients.
11. Do not place task-specific cases/prompts in this file. Put task-specific instructions in the prompt, not in AGENTS.md.

## 4. Main functional flows

### 4.1 Public website flow

Routes are in `routes/web.php`:

- `/` → public home page.
- `/products` → public verified product catalog.
- `/products/{produk}` → public product detail, only for verified products.
- `/umkm` → public UMKM list.
- `/umkm/{namaPelakuUsaha}` → public UMKM product list.

Main files:

- `app/Http/Controllers/Web/Public/HomeController.php`
  - Loads `LandingPageContent` by `section_key`.
  - Loads latest verified products with `kecamatan` and `gambarUtama`.
  - Returns `resources/views/public/home.blade.php`.

- `app/Http/Controllers/Web/Public/ProductController.php`
  - Lists verified products with search and filter.
  - Uses `Produk::verified()` and `Produk::search()`.
  - Blocks public detail access for unverified products.

- `app/Http/Controllers/Web/Public/UmkmController.php`
  - Groups verified products by `nama_pelaku_usaha`.
  - Shows UMKM detail based on slug-like URL converted back to name.

Public views:

- `resources/views/layouts/public.blade.php`
  - Public base layout.
- `resources/views/partials/public/navbar.blade.php`
  - Public navigation.
- `resources/views/partials/public/hero.blade.php`
  - Public hero section.
- `resources/views/partials/public/footer.blade.php`
  - Public footer.
- `resources/views/public/home.blade.php`
  - Public landing/home page.
- `resources/views/public/products/index.blade.php`
  - Public product catalog.
- `resources/views/public/products/show.blade.php`
  - Public product detail.
- `resources/views/public/umkm/index.blade.php`
  - Public UMKM list.
- `resources/views/public/umkm/show.blade.php`
  - Public UMKM detail.

### 4.2 Authentication flow

Web auth routes are in `routes/web.php`:

- `GET /login`
- `POST /login`
- `POST /logout`
- `/register` redirects to `/login`

Main file:

- `app/Http/Controllers/Web/Auth/AuthenticatedSessionController.php`
  - Login uses a single `identifier` field.
  - Admin/super admin usually log in with email.
  - Pelaku usaha/user can log in with NIB or email.
  - If password is null, `needsPasswordSetup()` blocks login and tells the user to contact admin.
  - Inactive/locked accounts cannot log in.
  - Redirects:
    - `admin` and `super_admin` → admin dashboard.
    - `user` → user dashboard.
    - unknown role → home.

Auth views:

- `resources/views/layouts/auth.blade.php`
- `resources/views/auth/login.blade.php`
- `resources/views/auth/register.blade.php`

Do not re-enable public self-registration unless the task explicitly asks for it. Current web registration is intentionally redirected to login.

### 4.3 User / pelaku usaha flow

Web routes:

- `/user/dashboard`
- `/user/account`
- `/user/products/setting`
- product image upload, set primary image, and delete image inside the user product settings prefix.

Main files:

- `app/Http/Controllers/Web/User/DashboardController.php`
  - User dashboard.

- `app/Http/Controllers/Web/User/AccountController.php`
  - Updates account name and password.

- `app/Http/Controllers/Web/User/ProductSettingController.php`
  - Lists products owned by the logged-in user.
  - Allows limited product display/settings updates.
  - Handles product image upload, primary image selection, and deletion.

User views:

- `resources/views/user/dashboard.blade.php`
- `resources/views/user/settings/index.blade.php`
- `resources/views/user/products/setting.blade.php`
- `resources/views/user/products/setting-edit.blade.php`

Rules:

- Users must only access their own products.
- Do not let users edit official PIRT fields unless the task explicitly changes the business policy.
- Product ownership uses `produks.user_id`.
- Product owner accounts may be created automatically from status commitment import when NIB exists.

### 4.4 Admin operational flow

Admin web routes use middleware:

```php
['auth', 'role:admin,super_admin']
```

Admin prefix/name:

```text
/admin
admin.*
```

Admin features:

- Dashboard
- Products
- Rekap PIRT import
- Product images
- Verification
- Status Pemenuhan Komitmen import
- Jenis Barang
- Landing Page
- Log Aktivitas

Main files:

- `app/Http/Controllers/Web/Admin/DashboardController.php`
  - Uses `DashboardStatisticService`.
  - Shows product/import/verification summary.

- `app/Http/Controllers/Web/Admin/ProductController.php`
  - Product CRUD for admin.
  - Uses `StoreProductRequest` and `UpdateProductRequest`.
  - Logs create/update/delete with `LogsAuditTrail`.
  - Deletes product images from storage when product is deleted.

- `app/Http/Controllers/Web/Admin/ProductImportController.php`
  - Handles Rekap Data PIRT import.
  - Uses `ImportProductRequest`.
  - Delegates to `ProductImportService::importRekapPirt()`.

- `app/Http/Controllers/Web/Admin/ProductImageController.php`
  - Handles admin product image upload/delete.
  - Delegates to `ProductImageService`.

- `app/Http/Controllers/Web/Admin/ProductVerificationController.php`
  - Lists verification tabs: all, verified, not yet, in progress.
  - Handles Status Pemenuhan Komitmen import.
  - Delegates import to `ProductImportService::importCommitmentStatus()`.
  - Delegates manual verification to `ProductVerificationService`.
  - Logs verification/import actions.

- `app/Http/Controllers/Web/Admin/JenisBarangController.php`
  - CRUD for product type/category table `jenis_barangs`.

- `app/Http/Controllers/Web/Admin/LandingPageController.php`
  - Lists and updates fixed landing page content sections.
  - Uses `UpdateLandingPageRequest`.
  - Logs changes to `landing_page_contents`.

- `app/Http/Controllers/Web/Admin/LogController.php`
  - Shows activity logs.

Admin views:

- `resources/views/layouts/admin.blade.php`
  - Main admin layout.
  - Includes sidebar, topbar, breadcrumb, and main content.
  - Contains the current admin theme styling and utility remaps.

- `resources/views/partials/admin/sidebar.blade.php`
  - Builds the sidebar menu.
  - Shows normal admin items for admin and super admin.
  - Adds super-admin-only items only when role is `super_admin`.

- `resources/views/partials/admin/topbar.blade.php`
- `resources/views/partials/admin/breadcrumb.blade.php`
- `resources/views/admin/dashboard.blade.php`
- `resources/views/admin/products/*.blade.php`
- `resources/views/admin/verifications/*.blade.php`
- `resources/views/admin/jenis-barang/*.blade.php`
- `resources/views/admin/landing-page/index.blade.php`
- `resources/views/admin/logs/index.blade.php`

Rules:

- Admin is operational, not fully privileged.
- Super-admin-only features must not be moved into the `/admin` route group unless the middleware/policy still blocks normal admin access.
- Sidebar changes must match route access rules.
- UI labels should be understandable for non-technical staff.

### 4.5 Super admin flow

Super admin web routes use middleware:

```php
['auth', 'role:super_admin']
```

Super admin prefix/name:

```text
/super-admin
super-admin.*
```

Main files:

- `app/Http/Controllers/Web/SuperAdmin/UserManagementController.php`
  - Manages user/admin accounts.
  - Does not allow editing/deleting the current logged-in account from this page.
  - Does not allow editing/deleting super admin accounts through regular user management.
  - Uses role names `user` and `admin` for assignable roles.
  - Logs create/update/delete.

- `app/Http/Controllers/Web/SuperAdmin/SystemSettingController.php`
  - Lists and updates system settings.
  - Uses `UpdateSystemSettingRequest`.
  - Logs setting updates.

- `app/Http/Controllers/Web/SuperAdmin/AuditTrailController.php`
  - Shows audit trail records.

Super admin views:

- `resources/views/super-admin/users/index.blade.php`
- `resources/views/super-admin/users/create.blade.php`
- `resources/views/super-admin/users/edit.blade.php`
- `resources/views/super-admin/users/_form.blade.php`
- `resources/views/super-admin/settings/index.blade.php`
- `resources/views/super-admin/audit-trails/index.blade.php`

Rules:

- Normal admin must not access super admin routes by direct URL.
- Normal admin must not see super admin menu items.
- User management must not accidentally allow creating another super admin unless the task explicitly requests it and the security implications are handled.

### 4.6 JSON API flow

API routes are in `routes/api.php`.

Auth API:

- `POST /api/auth/register`
- `POST /api/auth/login`
- `POST /api/auth/logout`
- `GET /api/auth/me`
- `POST /api/auth/update-profile`

Public API:

- `GET /api/produk`
- `GET /api/produk/filter`
- `GET /api/produk/{produk}`
- `GET /api/landing-page`

User API:

- `GET /api/user/dashboard`
- `GET /api/user/produk`
- `GET /api/user/produk/{produk}`
- `PATCH /api/user/produk/{produk}`
- image upload/delete for user products.

Admin API:

- `apiResource /api/admin/produk`
- Rekap PIRT import.
- Status Komitmen import.
- Product verification update/reject.
- Admin product image upload/delete.
- Landing page index/update.

Super admin API:

- `apiResource /api/super-admin/users`
- `apiResource /api/super-admin/settings`
- audit/activity logs.

API controller files:

- `app/Http/Controllers/Api/AuthController.php`
- `app/Http/Controllers/Api/ProdukController.php`
- `app/Http/Controllers/Api/User/DashboardController.php`
- `app/Http/Controllers/Api/User/ProductController.php`
- `app/Http/Controllers/Api/User/ProductImageController.php`
- `app/Http/Controllers/Api/Admin/ProductController.php`
- `app/Http/Controllers/Api/Admin/ProductImportController.php`
- `app/Http/Controllers/Api/Admin/ProductVerificationController.php`
- `app/Http/Controllers/Api/Admin/ProductImageController.php`
- `app/Http/Controllers/Api/Admin/LandingPageController.php`
- `app/Http/Controllers/Api/SuperAdmin/UserManagementController.php`
- `app/Http/Controllers/Api/SuperAdmin/SystemSettingController.php`
- `app/Http/Controllers/Api/SuperAdmin/AuditTrailController.php`

Rules:

- Keep API response structure consistent.
- Do not return Blade redirects from API controllers.
- Do not put business logic only in API controllers if web controllers need the same behavior.
- Use Sanctum for protected API routes.
- Use the existing `role` middleware for API role checks.

## 5. Import and verification flows

### 5.1 Rekap Data PIRT import

Main route:

- Web: `POST /admin/products/import/rekap-pirt`
- API: `POST /api/admin/produk/import/rekap-pirt`

Main files:

- `app/Http/Requests/Admin/ImportProductRequest.php`
  - Validates uploaded spreadsheet.

- `app/Http/Controllers/Web/Admin/ProductImportController.php`
  - Receives web upload.
  - Calls `ProductImportService`.

- `app/Http/Controllers/Api/Admin/ProductImportController.php`
  - Receives API upload.
  - Calls `ProductImportService`.

- `app/Services/ProductImportService.php`
  - Shared import orchestrator.
  - Runs import in a transaction.
  - Creates an `ImportLog`.
  - Returns import summary and failure details.

- `app/Imports/ProdukImport.php`
  - Reads Rekap Data PIRT rows starting from row 5.
  - Maps spreadsheet columns:
    - B: No SPPIRT
    - C: Nama Branding Produk
    - D: Kategori Pangan
    - E: Jenis Pangan
    - F: Kemasan
    - G: Cara Penyimpanan
    - H: NIB
    - I: Wilayah
    - J: Tanggal Pengajuan
    - K: Status OSS
    - L: No HP
    - M: Nama Pelaku Usaha
    - N: Alamat
  - Creates/updates `produks` by `no_sppirt`.
  - Creates `jenis_barangs` from `jenis_pangan` when needed.
  - New products default to `is_verified = false`.
  - Existing product verification status must not be reset by re-import.

Rules:

- Preserve row-level failure reporting.
- Invalid rows must be skipped with clear reasons.
- Do not reset `is_verified` for products that already exist.
- Keep import logic inside `app/Imports` and `ProductImportService`, not inside controllers.

### 5.2 Status Pemenuhan Komitmen import

Main route:

- Web: `POST /admin/verifications/import`
- API: `POST /api/admin/produk/import/status-komitmen`

Main files:

- `app/Http/Requests/Admin/ImportCommitmentStatusRequest.php`
  - Validates uploaded spreadsheet.

- `app/Http/Controllers/Web/Admin/ProductVerificationController.php`
  - Receives web upload.
  - Calls `ProductImportService::importCommitmentStatus()`.

- `app/Http/Controllers/Api/Admin/ProductVerificationController.php`
  - Receives API upload.
  - Calls shared service.

- `app/Imports/PirtCommitmentStatusImport.php`
  - Reads status commitment rows starting from row 2.
  - Maps spreadsheet columns:
    - B: No SPPIRT
    - C: Provinsi
    - D: Kab/Kota
    - E: Nama Pelaku Usaha
    - F: Alamat Usaha
    - G: Phone
    - H: Terdaftar
    - I: NIB
    - J: Verifikasi Produk
    - K: Verifikasi Label
    - L: PKP
    - M: CPPOB
    - N: Status Pemenuhan Komitmen
  - Writes to `pirt_commitment_statuses`.
  - Links status rows to `produks` by `no_sppirt`.
  - Updates `verifikasi_produks`.
  - Updates `produks.is_verified`.
  - If a product becomes verified and has NIB, creates/links a `user` account if needed.

Rules:

- Status file should be imported after Rekap Data PIRT so products already exist.
- Missing `no_sppirt` must fail the row.
- Unknown `no_sppirt` must fail the row with a clear message.
- Automatically created pelaku usaha accounts may have null email and null password; they cannot log in until admin sets credentials.
- Keep NIB identity consistent; do not create duplicate users for the same NIB.

### 5.3 Manual verification

Main files:

- `app/Http/Requests/Admin/UpdateProductVerificationRequest.php`
- `app/Services/ProductVerificationService.php`
- `app/Models/VerifikasiProduk.php`
- `app/Models/Produk.php`

Manual verification fields:

- `verifikasi_produk`
- `verifikasi_label`
- `pkp`
- `cppob_pemeriksaan_sarana`
- `catatan`

Rules:

- `ProductVerificationService::update()` is the source of truth for manual verification updates.
- Verification status should update both `verifikasi_produks` and `produks.is_verified`.
- `tanggal_verifikasi` and `masa_berlaku_pirt` are set when product becomes verified.
- Rejection should clear all checklist booleans and save optional note.

## 6. Database model map

### `roles`

Migration:

- `database/migrations/2024_01_01_000001_create_roles_table.php`

Model:

- `app/Models/Role.php`

Purpose:

- Stores role names: `user`, `admin`, `super_admin`.
- Related to users.

### `users`

Migration:

- `database/migrations/2024_01_01_000002_create_users_table.php`
- `database/migrations/2026_05_18_120000_add_nib_and_nullable_credentials_to_users_table.php`

Model:

- `app/Models/User.php`

Purpose:

- Stores admins, super admins, and pelaku usaha accounts.
- Supports nullable `email` and `password` after later migration.
- Supports unique nullable `nib`.
- `password` cast is hashed.
- Has helpers:
  - `hasRole()`
  - `isActive()`
  - `needsPasswordSetup()`

Relations:

- `role`
- `auditTrails`
- `activityLogs`
- `importLogs`
- `produks`

### `kecamatans`

Migration:

- `database/migrations/2024_01_01_000003_create_kecamatans_table.php`

Model:

- `app/Models/Kecamatan.php`

Purpose:

- Stores district data for Karanganyar.
- Related to products.

### `jenis_barangs`

Migration:

- `database/migrations/2024_01_01_000004_create_jenis_barangs_table.php`

Model:

- `app/Models/JenisBarang.php`

Purpose:

- Stores product type/category.
- Related to products.
- Rekap PIRT import can create records from spreadsheet `jenis_pangan`.

### `produks`

Migration:

- `database/migrations/2024_01_01_000005_create_produks_table.php`
- `database/migrations/2026_05_18_000013_widen_produk_text_columns.php`
- indexed by `database/migrations/2026_05_12_100001_add_indexes_for_performance.php`

Model:

- `app/Models/Produk.php`

Purpose:

- Main PIRT product data.
- Public catalog only shows records where `is_verified = true`.
- Unique key is `no_sppirt`.

Important relations:

- `user`
- `kecamatan`
- `jenisBarang`
- `gambarProduks`
- `gambarUtama`
- `verifikasi`
- `commitmentStatus`

Important scopes:

- `verified`
- `byKecamatan`
- `byJenisBarang`
- `ownedBy`
- `search`

### `gambar_produks`

Migration:

- `database/migrations/2024_01_01_000006_create_gambar_produks_table.php`

Model:

- `app/Models/GambarProduk.php`

Purpose:

- Product images stored on public disk.
- Has primary image flag.
- `getGambarUrlAttribute()` resolves display URL.

Related service:

- `app/Services/ProductImageService.php`

### `verifikasi_produks`

Migration:

- `database/migrations/2024_01_01_000007_create_verifikasi_produks_table.php`
- `database/migrations/2026_05_18_100001_update_verifikasi_produks_table.php`

Model:

- `app/Models/VerifikasiProduk.php`

Purpose:

- Stores manual/imported verification checklist and note.
- Related to product and verifying user.

### `import_logs`

Migration:

- `database/migrations/2024_01_01_000008_create_import_logs_table.php`

Model:

- `app/Models/ImportLog.php`

Purpose:

- Records import file name, row counts, success/failure counts, description, and importing user.

### `audit_trails`

Migration:

- `database/migrations/2024_01_01_000009_create_audit_trails_table.php`

Model:

- `app/Models/AuditTrail.php`

Purpose:

- Records create/update/delete/verify/import style audit events.
- Stores old/new data as JSON.
- Created through `LogsAuditTrail`.

### `activity_logs`

Migration:

- `database/migrations/2024_01_01_000010_create_activity_logs_table.php`

Model:

- `app/Models/ActivityLog.php`

Purpose:

- Stores user activity logs such as login/activity events.

### `landing_page_contents`

Migration:

- `database/migrations/2024_01_01_000011_create_landing_page_contents_table.php`

Model:

- `app/Models/LandingPageContent.php`

Purpose:

- Stores fixed public landing page section content.
- Current fields: `section_key`, `judul`, `konten`, `updated_by`.
- Public home reads all records keyed by `section_key`.

Rules:

- Use for public content sections.
- Do not store global app configuration here.
- Do not allow admins to edit technical layout unless the task explicitly asks for it.

### `pirt_commitment_statuses`

Migration:

- `database/migrations/2024_01_01_000012_create_pirt_commitment_statuses_table.php`

Model:

- `app/Models/PirtCommitmentStatus.php`

Purpose:

- Stores Status Pemenuhan Komitmen import results.
- Unique key is `no_sppirt`.
- May link to `produks`.

### `system_settings`

Migration:

- `database/migrations/2026_05_12_065652_create_system_settings_table.php`

Model:

- `app/Models/SystemSetting.php`

Purpose:

- Stores global app configuration.
- Current fields: `key`, `value`, `deskripsi`.

Rules:

- Use for global configuration only.
- Do not use this table for detailed landing page section content.
- Do not store secrets or `.env` values here.

## 7. Service map

### `app/Services/DashboardStatisticService.php`

Purpose:

- Provides dashboard statistics for admin and super admin.

Methods:

- `adminStats()`
- `superAdminStats()`

Use this for dashboard counts instead of duplicating dashboard queries in controllers.

### `app/Services/ProductImageService.php`

Purpose:

- Handles product image upload and deletion.
- Keeps primary image rules consistent.
- Deletes physical files from storage.

Methods:

- `storeMany(Produk $produk, array $files, int $primaryIndex = 0)`
- `delete(GambarProduk $gambarProduk)`

Use this service for both web and API image workflows.

### `app/Services/ProductImportService.php`

Purpose:

- Shared orchestrator for spreadsheet imports.
- Handles DB transaction, Laravel Excel import call, import log, and summary response.

Methods:

- `importRekapPirt(UploadedFile $file)`
- `importCommitmentStatus(UploadedFile $file)`

Rules:

- Do not duplicate import orchestration in controllers.
- Add reusable reader/validation helpers here or in a dedicated support class if imports grow.
- Keep `ImportLog` creation here.

### `app/Services/ProductVerificationService.php`

Purpose:

- Source of truth for product verification and rejection.

Methods:

- `update(Produk $produk, array $data)`
- `reject(Produk $produk, ?string $catatan = null)`

Rules:

- Use this service whenever verification status changes.
- Do not update `produks.is_verified` manually elsewhere unless this service is not appropriate and the reason is documented.

## 8. FormRequest map

Admin requests:

- `ImportProductRequest`
  - Validates Rekap Data PIRT import file.
- `ImportCommitmentStatusRequest`
  - Validates Status Pemenuhan Komitmen import file.
- `StoreProductRequest`
  - Validates product create form.
- `UpdateProductRequest`
  - Validates product update form.
- `StoreProductImageRequest`
  - Validates up to 5 product images, jpg/jpeg/png/webp, max 2 MB each.
- `StoreJenisBarangRequest`
  - Validates new `jenis_barang`.
- `UpdateJenisBarangRequest`
  - Validates updated `jenis_barang`.
- `UpdateProductVerificationRequest`
  - Validates verification checklist fields.
- `UpdateLandingPageRequest`
  - Validates landing page section content.

Super admin requests:

- `StoreUserRequest`
  - Validates user creation.
- `UpdateUserRequest`
  - Validates user update.
- `UpdateSystemSettingRequest`
  - Validates setting update.

Rules:

- Prefer FormRequests over inline controller validation.
- If web and API share the same input shape, reuse the same FormRequest where possible.
- Add custom messages in Indonesian when validation errors are shown to admins.

## 9. View and UI file map

### Layouts

- `resources/views/layouts/public.blade.php`
  - Public site wrapper.

- `resources/views/layouts/auth.blade.php`
  - Login/auth wrapper.

- `resources/views/layouts/admin.blade.php`
  - Admin/super admin wrapper.
  - Includes sidebar, topbar, breadcrumb.
  - Contains the current admin theme and legacy Tailwind utility remaps.

### Shared components

- `resources/views/components/alert.blade.php`
  - Alert component.
- `resources/views/components/badge-status.blade.php`
  - Verification/status badge component.
- `resources/views/components/modal-delete.blade.php`
  - Delete confirmation modal.
- `resources/views/components/product-card.blade.php`
  - Product card for public/catalog usage.

### Admin partials

- `resources/views/partials/admin/sidebar.blade.php`
  - Sidebar item source.
  - Role-aware menu rendering.
  - Update this file when adding/removing sidebar items, but always update route authorization too.

- `resources/views/partials/admin/topbar.blade.php`
  - Admin topbar.

- `resources/views/partials/admin/breadcrumb.blade.php`
  - Breadcrumb.

### Public partials

- `resources/views/partials/public/navbar.blade.php`
- `resources/views/partials/public/hero.blade.php`
- `resources/views/partials/public/footer.blade.php`

### Admin pages

- `resources/views/admin/dashboard.blade.php`
  - Admin dashboard summary.

- `resources/views/admin/products/index.blade.php`
  - Product list and Rekap PIRT import form.
- `resources/views/admin/products/create.blade.php`
- `resources/views/admin/products/edit.blade.php`
- `resources/views/admin/products/show.blade.php`
- `resources/views/admin/products/_form.blade.php`

- `resources/views/admin/verifications/index.blade.php`
  - Verification list and Status Pemenuhan Komitmen import form.
- `resources/views/admin/verifications/edit.blade.php`

- `resources/views/admin/jenis-barang/index.blade.php`
- `resources/views/admin/jenis-barang/create.blade.php`
- `resources/views/admin/jenis-barang/edit.blade.php`

- `resources/views/admin/landing-page/index.blade.php`
  - Admin landing page content editor.

- `resources/views/admin/logs/index.blade.php`
  - Activity log page.

### Super admin pages

- `resources/views/super-admin/users/*`
  - User management.
- `resources/views/super-admin/settings/index.blade.php`
  - System settings.
- `resources/views/super-admin/audit-trails/index.blade.php`
  - Audit trail list.

### Legacy/possibly unused views

These folders exist and must be checked before editing/removing:

- `resources/views/admin/categories/*`
- `resources/views/admin/umkm/*`
- `resources/views/admin/users/*`

They may be older admin views not referenced by the current `routes/web.php`. Verify usage before deleting.

## 10. Route file responsibilities

### `routes/web.php`

Purpose:

- Browser routes returning Blade views or redirects.
- Uses session-based auth.
- Defines public, guest, user, admin, and super admin route groups.

Rules:

- Do not put API JSON routes here.
- Keep route names stable unless all Blade/API references are updated.
- Use route model binding consistently.
- Keep role middleware on protected groups.

### `routes/api.php`

Purpose:

- JSON API routes.
- Uses Sanctum for authenticated API access.
- Defines public, auth, user, admin, and super admin API groups.

Rules:

- Do not return Blade views or redirects from these controllers.
- Keep role middleware on protected groups.
- If an API feature mirrors a web feature, share the service layer.

### `routes/channels.php`

Purpose:

- Broadcast authorization routes.
- Only change if adding broadcasting/private channels.

### `routes/console.php`

Purpose:

- Console command closures/schedules.
- Only change if adding console tasks.

## 11. Middleware and authorization

### Middleware

- `app/Http/Middleware/CheckRole.php`
  - Checks authenticated user's role by `role.nama_role`.
  - Used as `role:user`, `role:admin,super_admin`, and `role:super_admin`.

- `app/Http/Middleware/Authenticate.php`
  - Auth redirect behavior.

- `app/Http/Middleware/RedirectIfAuthenticated.php`
  - Guest route redirect behavior.

Rules:

- Do not replace route role middleware with UI-only checks.
- When adding a protected page, add middleware first, then sidebar/menu.

### Policies

- `app/Policies/ProdukPolicy.php`
- `app/Policies/UserPolicy.php`
- `app/Policies/SystemSettingPolicy.php`
- `app/Policies/AuditTrailPolicy.php`

Rules:

- Use policies for object-specific permissions when relevant.
- If a controller currently uses route middleware only, do not add inconsistent authorization logic without checking the full flow.

## 12. Audit and activity logging

### `app/Traits/LogsAuditTrail.php`

Purpose:

- Shared audit logging helper for create/update/delete/import/verify style actions.

Use it for:

- Product create/update/delete.
- Verification changes.
- Import actions.
- Landing page updates.
- System setting updates.
- User management changes.

Never log:

- raw passwords
- password hashes unless already present in sanitized model output and unavoidable; prefer removing them
- tokens
- sessions
- secrets
- full uploaded file contents

### `ActivityLog`

Purpose:

- User activity history.

### `AuditTrail`

Purpose:

- Change history for admin/super admin actions.

Rules:

- Keep audit logs useful but not noisy.
- Store before/after data where appropriate.
- Sanitize sensitive fields before logging.

## 13. File upload and storage rules

Product images:

- Use `ProductImageService`.
- Store on Laravel `public` disk.
- Validate type and size through FormRequest.
- Keep exactly one primary image where possible.
- Delete physical files when deleting image records.

Landing page images, if added:

- Use `Storage::disk('public')`.
- Store paths, not absolute machine paths.
- Render via `Storage::url()` or a consistent accessor/helper.
- Replace old images safely.
- Validate image file type and size.

Spreadsheet imports:

- Keep file validation in FormRequests.
- Keep import orchestration in `ProductImportService`.
- Keep spreadsheet row parsing in `app/Imports`.
- Keep failure summaries readable.

## 14. Database and migration rules

### General rule

For an existing deployed project, create a new migration when changing schema.

### Fresh migration exception

If the user explicitly says the database will be migrated fresh and asks to merge changes into existing table migrations, it is acceptable to edit existing migrations directly. In that case, update all dependent files in the same change.

### Always update together

When adding/removing/changing a column:

1. Migration.
2. Model `$fillable`.
3. Model `$casts` if needed.
4. Relationships if needed.
5. FormRequest validation.
6. Controller/service read/write logic.
7. Blade form and display.
8. API request/response if relevant.
9. Seeder defaults.
10. Tests/manual checklist.

### Do not do these without explicit instruction

- Do not rename tables casually.
- Do not rename route names casually.
- Do not rename model classes casually.
- Do not drop data-bearing columns without checking all usage.
- Do not modify `.env` secrets.
- Do not store secrets in database settings.

## 15. Landing page content rules

Current flow:

- `HomeController` loads all `LandingPageContent` records keyed by `section_key`.
- `Admin/LandingPageController` edits records.
- Current model fields are `section_key`, `judul`, `konten`, `updated_by`.

Conceptual boundary:

- Landing page content = public text/image/button content for fixed sections.
- System settings = global app configuration.

Rules:

- Do not let admin change Blade layout, section order, route names, CSS classes, or section keys unless explicitly required.
- If adding image/button/status fields, update migration, model, request, controller, admin form, public Blade, and seeders.
- Keep fixed sections seeded so admin edits existing sections rather than creating random keys.
- If a section has repeated child cards, use a child table rather than stuffing complex JSON into one text field unless the task asks for JSON.

Recommended fixed-section fields if expanded:

- `section_key`
- `judul`
- `subjudul`
- `konten`
- `image_path`
- `image_alt`
- `button_text`
- `button_url`
- `is_active`
- `updated_by`

## 16. System settings rules

Current flow:

- `SuperAdmin/SystemSettingController` lists and updates `SystemSetting`.
- `SystemSetting` model stores `key`, `value`, and `deskripsi`.

Use system settings for:

- app/site name
- tagline
- logo path
- contact email
- WhatsApp/contact number
- office address
- footer text
- default pagination
- import max file size
- maintenance-style display flags if implemented

Do not use system settings for:

- landing page section paragraphs
- product content
- verification data
- imported spreadsheet data
- secrets such as API keys, passwords, tokens, or `.env` values

Rules:

- Prefer seeded/whitelisted keys.
- If adding cached setting helpers, clear cache after update.
- Keep setting labels understandable for non-technical super admins.

## 17. Sidebar/menu rules

Current source:

- `resources/views/partials/admin/sidebar.blade.php`

Current behavior:

- Normal admin items are always available to `admin` and `super_admin`.
- Super admin items are appended only when `$role === 'super_admin'`.

Rules:

- Sidebar visibility must match route middleware.
- Do not show super-admin-only items to normal admin.
- Do not add too many menus; group by workflow.
- New menu requires:
  - route
  - controller
  - view
  - middleware
  - active route pattern
  - icon
  - empty state or fallback
  - audit/logging when relevant

Recommended grouping when redesigning sidebar:

- Utama: Dashboard
- Data PIRT: Produk, Jenis Barang, Verifikasi
- Konten Website: Landing Page
- Monitoring: Log Aktivitas, Riwayat Import if implemented
- Super Admin: Kelola User, System Settings, Audit Trail

## 18. Branding rules

Visible branding can be changed by task, but technical identifiers should stay stable unless explicitly requested.

Safe to update when rebranding:

- page titles
- sidebar header
- login page text
- navbar/footer text
- landing page seed content
- visible labels in Blade
- config `app.name` if the task asks
- documentation text

Do not automatically rename:

- route names
- database tables
- columns
- PHP namespaces
- model classes
- controller classes
- migration filenames
- storage paths

Reason:

- Technical renames can break route references, model binding, migrations, and existing data.

## 19. Legacy or possibly duplicate files

These files exist but are not clearly referenced by the current `routes/web.php` or `routes/api.php`. Check usage before editing or deleting:

- `app/Http/Controllers/Web/Admin/VerificationController.php`
  - Similar purpose to `ProductVerificationController`.
  - Current web routes use `ProductVerificationController`.

- `app/Http/Controllers/Web/Admin/ProductPageController.php`
  - Check references before use.

- `app/Http/Controllers/Api/Admin/ProdukAdminController.php`
  - Looks like older all-in-one admin API controller.
  - Current API routes use separate admin controllers.

- `app/Http/Controllers/Api/Admin/SystemSettingController.php`
  - Current API routes expose settings under `Api/SuperAdmin/SystemSettingController`.

- `app/Http/Controllers/Api/Admin/UserManagementController.php`
  - Current API routes expose users under `Api/SuperAdmin/UserManagementController`.

- `resources/views/admin/categories/*`
- `resources/views/admin/umkm/*`
- `resources/views/admin/users/*`

Rules:

- Do not call these dead code without checking the actual route map.
- Do not delete them unless the task explicitly includes cleanup and all references are verified.

## 20. Coding style rules

### PHP/Laravel

- Follow existing Laravel style.
- Use typed return values where existing code does.
- Prefer dependency injection for services.
- Prefer route model binding.
- Prefer Eloquent relationships and scopes.
- Use `DB::transaction()` for multi-model writes.
- Use `firstOrFail()`/`abort_if()`/`abort_unless()` for clear failure handling.
- Keep public methods in controllers small.
- Extract repeated logic into services or private helpers.
- Do not create large static utility classes unless truly needed.

### Blade/Tailwind

- Reuse layouts, components, and partials.
- Keep form labels and messages in Indonesian.
- Preserve responsive behavior.
- Keep admin UI simple for non-technical users.
- Avoid inline business logic in Blade beyond display conditions.
- Do not duplicate whole forms when a partial already exists.

### API

- Return JSON consistently.
- Use appropriate status codes.
- Do not expose sensitive fields.
- Keep API and web behavior aligned by sharing service logic.

## 21. Common change playbooks

### Adding or changing an admin CRUD page

Check/update:

1. route in `routes/web.php`
2. controller under `app/Http/Controllers/Web/Admin`
3. FormRequest under `app/Http/Requests/Admin`
4. model fillable/casts/relations
5. migration/seeder if schema/default data changes
6. Blade view under `resources/views/admin`
7. sidebar item if it is a top-level page
8. audit logging if it changes important data
9. tests/manual route check

### Adding or changing an API feature

Check/update:

1. route in `routes/api.php`
2. controller under `app/Http/Controllers/Api`
3. shared service if business logic overlaps with web
4. FormRequest if validating input
5. model and migration if data changes
6. response structure
7. auth/role middleware
8. tests/manual API calls

### Changing import behavior

Check/update:

1. relevant FormRequest
2. `ProductImportService`
3. relevant import class under `app/Imports`
4. `ImportLog` behavior
5. admin web controller and API controller
6. admin import Blade view
7. row failure messages
8. transaction behavior
9. manual tests with valid/invalid files

### Changing verification behavior

Check/update:

1. `UpdateProductVerificationRequest`
2. `ProductVerificationService`
3. `VerifikasiProduk` model/casts
4. `Produk` fields affected by verification
5. web/API verification controllers
6. admin verification views
7. public catalog visibility
8. audit log

### Changing landing page behavior

Check/update:

1. migration for `landing_page_contents` or child table
2. `LandingPageContent` model
3. `UpdateLandingPageRequest`
4. `Admin/LandingPageController`
5. `Api/Admin/LandingPageController`
6. `HomeController`
7. `resources/views/admin/landing-page/index.blade.php`
8. public Blade partials/home page
9. seeders for default sections
10. image storage if images are added

### Changing system settings

Check/update:

1. `system_settings` migration if fields change
2. `SystemSetting` model
3. `UpdateSystemSettingRequest`
4. `SuperAdmin/SystemSettingController`
5. settings view
6. any setting helper/cache
7. route/policy/middleware
8. audit log

### Changing sidebar

Check/update:

1. route exists
2. route middleware matches role
3. controller and view exist
4. active route pattern is correct
5. icon and label are clear
6. normal admin vs super admin visibility is correct
7. direct URL access is blocked when required

## 22. Testing checklist by change type

### General

```bash
php artisan test
npm run build
```

If migrations changed:

```bash
php artisan migrate:fresh --seed
```

If storage changed:

```bash
php artisan storage:link
```

### Authentication

Manually verify:

- admin login with email
- super admin login with email
- user login with NIB/email
- null-password user cannot log in
- inactive/locked user cannot log in
- logout works
- redirect path matches role

### Public website

Manually verify:

- home loads
- product catalog only shows verified products
- product detail blocks unverified products
- UMKM list/detail works
- missing/empty images do not break layout

### Admin

Manually verify:

- dashboard loads
- product list/search/filter works
- product create/update/delete works
- image upload/delete works
- verification tabs work
- landing page editor works
- logs page works

### Super admin

Manually verify:

- normal admin cannot access `/super-admin/*`
- super admin can manage users
- super admin can update settings
- audit trail page loads
- user cannot edit/delete own account from user management
- super admin account is protected from normal management actions

### Import

Manually verify:

- valid Rekap PIRT import
- invalid Rekap PIRT rows show friendly failure messages
- valid Status Pemenuhan Komitmen import
- unknown No SPPIRT produces row failure
- import log is created
- verified products appear publicly
- user account creation from NIB does not create duplicates

### UI build

Manually verify:

- admin layout still renders
- sidebar active states still work
- mobile/responsive layout is not broken
- Vite build succeeds

## 23. Final response expectations for coding agents

When finishing a task, report:

1. What changed.
2. Files changed.
3. Why each change was needed.
4. Commands/tests run.
5. Any checks not run and why.
6. Migration/seed impact.
7. Manual verification steps.
8. Any risk or follow-up needed.

Do not claim a command passed unless it was actually run.
Do not claim a UI was checked unless it was actually opened or reasoned from code with that limitation stated.
