<?php

namespace App\Filament\App\Widgets;

use App\Models\SystemAnnouncement;
use Filament\Widgets\Widget;

class AnnouncementBanner extends Widget
{
    protected static ?int $sort = -1;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.app.widgets.announcement-banner';

    public function getAnnouncements()
    {
        return SystemAnnouncement::where('is_active', true)
            ->latest()
            ->get();
    }
}