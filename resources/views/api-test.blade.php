<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .response-container { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 20px; }
        .form-container { margin-bottom: 30px; padding: 20px; border: 1px solid #dee2e6; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">اختبار واجهة API</h1>
        
        <!-- نموذج التسجيل -->
        <div class="form-container">
            <h3>تسجيل مستخدم جديد</h3>
            <form id="registerForm">
                <div class="mb-3">
                    <label for="name" class="form-label">الاسم</label>
                    <input type="text" class="form-control" id="reg-name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">البريد الإلكتروني</label>
                    <input type="email" class="form-control" id="reg-email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <input type="password" class="form-control" id="reg-password" required>
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                    <input type="password" class="form-control" id="reg-password-confirm" required>
                </div>
                <button type="submit" class="btn btn-primary">تسجيل</button>
            </form>
            <div id="register-response" class="response-container mt-3" style="display: none;"></div>
        </div>
        
        <!-- نموذج تسجيل الدخول -->
        <div class="form-container">
            <h3>تسجيل الدخول</h3>
            <form id="loginForm">
                <div class="mb-3">
                    <label for="email" class="form-label">البريد الإلكتروني</label>
                    <input type="email" class="form-control" id="login-email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <input type="password" class="form-control" id="login-password" required>
                </div>
                <button type="submit" class="btn btn-success">تسجيل الدخول</button>
            </form>
            <div id="login-response" class="response-container mt-3" style="display: none;"></div>
        </div>
        
        <!-- اختبار المنتجات -->
        <div class="form-container">
            <h3>عرض المنتجات</h3>
            <button id="getProducts" class="btn btn-info">عرض جميع المنتجات</button>
            <div id="products-response" class="response-container mt-3" style="display: none;"></div>
        </div>
        
        <!-- إضافة منتج جديد -->
        <div class="form-container">
            <h3>إضافة منتج جديد</h3>
            <form id="addProductForm">
                <div class="mb-3">
                    <label for="product-title" class="form-label">عنوان المنتج</label>
                    <input type="text" class="form-control" id="product-title" required>
                </div>
                <div class="mb-3">
                    <label for="product-description" class="form-label">وصف المنتج</label>
                    <textarea class="form-control" id="product-description" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="product-image" class="form-label">رابط الصورة</label>
                    <input type="text" class="form-control" id="product-image">
                </div>
                <div class="mb-3">
                    <label for="product-category" class="form-label">الفئة</label>
                    <input type="text" class="form-control" id="product-category">
                </div>
                <div class="mb-3">
                    <label for="product-price" class="form-label">السعر</label>
                    <input type="number" class="form-control" id="product-price" required>
                </div>
                <div class="mb-3">
                    <label for="product-discount" class="form-label">الخصم</label>
                    <input type="number" class="form-control" id="product-discount" value="0">
                </div>
                <div class="mb-3">
                    <label for="product-stock" class="form-label">المخزون</label>
                    <input type="number" class="form-control" id="product-stock" value="1">
                </div>
                <button type="submit" class="btn btn-primary">إضافة منتج</button>
            </form>
            <div id="add-product-response" class="response-container mt-3" style="display: none;"></div>
        </div>
        
        <!-- تعديل منتج -->
        <div class="form-container">
            <h3>تعديل منتج</h3>
            <form id="editProductForm">
                <div class="mb-3">
                    <label for="edit-product-id" class="form-label">رقم المنتج</label>
                    <input type="number" class="form-control" id="edit-product-id" required>
                </div>
                <div class="mb-3">
                    <label for="edit-product-title" class="form-label">عنوان المنتج</label>
                    <input type="text" class="form-control" id="edit-product-title" required>
                </div>
                <div class="mb-3">
                    <label for="edit-product-description" class="form-label">وصف المنتج</label>
                    <textarea class="form-control" id="edit-product-description" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="edit-product-image" class="form-label">رابط الصورة</label>
                    <input type="text" class="form-control" id="edit-product-image">
                </div>
                <div class="mb-3">
                    <label for="edit-product-category" class="form-label">الفئة</label>
                    <input type="text" class="form-control" id="edit-product-category">
                </div>
                <div class="mb-3">
                    <label for="edit-product-price" class="form-label">السعر</label>
                    <input type="number" class="form-control" id="edit-product-price" required>
                </div>
                <div class="mb-3">
                    <label for="edit-product-discount" class="form-label">الخصم</label>
                    <input type="number" class="form-control" id="edit-product-discount" value="0">
                </div>
                <div class="mb-3">
                    <label for="edit-product-stock" class="form-label">المخزون</label>
                    <input type="number" class="form-control" id="edit-product-stock" value="1">
                </div>
                <button type="submit" class="btn btn-warning">تحديث المنتج</button>
            </form>
            <div id="edit-product-response" class="response-container mt-3" style="display: none;"></div>
        </div>
        
        <!-- حذف منتج -->
        <div class="form-container">
            <h3>حذف منتج</h3>
            <form id="deleteProductForm">
                <div class="mb-3">
                    <label for="delete-product-id" class="form-label">رقم المنتج</label>
                    <input type="number" class="form-control" id="delete-product-id" required>
                </div>
                <button type="submit" class="btn btn-danger">حذف المنتج</button>
            </form>
            <div id="delete-product-response" class="response-container mt-3" style="display: none;"></div>
        </div>
        
        <!-- إنشاء طلب جديد -->
        <div class="form-container">
            <h3>إنشاء طلب جديد</h3>
            <form id="createOrderForm">
                <div class="mb-3">
                    <label for="order-product-id" class="form-label">رقم المنتج</label>
                    <input type="number" class="form-control" id="order-product-id" required>
                </div>
                <div class="mb-3">
                    <label for="order-customer-name" class="form-label">اسم العميل</label>
                    <input type="text" class="form-control" id="order-customer-name" required>
                </div>
                <div class="mb-3">
                    <label for="order-email" class="form-label">البريد الإلكتروني</label>
                    <input type="email" class="form-control" id="order-email" required>
                </div>
                <div class="mb-3">
                    <label for="order-mobile" class="form-label">رقم الهاتف</label>
                    <input type="text" class="form-control" id="order-mobile" required>
                </div>
                <div class="mb-3">
                    <label for="order-address" class="form-label">العنوان</label>
                    <textarea class="form-control" id="order-address" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-success">إنشاء طلب</button>
            </form>
            <div id="create-order-response" class="response-container mt-3" style="display: none;"></div>
        </div>
        
        <!-- تسجيل الخروج -->
        <div class="form-container">
            <h3>تسجيل الخروج</h3>
            <button id="logout" class="btn btn-danger">تسجيل الخروج</button>
            <div id="logout-response" class="response-container mt-3" style="display: none;"></div>
        </div>
    </div>

    <script>
        // معالج نموذج التسجيل
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const responseContainer = document.getElementById('register-response');
            responseContainer.style.display = 'block';
            responseContainer.innerHTML = 'جاري التسجيل...';
            
            fetch('/api/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    name: document.getElementById('reg-name').value,
                    email: document.getElementById('reg-email').value,
                    password: document.getElementById('reg-password').value,
                    password_confirmation: document.getElementById('reg-password-confirm').value
                })
            })
            .then(response => response.json())
            .then(data => {
                responseContainer.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
                if (data.token) {
                    localStorage.setItem('auth_token', data.token);
                }
            })
            .catch(error => {
                responseContainer.innerHTML = `<div class="alert alert-danger">خطأ: ${error.message}</div>`;
            });
        });

        // معالج نموذج تسجيل الدخول
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const responseContainer = document.getElementById('login-response');
            responseContainer.style.display = 'block';
            responseContainer.innerHTML = 'جاري تسجيل الدخول...';
            
            fetch('/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    email: document.getElementById('login-email').value,
                    password: document.getElementById('login-password').value
                })
            })
            .then(response => response.json())
            .then(data => {
                responseContainer.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
                if (data.token) {
                    localStorage.setItem('auth_token', data.token);
                }
            })
            .catch(error => {
                responseContainer.innerHTML = `<div class="alert alert-danger">خطأ: ${error.message}</div>`;
            });
        });

        // عرض المنتجات
        document.getElementById('getProducts').addEventListener('click', function() {
            const responseContainer = document.getElementById('products-response');
            responseContainer.style.display = 'block';
            responseContainer.innerHTML = 'جاري تحميل المنتجات...';
            
            const token = localStorage.getItem('auth_token');
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };
            
            if (token) {
                headers['Authorization'] = `Bearer ${token}`;
            }
            
            fetch('/api/products', {
                method: 'GET',
                headers: headers
            })
            .then(response => response.json())
            .then(data => {
                responseContainer.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
            })
            .catch(error => {
                responseContainer.innerHTML = `<div class="alert alert-danger">خطأ: ${error.message}</div>`;
            });
        });

        // إضافة منتج جديد
        document.getElementById('addProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const responseContainer = document.getElementById('add-product-response');
            responseContainer.style.display = 'block';
            responseContainer.innerHTML = 'جاري إضافة المنتج...';
            
            const token = localStorage.getItem('auth_token');
            if (!token) {
                responseContainer.innerHTML = '<div class="alert alert-warning">يجب تسجيل الدخول أولاً</div>';
                return;
            }
            
            fetch('/api/products', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    title: document.getElementById('product-title').value,
                    description: document.getElementById('product-description').value,
                    image_url: document.getElementById('product-image').value,
                    category: document.getElementById('product-category').value,
                    price: document.getElementById('product-price').value,
                    discount: document.getElementById('product-discount').value,
                    stock: document.getElementById('product-stock').value
                })
            })
            .then(response => response.json())
            .then(data => {
                responseContainer.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
            })
            .catch(error => {
                responseContainer.innerHTML = `<div class="alert alert-danger">خطأ: ${error.message}</div>`;
            });
        });

        // تعديل منتج
        document.getElementById('editProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const responseContainer = document.getElementById('edit-product-response');
            responseContainer.style.display = 'block';
            responseContainer.innerHTML = 'جاري تحديث المنتج...';
            
            const productId = document.getElementById('edit-product-id').value;
            const token = localStorage.getItem('auth_token');
            if (!token) {
                responseContainer.innerHTML = '<div class="alert alert-warning">يجب تسجيل الدخول أولاً</div>';
                return;
            }
            
            fetch(`/api/products/${productId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    title: document.getElementById('edit-product-title').value,
                    description: document.getElementById('edit-product-description').value,
                    image_url: document.getElementById('edit-product-image').value,
                    category: document.getElementById('edit-product-category').value,
                    price: document.getElementById('edit-product-price').value,
                    discount: document.getElementById('edit-product-discount').value,
                    stock: document.getElementById('edit-product-stock').value
                })
            })
            .then(response => response.json())
            .then(data => {
                responseContainer.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
            })
            .catch(error => {
                responseContainer.innerHTML = `<div class="alert alert-danger">خطأ: ${error.message}</div>`;
            });
        });

        // حذف منتج
        document.getElementById('deleteProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const responseContainer = document.getElementById('delete-product-response');
            responseContainer.style.display = 'block';
            responseContainer.innerHTML = 'جاري حذف المنتج...';
            
            const productId = document.getElementById('delete-product-id').value;
            const token = localStorage.getItem('auth_token');
            if (!token) {
                responseContainer.innerHTML = '<div class="alert alert-warning">يجب تسجيل الدخول أولاً</div>';
                return;
            }
            
            fetch(`/api/products/${productId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Authorization': `Bearer ${token}`
                }
            })
            .then(response => response.json())
            .then(data => {
                responseContainer.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
            })
            .catch(error => {
                responseContainer.innerHTML = `<div class="alert alert-danger">خطأ: ${error.message}</div>`;
            });
        });

        // إنشاء طلب جديد
        document.getElementById('createOrderForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const responseContainer = document.getElementById('create-order-response');
            responseContainer.style.display = 'block';
            responseContainer.innerHTML = 'جاري إنشاء الطلب...';
            
            fetch('/api/orders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    product_id: document.getElementById('order-product-id').value,
                    customer_name: document.getElementById('order-customer-name').value,
                    email: document.getElementById('order-email').value,
                    mobile: document.getElementById('order-mobile').value,
                    address: document.getElementById('order-address').value
                })
            })
            .then(response => response.json())
            .then(data => {
                responseContainer.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
            })
            .catch(error => {
                responseContainer.innerHTML = `<div class="alert alert-danger">خطأ: ${error.message}</div>`;
            });
        });

        // تسجيل الخروج
        document.getElementById('logout').addEventListener('click', function() {
            const responseContainer = document.getElementById('logout-response');
            responseContainer.style.display = 'block';
            responseContainer.innerHTML = 'جاري تسجيل الخروج...';
            
            const token = localStorage.getItem('auth_token');
            if (!token) {
                responseContainer.innerHTML = '<div class="alert alert-warning">أنت غير مسجل الدخول</div>';
                return;
            }
            
            fetch('/api/logout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Authorization': `Bearer ${token}`
                }
            })
            .then(response => response.json())
            .then(data => {
                responseContainer.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
                localStorage.removeItem('auth_token');
            })
            .catch(error => {
                responseContainer.innerHTML = `<div class="alert alert-danger">خطأ: ${error.message}</div>`;
            });
        });
    </script>
</body>
</html>