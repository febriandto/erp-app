<?php

namespace App\Core;

class MenuManager
{
    protected array $items = [];

    /**
     * Plugin memanggil ini untuk daftarkan menu
     */
    public function add(array $item): void
    {
        $this->items[] = array_merge([
            'title'    => '',
            'url'      => '#',
            'icon'     => 'ti ti-puzzle',
            'order'    => 100,
            'active'   => '',
            'children' => [],
        ], $item);
    }

    /**
     * Ambil semua menu, sorted by order
     */
    public function all(): array
    {
        $items = $this->items;
        usort($items, fn($a, $b) => $a['order'] <=> $b['order']);
        return $items;
    }
}