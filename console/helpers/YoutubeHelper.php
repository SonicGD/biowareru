<?php
/**
 * Created by PhpStorm.
 * User: sonic
 * Date: 14-Jun-15
 * Time: 14:18
 */

namespace biowareru\console\helpers;


use bioengine\common\modules\files\models\File;
use Google_Client;
use Google_Http_MediaFileUpload;
use Google_Service_YouTube;
use Google_Service_YouTube_Video;
use Google_Service_YouTube_VideoSnippet;
use Google_Service_YouTube_VideoStatus;

class YoutubeHelper
{
    public static function upload(File $file)
    {
        $client = self::getClient();
        if ($client) {

            $snippet = new Google_Service_YouTube_VideoSnippet();
            $snippet->setTitle($file->title);
            $snippet->setDescription($file->desc);
            $status = new Google_Service_YouTube_VideoStatus();
            $status->privacyStatus = 'public';
            $snippet->setCategoryId('20');

            $video = new Google_Service_YouTube_Video();
            $video->setSnippet($snippet);
            $video->setStatus($status);

            $chunkSizeBytes = 1 * 1024 * 1024;

            // Setting the defer flag to true tells the client to return a request which can be called
            // with ->execute(); instead of making the API call immediately.
            $client->setDefer(true);

            $youtube = new Google_Service_YouTube($client);

            // Create a request for the API's videos.insert method to create and upload the video.
            $insertRequest = $youtube->videos->insert('status,snippet', $video);
            $media = new Google_Http_MediaFileUpload(
                $client,
                $insertRequest,
                'video/*',
                null,
                true,
                $chunkSizeBytes
            );
            $media->setFileSize(filesize($file->size));

            $status = false;
            $handle = fopen($videoPath, 'rb');
            while (!$status && !feof($handle)) {
                $chunk = fread($handle, $chunkSizeBytes);
                $status = $media->nextChunk($chunk);
            }

            fclose($handle);

            $client->setDefer(false);

            var_dump($status['snippet']['title']);
            var_dump($status['id']);
        }
    }

    /**
     * @var Google_Client
     */
    private static $_client;

    /**
     * @return Google_Client
     */
    private static function getClient()
    {
        if (!self::$_client) {
            $OAUTH2_CLIENT_ID = '580173741933-a4qbnr6c6ic7k05050vqreobh0fp52t3.apps.googleusercontent.com';
            $OAUTH2_CLIENT_SECRET = 'ROnux265Uk7vBIidKxQMI43e';
            //$refreshToken = '1\/V-9z8MapSHmYsl2kNBn_1uGlbrYtGeNlpsV_YNDbHyJIgOrJDtdun6zK6XiATCKT';
            //$accessToken = 'ya29.kgFVYW70_3a4UU1Vh-BLQPKxlyktKUy4Q21XxzCPZgwjEqgxEWcB0Ey-OXB1itfCfcy9Gz_znhwRzw';

            $token = '{"access_token":"ya29.kgFVYW70_3a4UU1Vh-BLQPKxlyktKUy4Q21XxzCPZgwjEqgxEWcB0Ey-OXB1itfCfcy9Gz_znhwRzw","token_type":"Bearer","expires_in":3600,"refresh_token":"1\/V-9z8MapSHmYsl2kNBn_1uGlbrYtGeNlpsV_YNDbHyJIgOrJDtdun6zK6XiATCKT","created":1434275779}';

            $client = new Google_Client();
            $client->setClientId($OAUTH2_CLIENT_ID);
            $client->setClientSecret($OAUTH2_CLIENT_SECRET);
            $client->setScopes('https://www.googleapis.com/auth/youtube');
            $client->setAccessType('offline');
            $client->setApprovalPrompt('force');
            $redirect = 'http://bw.localhost.ru/site/oauth.html';
            $client->setRedirectUri($redirect);
            $client->setAccessToken($token);
            // Define an object that will be used to make all API requests.

            if ($client->getAccessToken()) {
                self::$_client = $client;
            }


        }
        return self::$_client;

    }

    public static function test()
    {

        $channelsResponse = self::getClient()->channels->listChannels('contentDetails', [
            'mine' => 'true'
        ]);
        var_dump($channelsResponse);
    }
}