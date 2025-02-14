<?php

namespace App\Filament\Resources\PurchaseResource\Pages;

use App\Filament\Resources\PurchaseResource;
use App\Models\Supplier;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchase extends CreateRecord
{
    protected static string $resource = PurchaseResource::class;


    protected function afterCreate(): void
    {
        /** @var Order $order */
        $record = $this->record;

        $suplierName = Supplier::find($record->supplier_id)?->supplier_name;

        /** @var User $user */
        $user = auth()->user();

        Notification::make()
            ->title('New purchase order created')
            ->icon('heroicon-o-shopping-bag')
            ->body("**{$suplierName} stored {$record->items->count()} products.**")
            ->sendToDatabase($user);
    }
}
