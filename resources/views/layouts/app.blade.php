<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ระบบจองห้องเรียน')</title>
    <!-- เพิ่ม Link ของ Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
        }

        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f4f8fc;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background-color: #004080;
            /* สีพื้นหลังน้ำเงินเข้ม */
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            /* เพิ่มเงา */
        }

        .navbar a {
            color: #ffffff;
        }

        .navbar a:hover {
            color: #ffc107;
            /* สีเหลืองเวลาที่เอาเมาส์ไปที่ลิงก์ */
        }

        .navbar-brand img {
            width: 200px;
            /* ขนาดของโลโก้ */
            height: auto;
            margin-right: 10px;
            /* ระยะห่างระหว่างโลโก้กับชื่อ */
        }

        .container {
            flex-grow: 1;
            /* ให้เนื้อหาขยายเต็มพื้นที่ */
        }

        .card {
            border: none;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #004080;
            border: none;
        }

        .btn-primary:hover {
            background-color: #003366;
        }

        .footer {
            background-color: #004080;
            color: #ffffff;
            padding: 20px 0;
            text-align: center;
            margin-top: auto;
            /* ทำให้ footer อยู่ติดขอบล่าง */
        }

        .footer a {
            color: #ffffff;
            text-decoration: none;
        }

        .footer a:hover {
            color: #ffc107;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <!-- โลโก้ -->
            <a class="navbar-brand" href="{{ route('rooms.index') }}">
                <img src="{{ asset('tsulogo.png') }}" alt="Logo"> ระบบจองห้องเรียน
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">เข้าสู่ระบบ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">สมัครสมาชิก</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('bookings.index') }}">รายการจองของฉัน</a>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link text-white">ออกจากระบบ</button>
                            </form>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container">
        @yield('content')
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2025 ระบบจองห้องเรียน</p>
    </div>

    <!-- เพิ่มสคริปต์ของ Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
