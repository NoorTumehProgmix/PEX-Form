<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Temp IP Check</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            margin: 0;
            padding: 2rem 1rem;
            background: #f4f6f8;
            color: #1a1a1a;
        }
        .wrap { max-width: 640px; margin: 0 auto; }
        .card {
            background: #fff;
            border: 1px solid #dde3ea;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        .badge {
            display: inline-block;
            background: #fff3cd;
            color: #856404;
            font-size: 0.85rem;
            padding: 0.25rem 0.6rem;
            border-radius: 999px;
            margin-bottom: 1rem;
        }
        h1 { font-size: 1.35rem; margin: 0 0 0.5rem; }
        p { margin: 0 0 1rem; line-height: 1.6; color: #555; }
        button {
            appearance: none;
            border: 0;
            border-radius: 8px;
            background: #0d6efd;
            color: #fff;
            font-size: 1rem;
            padding: 0.75rem 1.25rem;
            cursor: pointer;
        }
        button:disabled { opacity: 0.65; cursor: wait; }
        pre {
            margin: 1rem 0 0;
            padding: 1rem;
            background: #111827;
            color: #e5e7eb;
            border-radius: 8px;
            overflow-x: auto;
            font-size: 0.9rem;
            line-height: 1.5;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .hint { font-size: 0.9rem; color: #666; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <span class="badge">TEMP — delete after Cloudflare IP fix</span>
            <h1>What IP does the server see?</h1>
            <p>Click the button to ask the server which IP it detects for your request.</p>
            <button type="button" id="check-ip-btn">Check my IP</button>
            <pre id="ip-result" hidden></pre>
            <p class="hint">
                When Cloudflare is configured correctly, <code>get_client_ip</code> and
                <code>cf_connecting_ip</code> should show your real IP, not a Cloudflare proxy IP.
            </p>
        </div>
    </div>

    <script>
        (function () {
            var btn = document.getElementById('check-ip-btn');
            var result = document.getElementById('ip-result');
            var endpoint = @json(route('temp-ip-check.check'));

            btn.addEventListener('click', function () {
                btn.disabled = true;
                result.hidden = false;
                result.textContent = 'Loading...';

                fetch(endpoint, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        result.textContent = JSON.stringify(data, null, 2);
                    })
                    .catch(function () {
                        result.textContent = 'Request failed. Try again.';
                    })
                    .finally(function () {
                        btn.disabled = false;
                    });
            });
        })();
    </script>
</body>
</html>
