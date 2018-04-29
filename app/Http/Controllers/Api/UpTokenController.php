<?php
namespace App\Http\Controllers\Api;

use Qiniu\Auth;

class UpTokenController extends ApiController
{
    public function show()
    {
        $accessKey = config('qiniu.accessKey');
        $secretKey = config('qiniu.secretKey');
        $auth = new Auth($accessKey, $secretKey);

        $bucket = config('qiniu.bucket');
        $token = $auth->uploadToken($bucket);

        return $this->response->json(["uptoken" => $token]);
    }
}