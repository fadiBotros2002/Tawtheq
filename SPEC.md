# SPEC — نظام توثيق (Tawtheq)

> **آخر تحديث:** 2026-06-07  
> **الحالة:** مُنفَّذ ويعمل (33 اختبار يمرّ)

---

## ⚠️ تعليمات صيانة هذا الملف (إلزامي)

**أي تعديل على المشروع يجب أن يُحدَّث في هذا الملف فوراً.**

قبل البدء بأي مهمة جديدة، اقرأ هذا الملف أولاً.  
بعد إنهاء أي مهمة، حدّث الأقسام المتأثرة + تاريخ `آخر تحديث`.

الأقسام التي تتغير غالباً:
- المسارات (`routes/web.php`)
- جداول قاعدة البيانات
- الأدوار والصلاحيات
- سير العمل
- ما هو مُنفَّذ / ما هو غير مُنفَّذ

---

## 1. ما هو المشروع؟

**نظام ديوان** — أرشفة وتوثيق إلكتروني للوثائق الرسمية.

| ليس هذا المشروع | هو هذا المشروع |
|-----------------|----------------|
| CMS / مدونة / مقالات | رفع وثائق رسمية (PDF/صور) |
| نظام مراسلات بموافقة checker | رفع مباشر + رابط تحقق عام |
| تسجيل دخول بالبريد | تسجيل دخول بـ **username** فقط |
| تخزين محلي عام | تخزين **خاص على AWS S3** مع بث عبر Laravel |

---

## 2. التقنيات

| الطبقة | التقنية |
|--------|---------|
| Backend | Laravel 12, PHP 8.2+ |
| Auth UI | Laravel Breeze (Blade) |
| Frontend | Blade + Tailwind CSS + Alpine.js + Vite |
| Database | MySQL (إنتاج) / SQLite (تطوير) |
| Storage | AWS S3 عبر `league/flysystem-aws-s3-v3` |
| QR Code | `simplesoftwareio/simple-qrcode` |
| Tests | PHPUnit (33 test) |

---

## 3. المستخدمون والمصادقة

### 3.1 تسجيل الدخول
- الحقل: **`username`** + `password`
- **لا يوجد** تسجيل دخول بالبريد الإلكتروني
- **لا يوجد** تسجيل حساب عام (`/register` → 404)
- **يوجد** استعادة كلمة مرور عبر البريد (`/forgot-password`)
- **لا يوجد** تحقق بريد (`/verify-email` → 404)

### 3.2 البريد الإلكتروني
- **مطلوب** عند إنشاء المستخدم (admin أو Seeder)
- **قابل للتعديل** في الملف الشخصي (الاسم + البريد)
- **لا يُستخدم** لتسجيل الدخول — فقط لاستعادة كلمة المرور
- فريد (`unique`) في قاعدة البيانات

### 3.3 قواعد username
- فريد، **غير قابل للتعديل** بعد الإنشاء
- صيغة: `^[a-z][a-z0-9_]*$` (3–30 حرف)
- يبدأ بحرف إنجليزي صغير، ثم أحرف/أرقام/`_`

### 3.4 الأدوار

| الدور | القيمة في DB | الصلاحيات |
|-------|-------------|-----------|
| مدير | `admin` | يرى كل الوثائق + ينشئ مستخدمين |
| موظف | `user` | يرفع وثائق + يرى وثائقه فقط |

### 3.5 إنشاء المستخدمين
- **فقط** المدير (`admin`) أو الـ Seeder
- المسار: `/admin/users` (محمي بـ middleware `admin`)
- المستخدم العادي يعدّل **الاسم والبريد** في الملف الشخصي، لا username

### 3.6 حسابات Seeder

| username | email | password | role |
|----------|-------|----------|------|
| `admin` | `admin@diwan.local` | `123123123` | admin |
| `ahmad` | `ahmad@diwan.local` | `123123123` | user |

