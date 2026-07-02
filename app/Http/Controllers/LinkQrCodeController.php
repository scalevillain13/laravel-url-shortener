<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LinkQrCodeController extends Controller
{
    public function __invoke(Link $link): Response
    {
        $this->authorize('view', $link);

        $svg = QrCode::size(400)
            ->margin(2)
            ->generate($link->short_url);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'inline; filename="qr-'.$link->code.'.svg"',
        ]);
    }
}
