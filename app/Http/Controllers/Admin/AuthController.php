<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\ResponseCode;
use App\Helpers\Tree;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers\Admin
 */
class AuthController extends CController
{

    /**
     * @api {POST} /admin/auth/login 登录接口
     * @apiGroup Auth
     * @apiVersion 1.0.0
     *
     * @apiParam {String} username 用户名
     * @apiParam {String} password 密码
     *
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"OK","data":{"auth":{"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbC55ZWh3YW5nZy5jb21cL2FkbWluXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYwNDY0MzQzMSwiZXhwIjoxNjA0NjQ3MDMxLCJuYmYiOjE2MDQ2NDM0MzEsImp0aSI6IkROOWRmNUJXVHFsVGhrNjIiLCJzdWIiOjEsInBydiI6ImRmODgzZGI5N2JkMDVlZjhmZjg1MDgyZDY4NmM0NWU4MzJlNTkzYTkifQ.WVOd9tS0zAdN2BmNQ3tOida_fpRtjMYSoWf5A2bgiAQ","token_type":"Bearer","expires_time":"2020-11-06 15:17:11"},"admin_info":{"username":"admin","email":"","avatar":""}}}
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        // 处理其它登录业务逻辑
        $admin = services()->adminService->login($request->only(['username', 'password']));

        // 通过用户信息换取用户token
        if (!$admin || !$token = auth($this->guard)->login($admin)) {
            return $this->fail('账号不存在或密码填写错误...', [], ResponseCode::AUTH_LOGON_FAIL);
        }

        // 更新登录信息
        $admin->last_login_time = date('Y-m-d H:i:s');
        $admin->last_login_ip = $request->getClientIp();
        $admin->save();

        return $this->success([
            'auth' => $this->formatToken($token),
            'admin_info' => [
                'username' => $admin->username,
                'email' => $admin->email,
                'avatar' => $admin->avatar,
            ]
        ]);
    }

    /**
     * @api {POST} /admin/auth/logout 退出登录接口
     * @apiGroup Auth
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"Successfully logged out","data":[]}
     */
    public function logout()
    {
        if ($this->isLogin()) {
            auth($this->guard)->logout();
        }

        return $this->success([], 'Successfully logged out');
    }

    /**
     * 刷新授权Token
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->success($this->formatToken(auth($this->guard)->refresh()));
    }

    /**
     * 格式话Token数据
     *
     * @param string $token 授权token
     * @return array
     */
    private function formatToken($token)
    {
        $ttl = auth($this->guard)->factory()->getTTL();
        $expires_time = time() + $ttl * 60;

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_time' => date('Y-m-d H:i:s', $expires_time)
        ];
    }

    /**
     * @api {GET} /admin/auth/menus 获取授权菜单配置
     * @apiGroup Auth
     * @apiVersion 1.0.0
     *
     * @apiPermission Authorization
     *
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"OK","data":{"menus":[{"name":"\/other\/list","path":"\/other\/list","component":"component","meta":{"icon":"dot-chart","title":"\u81ea\u5b9a\u4e49\u7ec4\u4ef6","keepAlive":false,"target":false},"hidden":false},{"name":"\/system","path":"\/system","component":"RouteView","meta":{"icon":"solution","title":"\u7cfb\u7edf\u7ba1\u7406","keepAlive":false,"target":false},"hidden":false,"children":[{"name":"\/system\/users","path":"\/system\/users","component":"SystemUserPage","meta":{"icon":null,"title":"\u7528\u6237\u7ba1\u7406","keepAlive":false,"target":false},"hidden":false},{"name":"\/system\/roles","path":"\/system\/roles","component":"SystemRolePage","meta":{"icon":null,"title":"\u89d2\u8272\u7ba1\u7406","keepAlive":false,"target":false},"hidden":false},{"name":"\/system\/menus","path":"\/system\/menus","component":"SystemMenuPage","meta":{"icon":null,"title":"\u83dc\u5355\u7ba1\u7406","keepAlive":false,"target":false},"hidden":false}]}],"perms":["system:user:search","system:user:insert","system:user:change-status","system:role:search","system:user:change-password","system:role:insert","system:role:edit","system:role:delete","system:menu:insert","system:role:give-perms","system:user:delete","system:menu:edit","system:menu:delete","system:menu:search","system:user:give-role","system:user:edit"]}}
     */
    public function menus(){
        $adminInfo = $this->user();
        $menus = services()->rbacService->getAuthMenus($adminInfo);
        $perms = services()->rbacService->getAuthPerms($adminInfo);

        $tree = new Tree();
        $tree->init([
            'array'=>$menus,
        ]);

        $menus = $tree->getTreeArray(0);
        return $this->success([
            'menus'=>getMenuTree($menus),
            'perms'=>$perms
        ]);
    }
}
