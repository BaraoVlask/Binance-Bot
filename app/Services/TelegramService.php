<?php

namespace App\Services;

use DefStudio\Telegraph\Models\TelegraphChat;

class TelegramService
{
    public static function sendMessage(string $message): void
    {
        /** @var TelegraphChat[] $chats */
        $chats = TelegraphChat::all();

        foreach ($chats as $chat) {
            $chat->html($message)->send();
        }
    }
}
