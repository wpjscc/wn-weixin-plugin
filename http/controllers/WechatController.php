<?php


namespace Wpjscc\Weixin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as ControllerBase;
use Wpjscc\Weixin\Models\WechatUser as Mini;
use Wpjscc\Api\Models\User;
use System\Models\File;


class WechatController extends ControllerBase
{
    public function redirect(Request $request)
    {
        \Log::info('redirect:query', $request->query());
        $query = $request->query();
        $cacheKey = str_random(10);
        \Cache::put('wechat:cache:'.$cacheKey, $query, 60);
        $redirect = app('wechat_oauth')->getOauthRedirect(sprintf(route('wechat.callback').'?%s', http_build_query([
            'cache_key' => $cacheKey
        ])), '', 'snsapi_userinfo');
        \Log::info('redirect:query', [
            $redirect
        ]);
        return redirect($redirect);
    }

    public function callback(Request $request)
    {

        $accessToken = app('wechat_oauth')->getOauthAccessToken($request->code);

        $data = app('wechat_oauth')->getUserInfo($accessToken['access_token'], $accessToken['openid'], $lang = 'zh_CN');
        $openid = $data['openid'];
        $mini = Mini::saveWechatUser($openid, $data['nickname'], $data['headimgurl'], $data['sex'] ?? 0);
        if (!$mini->user) {
            $password = str_random(8);
            $user = new User;
            $user->name = $data['nickname'];
            $user->username = 'wechat_'.$openid;
            $user->email = 'wechat_'.$openid.'@qq.com';
            $user->password = $password;
            $user->forceSave();
            $mini->user_id = $user->id;
        } else {
            $user = $mini->user;
        }

        if (!$user->is_activated) {
            $user->is_activated = true;
            $user->forceSave();
        }
        $mini->save();

        $file = new File();
        $file->fromUrl($data['headimgurl']);
        $mini->avatar = $file;
        $mini->forceSave();
        

        $query = $request->query();
        if (isset($query['cache_key'])) {
            $cacheKey = $query['cache_key'];
            $requestQuery = \Cache::pull('wechat:cache:'.$cacheKey);
            if ($requestQuery && is_array($requestQuery)) {
                unset($query['cache_key']);
                unset($requestQuery['code']);
                unset($requestQuery['token']);
                unset($requestQuery['state']);
                $query = array_merge($query, $requestQuery);
            }
        }
        \Log::info('callback:query', $query);
        unset($query['token']);
        unset($query['code']);
        unset($query['state']);
        $redirect = $query['redirect'] ?? config('wechat.redirect');
        $redirect = config('wechat.redirect');
        unset($query['redirect']);
        $query['token'] = $user->createToken('wechat')->plainTextToken;
        return redirect(sprintf($redirect.'?%s', http_build_query($query)));

    }

    public function jsConfig()
    {
        $app = app('wechat');
        $js = $app->jssdk;
        \Log::info('url:'.request()->url);

        if (request()->url) {
            $js->setUrl(request()->url);
        }
        \Log::info('url:'.request()->url);


        $jsConfig = json_decode($js->buildConfig(array('chooseWXPay','updateAppMessageShareData', 'updateTimelineShareData','onMenuShareAppMessage','onMenuShareTimeline','onMenuShareAppMessage','getLocation','openLocation','scanQRCode'), false));
        return response()->json(['code'=>0,'msg'=>'','data'=>['js_config'=>$jsConfig]]);

    }
}
