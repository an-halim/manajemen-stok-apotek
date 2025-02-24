<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryResource\Pages;
use App\Filament\Resources\InventoryResource\RelationManagers;
use App\Filament\Resources\PurchaseResource\Pages\CreatePurchase;
use App\Models\Inventory;
use App\Models\Products;
use Filament\Forms;
use Filament\Forms\Components\Split;
use Filament\Forms\Form;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split as FilamentSplit;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Pages\SubNavigationPosition;

class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Stock Management';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Product')
                    ->relationship('product', 'name')
                    ->required(),
                Forms\Components\TextInput::make('quantity_received')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('quantity_available')
                    ->required()
                    ->numeric(),
            ]);
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->select('inventory.product_id', 'inventory.batch_code', 'products.name as product_name', \DB::raw('SUM(inventory.quantity_received) as total_quantity_received'))
    //         ->join('products', 'inventory.product_id', '=', 'products.id')  // Join the products table
    //         ->groupBy('inventory.product_id', 'products.name');  // Order by product name
    // }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->select(
    //             'inventory.id',  // Add this if needed
    //             'inventory.batch_code', // ✅ Add this line
    //             'inventory.product_id',
    //             'products.name as product_name',
    //             \DB::raw('SUM(inventory.quantity_received) as total_quantity_received'),
    //             \DB::raw('SUM(inventory.quantity_available) as total_quantity_available')
    //         )
    //         ->join('products', 'inventory.product_id', '=', 'products.id')
    //         ->groupBy('inventory.product_id', 'products.name', 'inventory.batch_code'); // ✅ Add batch_code to groupBy
    // }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->select(
                \DB::raw('MIN(inventory.id) as id'), // Pick one ID per group
                'inventory.product_id',
                'products.name as product_name',
                \DB::raw('GROUP_CONCAT(DISTINCT inventory.batch_code) as batch_codes'), // Combine all batch codes
                \DB::raw('SUM(inventory.quantity_received) as total_quantity_received'),
                \DB::raw('SUM(inventory.quantity_available) as total_quantity_available')
            )
            ->join('products', 'inventory.product_id', '=', 'products.id')
            ->groupBy('inventory.product_id', 'products.name'); // ✅ Only grouping by product_id
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product_name')
                    ->description('Product Name')
                    ->url(fn ($record) => route('filament.dashboard.resources.inventories.view', $record)),
                TextColumn::make('total_quantity_received')
                    ->summarize(Sum::make()->label('Quantity Received'))
                    ->description('Quantity Received'),
                TextColumn::make('total_quantity_available')
                    ->summarize(Sum::make()->label('Quantity Available'))
                    ->description('Quantity Available'),
                TextColumn::make('batch_codes')
                    ->description('Batch Codes'),

            ])
            ->defaultSort('products.name', 'asc')
            ->striped()
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make()
                    ->schema([
                        Components\Split::make([
                            Components\Grid::make(2)
                                ->schema([
                                    Components\Group::make([
                                        Components\TextEntry::make('product.name'),
                                        Components\TextEntry::make('total_quantity_received'),
                                    ]),
                                    Components\Group::make([
                                        Components\TextEntry::make('batch_codes'),
                                        Components\TextEntry::make('total_quantity_available')
                                            ->badge()
                                            ->color('success'),
                                    ]),
                                ]),
                        ])->from('lg'),
                    ]),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            RelationManagers\InventoryItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventories::route('/'),
            'create' => CreatePurchase::route('/create'),
            'view' => Pages\ViewInventory::route('/{record}'),
        ];
    }
}
