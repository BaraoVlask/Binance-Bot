<?php

namespace App\Services;

use DefStudio\Telegraph\Models\TelegraphChat;

class TelegramService
{
    public static function sendMessage(string $message): void
    {
        TelegraphChat::all()->each(fn(TelegraphChat $chat) => $chat->html($message)->send());
    }
}
