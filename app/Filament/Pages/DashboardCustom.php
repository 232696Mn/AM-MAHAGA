<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class DashboardCustom extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static string $view = 'filament.pages.dashboard-custom';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title = 'Dashboard';
}
