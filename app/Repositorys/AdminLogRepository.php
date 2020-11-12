<?php


namespace App\Repositorys;


use App\Models\AdminLog;
use App\Traits\PagingTrait;

class AdminLogRepository
{

    use PagingTrait;

    /**
     * 获取管理员日志列表
     *
     * @param int $page 分页数
     * @param int $page_size 分页大小
     * @param array $params 查询参数
     * @return array
     */
    public function findAllLogs(int $page, int $page_size, array $params = [])
    {
        $rowObj = AdminLog::select(['id', 'admin_id', 'admin_name', 'route', 'param', 'created_at', 'ip', 'useragent']);

        $orderBy = 'id';
        $sort = 'desc';
        if (isset($params['order_by'], $params['sort'])) {
            $orderBy = $params['order_by'];
            $sort = $params['sort'];
        }

        if (isset($params['admin_id'])) {
            $rowObj->where('admin_id', $params['admin_id']);
        }


        $total = $rowObj->count();
        $rows = $rowObj->orderBy($orderBy, $sort)->forPage($page, $page_size)->get()->toArray();
        return $this->getPagingRows($rows, $total, $page, $page_size);
    }

}
