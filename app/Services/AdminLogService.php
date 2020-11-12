<?php


namespace App\Services;


use App\Models\AdminLog;
use App\Repositorys\AdminLogRepository;
use App\Traits\PagingTrait;
use Illuminate\Http\Request;

class AdminLogService
{

    use PagingTrait;

    protected $adminLogRepository;

    public function __construct(AdminLogRepository $adminLogRepository)
    {
        $this->adminLogRepository = $adminLogRepository;
    }

    public function getLogs(Request $request)
    {
        $params = [];

        $orderBy = $request->only(['sortField', 'sortOrder']);
        if (isset($orderBy['sortField'], $orderBy['sortOrder'])) {
            $params['order_by'] = $orderBy['sortField'];
            $params['sort'] = get_orderby_sort($orderBy['sortOrder']);
        }

        if ($admin_id = $request->input('admin_id', 0)) {
            $params['admin_id'] = $admin_id;
        }

        return $this->adminLogRepository->findAllLogs(
            $request->input('page', 1),
            $request->input('page_size', 10),
            $params
        );
    }

    /**
     * 创建管理员账号
     *
     * @param Request $request
     * @return bool
     */
    public function create(Request $request)
    {

        try {
            $adminLog = new AdminLog();
            $adminLog->admin_id = auth('admin')->user()->id;
            $adminLog->admin_name = auth('admin')->user()->username;
            $adminLog->route = $request->path();
            $adminLog->param = json_encode($request->all());
            $adminLog->ip = $request->ip();
            $adminLog->useragent = $request->header('User-Agent');;
            $adminLog->created_at = date('Y-m-d H:i:s');

            return $adminLog->save();
        } catch (\Exception $e) {
            return false;
        }
    }

}
