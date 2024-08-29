<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('type_documents', function (Blueprint $table) {
            $table->id();
            $table->string('code', 2)->unique();
            $table->string('description');
            $table->timestamps();
        });
        DB::table('type_documents')->insert([
            ['code' => '01', 'description' => 'Factura'],
            ['code' => '03', 'description' => 'Boleta de venta'],
            ['code' => '06', 'description' => 'Carta de porte aéreo'],
            ['code' => '07', 'description' => 'Nota de crédito'],
            ['code' => '08', 'description' => 'Nota de débito'],
            ['code' => '09', 'description' => 'Guia de remisión remitente'],
            ['code' => '12', 'description' => 'Ticket de maquina registradora'],
            ['code' => '13', 'description' => 'Documento emitido por bancos, instituciones financieras, crediticias y de seguros que se encuentren bajo el control de la superintendencia de banca y seguros'],
            ['code' => '14', 'description' => 'Recibo de servicios públicos'],
            ['code' => '15', 'description' => 'Boletos emitidos por el servicio de transporte terrestre regular urbano de pasajeros y el ferroviario público de pasajeros prestado en vía férrea local'],
            ['code' => '16', 'description' => 'Boleto de viaje emitido por las empresas de transporte público interprovincial de pasajeros'],
            ['code' => '18', 'description' => 'Documentos emitidos por las AFP'],
            ['code' => '20', 'description' => 'Comprobante de retencion'],
            ['code' => '21', 'description' => 'Conocimiento de embarque por el servicio de transporte de carga marítima'],
            ['code' => '24', 'description' => 'Certificado de pago de regalías emitidas por Perupetro S.A.'],
            ['code' => '31', 'description' => 'Guía de remisión transportista'],
            ['code' => '37', 'description' => 'Documentos que emitan los concesionarios del servicio de revisiones técnicas'],
            ['code' => '40', 'description' => 'Comprobante de percepción'],
            ['code' => '41', 'description' => 'Comprobante de percepción – venta interna (físico - formato impreso)'],
            ['code' => '43', 'description' => 'Boleto de compañías de aviación transporte aéreo no regular'],
            ['code' => '45', 'description' => 'Documentos emitidos por centros educativos y culturales, universidades, asociaciones y fundaciones'],
            ['code' => '56', 'description' => 'Comprobante de pago SEAE'],
            ['code' => '71', 'description' => 'Guia de remisión remitente complementaria'],
            ['code' => '72', 'description' => 'Guia de remisión transportista complementaria'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_documents');
    }
};
