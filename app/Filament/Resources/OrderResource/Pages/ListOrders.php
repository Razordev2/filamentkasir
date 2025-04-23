<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getTableFilters(): array
    {
        return [
            Filter::make('Tanggal Transaksi')
                ->form([
                    DatePicker::make('from')->label('Dari Tanggal'),
                    DatePicker::make('until')->label('Sampai Tanggal'),
                ])
                ->query(function ($query, array $data) {
                    return $query
                        ->when($data['from'], fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                        ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']));
                }),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            ExportBulkAction::make()
                ->label('Export Transaksi')
                ->exportFields([
                    'customer_name' => 'Nama Pelanggan',
                    'product.name' => 'Produk',
                    'quantity' => 'Jumlah',
                    'paymentMethod.paymentmethods' => 'Metode Pembayaran',
                    'total_price' => 'Total Harga',
                    'created_at' => 'Tanggal Transaksi',
                ])
                ->fileName('Riwayat-Transaksi-' . now()->format('Y-m-d')),
        ];
    }
}
