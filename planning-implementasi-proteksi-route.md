# Planning Implementasi Proteksi Route Berdasarkan Role dan Permission

**Tanggal**: 2026-06-08  
**Status**: Planning  
**Prioritas**: Tinggi - Security Issue

---

## 1. Executive Summary

Aplikasi ticketing saat ini memiliki celah keamanan di mana route-route yang seharusnya hanya bisa diakses oleh role tertentu masih bisa diakses oleh role lain melalui akses paksa (force access) dengan mengetahui URL-nya.

Contoh:
- User dengan role `pegawai-dinas` bisa akses `/categories`, `/users`, `/knowledge`, `/roles` dengan langsung mengetikkan URL, meskipun tidak memiliki permission
- User bisa memanipulasi request untuk `store`, `update`, `destroy` tanpa validasi backend

**Solusi**: Implementasi protection middleware berbasis Spatie Permission di semua protected routes dan validation logic di controllers.

---

## 2. Analisis Kondisi Saat Ini

### 2.1 Role dan Permission yang Sudah Didefinisikan

#### Admin
- `view dashboard`
- `view tickets`, `assign tickets`, `change status tickets`
- `view kategori`, `create kategori`, `edit kategori`, `delete kategori`
- `view knowledge base`, `create knowledge base`, `edit knowledge base`, `delete knowledge base`, `train`
- `view log chatbot`, `validate log chatbot`
- `view pengguna`, `create pengguna`, `edit pengguna`, `delete pengguna`
- `view role permission`
- `view dashboard tickets menu`, `view dashboard model menu`

#### Kepala-Diskominfo
- `view dashboard`
- `view tickets`, `assign tickets`, `change status tickets`
- `view dashboard tickets menu`

#### Pegawai-Dinas
- `view dashboard`
- `create tickets`, `view my tickets`
- `view dashboard my tickets menu`

### 2.2 Routes yang Sudah Terlindungi (dengan middleware)

```php
// routes/web.php

// Sudah protected dengan auth + middleware can
Route::post('/{ticket}/assign', [TicketController::class, 'assign'])
    ->middleware('can:assign,ticket');

Route::post('/{ticket}/status', [TicketController::class, 'updateStatus'])
    ->middleware('can:updateStatus,ticket');

// Dashboard (tapi ada logic di controller yang tidak konsisten)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
```

### 2.3 Routes yang TIDAK Terlindungi (CELAH KEAMANAN)

| Route | Method | Controller | Permission Seharusnya | Status Saat Ini |
|-------|--------|------------|----------------------|-----------------|
| `/categories` | GET | CategoryController@index | `view kategori` | Hanya `auth` |
| `/categories` | POST | CategoryController@store | `create kategori` | Hanya `auth` |
| `/categories/{id}` | PUT | CategoryController@update | `edit kategori` | Hanya `auth` |
| `/categories/{id}` | DELETE | CategoryController@destroy | `delete kategori` | Hanya `auth` |
| `/knowledge` | GET | KnowledgeBaseController@index | `view knowledge base` | Hanya `auth` |
| `/knowledge` | POST | KnowledgeBaseController@store | `create knowledge base` | Hanya `auth` |
| `/knowledge/{id}` | PUT | KnowledgeBaseController@update | `edit knowledge base` | Hanya `auth` |
| `/knowledge/{id}` | DELETE | KnowledgeBaseController@destroy | `delete knowledge base` | Hanya `auth` |
| `/export-dataset` | POST | KnowledgeBaseController@exportDataset | `train` | Hanya `auth` |
| `/train-model` | POST | KnowledgeBaseController@trainModel | `train` | Hanya `auth` |
| `/chatbot/logs` | GET | ChatbotController@logs | `view log chatbot` | Hanya `auth` |
| `/chatbot/validate` | POST | ChatbotController@validatePrediction | `validate log chatbot` | Hanya `auth` |
| `/users` | GET | UserController@index | `view pengguna` | Hanya `auth` |
| `/users` | POST | UserController@store | `create pengguna` | Hanya `auth` |
| `/users/{id}` | PUT | UserController@update | `edit pengguna` | Hanya `auth` |
| `/users/{id}/password` | PUT | UserController@resetPassword | `edit pengguna` | Hanya `auth` |
| `/users/{id}` | DELETE | UserController@destroy | `delete pengguna` | Hanya `auth` |
| `/roles` | GET | RolePermissionController@index | `view role permission` | Hanya `auth` |
| `/roles/{id}` | POST | RolePermissionController@update | `view role permission` | Hanya `auth` |

