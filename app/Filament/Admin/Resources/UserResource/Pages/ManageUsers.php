<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Actions\Users\UserCreateAction;
use App\Filament\Admin\Resources\UserResource;
use App\Models\UserType;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

final class ManageUsers extends ManageRecords
{
    /**
     * The resource class associated with this page.
     */
    protected static string $resource = UserResource::class;

    /**
     * Get the header actions for this page.
     */
    public function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(fn (array $data, UserCreateAction $createAction): Model => $createAction->handle($data)),
        ];
    }

    /**
     * Get the tabs for this page.
     */
    public function getTabs(): array
    {
        /** @var \App\Models\User $model */
        $model = $this->getModel();

        $userTypes = [
            UserType::SUPER_ADMIN,
            UserType::ADMINISTRATOR,
        ];
        if (Auth::user()->type_id == UserType::ADMINISTRATOR) {
            $userTypes = [UserType::ADMINISTRATOR];
        }

        return [
            'all' => Tab::make(__('admin/users.table.tabs.all'))
                ->badge($model::query()->whereIn('type_id', $userTypes)->count()),
            'active' => Tab::make(__('admin/users.table.tabs.active'))
                ->modifyQueryUsing(fn (Builder $query) => $query->active())
                ->badge($model::query()->active()->whereIn('type_id', $userTypes)->count()),
            'archived' => Tab::make(__('admin/users.table.tabs.archived'))
                ->modifyQueryUsing(fn (Builder $query) => $query->archived())
                ->badge($model::query()->archived()->whereIn('type_id', $userTypes)->count()),
        ];
    }
}
