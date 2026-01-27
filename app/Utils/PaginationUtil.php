<?php

namespace App\Utils;

class PaginationUtil
{
    private static function buildQuery(array $params): string
    {
        $filtered = array_filter($params, fn($value) => $value !== null && $value !== '');

        return http_build_query($filtered);
    }

    public static function formatPaginationData($data, $pagination, $search, $perpage, $path)
    {
        $total = (int) $pagination['total'];
        $totalPages = max(1, (int) $pagination['totalPages']);
        $currentPage = max(1, (int) $pagination['currentPage']);
        $perpage = max(1, (int) $perpage);

        $start = ($currentPage - 1) * $perpage + 1;
        $end = min($start + count($data) - 1, $total);

        $queryBase = is_array($search)
            ? $search
            : ['search' => $search];

        $links = [];

        // Previous
        $links[] = [
            'url' => $currentPage > 1
                ? "{$path}?" . self::buildQuery(array_merge($queryBase, [
                    'page' => $currentPage - 1,
                    'perpage' => $perpage,
                ]))
                : null,
            'label' => '&laquo; Previous',
            'active' => false,
        ];

        for ($i = 1; $i <= $totalPages; $i++) {
            if ($totalPages > 10 && ($i < $currentPage - 2 || $i > $currentPage + 2)) {
                if ($i == 1 || $i == $totalPages) {
                    $links[] = [
                        'url' => "{$path}?" . self::buildQuery(array_merge($queryBase, [
                            'page' => $i,
                            'perpage' => $perpage,
                        ])),
                        'label' => (string) $i,
                        'active' => $i == $currentPage,
                    ];
                } elseif ($i == $currentPage - 3 || $i == $currentPage + 3) {
                    $links[] = [
                        'url' => null,
                        'label' => '...',
                        'active' => false,
                    ];
                }
                continue;
            }

            $links[] = [
                'url' => "{$path}?" . self::buildQuery(array_merge($queryBase, [
                    'page' => $i,
                    'perpage' => $perpage,
                ])),
                'label' => (string) $i,
                'active' => $i == $currentPage,
            ];
        }

        // Next
        $links[] = [
            'url' => $currentPage < $totalPages
                ? "{$path}?" . self::buildQuery(array_merge($queryBase, [
                    'page' => $currentPage + 1,
                    'perpage' => $perpage,
                ]))
                : null,
            'label' => 'Next &raquo;',
            'active' => false,
        ];

        return [
            'current_page' => $currentPage,
            'data' => $data,
            'first_page_url' => "{$path}?" . self::buildQuery(array_merge($queryBase, [
                'page' => 1,
                'perpage' => $perpage,
            ])),
            'from' => $total > 0 ? $start : null,
            'last_page' => $totalPages,
            'last_page_url' => "{$path}?" . self::buildQuery(array_merge($queryBase, [
                'page' => $totalPages,
                'perpage' => $perpage,
            ])),
            'links' => $links,
            'next_page_url' => $currentPage < $totalPages
                ? "{$path}?" . self::buildQuery(array_merge($queryBase, [
                    'page' => $currentPage + 1,
                    'perpage' => $perpage,
                ]))
                : null,
            'path' => $path,
            'per_page' => $perpage,
            'prev_page_url' => $currentPage > 1
                ? "{$path}?" . self::buildQuery(array_merge($queryBase, [
                    'page' => $currentPage - 1,
                    'perpage' => $perpage,
                ]))
                : null,
            'to' => $total > 0 ? $end : null,
            'total' => $total,
        ];
    }

    public static function emptyPagination($search, $perpage, $path)
    {
        $queryBase = is_array($search)
            ? $search
            : ['search' => $search];

        return [
            'current_page' => 1,
            'data' => [],
            'first_page_url' => "{$path}?" . self::buildQuery(array_merge($queryBase, [
                'page' => 1,
                'perpage' => $perpage,
            ])),
            'from' => null,
            'last_page' => 1,
            'last_page_url' => "{$path}?" . self::buildQuery(array_merge($queryBase, [
                'page' => 1,
                'perpage' => $perpage,
            ])),
            'links' => [],
            'next_page_url' => null,
            'path' => $path,
            'per_page' => (int) $perpage,
            'prev_page_url' => null,
            'to' => null,
            'total' => 0,
        ];
    }
}