### 2.4 Status Middleware

**Sudah tersedia di bootstrap/app.php:**
```php
$middleware->alias([
    'role' => RoleMiddleware::class,
    'permission' => PermissionMiddleware::class,
    'role_or_permission' => RoleOrPermissionMiddleware::class,
]);
```

**Syntax penggunaan middleware:**
- `->middleware('permission:view pengguna')` - Perlu exact permission
- `->middleware('role:admin')` - Perlu exact role
- `->middleware('permission:view pengguna|create pengguna')` - OR condition

---

## 3. Rencana Implementasi

### 3.1 Fase 1: Update Routes dengan Permission Middleware

**File: `routes/web.php`**

Tambahkan middleware permission pada route groups:

```php
// Categories - hanya admin dan staff tertentu
Route::middleware('permission:view kategori')->prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::post('/', [CategoryController::class, 'store'])->middleware('permission:create kategori')->name('store');
    Route::put('/{category}', [CategoryController::class, 'update'])->middleware('permission:edit kategori')->name('update');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->middleware('permission:delete kategori')->name('destroy');
});

// Knowledge Base - hanya admin
Route::middleware('permission:view knowledge base')->prefix('knowledge')->name('knowledge.')->group(function () {
    Route::get('/', [KnowledgeBaseController::class, 'index'])->name('index');
    Route::post('/', [KnowledgeBaseController::class, 'store'])->middleware('permission:create knowledge base')->name('store');
    Route::put('/{knowledge}', [KnowledgeBaseController::class, 'update'])->middleware('permission:edit knowledge base')->name('update');
    Route::delete('/{knowledge}', [KnowledgeBaseController::class, 'destroy'])->middleware('permission:delete knowledge base')->name('destroy');
});

// Training routes
Route::post('/export-dataset', [KnowledgeBaseController::class, 'exportDataset'])
    ->middleware('permission:train')
    ->name('knowledge.export-dataset');

Route::post('/train-model', [KnowledgeBaseController::class, 'trainModel'])
    ->middleware('permission:train')
    ->name('knowledge.train-model');

// Chatbot - logs dan validate only untuk admin
Route::middleware('permission:view log chatbot')->prefix('chatbot')->name('chatbot.')->group(function () {
    Route::get('/logs', [ChatbotController::class, 'logs'])->name('logs');
    Route::post('/validate', [ChatbotController::class, 'validatePrediction'])->middleware('permission:validate log chatbot')->name('validate');
});

// Users management - hanya admin
Route::middleware('permission:view pengguna')->prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::post('/', [UserController::class, 'store'])->middleware('permission:create pengguna')->name('store');
    Route::put('/{user}', [UserController::class, 'update'])->middleware('permission:edit pengguna')->name('update');
    Route::put('/{user}/password', [UserController::class, 'resetPassword'])->middleware('permission:edit pengguna')->name('password.reset');
    Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('permission:delete pengguna')->name('destroy');
});

// Roles and Permission Management - hanya admin
Route::middleware('permission:view role permission')->group(function () {
    Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index');
    Route::post('/roles/{role}', [RolePermissionController::class, 'update'])->name('roles.update');
});
```

### 3.2 Fase 2: Tambahkan Permission Check di Controllers

**File: `app/Http/Controllers/CategoryController.php`**

```php
// Di setiap method, tambahkan authorization check
public function index(): \Illuminate\View\View
{
    $this->authorize('view', Category::class);
    // ... existing code
}

public function store(Request $request)
{
    $this->authorize('create', Category::class);
    // ... existing code
}

public function update(Request $request, int $id)
{
    $category = Category::findOrFail($id);
    $this->authorize('update', $category);
    // ... existing code
}

public function destroy(Request $request, int $id)
{
    $category = Category::findOrFail($id);
    $this->authorize('delete', $category);
    // ... existing code
}
```

