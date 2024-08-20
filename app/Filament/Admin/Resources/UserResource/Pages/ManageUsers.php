<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Actions\Users\UserCreateAction;
use App\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(fn (array $data, UserCreateAction $createAction): Model => $createAction($data))
        ];
    }

    public function getTabs(): array
    {
        return [
            'active' => Tab::make(__('admin/users.table.tabs.active'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('archived_at')),
            'archived' => Tab::make(__('admin/users.table.tabs.archived'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('archived_at')),
        ];
    }
}
