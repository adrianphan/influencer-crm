<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Marie Collab CRM') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <style>
            :root {
                --bg: #f4f7fb;
                --surface: #ffffff;
                --line: #d9e1ef;
                --text: #152035;
                --muted: #5f6983;
                --brand: #0f7b6c;
            }

            *,
            *::before,
            *::after {
                box-sizing: border-box;
            }

            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                margin: 0;
                color: var(--text);
                background:
                    radial-gradient(circle at 10% 10%, #d4f6ef 0%, transparent 38%),
                    radial-gradient(circle at 100% 100%, #dceaff 0%, transparent 36%),
                    var(--bg);
            }

            .font-sans {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }

            .auth-wrap {
                width: 100%;
                max-width: 460px;
            }

            .auth-card {
                background: var(--surface);
                border: 1px solid var(--line);
                border-radius: 14px;
                box-shadow: 0 18px 32px rgba(16, 24, 40, 0.08);
                padding: 20px;
            }

            .auth-logo {
                text-align: center;
                margin-bottom: 18px;
            }

            .auth-logo a {
                display: inline-block;
                width: 100%;
                max-width: 460px;
            }

            .auth-logo svg {
                width: 100%;
                height: auto;
                max-height: 132px;
            }

            .auth-label {
                display: block;
                font-size: 14px;
                font-weight: 600;
                margin-bottom: 6px;
            }

            .auth-input {
                display: block;
                width: 100%;
                max-width: 100%;
                padding: 10px 12px;
                border: 1px solid var(--line);
                border-radius: 10px;
                font: inherit;
                transition: border-color 0.15s ease, box-shadow 0.15s ease;
            }

            .auth-input:focus {
                outline: none;
                border-color: #9ac7ff;
                box-shadow: 0 0 0 3px rgba(10, 93, 168, 0.12);
            }

            .auth-form-group {
                margin-bottom: 14px;
            }

            .auth-checkbox-row {
                margin: 6px 0 14px;
            }

            .auth-actions {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 12px;
                margin-top: 8px;
            }

            .auth-link {
                color: #0a5da8;
                font-size: 14px;
                font-weight: 600;
                text-decoration: underline;
                text-underline-offset: 2px;
            }

            .auth-button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 10px 14px;
                border: 0;
                border-radius: 10px;
                font-weight: 700;
                color: #fff;
                background: linear-gradient(135deg, var(--brand) 0%, #0c8f7d 100%);
                cursor: pointer;
            }

            .auth-button:hover {
                filter: brightness(0.96);
            }

            .auth-muted {
                color: var(--muted);
                font-size: 14px;
            }

            .auth-error {
                background: #fff1f1;
                border: 1px solid #f7c5c5;
                color: #b42318;
                border-radius: 10px;
                padding: 10px;
                margin-bottom: 10px;
            }

            .auth-status {
                background: #eaf8ef;
                border: 1px solid #b8e7c9;
                color: #127a3c;
                border-radius: 10px;
                padding: 10px;
                margin-bottom: 10px;
            }

            @media (max-width: 640px) {
                .auth-wrap {
                    max-width: 100%;
                }

                .auth-card {
                    padding: 16px;
                }

                .auth-logo {
                    margin-bottom: 10px;
                }

                .auth-logo a {
                    max-width: 340px;
                }

                .auth-form-group {
                    margin-bottom: 16px;
                }

                .auth-checkbox-row {
                    margin: 8px 0 16px;
                }

                .auth-actions {
                    align-items: stretch;
                }
            }
        </style>
    </head>
    <body>
        <div class="font-sans text-gray-900 antialiased">
            {{ $slot }}
        </div>
    </body>
</html>
