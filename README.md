# Techmart – Hệ thống quản trị và API thương mại điện tử

Dự án gồm 2 phần chính:
- `laravel-api/`: Backend API (Laravel 10, MySQL, Redis, JWT, Queue, Excel)
- `nuxt-app/`: Frontend Admin (Nuxt 3, TypeScript, TailwindCSS, PrimeVue, VeeValidate/Zod, i18n)

## 1) Yêu cầu hệ thống
- Node.js >= 18, npm >= 9
- PHP >= 8.2, Composer >= 2
- MySQL/MariaDB
- Redis (cache/queue)
- Git, và các tiện ích CLI quen thuộc

## 2) Clone & cấu trúc dự án
```bash
git clone <repo_url> techmart
cd techmart
# Cấu trúc chính
# - laravel-api/ (Laravel)
# - nuxt-app/ (Nuxt 4)
```

## 3) Backend (Laravel API)
### 3.1. Cài đặt
```bash
cd laravel-api
cp .env.example .env    # hoặc tự tạo .env
composer install --no-interaction --prefer-dist
php artisan key:generate
```

### 3.2. Cấu hình .env (ví dụ)
```env
APP_NAME=Techmart
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

# DB
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=techmart
DB_USERNAME=root
DB_PASSWORD=your_password

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Queue (Redis)
QUEUE_CONNECTION=redis

# Filesystems
FILESYSTEM_DISK=public

# JWT
JWT_SECRET=changeme
JWT_TTL=1440

# CORS
SANCTUM_STATEFUL_DOMAINS=localhost:3000
SESSION_DOMAIN=localhost
```
Tạo secret cho JWT nếu dùng tymon/jwt (nếu có):
```bash
php artisan jwt:secret
```

### 3.3. Database
```bash
php artisan migrate
php artisan db:seed   # nếu có seeder
```

### 3.4. Chạy server
```bash
php artisan serve --host=127.0.0.1 --port=8000
```
API sẽ chạy tại: `http://127.0.0.1:8000`

### 3.5. Queue/Job (khuyến nghị chạy nền)
```bash
php artisan queue:work --queue=default --tries=3
```

### 3.6. Cache
- Xoá cache theo tag/key được xử lý trong `App\Traits\HasCache`
- Lệnh hữu ích:
```bash
php artisan cache:clear
php artisan config:clear && php artisan config:cache
php artisan route:cache && php artisan view:cache
```

### 3.7. Upload & Storage
```bash
php artisan storage:link
```
Tệp upload dùng disk `public`, đường dẫn public qua `storage/`.

### 3.8. Excel (Import/Export)
- Các Job/Service trong `app/Services/Excel` và `app/Jobs/*ExcelJob.php`
- Cần queue hoạt động để xử lý export/import nhẹ nhàng.

### 3.9. Chuẩn query/filters
- Trait `HasQuery` hỗ trợ: keyword, simple/complex filter, date, relation
- Dùng `BaseService` để cấu hình `fieldSearchs`, `simpleFilter`, `complexFilter`, `with`…

### 3.10. Auth
- JWT hoặc Sanctum tuỳ config
- Header: `Authorization: Bearer <token>`

## 4) Frontend (Nuxt 3 Admin)
### 4.1. Cài đặt
```bash
cd ../nuxt-app
cp .env.example .env  # nếu có
npm ci   # hoặc: npm install
```

### 4.2. Cấu hình môi trường (ví dụ `.env` hoặc `nuxt.config.ts`)
- Base API URL trỏ về Laravel: `http://127.0.0.1:8000`
- i18n: vi/en

### 4.3. Chạy dev
```bash
npm run dev
```
App chạy tại: `http://localhost:3000`

### 4.4. Build & Preview
```bash
npm run build
npm run preview
```

### 4.5. Kiểm tra kiểu/lint (nếu bật)
```bash
npm run lint
npm run typecheck
```

## 5) Luồng phát triển & conventions
- FE sử dụng VeeValidate + Zod (inline schema) cho form validation
- Sử dụng PrimeVue (Select, MultiSelect, …), TailwindCSS cho UI
- i18n đặt trong `app/locales/en.json`, `app/locales/vi.json`
- Service gọi API trong `app/services/**`, composables trong `app/composables/**`
- Đặt constants/route name trong `app/utils/constants.ts`

## 6) Các lệnh nhanh (Windows/Git Bash)
```bash
# Backend
cd laravel-api && composer install && cp .env.example .env && php artisan key:generate && php artisan migrate && php artisan serve

# Frontend
cd nuxt-app && npm ci && npm run dev
```

## 7) Một số module tiêu biểu
- User & User Catalogue: CRUD, phân nhóm, publish (0: Ẩn, 1: Nháp, 2: Hiển thị)
- Address Order: quản lý địa chỉ giao hàng; đảm bảo mỗi user chỉ có 1 địa chỉ mặc định (`is_default`)
- Upload/Media: lưu qua disk `public`
- Cache theo tag/key: clear khi save/delete theo `HasCache`

## 8) Troubleshooting
- “Unknown column 'name' in where clause”: cấu hình `fieldSearchs` trong Service cho đúng cột (VD: `['title','full_name','phone']`)
- Chọn “Địa chỉ mặc định” không cập nhật do cache: đã clear cache các bản ghi liên quan sau khi set `is_default`
- Select PrimeVue không nhận giá trị: dùng `:modelValue` + `@update:modelValue` thay cho `v-model` trong slot Field
- Publish hiển thị sai: ép kiểu `Number(item.publish)` trước khi render

## 9) Sản xuất/Deploy (gợi ý)
- Laravel: bật cache config/route/view, queue worker, giám sát bằng Supervisor
- Nuxt: `npm run build` và serve bằng Node/nginx, cấu hình proxy về API
- Dùng `.env` riêng cho prod (APP_DEBUG=false)

## 10) Cấu trúc thư mục chính
```
techmart/
├─ laravel-api/
│  ├─ app/
│  │  ├─ Http/
│  │  ├─ Models/
│  │  ├─ Services/
│  │  ├─ Traits/
│  │  └─ ...
│  ├─ config/
│  ├─ database/
│  ├─ routes/
│  └─ ...
└─ nuxt-app/
   ├─ app/
   │  ├─ components/
   │  ├─ composables/
   │  ├─ pages/
   │  ├─ services/
   │  ├─ stores/
   │  └─ locales/
   └─ nuxt.config.ts
```

## 11) Liên hệ / đóng góp
- Tạo issue/PR theo mẫu trong repo
- Format code theo linting của dự án, commit rõ ràng

—
Nếu cần script “one-click” dev cho Windows/WSL, có thể bổ sung sau.