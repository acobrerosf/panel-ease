<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Actions\Users\UserArchiveAction;
use App\Actions\Users\UserDeleteAction;
use App\Actions\Users\UserInviteAction;
use App\Actions\Users\UserUnarchiveAction;
use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\UserType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

final class UserResource extends Resource
{
    /**
     * The model class associated with this resource.
     */
    protected static ?string $model = User::class;

    /**
     * The icon to be used for this resource in the navigation.
     */
    protected static ?string $navigationIcon = 'heroicon-o-users';

    /**
     * The sort order for this resource in the navigation.
     */
    protected static ?int $navigationSort = 4;

    /**
     * Get the navigation label for this resource.
     */
    public static function getNavigationLabel(): string
    {
        return __('admin/users.navigation.label');
    }

    /**
     * Get the plural label for this resource.
     */
    public static function getPluralLabel(): ?string
    {
        return self::getNavigationLabel();
    }

    /**
     * Get the navigation group for this resource.
     */
    public static function getNavigationGroup(): ?string
    {
        return __('admin/users.navigation.group');
    }

    /**
     * Get the model label for this resource.
     */
    public static function getModelLabel(): string
    {
        return __('admin/users.model');
    }

    /**
     * Define the form schema for creating/editing users.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->columns(['lg' => 2])
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('admin/users.form.fields.name'))
                    ->columnSpan(1)
                    ->maxLength(255)
                    ->required(),

                Forms\Components\Select::make('type_id')
                    ->label(__('admin/users.form.fields.type_id'))
                    ->relationship(
                        name: 'type',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('id')
                    )
                    ->getOptionLabelFromRecordUsing(fn (UserType $type): string => $type->name)
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('email')
                    ->label(__('admin/users.form.fields.email'))
                    ->columnSpanFull()
                    ->maxLength(255)
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required(),
            ]);
    }

    /**
     * Define the table configuration for displaying users.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn (Builder $query) => $query->when(
                    value: Auth::user()->type_id != UserType::SUPER_ADMIN,
                    callback: fn (Builder $query) => $query->where('type_id', '<>', UserType::SUPER_ADMIN)
                )
                    ->whereNull('deleted_at')
            )
            ->defaultSort('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('admin/users.table.columns.name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('admin/users.table.columns.email'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('type.name')
                    ->label(__('admin/users.table.columns.type_id'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label(__('admin/users.table.columns.email_verified_at'))
                    ->boolean()
                    ->getStateUsing(fn (User $record) => $record->hasVerifiedEmail())
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('invite')
                    ->label(__('admin/users.actions.invite.label'))
                    ->color('success')
                    ->icon('heroicon-o-paper-airplane')
                    ->requiresConfirmation()
                    ->visible(fn (User $record) => ! $record->hasVerifiedEmail() && $record->isActive())
                    ->action(function (User $record, UserInviteAction $inviteAction): void {
                        $success = $inviteAction->handle($record);
                        if (! $success) {
                            Notification::make()
                                ->danger()
                                ->title(__('admin/users.actions.invite.notification_title_failed'))
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->success()
                            ->title(__('admin/users.actions.invite.notification_title_success'))
                            ->send();
                    }),

                Tables\Actions\Action::make('unarchive')
                    ->label(__('admin/users.actions.unarchive.label'))
                    ->color('info')
                    ->icon('heroicon-o-arrow-left-start-on-rectangle')
                    ->requiresConfirmation()
                    ->visible(fn (User $record) => $record->isArchived() && $record->id !== Auth::user()->id)
                    ->action(function (User $record, UserUnarchiveAction $unarchiveAction): void {
                        $unarchiveAction->handle($record);

                        Notification::make()
                            ->success()
                            ->title(__('admin/users.actions.unarchive.notification_title_success'))
                            ->send();
                    }),

                Tables\Actions\EditAction::make()
                    ->visible(fn (User $record) => $record->id !== Auth::user()->id),

                Tables\Actions\Action::make('archive')
                    ->label(__('admin/users.actions.archive.label'))
                    ->color('info')
                    ->icon('heroicon-o-archive-box-x-mark')
                    ->requiresConfirmation()
                    ->visible(fn (User $record) => $record->isActive() && $record->id !== Auth::user()->id)
                    ->action(function (User $record, UserArchiveAction $archiveAction): void {
                        $archiveAction->handle($record);

                        Notification::make()
                            ->success()
                            ->title(__('admin/users.actions.archive.notification_title_success'))
                            ->send();
                    }),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn (User $record) => $record->id !== Auth::user()->id)
                    ->using(fn (User $record, UserDeleteAction $deleteAction) => $deleteAction->handle($record)),
            ])
            ->bulkActions([
                //
            ]);
    }

    /**
     * Get the pages related to this resource.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }

    /**
     * Get the Eloquent query for this resource.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
