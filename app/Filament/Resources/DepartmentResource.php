<?php

namespace App\Filament\Resources;

use App\Exports\DepartmentsExport;
use App\Filament\Resources\CollegeResource\RelationManagers\FacultiesRelationManager;
use App\Filament\Resources\DepartmentResource\Pages;
use App\Filament\Resources\DepartmentResource\RelationManagers;
use App\Filament\Resources\DepartmentResource\RelationManagers\HodRelationManager;
use App\Filament\Resources\DepartmentResource\RelationManagers\StudentsRelationManager;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Maatwebsite\Excel\Facades\Excel;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'College Departments';
    protected static ?string $slug = 'college-departments';
    protected static ?string $modelLabel = 'Department';
    protected static ?string $navigationGroup = 'Affliated Institutions';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Department Information')->schema([
                    TextInput::make('name')
                        ->required()->label("Department Name")
                        ->maxLength(255),
                    TextInput::make('dept_code')->required(),
                    FileUpload::make('image')->preserveFilenames()
                    ->openable()->downloadable()->image()->directory('departments')->nullable()
                    ->columnSpanFull(),
                ])->columns(2),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('dept_code')->sortable()->searchable(),
                ImageColumn::make('image'),
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('updated_at')->dateTime()->sortable(),
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
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('export')->label('Export')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (Collection $records){
                        return Excel::download(new DepartmentsExport($records, 1), 'Departments.xlsx');  
                    })
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            FacultiesRelationManager::class,
            HodRelationManager::class,
            StudentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'view' => Pages\ViewDepartment::route('/{record}'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}
