<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Product\Resources\ProductsResource;
use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\RelationManagers;
use App\Models\Inventory;
use App\Models\Products;
use App\Models\PurchaseItem;
use App\Models\Sale;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Arr;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Transactions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema(static::getInvoiceFormSchema())
                            ->columns(2),

                        Forms\Components\Section::make()
                            ->schema(static::getDetailsFormSchema())
                            ->columns(2),

                        Forms\Components\Section::make('Sale items')
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
                                Forms\Components\Placeholder::make('total_price')
                                    ->content(function (Forms\Get $get) {
                                        $map = Arr::map($get('items'), function ($item) {
                                            return $item['selling_price'] * $item['sale_quantity'];
                                        });
                                         return 'Rp ' . number_format(array_sum($map), 2, ',', '.');
                                    }),
                                Forms\Components\Select::make('payment_method')
                                    ->label('Payment Method')
                                    ->options([
                                        'Cash' => 'Cash',
                                        'Bank Transfer' => 'Bank Transfer',
                                    ])
                                    ->default(fn ($record) => $record?->payment_method)
                                    ->required(),
                            ])
                            ->columns(2)
                            ->hidden(fn (?Sale $record) => $record !== null),
                    ])
                    ->columnSpan(['lg' => fn (?Sale $record) => $record === null ? 3 : 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make("Payment")
                            ->schema([
                                Forms\Components\Placeholder::make('Total')
                                    ->label('Total Purchase Price')
                                    ->content(fn (Sale $record): ?string => $record->items()->sum('selling_price')),
                                Forms\Components\Placeholder::make('Payment Method')
                                    ->label('Payment Method')
                                    ->content(fn (Sale $record): ?string => $record->payment_method),
                            ]),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Placeholder::make('created_at')
                                    ->label('Created at')
                                    ->content(fn (Sale $record): ?string => $record->created_at?->diffForHumans()),

                                Forms\Components\Placeholder::make('updated_at')
                                    ->label('Last modified at')
                                    ->content(fn (Sale $record): ?string => $record->updated_at?->diffForHumans()),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?Sale $record) => $record === null),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Invoice Number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer Name')
                    ->searchable()
                    ->sortable(),


                Tables\Columns\TextColumn::make('total_item')
                    ->label('Total Item')
                    ->getStateUsing(function ($record) {
                        $total = $record->items->count();

                        return $total;
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total Price')
                    ->getStateUsing(function ($record) {
                        $total = $record->items->map(function ($item) {
                            return ($item->selling_price ?? 0) * ($item->sale_quantity ?? 1);
                        })->sum();

                        return $total;
                    })
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sale_date')
                    ->label('Sale Date')
                    ->searchable()
                    ->sortable(),

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
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }

    //  /** @return Builder<Order> */
    //  public static function getEloquentQuery(): Builder
    //  {
    //      return parent::getEloquentQuery()->withoutGlobalScope(SoftDeletingScope::class);
    //  }

      /** @return Forms\Components\Component[] */
     public static function getDetailsFormSchema(): array
     {
         return [
            Forms\Components\TextInput::make('customer_name')
                 ->label('Customer Name')
                 ->required(),
             Forms\Components\DatePicker::make('sale_date')
                 ->required()
                 ->default(Carbon::now())
                 ->required(),
         ];
     }

      /** @return Forms\Components\Component[] */
      public static function getInvoiceFormSchema(): array
      {
          return [
            Forms\Components\TextInput::make('invoice_number')
                    ->label('Invoice Number')
                    ->disabled()
                    ->dehydrated()
                    ->default(fn () => Sale::generateInvoiceNumber())
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
                    ->options(
                        Products::whereIn('id', Inventory::pluck('product_id'))
                                ->pluck('name', 'id')
                    )
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $nearExpiredItem = PurchaseItem::where('product_id', $state)
                            ->where('expiry_date', '>=', now())
                            ->orderBy('expiry_date') // Get the nearest expiry first
                            ->first();

                        if ($nearExpiredItem) {
                            $set('selling_price', $nearExpiredItem->selling_price ?? 0);
                            $set('available_stock', Inventory::where('product_id', $state)->sum('quantity_available'));
                        } else {
                            $set('selling_price', 0);
                            $set('available_stock', 0);
                        }
                    })
                    ->columnSpan(fn (string $context) => $context === 'create' ? 3 : 4)
                    ->searchable(),

                Forms\Components\TextInput::make('available_stock')
                    ->label('Available Stock')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan([
                        'md' => 2,
                    ])
                    ->default(0)
                    ->visible(fn (string $context) => $context === 'create'),

                Forms\Components\TextInput::make('sale_quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->required()
                    ->default(1)
                    ->live(onBlur: true)
                    ->columnSpan([
                        'md' => 1,
                    ])
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $set('total', $state * ($get('selling_price') ?? 0));
                    }),

                Forms\Components\TextInput::make('selling_price')
                    ->label('Price/Unit')
                    ->disabled()
                    ->dehydrated(true)
                    ->columnSpan([
                        'md' => 2,
                    ])
                    ->default(0)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $set('total', ($get('sale_quantity') ?? 0) * $state);
                    }),

                Forms\Components\TextInput::make('total')
                    ->label('Total')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan([
                        'md' => 2,
                    ])
                    ->default(0),
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
}
