<?php

namespace App\Console\Commands;

use App\Models\User;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateMissingQrs extends Command
{
    protected $signature = 'qr:generate-missing';
    protected $description = 'Genera QRs faltantes para usuarios';

    public function handle()
    {
        $users = User::whereNull('url_qr')->orWhere('url_qr', '')->get();

        $this->info("Encontrados {$users->count()} usuarios sin QR.");

        foreach ($users as $user) {
            try {
                $renderer = new ImageRenderer(
                    new RendererStyle(400),
                    new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
                );
                $writer = new Writer($renderer);
                $qrCode = $writer->writeString($user->email);

                $filename = 'qr_' . md5($user->email) . '.svg';
                Storage::disk('public')->put('qr/' . $filename, $qrCode);

                $user->update([
                    'url_qr' => 'qr/' . $filename,
                ]);

                $this->info("QR generado para: {$user->email}");
            } catch (\Exception $e) {
                $this->error("Error generando QR para {$user->email}: " . $e->getMessage());
            }
        }
    }
}
