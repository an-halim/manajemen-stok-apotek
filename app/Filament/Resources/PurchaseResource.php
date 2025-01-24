<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Product\Resources\ProductsResource;
use App\Filament\Resources\PurchaseResource\Pages;
use App\Filament\Resources\PurchaseResource\RelationManagers;
use App\Models\Purchase;
use App\Models\Products;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Transactions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema(static::getDetailsFormSchema())
                            ->columns(2),

                        Forms\Components\Section::make('Purchase items')
                            ->headerActions([
                                Action::make('reset')
                                    ->modalHeading('Are you sure?')
                                    ->modalDescription('All existing items will be removed from the item.')
                                    ->requiresConfirmation()
                                    ->color('danger')
                                    ->action(fn (Forms\Set $set) => $set('items', [])),
                            ])
                            ->schema([
                                static::getItemsRepeater(),
                            ])
                            ->collapsible(),
                        Forms\Components\Section::make('Payment')
                            ->schema([
                                Forms\Components\TextInput::make('total_price')
                                    ->label('Total Price')
                                    ->disabled() // Prevent manual input
                                    ->reactive() // Update automatically
                                    ->dehydrated(false) // Don't save this field to the database
                                    ->afterStateUpdated(fn ($set, $get) =>
                                        $set('total_price', collect($get('items'))->sum(fn ($item) => ($item['purchase_price'] ?? 0) * ($item['quantity_purchased'] ?? 1000)))
                                    ),
                                Forms\Components\Select::make('payment_method')
                                    ->options([
                                        'Cash' => 'Cash',
                                        'Bank Transfer' => 'Bank Transfer',
                                    ]),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => fn (?Purchase $record) => $record === null ? 3 : 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make("Payment")
                            ->schema([
                                Forms\Components\Placeholder::make('Total')
                                    ->label('Total Purchase Price')
                                    ->content(fn (Purchase $record): ?string => $record->items()->sum('purchase_price')),
                                Forms\Components\Placeholder::make('Payment method')
                                    ->label('Total Purchase Price')
                                    ->content('Cash'),
                            ]),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Placeholder::make('created_at')
                                    ->label('Created at')
                                    ->content(fn (Purchase $record): ?string => $record->created_at?->diffForHumans()),

                                Forms\Components\Placeholder::make('updated_at')
                                    ->label('Last modified at')
                                    ->content(fn (Purchase $record): ?string => $record->updated_at?->diffForHumans()),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?Purchase $record) => $record === null),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('supplier.id')
                    ->getStateUsing(fn ($record): ?string => Supplier::find($record->id)?->supplier_name ?? null)
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchase_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total Purchase Price')
                    ->getStateUsing(function ($record) {
                        return $record->items()->sum('purchase_price');
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
            'edit' => Pages\EditPurchase::route('/{record}/edit'),
        ];
    }


     /** @return Forms\Components\Component[] */
     public static function getDetailsFormSchema(): array
     {
         return [
            Forms\Components\Select::make('supplier_id')
                 ->label('Supplier')
                 ->options(Supplier::query()->pluck('supplier_name', 'id'))
                 ->required()
                 ->reactive()
                 ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                 ->searchable(),
             Forms\Components\DatePicker::make('purchase_date')
                 ->required(),
         ];
     }

     public static function getItemsRepeater(): Repeater
    {
        return Repeater::make('items')
            ->relationship()
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Product')
                    ->options(Products::query()->pluck('name', 'id'))
                    ->required()
                    ->reactive()
                    ->columnSpan([
                        'md' => 4,
                    ])
                    ->searchable(),

                Forms\Components\TextInput::make('quantity_purchased')
                    ->label('Quantity')
                    ->numeric()
                    ->default(1)
                    ->columnSpan([
                        'md' => 2,
                        'lg' => 1,
                    ])
                    ->required()
                    ->reactive(),

                Forms\Components\TextInput::make('purchase_price')
                    ->label('Unit Price')
                    ->numeric()
                    ->required()
                    ->reactive()
                    ->columnSpan([
                        'md' => 2,
                    ]),


                Forms\Components\DatePicker::make('expiry_date')
                    ->label('Expiry Date')
                    ->required()
                    ->columnSpan([
                        'md' => 1,
                    ]),
            ])
            ->extraItemActions([
                Action::make('openProduct')
                    ->tooltip('Open product')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(function (array $arguments, Repeater $component): ?string {
                        $itemData = $component->getRawItemState($arguments['item']);

                        $product = Products::find($itemData['product_id']);

                        if (! $product) {
                            return null;
                        }

                        return ProductsResource::getUrl('edit', ['record' => $product]);
                    }, shouldOpenInNewTab: true)
                    ->hidden(fn (array $arguments, Repeater $component): bool => blank($component->getRawItemState($arguments['item'])['product_id'])),
            ])
            ->defaultItems(1)
            ->hiddenLabel()
            ->columns([
                'md' => 10,
            ])
            ->required()
            ->reactive(); // Ensure this triggers updates when repeater items change
    }

     /** @return Builder<Order> */
     public static function getEloquentQuery(): Builder
     {
         return parent::getEloquentQuery()->withoutGlobalScope(SoftDeletingScope::class);
     }
}