### 3.7 استعادة كلمة المرور
1. المستخدم يفتح `/forgot-password` ويدخل **البريد الإلكتروني**
2. يُرسل رابط إعادة التعيين عبر Laravel Notifications
3. الرابط يوجّه إلى `/reset-password/{token}`
4. بعد التعيين → redirect إلى `/login`

---

## 4. الوثائق (Documents)

### 4.1 أنواع الوثائق

| القيمة | التسمية العربية |
|--------|----------------|
| `inbound` | وارد |
| `outbound` | صادر |

Enum: `App\Enums\DocumentType`

### 4.2 التسلسل (Sequence)
- رقم صحيح **تراكمي عالمي** — لا يتصفر أبداً: `1, 2, 3, 4...`
- يزداد مع **كل وثيقة جديدة** بغض النظر عن النوع أو المستخدم
- يُولَّد داخل `DB::transaction` + `lockForUpdate()` لمنع race conditions
- يُخزَّن كـ `integer`، يُعرض في الروابط بصيغة **4 أرقام** مع padding: `0001`, `0004`

### 4.3 تاريخ الرفع (`upload_date`)
- صيغة: **`dmY`** (يوم/شهر/سنة بدون فواصل)
- مثال: `07062026` = 7 يونيو 2026
- يُحسب عند الرفع: `now()->format('dmY')`

### 4.4 الملفات المقبولة
- PDF, JPG, JPEG, PNG
- حد أقصى: 50 MB (`max:51200` KB)

### 4.5 مسار S3 (خاص)
```
documents/{username}/{type}/{upload_date}/{filename}
```
- visibility: **private** (لا روابط S3 مباشرة أبداً)
- البث عبر: `Storage::disk('s3')->response($path)`

---

## 5. روابط التحقق العامة

### 5.1 صيغة الرابط (إلزامية)

```
/{username}/{doctype}/{date}/{sequence}
```

**مثال:**
```
/ahmad/outbound/07062026/0004
```

| الجزء | القاعدة |
|-------|---------|
| `username` | `[a-z][a-z0-9_]*` |
| `doctype` | `inbound` أو `outbound` |
| `date` | 8 أرقام `dmY` |
| `sequence` | 4 أرقام مع padding |

### 5.2 بث الملف العام

```
/{username}/{doctype}/{date}/{sequence}/file
```

### 5.3 قاعدة المسارات (مهمة)
مسارات التحقق العامة **يجب أن تكون في آخر `routes/web.php`**  
بعد `require auth.php` — حتى لا تعترض `/login`, `/documents`, `/admin`.

---

## 6. سير العمل (Workflow)

```
1. المستخدم يسجّل دخول (username)
        ↓
2. يختار نوع المعاملة (وارد/صادر) ويرفع الملف
        ↓
3. DocumentService:
   - يقفل آخر sequence (lockForUpdate)
   - يحسب التالي (+1)
   - يرفع الملف لـ S3 خاص
   - ينشئ سجل Document
        ↓
4. يُعرض:
   - رابط التحقق العام (نسخ + فتح)
   - QR Code يوجّه للرابط (تنزيل PNG)
        ↓
5. أي شخص يفتح الرابط العام:
   - يرى metadata (username, type, date, sequence, filename)
   - معاينة الملف عبر /file (stream من S3 بدون كشف URL)
```

---

## 7. المسارات (Routes)

### 7.1 عام (بدون auth)

| Method | Path | الاسم | الوصف |
|--------|------|-------|-------|
| GET | `/` | — | redirect → login |
| GET | `/locale/{locale}` | `locale.switch` | تبديل اللغة (`ar` \| `en`) |
| GET | `/{username}/{doctype}/{date}/{sequence}` | `documents.verify` | صفحة تحقق عامة |
| GET | `/{username}/{doctype}/{date}/{sequence}/file` | `documents.verify.stream` | بث الملف للعامة |

### 7.2 يتطلب auth

