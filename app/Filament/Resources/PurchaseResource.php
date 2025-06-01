<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Product\Resources\ProductsResource;
use App\Filament\Resources\PurchaseResource\Pages;
use App\Filament\Resources\PurchaseResource\RelationManagers;
use App\Models\Purchase;
use App\Models\Products;
use App\Models\Supplier;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Arr;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Pembelian';
    protected static ?string $modelLabel = 'Pembelian';
    protected static ?string $pluralModelLabel = 'Pembelian';

    public static function getNavigationBadge(): ?string
    {
        return (string) Purchase::count();
    }

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
                                    ->action(fn(Forms\Set $set) => $set('items', [])),
                            ])
                            ->schema([
                                static::getItemsRepeater(),
                            ])
                            ->collapsible(),
                        Forms\Components\Section::make('Payment')
                            ->schema([
                                Forms\Components\Placeholder::make('total_price')
                                    ->content(function (Forms\Get $get) {
                                        $map = Arr::map($get('items'), function ($item) {
                                            return $item['purchase_price'] * $item['quantity_purchased'];
                                        });
                                        return 'Rp ' . number_format(array_sum($map), 2, ',', '.');
                                    }),
                                Forms\Components\Select::make('payment_method')
                                    ->options([
                                        'Cash' => 'Cash',
                                        'Bank Transfer' => 'Bank Transfer',
                                    ]),
                            ])
                            ->columns(2)
                            ->hidden(fn(?Purchase $record) => $record !== null),
                    ])
                    ->columnSpan(['lg' => fn(?Purchase $record) => $record === null ? 4 : 3]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make("Payment")
                            ->schema([
                                Forms\Components\Placeholder::make('Total')
                                    ->label('Total Purchase Price')
                                    ->content(function (Purchase $record) {
                                        $total = $record->items->map(function ($item) {
                                            return ($item->purchase_price ?? 0) * ($item->quantity_purchased ?? 1);
                                        })->sum();

                                        return 'Rp ' . number_format($total, 2, ',', '.');
                                    }),
                                Forms\Components\Placeholder::make('Payment method')
                                    ->label('Total Purchase Price')
                                    ->content(fn(Purchase $record): ?string => $record->payment_method),
                            ]),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Placeholder::make('created_at')
                                    ->label('Created at')
                                    ->content(fn(Purchase $record): ?string => $record->created_at?->diffForHumans()),

                                Forms\Components\Placeholder::make('updated_at')
                                    ->label('Last modified at')
                                    ->content(fn(Purchase $record): ?string => $record->updated_at?->diffForHumans()),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn(?Purchase $record) => $record === null),
            ])
            ->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('supplier.supplier_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchase_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total Purchase Price')
                    ->getStateUsing(function ($record) {
                        $total = $record->items->map(function ($item) {
                            return ($item->purchase_price ?? 0) * ($item->quantity_purchased ?? 1);
                        })->sum();

                        return $total;
                    })
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_item')
                    ->label('Total Item')
                    ->getStateUsing(function ($record) {
                        $total = $record->items->count();

                        return $total;
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
                ->relationship('supplier', 'supplier_name')
                ->required()
                ->reactive()
                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                ->searchable()
                ->createOptionForm([
                    Forms\Components\TextInput::make('supplier_name')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label('Email address')
                        ->required()
                        ->email()
                        ->maxLength(255)
                        ->unique(),

                    Forms\Components\TextInput::make('phone_number')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('contact_person')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('address')
                        ->maxLength(255),

                ])
                ->createOptionAction(function (Action $action) {
                    return $action
                        ->modalHeading('Create supplier')
                        ->modalSubmitActionLabel('Create supplier')
                        ->modalWidth('lg');
                }),
            Forms\Components\DatePicker::make('purchase_date')
                ->required()
                ->default(Carbon::now()),
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
                    ->required()
                    ->default(1)
                    ->live(onBlur: true)
                    ->columnSpan([
                        'md' => 2,
                    ]),
                Forms\Components\TextInput::make('purchase_price')
                    ->label('Price/Unit')
                    ->numeric()
                    ->required()
                    ->live(onBlur: true)
                    ->columnSpan([
                        'md' => 2,
                    ]),

                Forms\Components\TextInput::make('selling_price')
                    ->label('Sale Price/Unit')
                    ->numeric()
                    ->required()
                    ->live(onBlur: true)
                    ->columnSpan([
                        'md' => 2,
                    ]),

                Forms\Components\TextInput::make('batch_code')
                    ->label('Batch Code')
                    ->columnSpan([
                        'md' => 2,
                        'lg' => 4,
                    ])
                    ->required()
                    ->reactive(),


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
                    ->hidden(fn(array $arguments, Repeater $component): bool => blank($component->getRawItemState($arguments['item'])['product_id'])),
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
