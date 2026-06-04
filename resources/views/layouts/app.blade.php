<!DOCTYPE html>
<html>
<head>
    <title>Marie Collab CRM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f6f8fb;
            --surface: #ffffff;
            --surface-soft: #f9fbff;
            --text: #162033;
            --muted: #5d6782;
            --line: #dde4f2;
            --brand: #0f7b6c;
            --brand-2: #0a5da8;
            --danger: #b42318;
            --success: #127a3c;
            --radius: 12px;
            --shadow: 0 10px 25px rgba(16, 24, 40, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            max-width: 1120px;
            margin: 0 auto;
            padding: 18px 14px 28px;
            line-height: 1.5;
            color: var(--text);
            background:
                radial-gradient(circle at 5% 0%, #d7f5ef 0%, transparent 38%),
                radial-gradient(circle at 100% 100%, #d9ecff 0%, transparent 34%),
                var(--bg);
        }

        h1, h2, h3, h4 {
            letter-spacing: -0.02em;
            margin-top: 0;
        }

        a {
            color: var(--brand-2);
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        nav {
            display: flex;
            gap: 10px;
            margin-bottom: 14px;
            flex-wrap: wrap;
            align-items: center;
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 10px;
            backdrop-filter: blur(6px);
            box-shadow: var(--shadow);
        }

        nav a {
            color: var(--text);
            font-weight: 600;
            border: 1px solid transparent;
            border-radius: 10px;
            padding: 8px 11px;
            transition: 0.15s ease;
        }

        nav a:hover {
            border-color: var(--line);
            background: var(--surface-soft);
            text-decoration: none;
        }

        .card {
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 16px;
            margin-bottom: 16px;
            background: var(--surface);
            box-shadow: var(--shadow);
        }

        .success {
            background: #eaf8ef;
            border: 1px solid #b8e7c9;
            color: var(--success);
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 14px;
        }

        .error-list {
            background: #fff1f1;
            border: 1px solid #f7c5c5;
            color: var(--danger);
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 14px;
        }

        .warning {
            background: #fff7e8;
            border: 1px solid #f1d08a;
            color: #785400;
            padding: 10px 12px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .muted {
            color: var(--muted);
        }

        input, select, textarea, button {
            font: inherit;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="date"],
        input[type="datetime-local"],
        select,
        textarea {
            width: 100%;
            max-width: 680px;
            padding: 10px 12px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fff;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #9ac7ff;
            box-shadow: 0 0 0 3px rgba(10, 93, 168, 0.12);
        }

        button {
            padding: 9px 13px;
            border: 1px solid transparent;
            border-radius: 10px;
            background: #eaf1ff;
            color: #0a4e8a;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.12s ease, box-shadow 0.12s ease, background 0.12s ease;
        }

        button:hover {
            background: #dceaff;
            box-shadow: 0 6px 14px rgba(10, 93, 168, 0.15);
            transform: translateY(-1px);
        }

        .logout-form {
            display: inline;
            margin: 0;
        }

        .btn,
        .btn-primary {
            display: inline-block;
            padding: 9px 13px;
            border-radius: 10px;
            border: 1px solid transparent;
            text-decoration: none;
            font-weight: 600;
            transition: 0.12s ease;
        }

        .btn {
            color: var(--text);
            background: #eef3fb;
            border-color: #d4deee;
        }

        .btn:hover {
            background: #e3ecfa;
            text-decoration: none;
        }

        .btn-primary {
            color: #fff;
            background: linear-gradient(135deg, var(--brand) 0%, #0c8f7d 100%);
            border-color: transparent;
        }

        .btn-primary:hover {
            filter: brightness(0.96);
            text-decoration: none;
        }

        @media (max-width: 720px) {
            body {
                padding: 12px;
            }

            nav {
                position: sticky;
                top: 8px;
                z-index: 10;
            }
        }
    </style>
</head>
<body>
    <nav>
        @auth
            <a href="/businesses">Dashboard</a>
            <a href="/businesses">Businesses</a>
            <a href="/businesses/create">Add Business</a>
            <a href="/lead-finder">Lead Finder</a>
            <a href="/creator-profile">Creator Profile</a>
            <form class="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        @else
            <a href="{{ route('login') }}">Login</a>
            @if (Route::has('register'))
                <a href="{{ route('register') }}">Register</a>
            @endif
        @endauth
    </nav>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="error-list">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="error-list">
            <strong>Please fix these issues:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="warning">
        Draft-only mode: this CRM generates message drafts for human review. It does not auto-send emails or DMs.
    </div>

    @yield('content')
</body>
</html>
