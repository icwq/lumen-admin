<?php


namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;

class AdminLogsController extends CController
{

    /**
     * @api {GET} /admin/admins-logs/lists 获取管理员操作日志列表
     * @apiGroup Admins
     * @apiVersion 1.0.0
     *
     * @apiParam {Int} page 页码,最小为1
     * @apiParam {Int} page_size 每页数量(10,20,30,50,100)
     * @apiParam {Int} status 状态0,1,2
     *
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"OK","data":{"rows":[{"id":2,"admin_id":1,"route":"admin\/admins\/lists","param":"{\"page\":\"1\",\"page_size\":\"10\",\"status\":\"0\"}","created_at":"2020-11-09 16:01:23","updated_at":"2020-11-09 16:01:23"},{"id":1,"admin_id":1,"route":"admin\/admins\/lists","param":"{\"page\":\"1\",\"page_size\":\"10\",\"status\":\"0\"}","created_at":"2020-11-09 16:01:17","updated_at":"2020-11-09 16:01:17"}],"page":1,"page_size":10,"page_total":1,"total":2}}
     */
    public function lists(Request $request)
    {
        $this->validate($request, [
            'page' => 'required|integer:min:1',
            'page_size' => 'required|in:10,20,30,50,100'
        ]);

        $result = services()->adminLogService->getLogs($request);
        return $this->success($result);
    }
}