**Atau buat Policy File: `app/Policies/CategoryPolicy.php`**

```php
<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function view(User $user): bool
    {
        return $user->hasPermissionTo('view kategori');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create kategori');
    }

    public function update(User $user, Category $category): bool
    {
        return $user->hasPermissionTo('edit kategori');
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->hasPermissionTo('delete kategori');
    }
}
```

**Alternatif lebih sederhana: Tambahkan di Controller**

```php
public function __construct()
{
    $this->middleware('permission:view kategori')->only(['index']);
    $this->middleware('permission:create kategori')->only(['store']);
    $this->middleware('permission:edit kategori')->only(['update']);
    $this->middleware('permission:delete kategori')->only(['destroy']);
}
```

### 3.3 Fase 3: Tambahkan Authorization untuk Ticket Routes

Ticket memiliki business logic yang lebih kompleks. Tambahkan checks di `TicketController`:

```php
// Di TicketController

// Tickets index - hanya bisa lihat ticket mereka atau jika admin/staff
public function index()
{
    if (auth()->user()->hasPermissionTo('view tickets')) {
        // Admin/staff bisa lihat semua tickets
        $tickets = Ticket::all();
    } else {
        // User normal hanya lihat ticket mereka
        abort_if(!auth()->user()->hasPermissionTo('view my tickets'), 403);
        $tickets = Ticket::where('user_id', auth()->id())->get();
    }
    // ... existing code
}

// Tickets create - perlu permission create
public function create()
{
    $this->authorize('create', Ticket::class);
    // ... existing code
}

// Tickets show - bisa lihat jika pemilik atau assigned atau admin
public function show(Ticket $ticket)
{
    abort_if(
        !($ticket->user_id === auth()->id() || 
          $ticket->assigned_to === auth()->id() || 
          auth()->user()->hasPermissionTo('view tickets')),
        403
    );
    // ... existing code
}
```

### 3.4 Fase 4: Add Dashboard Permission Checks

**File: `app/Http/Controllers/DashboardController.php`**

```php
public function index(): \Illuminate\View\View
{
    // Ensure user has permission to view dashboard
    $this->authorize('view', Dashboard::class); // atau simple:
    abort_if(!auth()->user()->hasPermissionTo('view dashboard'), 403);

    // ... existing code
}
```

### 3.5 Fase 5: Exception Handling

Tambahkan custom exception handling untuk unauthorized access di `bootstrap/app.php`:

```php
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->renderable(function (AuthorizationException $e, $request) {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return abort(403, 'Anda tidak memiliki akses ke resource ini.');
    });
})
```

---

## 4. Checklist Implementasi

### Fase 1: Route Protection
- [ ] Update `routes/web.php` - Tambah middleware permission pada categories
- [ ] Update `routes/web.php` - Tambah middleware permission pada knowledge base
- [ ] Update `routes/web.php` - Tambah middleware permission pada chatbot
- [ ] Update `routes/web.php` - Tambah middleware permission pada users
- [ ] Update `routes/web.php` - Tambah middleware permission pada roles
- [ ] Update `routes/web.php` - Tambah middleware permission pada training routes
- [ ] Test setiap route apakah benar 403 jika tidak ada permission

### Fase 2: Controller Authorization
- [ ] Tambah `$this->authorize()` di CategoryController
- [ ] Tambah `$this->authorize()` di UserController
- [ ] Tambah `$this->authorize()` di RolePermissionController
- [ ] Tambah `$this->authorize()` di KnowledgeBaseController
- [ ] Tambah `$this->authorize()` di ChatbotController

### Fase 3: Ticket-specific Logic
- [ ] Update TicketController::index() - filter berdasarkan role
- [ ] Update TicketController::show() - check ownership atau role
- [ ] Update TicketController::update() - check ownership atau assigned
- [ ] Update TicketController::messages() - check access
- [ ] Update TicketController::datatable() - add permission filter

