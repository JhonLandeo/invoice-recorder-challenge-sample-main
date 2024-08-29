<?php

namespace App\Console\Commands;

use App\Models\Currency;
use App\Models\TypeDocument;
use App\Models\Voucher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class FillVoucherFieldsFromXml extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:old-vouchers';
    protected $description = 'Actualizar los vouchers antiguos con las nuevas columnas currency_id, type_document_id, series y correlative usando el XML almacenado';

    public function handle()
    {
        // Obtener todos los vouchers antiguos que necesitan ser actualizados
        $vouchers = Voucher::whereNull('currency_id')
                            ->orWhereNull('type_document_id')
                            ->orWhereNull('series')
                            ->orWhereNull('correlative')
                            ->get();

        foreach ($vouchers as $voucher) {
            try {
                $xml = new SimpleXMLElement($voucher->xml_content);

                // Actualizar currency_id
                $currencyCode = (string) $xml->xpath('//cbc:DocumentCurrencyCode')[0];
                $currency = Currency::where('currency_code', $currencyCode)->first();
                if ($currency) {
                    $voucher->currency_id = $currency->id;
                }

                // Actualizar type_document_id
                $typeDocumentCode = (string) $xml->xpath('//cbc:InvoiceTypeCode')[0];
                $typeDocument = TypeDocument::where('code', $typeDocumentCode)->first();
                if ($typeDocument) {
                    $voucher->type_document_id = $typeDocument->id;
                }

                // Actualizar series y correlative
                $documentId = (string) $xml->xpath('//cbc:ID')[0];
                list($series, $correlative) = explode('-', $documentId, 2);
                $voucher->series = $series;
                $voucher->correlative = str_pad($correlative, 8, '0', STR_PAD_LEFT);

                // Guardar los cambios en la base de datos
                $voucher->save();

                $this->info("Voucher con ID {$voucher->id} actualizado correctamente.");

            } catch (\Exception $e) {
                $this->error("Error al actualizar el voucher con ID {$voucher->id}: " . $e->getMessage());
            }
        }

        $this->info('Actualización completada para todos los vouchers antiguos.');
    }
}
