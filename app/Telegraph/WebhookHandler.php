<?php

namespace App\Telegraph;

use DefStudio\Telegraph\DTO\User;
use \DefStudio\Telegraph\Handlers\WebhookHandler as TelegraphWebhookHandler;

class WebhookHandler extends TelegraphWebhookHandler
{
    protected function handleChatMemberJoined(User $member): void
    {
        // .. do nothing
    }
}
