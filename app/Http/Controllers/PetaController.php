<?php

namespace App\Http\Controllers;

use App\Models\Map;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PetaController extends Controller
{
    public function show(string $slug = 'jkt48')
    {
        $map = Map::where('slug', $slug)->where('is_published', true)->firstOrFail();
        return view('peta.index', ['map' => $map]);
    }

    /** Feeds the frontend JS in the same shape the Google Sheets API returned. */
    public function data(string $slug = 'jkt48'): JsonResponse
    {
        $map = Map::with(['settings', 'points', 'polylines', 'polygonLayers.settings'])
            ->where('slug', $slug)->where('is_published', true)->firstOrFail();

        return response()->json([
            'options'   => $this->kvRows($map->settingsMap()),
            'points'    => $map->points->map(fn ($p) => [
                'Group'        => $p->group,
                'Marker Icon'  => $p->marker_icon,
                'Marker Color' => $p->marker_color,
                'Icon Color'   => $p->icon_color,
                'Custom Size'  => $p->custom_size ?? '',
                'Name'         => $p->name,
                'Image'        => $p->image ?? '',
                'Description'  => $p->description ?? '',
                'Location'     => $p->location ?? '',
                'Latitude'     => $p->latitude,
                'Longitude'    => $p->longitude,
            ] + ($p->extras ?? [])),
            'polylines' => $map->polylines->map(fn ($l) => [
                'GeoJSON URL'  => $l->geojson_url,
                'Display Name' => $l->display_name,
                'Description'  => $l->description ?? '',
                'Color'        => $l->color ?? '',
            ]),
            'polygons'  => $map->polygonLayers->sortBy('sort')->values()->map(
                fn ($layer) => $this->kvRows($layer->settingsMap())
            ),
        ]);
    }

    private function kvRows(array $kv): array
    {
        $out = [];
        foreach ($kv as $k => $v) {
            $out[] = ['Setting' => $k, 'Customize' => $v];
        }
        return $out;
    }
}
