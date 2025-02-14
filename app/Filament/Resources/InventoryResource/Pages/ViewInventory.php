<?php

namespace App\Filament\Resources\InventoryResource\Pages;

use App\Filament\Resources\InventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInventory extends ViewRecord
{
    protected static string $resource = InventoryResource::class;

    public function getTitle(): string
    {
        $record = $this->getRecord();

        return $record->product->name ?? 'Inventory Detail';
    }
}
