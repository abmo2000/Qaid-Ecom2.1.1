<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\WhatsAppConnection;
use App\Models\User;
use App\Services\EvolutionWhatsAppService;
use Filament\Widgets\Widget;

class WhatsAppConnectionAlert extends Widget
{
    protected string $view = 'filament.widgets.whatsapp-connection-alert';
    protected static ?int $sort = 1;

    public string $state = 'close';

    public static function canView(): bool
    {
        return auth()->user() instanceof User && auth()->user()->isAdmin();
    }

    public function mount(EvolutionWhatsAppService $whatsApp): void
    {
        $this->state = $whatsApp->connectionState()['state'];
    }

    public function refreshConnection(EvolutionWhatsAppService $whatsApp): void
    {
        $this->state = $whatsApp->connectionState()['state'];
    }

    public function connectionUrl(): string
    {
        return WhatsAppConnection::getUrl();
    }
}