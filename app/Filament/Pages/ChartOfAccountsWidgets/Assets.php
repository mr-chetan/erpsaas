<?php

namespace App\Filament\Pages\ChartOfAccountsWidgets;

use App\Models\Asset;
use App\Models\Company;
use App\Models\Department;
use Filament\Forms;
use Filament\Tables;
use Filament\Widgets\TableWidget as PageWidget;
use Illuminate\Database\Eloquent\Builder;

class Assets extends PageWidget
{
    protected int|string|array $columnSpan = [
        'md' => 2,
        'xl' => 3,
    ];

    protected function getTableQuery(): Builder
    {
        return Asset::query();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('company.name', 'name')->hidden(),
            Tables\Columns\TextColumn::make('department.name', 'name')->hidden(),
            Tables\Columns\TextColumn::make('code'),
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('type'),
            Tables\Columns\TextColumn::make('description')->hidden(),
            Tables\Columns\TextColumn::make('expense_transactions_sum_amount')->sum('expense_transactions', 'amount')->money('USD', 2)->label('Amount'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\ActionGroup::make([
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make()
                ->form([
                    Forms\Components\Select::make('company_id')
                    ->label('Company')
                    ->options(Company::all()->pluck('name', 'id')->toArray())
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('department_id', null)),

                    Forms\Components\Select::make('department_id')
                    ->label('Department')
                    ->options(function (callable $get) {
                        $company = Company::find($get('company_id'));

                        if (! $company) {
                            return Department::all()->pluck('name', 'id');
                        }

                        return $company->departments->pluck('name', 'id');
                    }),

                    Forms\Components\TextInput::make('code')
                        ->required(),
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('type')
                        ->required()
                        ->options([
                            'Current Asset' => 'Current Asset',
                            'Fixed Asset' => 'Fixed Asset',
                            'Tangible Asset' => 'Tangible Asset',
                            'Intangible Asset' => 'Intangible Asset',
                            'Operating Asset' => 'Operating Asset',
                            'Non-Operating Asset' => 'Non-Operating Asset',
                        ]),
                    Forms\Components\TextInput::make('description')
                        ->maxLength(255),
                ]),

                Tables\Actions\EditAction::make()
                ->form([
                    Forms\Components\Select::make('company_id')
                    ->label('Company')
                    ->options(Company::all()->pluck('name', 'id')->toArray())
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('department_id', null)),

                    Forms\Components\Select::make('department_id')
                    ->label('Department')
                    ->options(function (callable $get) {
                        $company = Company::find($get('company_id'));

                        if (! $company) {
                            return Department::all()->pluck('name', 'id');
                        }

                        return $company->departments->pluck('name', 'id');
                    }),

                    Forms\Components\TextInput::make('code')->required()->unique()->numeric()->minValue(100)->maxValue(199),
                    Forms\Components\TextInput::make('name')->required()->maxLength(50)->unique(),
                    Forms\Components\Select::make('type')
                        ->required()
                        ->options([
                            'Current Asset' => 'Current Asset',
                            'Fixed Asset' => 'Fixed Asset',
                            'Tangible Asset' => 'Tangible Asset',
                            'Intangible Asset' => 'Intangible Asset',
                            'Operating Asset' => 'Operating Asset',
                            'Non-Operating Asset' => 'Non-Operating Asset',
                        ]),
                    Forms\Components\TextInput::make('description')
                        ->maxLength(255),
                ]),
            ]),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Tables\Actions\CreateAction::make()
            ->form([
                Forms\Components\Select::make('company_id')
                ->label('Company')
                ->options(Company::all()->pluck('name', 'id')->toArray())
                ->reactive()
                ->afterStateUpdated(fn (callable $set) => $set('department_id', null)),

                Forms\Components\Select::make('department_id')
                ->label('Department')
                ->options(function (callable $get) {
                    $company = Company::find($get('company_id'));

                    if (! $company) {
                        return Department::all()->pluck('name', 'id');
                    }

                    return $company->departments->pluck('name', 'id');
                }),

                Forms\Components\TextInput::make('code')->required()->unique()->numeric()->minValue(118)->maxValue(199),
                Forms\Components\TextInput::make('name')->required()->maxLength(50)->unique(),
                Forms\Components\Select::make('type')
                    ->required()
                    ->options([
                        'Current Asset' => 'Current Asset',
                        'Fixed Asset' => 'Fixed Asset',
                        'Tangible Asset' => 'Tangible Asset',
                        'Intangible Asset' => 'Intangible Asset',
                        'Operating Asset' => 'Operating Asset',
                        'Non-Operating Asset' => 'Non-Operating Asset',
                    ]),
                Forms\Components\TextInput::make('description')
                    ->maxLength(255),
            ]),
        ];
    }
}
