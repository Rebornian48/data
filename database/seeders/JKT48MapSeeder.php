<?php

namespace Database\Seeders;

use App\Models\Map;
use App\Models\MapPoint;
use App\Models\MapPolygonLayer;
use App\Models\MapPolygonSetting;
use App\Models\MapPolyline;
use App\Models\MapSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class JKT48MapSeeder extends Seeder
{
    // Default is the map the current static site ships with.
    // Both are overridable via .env (JKT48_MAP_SHEET_ID / GOOGLE_SHEETS_API_KEY).
    private const DEFAULT_SHEET_ID = '1FinIC52jFCi5fL7oN5qZ-BKocZvBfxbiCTrzXetnANo';

    private string $sheetId;
    private string $apiKey;

    public function run(): void
    {
        $this->sheetId = (string) env('JKT48_MAP_SHEET_ID', self::DEFAULT_SHEET_ID);
        $this->apiKey  = (string) env('GOOGLE_SHEETS_API_KEY', '');

        if ($this->apiKey === '') {
            $this->command?->error('GOOGLE_SHEETS_API_KEY belum di-set di .env — seeder dibatalkan.');
            return;
        }

        $meta = $this->fetch('');
        $tabs = collect($meta['sheets'])->pluck('properties.title')->all();

        $map = Map::updateOrCreate(
            ['slug' => 'jkt48'],
            [
                'title'           => 'Peta JKT48',
                'subtitle'        => 'Explore the Journey of JKT48',
                'google_sheet_id' => $this->sheetId,
                'is_published'    => true,
            ]
        );

        $map->settings()->delete();
        $map->points()->delete();
        $map->polylines()->delete();
        $map->polygonLayers()->delete();

        if (in_array('Options', $tabs, true)) {
            foreach ($this->rows('Options') as $r) {
                $key = trim((string) ($r['Setting'] ?? ''));
                if ($key === '' || !array_key_exists('Customize', $r)) continue;
                MapSetting::create(['map_id' => $map->id, 'key' => $key, 'value' => $r['Customize']]);
            }
        }

        if (in_array('Points', $tabs, true)) {
            $sort = 0;
            foreach ($this->rows('Points') as $r) {
                $name = trim((string) ($r['Name'] ?? ''));
                if ($name === '') continue;
                MapPoint::create([
                    'map_id'       => $map->id,
                    'group'        => $r['Group']        ?? null,
                    'marker_icon'  => $r['Marker Icon']  ?? null,
                    'marker_color' => $r['Marker Color'] ?? null,
                    'icon_color'   => $r['Icon Color']   ?? null,
                    'custom_size'  => $r['Custom Size']  ?? null,
                    'name'         => $name,
                    'image'        => $r['Image']        ?? null,
                    'description'  => $r['Description']  ?? null,
                    'location'     => $r['Location']     ?? null,
                    'latitude'     => $this->num($r['Latitude']  ?? null),
                    'longitude'    => $this->num($r['Longitude'] ?? null),
                    'extras'       => $this->extras($r, ['Group','Marker Icon','Marker Color','Icon Color','Custom Size','Name','Image','Description','Location','Latitude','Longitude']),
                    'sort'         => $sort++,
                ]);
            }
        }

        if (in_array('Polylines', $tabs, true)) {
            $sort = 0;
            foreach ($this->rows('Polylines') as $r) {
                $url = trim((string) ($r['GeoJSON URL'] ?? ''));
                if ($url === '') continue;
                MapPolyline::create([
                    'map_id'       => $map->id,
                    'display_name' => $r['Display Name'] ?? '',
                    'geojson_url'  => $url,
                    'description'  => $r['Description']  ?? null,
                    'color'        => $r['Color']        ?? null,
                    'sort'         => $sort++,
                ]);
            }
        }

        $layerSort = 0;
        foreach ($tabs as $tab) {
            if (!str_starts_with($tab, 'Polygons')) continue;
            $layer = MapPolygonLayer::create(['map_id' => $map->id, 'name' => $tab, 'sort' => $layerSort++]);
            foreach ($this->rows($tab) as $r) {
                $key = trim((string) ($r['Setting'] ?? ''));
                if ($key === '' || !array_key_exists('Customize', $r)) continue;
                MapPolygonSetting::create(['polygon_layer_id' => $layer->id, 'key' => $key, 'value' => $r['Customize']]);
            }
        }
    }

    private function fetch(string $path): array
    {
        $url = 'https://sheets.googleapis.com/v4/spreadsheets/' . $this->sheetId . $path
             . (str_contains($path, '?') ? '&' : '?') . 'key=' . $this->apiKey;

        return Http::withOptions(['verify' => $this->caBundle()])
            ->get($url)->throw()->json();
    }

    /**
     * Locate a CA bundle for cURL. Windows/XAMPP PHP often ships without one;
     * fall back to disabling verify (dev-only, public data).
     */
    private function caBundle()
    {
        foreach ([
            getenv('CURL_CA_BUNDLE') ?: null,
            getenv('SSL_CERT_FILE')  ?: null,
            ini_get('curl.cainfo')   ?: null,
            ini_get('openssl.cafile') ?: null,
            'C:/xampp/phpMyAdmin/vendor/composer/ca-bundle/res/cacert.pem',
            'C:/xampp/perl/vendor/lib/Mozilla/CA/cacert.pem',
            '/etc/ssl/certs/ca-certificates.crt',
        ] as $path) {
            if ($path && is_file($path)) return $path;
        }
        return false; // last-resort: skip verification
    }

    /** Fetch a sheet's values and turn into [{header: value, ...}, ...]. */
    private function rows(string $sheet): array
    {
        $data = $this->fetch('/values/' . rawurlencode($sheet));
        $values = $data['values'] ?? [];
        if (count($values) < 2) return [];
        $headers = array_map('trim', array_shift($values));
        $out = [];
        foreach ($values as $row) {
            $r = [];
            foreach ($headers as $i => $h) {
                $r[$h] = $row[$i] ?? '';
            }
            $out[] = $r;
        }
        return $out;
    }

    private function num($v): ?float
    {
        if ($v === null || $v === '') return null;
        return is_numeric($v) ? (float) $v : null;
    }

    private function extras(array $row, array $known): ?array
    {
        $extra = array_diff_key($row, array_flip($known));
        return $extra ? $extra : null;
    }
}
