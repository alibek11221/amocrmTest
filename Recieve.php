<?php

require __DIR__.'/vendor/autoload.php';

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\NotesCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\NoteType\CallInNote;
use AmoCRM\Models\NoteType\CallNote;
use League\OAuth2\Client\Token\AccessToken;
use Ramsey\Uuid\Nonstandard\Uuid;
use Twilio\TwiML\VoiceResponse;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
// Start our TwiML response
$response = new VoiceResponse;

// Read a message aloud to the caller
$response->say(
    "Thank you for calling! Have a great day.",
    ["voice" => "alice"]
);

echo $response;

$clientId = '';
$clientSecret = '';
$redirectUri = '';
$apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);
$accessToken = new AccessToken(
    [
        'access_token'  => $_ENV['ACCESS_TOKEN'],
        'refresh_token' => $_ENV['REFRESH_TOKEN'],
        'expires'       => $_ENV['EXPIRES'],
        'baseDomain'    => $_ENV['BASE_DOMAIN'],
    ]
);
$apiClient->setAccessToken($accessToken)->setAccountBaseDomain($accessToken->getValues()['baseDomain']);
$notesCollection = new NotesCollection();
$сallInNote = new CallInNote();
$сallInNote->setEntityId(276783)
    ->setPhone('+79287388602')
    ->setCallStatus(CallNote::CALL_STATUS_SUCCESS_CONVERSATION)
    ->setCallResult('Принял звонок')
    ->setDuration(148)
    ->setUniq(Uuid::uuid4())
    ->setLink('https://example.test/test.mp3')
    ->setSource('TWILIO');

$notesCollection->add($сallInNote);
//
try {
    $leadNotesService = $apiClient->notes(EntityTypesInterface::LEADS);
    $notesCollection = $leadNotesService->add($notesCollection);
} catch (AmoCRMApiException $e) {
    var_dump($e->getLastRequestInfo());
    die;
}
