<?php

require __DIR__.'/vendor/autoload.php';

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\NotesCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\NoteType\CallNote;
use AmoCRM\Models\NoteType\CallOutNote;
use League\OAuth2\Client\Token\AccessToken;
use Ramsey\Uuid\Uuid;
use Twilio\Rest\Client;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$account_sid = $_ENV['ACCOUNT_SID'];
$auth_token = $_ENV['AUTH_TOKEN'];

$twilio_number = "+18176637255";

$to_number = "+79287388602";

$client = new Client($account_sid, $auth_token);

$client->account->calls->create(
    $to_number,
    $twilio_number,
    [
        "url" => "http://demo.twilio.com/docs/voice.xml",
    ]
);
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
$callOutNote = new CallOutNote();
$callOutNote->setEntityId(276783)
    ->setPhone('+79287388602')
    ->setCallStatus(CallNote::CALL_STATUS_SUCCESS_CONVERSATION)
    ->setCallResult('Позвонил')
    ->setDuration(148)
    ->setUniq(Uuid::uuid4())
    ->setLink('https://example.test/test.mp3')
    ->setSource('TWILIO');

$notesCollection->add($callOutNote);
//
try {
    $leadNotesService = $apiClient->notes(EntityTypesInterface::LEADS);
    $notesCollection = $leadNotesService->add($notesCollection);
} catch (AmoCRMApiException $e) {
    var_dump($e->getLastRequestInfo());
    die;
}

