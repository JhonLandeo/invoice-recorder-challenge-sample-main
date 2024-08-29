<?php

namespace App\Services;

use App\Events\Vouchers\VouchersCreated;
use App\Jobs\ProcessVouchers;
use App\Models\Currency;
use App\Models\TypeDocument;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherLine;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class VoucherService
{
    public function getVouchers(int $page, int $paginate, ?string $series = null, ?string $correlative = null, ?string $fromDate = null, ?string $toDate = null): LengthAwarePaginator
    {
        $query = Voucher::with(['lines', 'user']);
    
        if (!is_null($series)) {
            $query->where('series', $series);
        }
    
        if (!is_null($correlative)) {
            $query->where('correlative', $correlative);
        }
    
        if (!is_null($fromDate)) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
    
        if (!is_null($toDate)) {
            $query->whereDate('created_at', '<=', $toDate);
        }
    
        return $query->paginate(perPage: $paginate, page: $page);
    }
    

    /**
     * @param string[] $xmlContents
     * @param User $user
     * @return void
     */
    public function storeVouchersFromXmlContents(array $xmlContents, User $user)
    {
        $voucherService = app(VoucherService::class);
        ProcessVouchers::dispatch($xmlContents, $user, $voucherService);
    }

    public function storeVoucherFromXmlContent(string $xmlContent, User $user): Voucher
    {
        $xml = new SimpleXMLElement($xmlContent);

        $issuerName = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyName/cbc:Name')[0];
        $issuerDocumentType = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $issuerDocumentNumber = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $receiverName = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName')[0];
        $receiverDocumentType = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $receiverDocumentNumber = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $totalAmount = (string) $xml->xpath('//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount')[0];

        $currencyCode = (string) $xml->xpath('//cbc:DocumentCurrencyCode')[0];
        $typeDocumentCode = (string) $xml->xpath('//cbc:InvoiceTypeCode')[0];

        $typeDocument = TypeDocument::where('code', $typeDocumentCode)->first();
        if (!$typeDocument) {
            throw new \Exception("Tipo de documento no encontrado: $typeDocumentCode");
        }

        $currency = Currency::where('currency_code', $currencyCode)->first();
        if (!$currency) {
            throw new \Exception("Moneda no encontrada: $currencyCode");
        }

        $documentId = (string) $xml->xpath('//cbc:ID')[0];
        list($series, $correlative) = explode('-', $documentId, 2);
        $correlative = str_pad($correlative, 8, '0', STR_PAD_LEFT);

        $voucher = new Voucher([
            'issuer_name' => $issuerName,
            'issuer_document_type' => $issuerDocumentType,
            'issuer_document_number' => $issuerDocumentNumber,
            'receiver_name' => $receiverName,
            'receiver_document_type' => $receiverDocumentType,
            'receiver_document_number' => $receiverDocumentNumber,
            'total_amount' => $totalAmount,
            'xml_content' => $xmlContent,
            'user_id' => $user->id,
            'currency_id' => $currency->id,
            'type_document_id' => $typeDocument->id,
            'series' => $series,
            'correlative' => $correlative,
        ]);
        $voucher->save();

        foreach ($xml->xpath('//cac:InvoiceLine') as $invoiceLine) {
            $name = (string) $invoiceLine->xpath('cac:Item/cbc:Description')[0];
            $quantity = (float) $invoiceLine->xpath('cbc:InvoicedQuantity')[0];
            $unitPrice = (float) $invoiceLine->xpath('cac:Price/cbc:PriceAmount')[0];

            $voucherLine = new VoucherLine([
                'name' => $name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'voucher_id' => $voucher->id,
            ]);

            $voucherLine->save();
        }

        return $voucher;
    }

    /**
     * Deletes a voucher by its ID.
     *
     * @param int $id The ID of the voucher to be deleted.
     * 
     * @return void
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no voucher is found with the given ID.
     */
    public function deleteVoucher(string $id): void
    {
        $voucher = Voucher::withTrashed()->find($id);

        if (!$voucher) {
            throw new \Exception("Voucher with ID $id not found.");
        }
    
        if ($voucher->trashed()) {
            throw new \Exception("Voucher with ID $id has already been deleted.");
        }
    
        $voucher->delete();
    }
}
