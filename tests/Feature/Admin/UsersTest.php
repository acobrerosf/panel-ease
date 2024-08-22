<?php

use App\Filament\Admin\Resources\UserResource;
use App\Filament\Admin\Resources\UserResource\Pages\ManageUsers;
use App\Models\User;
use App\Models\UserType;
use Filament\Notifications\Auth\ResetPassword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

use function Pest\Livewire\livewire;

it('can render index page', function () {
    $this->get(UserResource::getUrl('index'))->assertSuccessful();
});

it('can list users', function () {
    $users = User::factory()->count(5)->create();
 
    livewire(ManageUsers::class)
        ->assertCanSeeTableRecords($users)
        ->assertCountTableRecords(6);
});

it('cannot display deleted users by default', function() {
    $users = User::factory()->count(3)->create();
    $deletedUsers = User::factory()->trashed()->count(3)->create();
 
    livewire(ManageUsers::class)
        ->assertCanSeeTableRecords($users)
        ->assertCanNotSeeTableRecords($deletedUsers)
        ->assertCountTableRecords(4);
});

it('cannot display full administrator users to administrator users', function() {
    $adminUser = User::factory()->create(['type_id' => UserType::ADMINISTRATOR]);
    $this->actingAs($adminUser, 'admin');

    $superAdminUsers = User::factory()->state(['type_id' => UserType::FULL_ADMINISTRATOR])->count(3)->create();
    $adminUsers = User::factory()->state(['type_id' => UserType::ADMINISTRATOR])->count(3)->create();
 
    livewire(ManageUsers::class)
        ->assertCanSeeTableRecords($adminUsers)
        ->assertCanNotSeeTableRecords($superAdminUsers)
        ->assertCountTableRecords(4);
});

it('cannot display actions to modify your account', function() {
    $user = auth()->user();
 
    livewire(ManageUsers::class)
        ->assertTableActionHidden('invite', $user)
        ->assertTableActionHidden('unarchive', $user)
        ->assertTableActionHidden('edit', $user)
        ->assertTableActionHidden('archive', $user)
        ->assertTableActionHidden('delete', $user);
});

it('displays the invite action for non-registered users only', function() {
    $user = User::factory()->create([
        'email_verified_at' => null
    ]);
 
    livewire(ManageUsers::class)
        ->assertTableActionVisible('invite', $user);
});

it('hides the invite action for registered users', function() {
    $user = User::factory()->create();
 
    livewire(ManageUsers::class)
        ->assertTableActionHidden('invite', $user);
});

it('displays the archive action for active users only', function() {
    $activeUser = User::factory()->create();
 
    livewire(ManageUsers::class)
        ->assertTableActionVisible('archive', $activeUser)
        ->assertTableActionHidden('unarchive', $activeUser);
});

it('displays the active action for archived users only', function() {
    $archivedUser = User::factory()->create(['archived_at' => now()]);
 
    livewire(ManageUsers::class)
        ->assertTableActionVisible('unarchive', $archivedUser)
        ->assertTableActionHidden('archive', $archivedUser);
});

it('can create a new user', function() { 
    Notification::fake();

    expect(
        DB::table('user_password_reset_tokens')
            ->where('email', 'admin@email.com')
            ->first()
    )->toBe(null);

    Notification::assertNothingSent();
    
    livewire(ManageUsers::class)
        ->callAction('create', data: [
            'name' => 'Admin',
            'type_id' => UserType::ADMINISTRATOR,
            'email' => 'admin@email.com'
        ]);

    $user = User::where('email', 'admin@email.com')->first();
    expect($user)->not->toBe(null);
        
    expect(
        DB::table('user_password_reset_tokens')
            ->where('email', $user->email)
            ->first()
    )->not->toBe(null);

    Notification::assertSentTo(
        [$user], ResetPassword::class
    );

    Notification::assertCount(1);
});

it('can edit an existing user', function() {
    $user = User::factory()->create();    
    
    livewire(ManageUsers::class)
        ->callTableAction('edit', $user, [
            ...$user->toArray(),
            'email' => 'edited@email.com'
        ]);

    expect($user->fresh()->email)->toBe('edited@email.com');
});

it('can delete user', function() {
    $user = User::factory()->create();
 
    livewire(ManageUsers::class)
        ->callTableAction('delete', $user);

    expect($user->fresh()->deleted_at)->not->toBe(null);
});

it('can archive user', function() {
    $user = User::factory()->create();
 
    livewire(ManageUsers::class)
        ->callTableAction('archive', $user);
        
    expect($user->fresh()->isArchived())->toBe(true);
});

it('can unarchive user', function() {
    $user = User::factory()->create(['archived_at' => now()]);
 
    livewire(ManageUsers::class)
        ->callTableAction('unarchive', $user);

    expect($user->fresh()->isActive())->toBe(true);
});

it('can send invite to user', function() {
    Notification::fake();

    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);
    
    expect(
        DB::table('user_password_reset_tokens')
            ->where('email', $user->email)
            ->first()
    )->toBe(null);

    Notification::assertNothingSent();
 
    livewire(ManageUsers::class)
        ->callTableAction('invite', $user);

    Notification::assertSentTo(
        [$user], ResetPassword::class
    );

    Notification::assertCount(1);
        
    expect(
        DB::table('user_password_reset_tokens')
            ->where('email', $user->email)
            ->first()
    )->not->toBe(null);
});
