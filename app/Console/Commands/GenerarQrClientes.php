<?php

namespace App\Console\Commands;

use App\Models\User;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerarQrClientes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qr:generar-clientes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera códigos QR para todos los clientes basados en su email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $clientes = User::role('Cliente')->get();

        if ($clientes->isEmpty()) {
            $this->error('No se encontraron clientes en el sistema.');
            return;
        }

        $this->info("Generando QR para {$clientes->count()} clientes...");

        $progressBar = $this->output->createProgressBar($clientes->count());
        $progressBar->start();

        foreach ($clientes as $cliente) {
            // Si ya tiene QR, saltamos
            if ($cliente->url_qr) {
                $progressBar->advance();
                continue;
            }

            // Generar QR con el email del cliente como PNG
            $renderer = new ImageRenderer(
                new RendererStyle(400),
                new ImagickImageBackEnd()
            );
            $writer = new Writer($renderer);
            $qrCode = $writer->writeString($cliente->email);

            // Guardar en storage/app/public/qr
            $filename = 'qr_' . md5($cliente->email) . '.png';
            Storage::disk('public')->put('qr/' . $filename, $qrCode);

            // Actualizar usuario con la URL del QR
            $cliente->update([
                'url_qr' => 'qr/' . $filename,
            ]);

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->info('¡Códigos QR generados exitosamente!');
    }
}
