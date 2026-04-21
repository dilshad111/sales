<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $companySetting->name ?? 'Sales App' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .welcome-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 2rem;
            padding: 3.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .icon-box {
            width: 80px;
            height: 80px;
            background: #696cff;
            color: white;
            border-radius: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 2rem;
            box-shadow: 0 10px 20px rgba(105, 108, 255, 0.3);
        }
        .btn-primary {
            background: #696cff;
            border: none;
            padding: 0.8rem 2.5rem;
            border-radius: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: #5f61e6;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(105, 108, 255, 0.4);
        }
    </style>
</head>
<body>
    <div class="welcome-card animate__animated animate__fadeIn">
        <div class="icon-box">
            <i class="fas fa-cube"></i>
        </div>
        <h2 class="fw-800 mb-3 text-dark">{{ $companySetting->name ?? 'Sales Management' }}</h2>
        <p class="text-muted mb-4">Enterprise Resource Planning & Financial Management System</p>
        
        @if (Route::has('login'))
            <div class="d-grid gap-2">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-lg">Go to Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Log In</a>
                @endauth
            </div>
        @endif
        
        <div class="mt-4 pt-3 border-top">
            <small class="text-muted">&copy; {{ date('Y') }} {{ $companySetting->name }}</small>
        </div>
    </div>
</body>
</html>
