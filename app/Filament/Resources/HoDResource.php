<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HoDResource\Pages;
use App\Filament\Resources\HoDResource\RelationManagers;
use App\Models\HoD;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HoDResource extends Resource
{
    protected static ?string $model = HoD::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('HoD Name')->schema([
                        Select::make('user_id')->relationship('user', 'name', fn($query) => $query->where('role', 'hod'))
                            ->searchable()->preload()->required(),
                    ])
                ])->columnSpan(1),
                Group::make()->schema([
                    Section::make('College Details')->schema([
                        Select::make('college_id')->relationship('college', 'name')->preload()->searchable()->required(),
                    ]),
                ])->columnSpan(1),
                Group::make()->schema([
                    Section::make('Department Details')->schema([
                        Select::make('department_id')->relationship('department', 'name')->preload()->searchable()->required(),
                    ]),
                ])->columnSpan(1),
                Group::make()->schema([
                    Section::make('Status')->schema([
                        Toggle::make('status')->required()->default(true),
                    ])->columns(2),
                ])->columnSpan(1),
                Group::make()->schema([   
                    Section::make('Faculty Details')->schema([
                        TextInput::make('qualification')->required()->maxLength(255),
                        TextInput::make('experience')->required()->maxLength(255),
                        TextInput::make('specialization')->required()->maxLength(255),
                        DatePicker::make('joining_date')->required(),
                        DatePicker::make('leaving_date')->default(null),
                    ])->columns(2),
                ])->columnSpanFull(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->sortable(),
                TextColumn::make('college.name')->sortable(),
                TextColumn::make('department.name')->sortable(),
                TextColumn::make('qualification')->searchable(),
                TextColumn::make('experience')->searchable(),
                TextColumn::make('specialization')->searchable(),
                IconColumn::make('status')->boolean(),
                TextColumn::make('joining_date')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('leaving_date')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
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
            'index' => Pages\ListHoDS::route('/'),
            'create' => Pages\CreateHoD::route('/create'),
            'edit' => Pages\EditHoD::route('/{record}/edit'),
        ];
    }
}
