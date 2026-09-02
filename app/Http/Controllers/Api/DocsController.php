<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DocsController extends Controller
{
    public function ui(): Response
    {
        return response()
            ->view('api.docs')
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function spec(): JsonResponse
    {
        $path = public_path('api-docs/openapi.json');
        if (! is_file($path)) {
            return response()->json([
                'error' => 'OpenAPI specification file not found.',
                'path'  => $path,
            ], 404);
        }

        $spec = json_decode(file_get_contents($path), true);
        if (! is_array($spec)) {
            return response()->json(['error' => 'Invalid openapi.json'], 500);
        }

        // Inject the current host so "Try it out" works without editing the file.
        $spec['servers'] = [[
            'url'         => url('/api'),
            'description' => 'Current host',
        ]];

        return response()->json($spec, 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
