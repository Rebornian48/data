<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>JKT48 Data API — Swagger UI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="https://unpkg.com/swagger-ui-dist@5/favicon-32x32.png" sizes="32x32">
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
    <style>
        html, body { margin: 0; padding: 0; background: #fafafa; }
        .topbar { display: none; } /* hide default Swagger topbar */
        .app-header {
            background: #ef4c74; color: #fff; padding: 14px 24px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex; align-items: center; gap: 14px;
        }
        .app-header h1 { margin: 0; font-size: 18px; font-weight: 600; letter-spacing: .2px; }
        .app-header small { opacity: .85; font-size: 12px; }
        .app-header a { color: #fff; text-decoration: underline; font-size: 12px; margin-left: auto; }
    </style>
</head>
<body>
    <header class="app-header">
        <h1>JKT48 Data API</h1>
        <small>Swagger UI · OpenAPI 3.0</small>
        <a href="{{ route('api.docs.spec') }}" target="_blank" rel="noopener">openapi.json</a>
    </header>

    <div id="swagger-ui"></div>

    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js" crossorigin></script>
    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-standalone-preset.js" crossorigin></script>
    <script>
        window.addEventListener('load', function () {
            window.ui = SwaggerUIBundle({
                url: {!! json_encode(route('api.docs.spec')) !!},
                dom_id: '#swagger-ui',
                deepLinking: true,
                docExpansion: 'list',
                defaultModelsExpandDepth: 0,
                tryItOutEnabled: true,
                persistAuthorization: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [SwaggerUIBundle.plugins.DownloadUrl],
                layout: 'StandaloneLayout'
            });
        });
    </script>
</body>
</html>
