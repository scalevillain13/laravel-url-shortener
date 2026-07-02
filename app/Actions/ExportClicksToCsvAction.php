<?php

namespace App\Actions;

use App\Models\Link;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportClicksToCsvAction
{
    public function execute(Link $link): StreamedResponse
    {
        $filename = sprintf(
            'clicks-%s-%s.csv',
            $link->code,
            now()->format('Y-m-d'),
        );

        return response()->streamDownload(function () use ($link): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['IP-адрес', 'User-Agent', 'Дата и время']);

            $link->clicks()
                ->orderByDesc('clicked_at')
                ->cursor()
                ->each(function ($click) use ($handle): void {
                    fputcsv($handle, [
                        $click->ip_address,
                        $click->user_agent,
                        $click->clicked_at->format('d.m.Y H:i:s'),
                    ]);
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
