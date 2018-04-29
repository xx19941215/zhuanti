<?php

namespace App\Http\Controllers\Api;

use App\Shop;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends ApiController
{
    const PASSPORT_API = 'https://passport.17zwd.com/login';
    const SHOP_INFO_API = 'https://api.17zwd.com/shop/%s/detail';


    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);

        $client = new Client();
        $res = $client->post(self::PASSPORT_API, [
            'form_params' => [
                'username' => $request->username,
                'password' => $request->password,
            ]
        ]);

        $res = json_decode($res->getBody()->getContents(), true);

        if ($res['msg'] == "success") {
            $userData = $res['data'];
            $existedUser = User::where(
                'id', '=', $userData['id']
            )
                ->first();

            if (!$existedUser) {
                $existedUser = new User([
                    'id' => $userData['id'],
                    'avt' => $userData['avatar'],
                    'vote_today' => 0,
                ]);


                array_map(function ($shop) use ($client, $userData) {
                    $shopRes = json_decode($client->get(sprintf(self::SHOP_INFO_API, $shop['shop_id']))->getBody()->getContents(), true);
                    if ($shopRes['msg'] == 'success') {

                        $shopInfo = $shopRes['data'];

                        $shop = new Shop([
                            'id' => $shopInfo['shop_id'],
                            'name' => $shopInfo['shop_name'],
                            'shop_mobile_url' => $shopInfo['shop_mobile_url'],
                            'shop_pc_url' => str_replace('.m', '',$shopInfo['shop_mobile_url']),
                            'user_id' => $userData['id']
                        ]);

                        $shop->save();

                    } else {
                        throw new \Exception('请求店铺信息异常');
                    }

                }, $userData['shops']);

            }

            Auth::login($existedUser, true);

            setcookie('17zwd_user_token', $userData['token'], time() + (60 * 60 * 6), '/', '17zwd.com');

            return $this->response->json([
                'status' => 'success',
                'user' => $existedUser,
                'csrf_token' => csrf_token()
            ]);

        }

        return $this->response->json([
            'status' => 'error',
            'msg' => $res['msg'],
            'code' => $res['code'],
        ]);
    }
}