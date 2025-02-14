<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Product\Resources\ProductsResource;
use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\RelationManagers;
use App\Models\Products;
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

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

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
                //
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
            Forms\Components\Select::make('customer_name')
                 ->label('Customer Name')
                 ->required()
                 ->reactive()
                 ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                 ->searchable(),
             Forms\Components\DatePicker::make('date')
                 ->required()
                 ->default(Carbon::now())
         ];
     }

     public static function getItemsRepeater(): Repeater
    {
        return Repeater::make('saleItems')
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