| Method | Path | الاسم | الوصف |
|--------|------|-------|-------|
| GET | `/login` | `login` | تسجيل دخول |
| POST | `/login` | — | |
| GET | `/forgot-password` | `password.request` | نموذج استعادة كلمة المرور |
| POST | `/forgot-password` | `password.email` | إرسال رابط الاستعادة |
| GET | `/reset-password/{token}` | `password.reset` | نموذج كلمة مرور جديدة |
| POST | `/reset-password` | `password.store` | حفظ كلمة المرور الجديدة |
| POST | `/logout` | `logout` | |
| GET | `/dashboard` | `dashboard` | redirect → documents.index |
| GET | `/documents` | `documents.index` | قائمة الوثائق |
| GET | `/documents/create` | `documents.create` | نموذج رفع |
| POST | `/documents` | `documents.store` | حفظ وثيقة |
| GET | `/documents/{document}` | `documents.show` | تفاصيل + QR + نسخ الرابط |
| GET | `/documents/{document}/stream` | `documents.stream` | بث الملف (مسجّل) |
| GET | `/profile` | `profile.edit` | الملف الشخصي |
| PATCH | `/profile` | `profile.update` | تحديث الاسم والبريد |

### 7.3 يتطلب admin

| Method | Path | الاسم | الوصف |
|--------|------|-------|-------|
| GET | `/admin/users` | `admin.users.index` | قائمة المستخدمين |
| GET | `/admin/users/create` | `admin.users.create` | نموذج إنشاء |
| POST | `/admin/users` | `admin.users.store` | حفظ مستخدم |

---

## 8. قاعدة البيانات

### 8.1 `users`

| العمود | النوع | ملاحظات |
|--------|-------|---------|
| `id` | bigint | PK |
| `username` | string | unique, immutable |
| `name` | string | قابل للتعديل |
| `email` | string | unique، لاستعادة كلمة المرور فقط |
| `password` | string | hashed |
| `role` | enum | `admin` \| `user` |
| `email_verified_at` | timestamp nullable | legacy من Breeze |
| `remember_token` | string | |
| `timestamps` | | |

### 8.2 `documents`

| العمود | النوع | ملاحظات |
|--------|-------|---------|
| `id` | bigint | PK |
| `user_id` | FK → users | cascade delete |
| `type` | enum | `inbound` \| `outbound` |
| `upload_date` | string(8) | `dmY` |
| `sequence` | unsigned int | unique عالمي |
| `s3_path` | string | مسار S3 الخاص |
| `original_filename` | string | اسم الملف الأصلي |
| `mime_type` | string nullable | |
| `timestamps` | | |

**Indexes:**
- `sequence` → unique
- `(user_id, type, upload_date, sequence)` → unique

---

## 9. هيكل الكود

```
app/
├── Enums/
│   └── DocumentType.php          # inbound | outbound
├── Http/
│   ├── Controllers/
│   │   ├── DocumentController.php
│   │   ├── LocaleController.php
│   │   └── Admin/UserController.php
│   ├── Middleware/
│   │   ├── EnsureUserIsAdmin.php  # alias: admin
│   │   └── SetLocale.php          # ar/en من الجلسة
│   └── Requests/
│       ├── StoreDocumentRequest.php
│       ├── Admin/StoreUserRequest.php
│       └── Auth/LoginRequest.php    # username-based
├── Models/
│   ├── Document.php
│   └── User.php
└── Services/
    └── DocumentService.php        # sequence + S3 upload

resources/views/
├── documents/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── show.blade.php             # QR + verify URL + نسخ/تنزيل
├── public/
│   └── verify.blade.php           # صفحة التحقق العامة
└── admin/users/
    ├── index.blade.php
    └── create.blade.php

routes/
├── web.php                        # مسارات التحقق في النهاية
└── auth.php                       # login/logout/password فقط

database/
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   └── 2026_06_07_000002_create_documents_table.php
└── seeders/DatabaseSeeder.php

lang/
├── ar/diwan.php                   # ترجمات التطبيق (عربي)
├── en/diwan.php                   # ترجمات التطبيق (إنجليزي)
├── ar/passwords.php               # رسائل استعادة كلمة المرور
└── en/passwords.php
```

