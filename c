esources\views\admin\employees.blade.php
@extends('layouts.admin')
@section('content')
    <h1>👥 إدارة الموظفين</h1>
    <a href="#" class="btn-add" onclick="openModal('addEmployeeModal')">➕ إضافة موظف</a>
    
    <table class="styled-table">
        <tr>
            <th>ID</th><th>الاسم</th><th>البريد</th><th>المنصب</th><th>الإجراءات</th>
        </tr>
        @foreach($employees as $e)
            <tr>
                <td>{{ $e->id }}</td>
                <td>{{ $e->name }}</td>
                <td>{{ $e->email }}</td>
                <td>{{ $e->position }}</td>
                <td>
                    <a href="#" class="btn-edit" onclick="openModal('editEmployeeModal{{ $e->id }}')">✏️</a>
                    <form method="POST" action="#" style="display:inline">
                        @csrf @method('DELETE')
                        <button class="btn-delete" onclick="return confirm('هل أنت متأكد؟')">🗑️</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
    
    <!-- نافذة إضافة موظف جديد -->
    <div id="addEmployeeModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addEmployeeModal')">&times;</span>
            <h2>إضافة موظف جديد</h2>
            <form method="POST" action="#">
                @csrf
                <div class="form-group">
                    <label for="name">الاسم</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">كلمة المرور</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="position">المنصب</label>
                    <input type="text" id="position" name="position" class="form-control" required>
                </div>
                <button type="submit" class="btn-submit">إضافة الموظف</button>
            </form>
        </div>
    </div>
@endsection
esources\views\admin\suppliers.blade.php
@extends('layouts.admin')
@section('content')
    <h1>🏭 إدارة الموردين</h1>
    <a href="#" class="btn-add" onclick="openModal('addSupplierModal')">➕ إضافة مورد</a>
    
    <table class="styled-table">
        <tr>
            <th>ID</th><th>الاسم</th><th>الشركة</th><th>الهاتف</th><th>الإجراءات</th>
        </tr>
        @foreach($suppliers as $s)
            <tr>
                <td>{{ $s->id }}</td>
                <td>{{ $s->name }}</td>
                <td>{{ $s->company }}</td>
                <td>{{ $s->phone }}</td>
                <td>
                    <a href="#" class="btn-edit" onclick="openModal('editSupplierModal{{ $s->id }}')">✏️</a>
                    <form method="POST" action="#" style="display:inline">
                        @csrf @method('DELETE')
                        <button class="btn-delete" onclick="return confirm('هل أنت متأكد؟')">🗑️</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
    
    <!-- نافذة إضافة مورد جديد -->
    <div id="addSupplierModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addSupplierModal')">&times;</span>
            <h2>إضافة مورد جديد</h2>
            <form method="POST" action="#">
                @csrf
                <div class="form-group">
                    <label for="name">الاسم</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="company">الشركة</label>
                    <input type="text" id="company" name="company" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="phone">رقم الهاتف</label>
                    <input type="text" id="phone" name="phone" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" class="form-control">
                </div>
                <div class="form-group">
                    <label for="address">العنوان</label>
                    <textarea id="address" name="address" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn-submit">إضافة المورد</button>
            </form>
        </div>
    </div>
@endsection
esources\views\admin\feedback.blade.php
@extends('layouts.admin')
@section('content')
    <h1>📝 إدارة الملاحظات</h1>
    
    <table class="styled-table">
        <tr>
            <th>ID</th><th>الاسم</th><th>الملاحظة</th><th>التاريخ</th><th>الإجراءات</th>
        </tr>
        @foreach($feedback as $f)
            <tr>
                <td>{{ $f->id }}</td>
                <td>{{ $f->user->name ?? $f->name }}</td>
                <td>{{ $f->feedback }}</td>
                <td>{{ $f->created_at->format('Y-m-d') }}</td>
                <td>
                    <form method="POST" action="#" style="display:inline">
                        @csrf @method('DELETE')
                        <button class="btn-delete" onclick="return confirm('هل أنت متأكد؟')">🗑️</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
