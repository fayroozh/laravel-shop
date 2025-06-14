<!-- resources/views/login.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول</title>
</head>
<body>

    @if(session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}">
        @csrf

        <label for="email">البريد الإلكتروني:</label><br>
        <input type="email" name="email" id="email" required><br><br>

        <label for="password">كلمة المرور:</label><br>
        <input type="password" name="password" id="password" required><br><br>

        <button type="submit">تسجيل الدخول</button>
    </form>

</body>
</html>
