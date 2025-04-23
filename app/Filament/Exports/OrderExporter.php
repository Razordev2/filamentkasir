<?php

namespace App\Filament\Exports;

use App\Models\Order;
use Filament\Actions\Exports\ExportColumn;
use Filament\Tables\Actions\ExportAction;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
class OrderExporter extends Exporter
{
    protected static ?string $model = Order::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id'),
            ExportColumn::make('customer_name'),
            ExportColumn::make('product_name')->label('Produk')
                ->getValueUsing(fn ($record) => $record->product->name ?? 'Produk Tidak Ditemukan'),
            ExportColumn::make('quantity'),
            ExportColumn::make('paymentMethod.paymentmethods'),
            ExportColumn::make('total_price'),
            ExportColumn::make('created_at')
                ->label('Tanggal Transaksi')
                ->getValueUsing(fn ($record) => $record->created_at?->format('d-m-Y')),
        ];
    }

    public static function configureExportAction(ExportAction $action): ExportAction
    {
        return $action
            ->form([
                DatePicker::make('from_date')->label('Dari Tanggal'),
                DatePicker::make('to_date')->label('Sampai Tanggal'),
                Select::make('bulan')
                    ->label('Bulan')
                    ->options([
                        '01' => 'Januari',
                        '02' => 'Februari',
                        '03' => 'Maret',
                        '04' => 'April',
                        '05' => 'Mei',
                        '06' => 'Juni',
                        '07' => 'Juli',
                        '08' => 'Agustus',
                        '09' => 'September',
                        '10' => 'Oktober',
                        '11' => 'November',
                        '12' => 'Desember',
                    ]),
                Select::make('tahun')
                    ->label('Tahun')
                    ->options([
                        '2023' => '2023',
                        '2024' => '2024',
                        '2025' => '2025',
                    ]),
            ])
            ->modifyQueryUsing(function ($query, array $data) {
                if (!empty($data['from_date'])) {
                    $query->whereDate('created_at', '>=', $data['from_date']);
                }

                if (!empty($data['to_date'])) {
                    $query->whereDate('created_at', '<=', $data['to_date']);
                }

                if (!empty($data['bulan'])) {
                    $query->whereMonth('created_at', $data['bulan']);
                }

                if (!empty($data['tahun'])) {
                    $query->whereYear('created_at', $data['tahun']);
                }

                return $query;
            });
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your order export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
