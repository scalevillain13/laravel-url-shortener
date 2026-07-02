<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — короткие ссылки</title>
    <style>
        :root { --navy:#1e3a5f; --gold:#c9a227; --bg:#f4f6f9; --card:#fff; --text:#0f172a; --muted:#64748b; }
        * { box-sizing:border-box; }
        body { margin:0; font-family:Inter,system-ui,sans-serif; background:var(--bg); color:var(--text); }
        .wrap { max-width:960px; margin:0 auto; padding:2rem 1.25rem 3rem; }
        .nav { display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; }
        .brand { font-weight:700; color:var(--navy); letter-spacing:-.02em; }
        .nav a { color:var(--navy); text-decoration:none; font-weight:600; margin-left:1rem; }
        .hero { background:linear-gradient(135deg,#0f1b2d,#1e3a5f); color:#fff; border-radius:1rem; padding:2rem; margin-bottom:1.5rem; }
        .hero h1 { margin:0 0 .5rem; font-size:clamp(1.6rem,4vw,2.2rem); }
        .hero p { margin:0; color:#cbd5e1; max-width:36rem; line-height:1.55; }
        .card { background:var(--card); border:1px solid #e2e8f0; border-radius:.875rem; padding:1.5rem; box-shadow:0 8px 24px rgba(15,23,42,.05); }
        label { display:block; font-size:.85rem; font-weight:600; margin-bottom:.35rem; color:#334155; }
        input { width:100%; padding:.7rem .85rem; border:1px solid #cbd5e1; border-radius:.5rem; font-size:1rem; }
        input:focus { outline:2px solid rgba(30,58,95,.25); border-color:var(--navy); }
        .grid { display:grid; gap:.85rem; grid-template-columns:1fr 1fr 1fr; }
        @media (max-width:720px){ .grid { grid-template-columns:1fr; } }
        .btn { margin-top:1rem; background:var(--navy); color:#fff; border:0; border-radius:.5rem; padding:.75rem 1.1rem; font-weight:600; cursor:pointer; }
        .btn:hover { opacity:.92; }
        .alert { margin-top:1rem; padding:.75rem .9rem; border-radius:.5rem; background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
        .alert code { font-weight:700; }
        .links { margin-top:1.5rem; }
        .links h2 { font-size:1.1rem; margin:0 0 .75rem; }
        .links ul { list-style:none; padding:0; margin:0; }
        .links li { padding:.65rem 0; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; gap:1rem; }
        .links a { color:var(--navy); font-weight:600; text-decoration:none; }
        .muted { color:var(--muted); font-size:.875rem; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="nav">
        <div class="brand">{{ config('app.name') }}</div>
        <div>
            @auth
                <a href="{{ url('/admin') }}">Кабинет</a>
            @else
                <a href="{{ url('/admin/login') }}">Вход</a>
                <a href="{{ url('/admin/register') }}">Регистрация</a>
            @endauth
        </div>
    </div>

    <section class="hero">
        <h1>Сокращайте ссылки. Следите за переходами.</h1>
        <p>Вставьте длинный URL — получите короткую ссылку и аналитику в личном кабинете.</p>
    </section>

    <section class="card">
        <form method="POST" action="{{ route('home.store') }}">
            @csrf
            <label for="original_url">Оригинальный URL (HTTPS)</label>
            <input id="original_url" name="original_url" type="url" placeholder="https://example.com/page" value="{{ old('original_url', session('prefill_original_url', '')) }}" required>

            @guest
                <p class="muted" style="margin-top:.75rem;">Для сохранения ссылки нужна <a href="{{ url('/admin/register') }}">регистрация</a>.</p>
            @endguest

            @auth
                <div class="grid" style="margin-top:.85rem;">
                    <div>
                        <label for="utm_source">UTM Source</label>
                        <input id="utm_source" name="utm_source" value="{{ old('utm_source') }}" placeholder="newsletter">
                    </div>
                    <div>
                        <label for="utm_medium">UTM Medium</label>
                        <input id="utm_medium" name="utm_medium" value="{{ old('utm_medium') }}" placeholder="email">
                    </div>
                    <div>
                        <label for="utm_campaign">UTM Campaign</label>
                        <input id="utm_campaign" name="utm_campaign" value="{{ old('utm_campaign') }}" placeholder="spring_sale">
                    </div>
                </div>
            @endauth

            @if ($errors->any())
                <div class="alert" style="background:#fef2f2;color:#991b1b;border-color:#fecaca;margin-top:.85rem;">
                    {{ $errors->first() }}
                </div>
            @endif

            <button class="btn" type="submit">Создать короткую ссылку</button>
        </form>

        @if (session('short_url'))
            <div class="alert">Готово: <code>{{ session('short_url') }}</code></div>
        @endif
    </section>

    @auth
        @if ($recentLinks->isNotEmpty())
            <section class="links card" style="margin-top:1rem;">
                <h2>Последние ссылки</h2>
                <ul>
                    @foreach ($recentLinks as $link)
                        <li>
                            <a href="{{ $link->short_url }}" target="_blank">{{ $link->short_url }}</a>
                            <span class="muted">{{ $link->clicks_count ?? $link->clicks()->count() }} переходов</span>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    @endauth
</div>
</body>
</html>
