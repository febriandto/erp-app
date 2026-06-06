<?php

namespace App\Core;

use Illuminate\Support\Facades\Gate;

class MenuManager
{
    protected array $items = [];

    public function add(array $item): void
    {
        $this->items[] = array_merge([
            'title'      => '',
            'url'        => '#',
            'icon'       => 'ti ti-puzzle',
            'order'      => 100,
            'active'     => '',
            'permission' => null,
            'children'   => [],
        ], $item);
    }

    public function all(): array
    {
        $user = auth()->user();

        $items = collect($this->items)
            ->filter(function ($item) use ($user) {
                if (!$item['permission']) return true;
                return $user && Gate::forUser($user)->allows($item['permission']);
            })
            ->map(function ($item) use ($user) {
                $item['children'] = collect($item['children'])
                    ->filter(function ($child) use ($user) {
                        $perm = $child['permission'] ?? null;
                        if (!$perm) return true;
                        return $user && Gate::forUser($user)->allows($perm);
                    })
                    ->values()
                    ->toArray();
                return $item;
            })
            ->sortBy('order')
            ->values()
            ->toArray();

        return $items;
    }
}