### Fase 4: Dashboard
- [ ] Update DashboardController::index() - add permission check

### Fase 5: Testing & Validation
- [ ] Test dengan user role `pegawai-dinas` - tidak bisa akses `/categories`
- [ ] Test dengan user role `pegawai-dinas` - tidak bisa akses `/users`
- [ ] Test dengan user role `kepala-diskominfo` - tidak bisa akses `/knowledge`
- [ ] Test dengan user role `admin` - bisa akses semua
- [ ] Test with curl atau Postman untuk bypass attempt
- [ ] Check response status 403 saat unauthorized

### Fase 6: Documentation & Deployment
- [ ] Dokumentasi authorization rules di setiap controller
- [ ] Add permission seeds untuk new features
- [ ] Database migration jika perlu permission baru
- [ ] Deployment & testing di production

---

## 5. Testing Strategy

### 5.1 Manual Testing

**Test Case 1: User Pegawai-Dinas tidak bisa akses Categories**
```
User: pegawai-dinas
Action: GET /categories
Expected: 403 Forbidden
Reason: Tidak memiliki 'view kategori' permission
```

**Test Case 2: Admin bisa akses Categories**
```
User: admin
Action: GET /categories
Expected: 200 OK dengan data
Reason: Memiliki 'view kategori' permission
```

**Test Case 3: Force Access dengan URL**
```
User: pegawai-dinas
Action: POST /categories dengan data yang valid
Expected: 403 Forbidden
Reason: Middleware permission check sebelum controller
```

### 5.2 Automated Testing

Buat test file `tests/Feature/AuthorizationTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $pegawaiDinasUser;
    protected User $kepalaUser;

    protected function setUp(): void
    {
        parent::setUp();
        // Create roles and users
        // ... setup code
    }

    public function test_pegawai_dinas_cannot_access_categories_index()
    {
        $response = $this->actingAs($this->pegawaiDinasUser)
            ->get('/categories');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_categories_index()
    {
        $response = $this->actingAs($this->adminUser)
            ->get('/categories');

        $response->assertStatus(200);
    }

    // ... more tests
}
```

---

## 6. Implementation Order

1. **Priority 1** (Highest Risk):
   - `/categories` - Create/Update/Delete
   - `/users` - Create/Update/Delete
   - `/roles` - Update permissions

2. **Priority 2** (Medium Risk):
   - `/knowledge` - Create/Update/Delete
   - `/train-model`, `/export-dataset`

3. **Priority 3** (Lower Risk):
   - `/chatbot/logs`, `/chatbot/validate`
   - Dashboard view logic

---

## 7. Potential Issues & Mitigation

| Issue | Mitigation |
|-------|-----------|
| Existing permissions di database tidak sesuai | Jalankan `php artisan cache:clear` setelah seed |
| User tidak bisa akses feature yang seharusnya | Check user roles dan permissions di database |
| API routes tidak protected | Extend protection ke API routes jika diperlukan |
| Response message terlalu detail | Configure exception message di config |
| Performance hit dari permission check | Permission sudah di-cache oleh Spatie |

---

## 8. Success Criteria

✅ **Implementation dianggap sukses jika:**
- Semua protected routes mengembalikan 403 saat user unauthorized
- Admin bisa akses semua protected routes
- Setiap role hanya bisa akses sesuai permission yang diberikan
- Unit test untuk setiap authorization scenario passed
- Zero security warning dari manual penetration testing

---

## 9. Referensi Code

### Laravel Authorization Documentation
- Policies: https://laravel.com/docs/authorization#creating-policies
- Gate: https://laravel.com/docs/authorization#gates
- Middleware: https://laravel.com/docs/middleware

### Spatie Permission
- Middleware Usage: https://spatie.be/docs/laravel-permission/v6/middleware-headers
- Permission Caching: https://spatie.be/docs/laravel-permission/v6/cache-header

---

**Status**: ✏️ Ready for Implementation  
**Next Step**: Approve planning → Implement Fase 1 → Test → Deploy
