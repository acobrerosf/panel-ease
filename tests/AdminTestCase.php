<?php

namespace Tests;

use App\Enums\PanelEnums;
use App\Models\User;
use App\Models\UserType;
use Filament\Facades\Filament;

class AdminTestCase extends TestCase
{
    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Set admin panel.
        Filament::setCurrentPanel(
            Filament::getPanel(PanelEnums::Admin->value)
        );

        // Authenticate full administrator user.
        $fullAdminUser = User::factory()->create(['type_id' => UserType::FULL_ADMINISTRATOR]);
        $this->actingAs($fullAdminUser, 'admin');
    }
}
