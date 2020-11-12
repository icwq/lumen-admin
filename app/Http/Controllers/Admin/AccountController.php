<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

/**
 * 管理员个人账户控制器
 *
 * Class AccountController
 * @package App\Http\Controllers\Admin
 */
class AccountController extends CController
{

    /**
     * @api {GET} /admin/account/detail 获取当前用户信息
     * @apiGroup Account
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"OK","data":{"username":"admin","nickname":"","email":"","avatar":"","profile":""}}
     */
    public function detail()
    {
        $adminInfo = $this->user();

        return $this->success([
            'username' => $adminInfo->username,
            'nickname' => $adminInfo->nickname,
            'email' => $adminInfo->email,
            'avatar' => $adminInfo->avatar,
            'profile' => ''
        ]);
    }

    /**
     * @api {POST} /admin/account/update-password 修改当前登录账号密码
     * @apiGroup Account
     * @apiVersion 1.0.0
     *
     * @apiParam {String} old_password 旧密码
     * @apiParam {String} password 密码
     * @apiParam {String} password2 确认密码
     *
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"\u5f53\u524d\u767b\u5f55\u8d26\u53f7\u5bc6\u7801\u5df2\u4fee\u6539...","data":[]}
     */
    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required',
            'password2' => 'required|same:password',
        ]);

        $admin = $this->user();

        if (!check_password($request->input('old_password'), $admin->password)) {
            return $this->fail('旧密填写错误...');
        }

        $admin->password = $request->input('password');
        $admin->save();

        return $this->success([], '当前登录账号密码已修改...');
    }

    /**
     * @api {POST} /admin/account/update-account 修改当前账号相关信息
     * @apiGroup Account
     * @apiVersion 1.0.0
     *
     * @apiParam {String} email 邮箱
     * @apiParam {String} avatar 头像
     * @apiParam {String} nickname 昵称
     *
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"\u7ba1\u7406\u5458\u4fe1\u606f\u4fee\u6539\u6210\u529f...","data":[]}
     */
    public function updateAccount(Request $request)
    {
        $this->validate($request, [
            'email' => 'present|email',
            'avatar' => 'present|url',
            'nickname' => 'present',
        ]);

        $admin = $this->user();
        $admin->email = $request->input('email', '');
        $admin->avatar = $request->input('avatar', '');
        $admin->nickname = $request->input('nickname', '');
        $admin->save();

        return $this->success([], '管理员信息修改成功...');
    }
}