---

## 10. متغيرات البيئة (.env)

```env
APP_NAME=Tawtheq
APP_LOCALE=ar
APP_FALLBACK_LOCALE=en
FILESYSTEM_DISK=s3

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
AWS_BUCKET=
```

---

## 10.1 الترجمة (Localization)

| اللغة | القيمة | الاتجاه |
|-------|--------|---------|
| العربية (افتراضي) | `ar` | RTL |
| English | `en` | LTR |

- التبديل: `/locale/{locale}` — يُحفظ في الجلسة
- مكوّن `<x-locale-switcher />` في شريط التنقل وصفحات الضيف — أيقونة لغة (globe) مع قائمة منسدلة
- صفحة التحقق العامة: `<x-locale-switcher labeled />` — زر واضح بعنوان «اللغة» واللغة الحالية
- صفحة التحقق العامة: شارة خضراء «الوثيقة موثّقة» مع أيقونة ✓
- ملفات الترجمة: `lang/{ar,en}/diwan.php`
- `DocumentType::label()` يستخدم `__('diwan.document_type.*')`

---

## 11. ما هو مُنفَّذ ✅

- [x] تسجيل دخول بـ username
- [x] أدوار admin / user
- [x] إنشاء مستخدمين (admin فقط + seeder) مع بريد إلكتروني
- [x] استعادة كلمة المرور عبر البريد (forgot password)
- [x] تحديث البريد في الملف الشخصي
- [x] ترجمة ar/en مع مبدّل لغة
- [x] رفع وثائق (inbound/outbound)
- [x] تسلسل تراكمي عالمي مع DB locking
- [x] تخزين خاص على S3
- [x] بث الملفات عبر Laravel proxy (بدون روابط S3)
- [x] رابط تحقق عام بالصيغة المحددة
- [x] QR Code بعد الرفع (تنزيل PNG + نسخ رابط التحقق)
- [x] صفحة تحقق عامة + معاينة ملف
- [x] 33 اختبار PHPUnit

---

## 12. ما هو غير مُنفَّذ ❌

- [ ] فلترة/بحث في قائمة الوثائق
- [ ] تعديل أو حذف وثيقة بعد الرفع
- [ ] اختبارات Document workflow (feature tests)
- [ ] دعم MinIO محلي للتطوير بدون AWS
- [ ] API REST
- [ ] إشعارات
- [ ] سجل تدقيق (audit log)

---

## 13. أوامر مفيدة

```bash
# إعداد أولي
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed

# تشغيل
composer dev          # server + queue + logs + vite
php artisan serve     # server فقط

# اختبارات
php artisan test
```

---

## 14. سجل التغييرات

| التاريخ | التغيير |
|---------|---------|
| 2026-06-07 | تحويل المشروع من نظام مراسلات (Correspondence) إلى نظام ديوان (Document + S3 + QR + verify URL) |
| 2026-06-07 | إنشاء هذا الملف SPEC.md |
| 2026-06-07 | إضافة البريد الإلكتروني + forgot password + ترجمة ar/en |
| 2026-06-07 | تحديث مبدّل اللغة: أيقونة عصرية + قائمة منسدلة بدل أزرار الأعلام |
| 2026-06-07 | إصلاح موضع مبدّل اللغة في صفحات الضيف (زاوية بطاقة النموذج العلوية) |
| 2026-06-07 | إصلاح 419: توحيد APP_URL مع المتصفح + روابط نسبية لتبديل اللغة والنماذج |
| 2026-06-07 | تغيير اسم المشروع من cms إلى Tawtheq (APP_NAME، قاعدة البيانات، npm، الترجمات) |
| 2026-06-07 | صفحة التحقق العامة: شارة توثيق خضراء + مبدّل لغة بعنوان واضح (`labeled`) |
| 2026-06-07 | صفحة تفاصيل الوثيقة: زر نسخ رابط التحقق + تنزيل QR كـ PNG |
