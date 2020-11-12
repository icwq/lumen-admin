<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;

class AdminsController extends CController
{

    /**
     * @api {POST} /admin/admins/create 添加管理员账号
     * @apiGroup Admins
     * @apiVersion 1.0.0
     *
     * @apiParam {String} username 用户名
     * @apiParam {String} password 密码
     * @apiParam {String} password2 确认密码
     *
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"\u7ba1\u7406\u5458\u8d26\u53f7\u6dfb\u52a0\u6210\u529f...","data":[]}
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
            'password2' => 'required',
        ]);

        $result = services()->adminService->create($request);
        if (!$result) {
            return $this->fail('管理员账号添加失败...');
        }

        return $this->success([], '管理员账号添加成功...');
    }

    /**
     * @api {POST} /admin/admins/delete 删除管理员账号
     * @apiGroup Admins
     * @apiVersion 1.0.0
     *
     * @apiParam {String} admin_id 管理员ID
     *
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"\u7ba1\u7406\u5458\u8d26\u53f7\u5220\u9664\u6210\u529f...","data":[]}
     */
    public function delete(Request $request)
    {
        $this->validate($request, [
            'admin_id' => 'required|integer:min:1'
        ]);

        $admin_id = $request->input('admin_id');
        if ($this->user()->id == $admin_id || !services()->adminService->delete($admin_id)) {
            return $this->fail('管理员账号删除失败...');
        }

        return $this->success([], '管理员账号删除成功...');
    }

    /**
     * @api {POST} /admin/admins/update-password 修改指定管理员登录密码
     * @apiGroup Admins
     * @apiVersion 1.0.0
     *
     * @apiParam {String} id 管理员ID
     * @apiParam {String} password 密码
     * @apiParam {String} password2 确认密码
     *
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"\u7ba1\u7406\u5458\u5bc6\u7801\u5df2\u4fee\u6539...","data":[]}
     */
    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer:min:1',
            'password' => 'required',
            'password2' => 'required|same:password',
        ]);

        $result = services()->adminService->updatePassword(
            $request->input('id'),
            $request->input('password')
        );

        if (!$result) {
            return $this->fail('管理员密码修改失败...');
        }

        return $this->success([], '管理员密码已修改...');
    }


    /**
     * @api {POST} /admin/admins/update-status 修改管理员账户状态
     * @apiGroup Admins
     * @apiVersion 1.0.0
     *
     * @apiParam {String} admin_id 管理员ID
     * @apiParam {Int} status 状态0,1
     *
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"\u7ba1\u7406\u5458\u72b6\u6001\u4fee\u6539\u6210\u529f...","data":[]}
     */
    public function updateStatus(Request $request)
    {
        $this->validate($request, [
            'admin_id' => 'required|integer:min:1',
            'status' => 'required|in:0,1',
        ]);

        // 状态映射
        $arr = [
            '0' => Admin::STATUS_ENABLES,
            '1' => Admin::STATUS_DISABLES
        ];

        $result = services()->adminService->updateStatus(
            $request->input('admin_id'),
            $arr[$request->input('status')]
        );

        if (!$result) {
            return $this->fail('管理员状态修改失败');
        }

        return $this->success([], '管理员状态修改成功...');
    }

    /**
     * @api {GET} /admin/admins/lists 获取管理员列表
     * @apiGroup Admins
     * @apiVersion 1.0.0
     *
     * @apiParam {Int} page 页码,最小为1
     * @apiParam {Int} page_size 每页数量(10,20,30,50,100)
     * @apiParam {Int} status 状态0,1,2
     *
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"OK","data":{"rows":[{"id":1,"username":"admin","nickname":"","email":"","avatar":"","status":10,"last_login_time":"2020-11-06 14:17:11","last_login_ip":"127.0.0.1","created_at":"2020-10-30 18:01:47","updated_at":"2020-10-30 18:01:47"}],"page":1,"page_size":10,"page_total":1,"total":1}}
     */
    public function lists(Request $request)
    {
        $this->validate($request, [
            'page' => 'required|integer:min:1',
            'page_size' => 'required|in:10,20,30,50,100',
            'status' => 'in:0,1,2',
        ]);

        $result = services()->adminService->getAdmins($request);
        return $this->success($result);
    }
}