@endsection
esources\views\admin\orders.blade.php
@extends('layouts.admin')
@section('content')
    <h1>🛒 إدارة الطلبات</h1>
    <a href="#" class="btn-add" onclick="openModal('addOrderModal')">➕ إضافة طلب</a>
    
    <table class="styled-table">
        <tr>
            <th>ID</th><th>العميل</th><th>المنتج</th><th>الكمية</th><th>الحالة</th><th>الإجراءات</th>
        </tr>
        @foreach($orders as $o)
            <tr>
                <td>{{ $o->id }}</td>
                <td>{{ $o->user->name ?? 'زائر' }}</td>
                <td>{{ $o->product_name ?? '-' }}</td>
                <td>{{ $o->quantity }}</td>
                <td>{{ $o->status }}</td>
                <td>
                    <a href="#" class="btn-edit" onclick="openModal('editOrderModal{{ $o->id }}')">✏️</a>
                    <form method="POST" action="#" style="display:inline">
                        @csrf @method('DELETE')
                        <button class="btn-delete" onclick="return confirm('هل أنت متأكد؟')">🗑️</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
    
    <!-- نافذة إضافة طلب جديد -->
    <div id="addOrderModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addOrderModal')">&times;</span>
            <h2>إضافة طلب جديد</h2>
            <form method="POST" action="#">
                @csrf
                <div class="form-group">
                    <label for="user_id">العميل</label>
                    <select id="user_id" name="user_id" class="form-control">
                        <option value="">اختر العميل</option>
                        <!-- هنا يمكن إضافة خيارات العملاء من قاعدة البيانات -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="product_id">المنتج</label>
                    <select id="product_id" name="product_id" class="form-control" required>
                        <option value="">اختر المنتج</option>
                        <!-- هنا يمكن إضافة خيارات المنتجات من قاعدة البيانات -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="quantity">الكمية</label>
                    <input type="number" id="quantity" name="quantity" class="form-control" min="1" required>
                </div>
                <div class="form-group">
                    <label for="status">الحالة</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="pending">قيد الانتظار</option>
                        <option value="processing">قيد المعالجة</option>
                        <option value="shipped">تم الشحن</option>
                        <option value="delivered">تم التسليم</option>
                        <option value="cancelled">ملغي</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit">إضافة الطلب</button>
            </form>
        </div>
    </div>
@endsection
esources\views\admin\dashboard.blade.php
@extends('layouts.admin')

@section('content')
    <h1>📊 إحصائيات عامة</h1>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        <div class="card">
            <div class="title">📦 عدد المنتجات</div>
            <div style="font-size: 32px; font-weight: bold; color: #3498db;">{{ $products }}</div>
            <a href="{{ route('admin.products') }}" style="display: inline-block; margin-top: 10px; color: #3498db;">عرض التفاصيل</a>
        </div>
        
        <div class="card">
            <div class="title">🛒 عدد الطلبات</div>
            <div style="font-size: 32px; font-weight: bold; color: #e74c3c;">{{ $orders }}</div>
            <a href="{{ route('admin.orders') }}" style="display: inline-block; margin-top: 10px; color: #e74c3c;">عرض التفاصيل</a>
        </div>
        
        <div class="card">
            <div class="title">👥 عدد الموظفين</div>
            <div style="font-size: 32px; font-weight: bold; color: #2ecc71;">{{ $employees }}</div>
            <a href="{{ route('admin.employees') }}" style="display: inline-block; margin-top: 10px; color: #2ecc71;">عرض التفاصيل</a>
        </div>
        
        <div class="card">
            <div class="title">🏭 عدد الموردين</div>
            <div style="font-size: 32px; font-weight: bold; color: #f39c12;">{{ $suppliers }}</div>
            <a href="{{ route('admin.suppliers') }}" style="display: inline-block; margin-top: 10px; color: #f39c12;">عرض التفاصيل</a>
        </div>
        
        <div class="card">
            <div class="title">📝 عدد الملاحظات</div>
            <div style="font-size: 32px; font-weight: bold; color: #9b59b6;">{{ $feedback }}</div>
            <a href="{{ route('admin.feedback') }}" style="display: inline-block; margin-top: 10px; color: #9b59b6;">عرض التفاصيل</a>
        </div>
    </div>
@endsection
outes\web.php
// تعديل مسارات التقارير
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // لوحة التحكم
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // جميع عمليات CRUD للمنتجات
    Route::resource('products', ProductController::class);
    
    // مسارات إضافية للوحة التحكم
    Route::get('/employees', [App\Http\Controllers\EmployeeController::class, 'index'])->name('employees');
    Route::get('/suppliers', [App\Http\Controllers\SupplierController::class, 'index'])->name('suppliers');
    Route::get('/orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders');
    Route::get('/feedback', [App\Http\Controllers\FeedbackController::class, 'index'])->name('feedback');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('/reports/employees', [ReportController::class, 'employees'])->name('reports.employees');
    Route::get('/reports/customers', [ReportController::class, 'customers'])->name('reports.customers');
});