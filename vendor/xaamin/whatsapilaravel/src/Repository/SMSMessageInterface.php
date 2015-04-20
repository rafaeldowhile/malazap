<?php
namespace Xaamin\WhatsapiLaravel\Repository;

interface SMSMessageInterface {
    public function sendMessage($to, $message);
    public function checkForNewMessages();
    public function sendMessageImage($to, $image);
}
