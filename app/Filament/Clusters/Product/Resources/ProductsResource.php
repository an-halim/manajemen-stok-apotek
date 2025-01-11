<?php

namespace App\Filament\Clusters\Product\Resources;

use App\Filament\Clusters\Product;
use App\Filament\Clusters\Product\Resources\ProductsResource\Pages;
use App\Filament\Clusters\Product\Resources\ProductsResource\RelationManagers;
use App\Models\Products;
use App\Models\Category;
// use Filament\Actions\Action;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductsResource extends Resource
{
    protected static ?string $model = Products::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Product::class;

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('category')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('slug')
                                            ->label('Category Slug')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(),
                                    ])
                                    ->createOptionAction(function (Action $action) {
                                        return $action
                                            ->modalHeading('Create Category')
                                            ->modalSubmitActionLabel('Create Category')
                                            ->modalWidth('lg');
                                    }),

                                Forms\Components\MarkdownEditor::make('description')
                                    ->columnSpan('full'),

                                Forms\Components\Toggle::make('status')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->inline(false)
                                    ->required()
                                    ->default(false),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make('Images')
                            ->schema([
                                Forms\Components\FileUpload::make('media')
                                    ->multiple()
                                    ->maxFiles(5)
                                    ->hiddenLabel(),
                            ])
                            ->collapsible(),
                    ])
                    ->columnSpan(['lg' => 2]),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category')
                    ->getStateUsing(fn ($record): ?string => Category::find($record->category_id)?->name ?? null)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit')
                    ->searchable(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProducts::route('/create'),
            'edit' => Pages\EditProducts::route('/{record}/edit'),
        ];
    }
}
