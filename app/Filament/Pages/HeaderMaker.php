<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class HeaderMaker extends Page
{
    protected string $view = 'filament.pages.header-maker';
     protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-paint-brush';

    protected static ?int $navigationSort = 7;
}
