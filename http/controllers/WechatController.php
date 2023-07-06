<?php


namespace WPjscc\Weixin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as ControllerBase;
use Wpjscc\Weixin\Models\WechatUser as Mini;
use Wpjscc\Api\Models\User;
use System\Models\File;


class WechatController extends ControllerBase
{
    public function redirect(Request $request)
    {
        $redirect = app('wechat_oauth')->getOauthRedirect(sprintf(route('wechat.callback').'?%s', http_build_query($request->query())), 'snsapi_userinfo');
        return redirect($redirect);
    }

    public function callback(Request $request)
    {
        try {
            $accessToken = app('wechat_oauth')->getOauthAccessToken($request->code);

            $data = app('wechat_oauth')->getUserInfo($accessToken['access_token'], $accessToken['openid'], $lang = 'zh_CN');
            $openid = $data['openid'];
            $mini = Mini::saveWechatUser($openid, $data['nickname'], $data['headimgurl']);
            if (!$mini->user_id) {
                $user = new User();
                $user->name = $data['nickname'];
                $user->username = 'wechat_'.$openid;
                $user->email = 'wechat_'.$openid.'@qq.com';
                $user->password = str_random(8);
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
            $user->avatar = $file;
            $user->forceSave();
            

            $query = $request->query();
            unset($query['token']);
            unset($query['code']);
            unset($query['state']);
            $redirect = $query['redirect'] ?? config('wechat.redirect');
            $redirect = config('wechat.redirect');
            unset($query['redirect']);
            $query['token'] = $user->createToken('wechat')->plainTextToken;
            return redirect(sprintf($redirect.'?%s', http_build_query($query)));

        } catch (\Throwable $th) {
            //throw $th;
            return $th->getMessage();
        }
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
