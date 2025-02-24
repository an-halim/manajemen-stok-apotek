<?php

namespace App\Filament\Resources\InventoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InventoryItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'inventoryItems';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('product_name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_name')
            ->columns([
                Tables\Columns\TextColumn::make('getSupplierAttribute.supplier.supplier_name'),
                Tables\Columns\TextColumn::make('product.name'),
                Tables\Columns\TextColumn::make('quantity_received'),
                Tables\Columns\TextColumn::make('quantity_available'),
                Tables\Columns\TextColumn::make('getSupplierAttribute.purchase_date')
                    ->label('Purchase Date'),
                Tables\Columns\TextColumn::make('purchaseItem.expiry_date')
                    ->label('Expiry Date'),
                Tables\Columns\TextColumn::make('batch_code'),
            ])
            ->filters([

            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
