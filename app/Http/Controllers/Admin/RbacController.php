<?php

namespace App\Http\Controllers\Admin;

use App\Models\Rbac\AdminPermission;
use App\Models\Rbac\Role;
use App\Models\Rbac\RoleAdmin;
use App\Traits\PagingTrait;
use Illuminate\Http\Request;

/**
 * Class RbacController 权限管理
 *
 * @package App\Http\Controllers\Admin
 */
class RbacController extends CController
{
    use PagingTrait;

    public function __construct()
    {
        $this->middleware('rbac', ['except' => [
            'getRolePerms',
            'getAdminPerms'
        ]]);
    }

    /**
     * @api {POST} /admin/rbac/create-role 添加角色信息
     * @apiGroup RBAC
     * @apiVersion 1.0.0
     *
     * @apiParam {String} name 名称
     * @apiParam {String} display_name 展示标题
     * @apiParam {String} description 描述
     *
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"\u89d2\u8272\u6dfb\u52a0\u6210\u529f...","data":[]}
     */
    public function createRole(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'display_name' => 'required',
            'description' => 'required',
        ]);

        $result = services()->rbacService->createRole($request);
        if (!$result) {
            return $this->fail('角色添加失败...');
        }

        return $this->success([], '角色添加成功...');
    }

    /**
     * @api {POST} /admin/rbac/edit-role 修改角色信息
     * @apiGroup RBAC
     * @apiVersion 1.0.0
     *
     * @apiParam {Int} role_id 角色ID
     * @apiParam {String} name 名称
     * @apiParam {String} display_name 展示标题
     * @apiParam {String} description 描述
     *
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"\u89d2\u8272\u4fe1\u606f\u4fee\u6539\u6210\u529f...","data":[]}
     */
    public function editRole(Request $request)
    {
        $this->validate($request, [
            'role_id' => 'required|integer:min:1',
            'name' => 'required',
            'display_name' => 'required',
            'description' => 'required',
        ]);

        $result = services()->rbacService->editRole($request);
        if (!$result) {
            return $this->fail('角色信息修改失败...');
        }

        return $this->success([], '角色信息修改成功...');
    }

    /**
     * @api {POST} /admin/rbac/delete-role 删除角色
     * @apiGroup RBAC
     * @apiVersion 1.0.0
     *
     * @apiParam {Int} role_id 角色ID
     *
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"\u89d2\u8272\u4fe1\u606f\u5220\u9664\u6210\u529f...","data":[]}
     */
    public function deleteRole(Request $request)
    {
        $this->validate($request, ['role_id' => 'required|integer|min:1']);

        $result = services()->rbacService->deleteRole($request->input('role_id'));
        if (!$result) {
            return $this->fail('角色信息删除失败...');
        }

        return $this->success([], '角色信息删除成功...');
    }

    /**
     * 创建权限
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    /**
     * @api {POST} /admin/rbac/create-permission 创建权限
     * @apiGroup RBAC
     * @apiVersion 1.0.0
     *
     * @apiParam {Int} type 权限类型[0:目录;1:菜单;2:权限]
     * @apiParam {Int} parent_id 上级ID
     * @apiParam {String} title 标题
     * @apiParam {String} path 权限路由地址
     * @apiParam {String} component 前端页面组件名称
     * @apiParam {String} perms 权限标识
     * @apiParam {String} icon 菜单图标
     * @apiParam {Int} sort 排序[值越小越靠前 0-9999]
     * @apiParam {Int} hidden 是否隐藏 0，1
     * @apiParam {Int} is_frame 是否外链[0:否;1:是]
     *
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"\u6743\u9650\u6dfb\u52a0\u6210\u529f...","data":[]}
     */
    public function createPermission(Request $request)
    {
        $this->validate($request, [
            'type' => 'required|in:0,1,2',
            'parent_id' => 'required|integer|min:0',
            'title' => 'required',
            'path' => 'present',
            'component' => 'present',
            'perms' => 'present',
            'icon' => 'present',
            'sort' => 'present|integer|min:0|max:9999',
            'hidden' => 'required|in:0,1',
            'is_frame' => 'required|in:0,1',
        ]);

        $result = services()->rbacService->createPermission($request);
        if (!$result) {
            return $this->fail('权限添加失败...');
        }

        return $this->success([], '权限添加成功...');
    }

    /**
     * @api {POST} /admin/rbac/edit-permission 修改权限信息
     * @apiGroup RBAC
     * @apiVersion 1.0.0
     *
     * @apiParam {Int} id 权限ID
     * @apiParam {Int} type 权限类型[0:目录;1:菜单;2:权限]
     * @apiParam {Int} parent_id 上级ID
     * @apiParam {String} title 标题
     * @apiParam {String} path 权限路由地址
     * @apiParam {String} component 前端页面组件名称
     * @apiParam {String} perms 权限标识
     * @apiParam {String} icon 菜单图标
     * @apiParam {Int} sort 排序[值越小越靠前 0-9999]
     * @apiParam {Int} hidden 是否隐藏 0，1
     * @apiParam {Int} is_frame 是否外链[0:否;1:是]
     *
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"\u6743\u9650\u4fee\u6539\u6210\u529f...","data":[]}
     */
    public function editPermission(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer|min:1',
            'type' => 'required|in:0,1,2',
            'parent_id' => 'required|integer|min:0',
            'title' => 'required',
            'path' => 'present',
            'component' => 'present',
            'perms' => 'present',
            'icon' => 'present',
            'sort' => 'present|integer|min:0|max:9999',
            'hidden' => 'required|in:0,1',
            'is_frame' => 'required|in:0,1',
        ]);

        $result = services()->rbacService->editPermission($request);
        if (!$result) {
            return $this->fail('权限修改失败...');
        }

        return $this->success([], '权限修改成功...');
    }

    /**
     * @api {POST} /admin/rbac/delete-permission 删除权限信息
     * @apiGroup RBAC
     * @apiVersion 1.0.0
     *
     * @apiParam {Int} id 权限ID
     *
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"\u6743\u9650\u5220\u9664\u6210\u529f...","data":[]}
     */
    public function deletePermission(Request $request)
    {
        $this->validate($request, ['id' => 'required|integer|min:1',]);

        $result = services()->rbacService->deletePermission($request->input('id'));
        if (!$result) {
            return $this->fail('权限删除失败...');
        }

        return $this->success([], '权限删除成功...');
    }

    /**
     * @api {POST} /admin/rbac/give-role-permission 分配角色权限
     * @apiGroup RBAC
     * @apiVersion 1.0.0
     *
     * @apiParam {Int} role_id 角色ID
     * @apiParam {Int} permissions 权限ID 1,2,3,4
     *
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"\u89d2\u8272\u6743\u9650\u5206\u914d\u6210\u529f...","data":[]}
     */
    public function giveRolePermission(Request $request)
    {
        $this->validate($request, [
            'role_id' => 'required|integer:min:1',
            'permissions' => 'present'
        ]);

        $permissions = $request->input('permissions', '');
        $permissions = explode(',', $permissions);
        $permissions = array_unique(array_filter($permissions));

        $result = services()->rbacService->giveRolePermission($request->input('role_id'), $permissions);
        if (!$result) {
            return $this->fail('角色权限分配失败...');
        }

        return $this->success([], '角色权限分配成功...');
    }

    /**
     * @api {POST} /admin/rbac/give-admin-permission 分配管理角色及权限
     * @apiGroup RBAC
     * @apiVersion 1.0.0
     *
     * @apiParam {Int} admin_id 管理员ID
     * @apiParam {Int} role_id 角色ID
     * @apiParam {Int} permissions 权限ID 1,2,3,4
     *
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"\u7ba1\u7406\u5458\u6743\u9650\u5206\u914d\u6210\u529f...","data":[]}
     */
    public function giveAdminPermission(Request $request)
    {
        $this->validate($request, [
            'admin_id' => 'required|integer:min:1',
            'role_id' => 'present|integer:min:0',
            'permissions' => 'present',
        ]);

        $permissions = $request->input('permissions', '');
        $permissions = explode(',', $permissions);

        $result = services()->rbacService->giveAdminRole(
            $request->input('admin_id'),
            $request->input('role_id'),
            array_unique(array_filter($permissions))
        );

        if (!$result) {
            return $this->fail('管理员权限分配失败...');
        }

        return $this->success([], '管理员权限分配成功...');
    }

    /**
     * @api {GET} /admin/rbac/roles 获取角色列表
     * @apiGroup RBAC
     * @apiVersion 1.0.0
     *
     * @apiParam {Int} page 分页
     * @apiParam {Int} page_size 每页数量
     *
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"OK","data":{"rows":[{"id":2,"name":"111","display_name":"asasa","description":"asas","created_at":"2020-11-06 15:16:59","updated_at":"2020-11-06 15:16:59"}],"page":1,"page_size":20,"page_total":1,"total":1}}
     */
    public function roles(Request $request)
    {
        $this->validate($request, [
            'page' => 'required|integer:min:1',
            'page_size' => 'required|in:10,20,30,50,100',
        ]);

        $result = services()->rbacService->roles($request);
        return $this->success($result);
    }

    /**
     * @api {GET} /admin/rbac/roles 获取权限列表
     * @apiGroup RBAC
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"OK","data":{"rows":[{"id":3,"parent_id":2,"type":1,"path":"\/system\/users","perms":"","sort":0,"icon":"","title":"\u7528\u6237\u7ba1\u7406","hidden":0,"is_frame":0,"component":"SystemUserPage"},{"id":6,"parent_id":3,"type":2,"path":"","perms":"system:user:search","sort":0,"icon":"","title":"\u7528\u6237\u6570\u636e","hidden":0,"is_frame":0,"component":""},{"id":9,"parent_id":4,"type":2,"path":"","perms":"system:role:search","sort":0,"icon":"","title":"\u89d2\u8272\u6570\u636e","hidden":0,"is_frame":0,"component":""},{"id":37,"parent_id":4,"type":2,"path":"","perms":"system:role:insert","sort":0,"icon":"","title":"\u89d2\u8272\u65b0\u589e","hidden":0,"is_frame":0,"component":""},{"id":38,"parent_id":4,"type":2,"path":"","perms":"system:role:edit","sort":0,"icon":"","title":"\u89d2\u8272\u7f16\u8f91","hidden":0,"is_frame":0,"component":""},{"id":39,"parent_id":4,"type":2,"path":"","perms":"system:role:delete","sort":0,"icon":"","title":"\u89d2\u8272\u5220\u9664","hidden":0,"is_frame":0,"component":""},{"id":93,"parent_id":4,"type":2,"path":"","perms":"system:role:give-perms","sort":0,"icon":"","title":"\u89d2\u8272\u5206\u914d\u6743\u9650","hidden":0,"is_frame":0,"component":""},{"id":104,"parent_id":5,"type":2,"path":"","perms":"system:menu:search","sort":0,"icon":"","title":"\u83dc\u5355\u6570\u636e","hidden":0,"is_frame":0,"component":""},{"id":108,"parent_id":0,"type":1,"path":"\/other\/list","perms":"","sort":0,"icon":"dot-chart","title":"\u81ea\u5b9a\u4e49\u7ec4\u4ef6","hidden":0,"is_frame":0,"component":"component"},{"id":2,"parent_id":0,"type":0,"path":"\/system","perms":"","sort":1,"icon":"solution","title":"\u7cfb\u7edf\u7ba1\u7406","hidden":0,"is_frame":0,"component":""},{"id":7,"parent_id":3,"type":2,"path":"","perms":"system:user:insert","sort":1,"icon":"","title":"\u7528\u6237\u65b0\u589e","hidden":0,"is_frame":0,"component":""},{"id":40,"parent_id":5,"type":2,"path":"","perms":"system:menu:insert","sort":1,"icon":"","title":"\u83dc\u5355\u65b0\u589e","hidden":0,"is_frame":0,"component":""},{"id":102,"parent_id":5,"type":2,"path":"","perms":"system:menu:edit","sort":2,"icon":"","title":"\u83dc\u5355\u7f16\u8f91","hidden":0,"is_frame":0,"component":""},{"id":122,"parent_id":3,"type":2,"path":"","perms":"system:user:edit","sort":2,"icon":"","title":"\u7528\u6237\u7f16\u8f91","hidden":0,"is_frame":0,"component":""},{"id":4,"parent_id":2,"type":1,"path":"\/system\/roles","perms":"","sort":3,"icon":"","title":"\u89d2\u8272\u7ba1\u7406","hidden":0,"is_frame":0,"component":"SystemRolePage"},{"id":5,"parent_id":2,"type":1,"path":"\/system\/menus","perms":"system:menu:page","sort":3,"icon":"","title":"\u83dc\u5355\u7ba1\u7406","hidden":0,"is_frame":0,"component":"SystemMenuPage"},{"id":8,"parent_id":3,"type":2,"path":"","perms":"system:user:change-status","sort":3,"icon":"","title":"\u4fee\u6539\u72b6\u6001","hidden":0,"is_frame":0,"component":""},{"id":96,"parent_id":3,"type":2,"path":"","perms":"system:user:delete","sort":3,"icon":"","title":"\u7528\u6237\u5220\u9664","hidden":0,"is_frame":0,"component":""},{"id":103,"parent_id":5,"type":2,"path":"","perms":"system:menu:delete","sort":3,"icon":"","title":"\u83dc\u5355\u5220\u9664","hidden":0,"is_frame":0,"component":""},{"id":36,"parent_id":3,"type":2,"path":"","perms":"system:user:change-password","sort":4,"icon":"","title":"\u4fee\u6539\u5bc6\u7801","hidden":0,"is_frame":0,"component":""},{"id":121,"parent_id":3,"type":2,"path":"","perms":"system:user:give-role","sort":5,"icon":"","title":"\u5206\u914d\u89d2\u8272","hidden":0,"is_frame":0,"component":""}],"page":1,"page_size":10000,"page_total":1,"total":21}}
     */
    public function permissions()
    {
        $rows = services()->rbacService->getRepository()->findAllPerms(['id', 'parent_id', 'type', 'path', 'perms', 'sort', 'icon', 'title', 'hidden', 'is_frame', 'component']);

        $result = $this->getPagingRows($rows, count($rows), 1, 10000);
        return $this->success($result);
    }

    /**
     * @api {GET} /admin/rbac/get-role-permission 获取角色权限列表
     * @apiGroup RBAC
     * @apiVersion 1.0.0
     * @apiParam {Int} role_id 角色ID
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"OK","data":{"permissions":[{"id":108,"parent_id":0,"title":"\u81ea\u5b9a\u4e49\u7ec4\u4ef6","children":[]},{"id":2,"parent_id":0,"title":"\u7cfb\u7edf\u7ba1\u7406","children":[{"id":3,"parent_id":2,"title":"\u7528\u6237\u7ba1\u7406","children":[{"id":6,"parent_id":3,"title":"\u7528\u6237\u6570\u636e","children":[]},{"id":7,"parent_id":3,"title":"\u7528\u6237\u65b0\u589e","children":[]},{"id":122,"parent_id":3,"title":"\u7528\u6237\u7f16\u8f91","children":[]},{"id":8,"parent_id":3,"title":"\u4fee\u6539\u72b6\u6001","children":[]},{"id":96,"parent_id":3,"title":"\u7528\u6237\u5220\u9664","children":[]},{"id":36,"parent_id":3,"title":"\u4fee\u6539\u5bc6\u7801","children":[]},{"id":121,"parent_id":3,"title":"\u5206\u914d\u89d2\u8272","children":[]}]},{"id":4,"parent_id":2,"title":"\u89d2\u8272\u7ba1\u7406","children":[{"id":9,"parent_id":4,"title":"\u89d2\u8272\u6570\u636e","children":[]},{"id":37,"parent_id":4,"title":"\u89d2\u8272\u65b0\u589e","children":[]},{"id":38,"parent_id":4,"title":"\u89d2\u8272\u7f16\u8f91","children":[]},{"id":39,"parent_id":4,"title":"\u89d2\u8272\u5220\u9664","children":[]},{"id":93,"parent_id":4,"title":"\u89d2\u8272\u5206\u914d\u6743\u9650","children":[]}]},{"id":5,"parent_id":2,"title":"\u83dc\u5355\u7ba1\u7406","children":[{"id":104,"parent_id":5,"title":"\u83dc\u5355\u6570\u636e","children":[]},{"id":40,"parent_id":5,"title":"\u83dc\u5355\u65b0\u589e","children":[]},{"id":102,"parent_id":5,"title":"\u83dc\u5355\u7f16\u8f91","children":[]},{"id":103,"parent_id":5,"title":"\u83dc\u5355\u5220\u9664","children":[]}]}]}],"role_perms":[2,3,4]}}
     */
    public function getRolePerms()
    {
        $this->validate(request(), [
            'role_id' => 'required|integer:min:1'
        ]);

        // 权限 Tree
        $perms = services()->rbacService->getPermsTree();
        $rolePerms = services()->rbacService->getRepository()->findRolePermsIds(request()->input('role_id'));

        return $this->success([
            'permissions' => $perms,
            'role_perms' => $rolePerms
        ]);
    }

    /**
     * @api {GET} /admin/rbac/get-admin-permission 获取管理员相关权限
     * @apiGroup RBAC
     * @apiVersion 1.0.0
     * @apiParam {Int} admin_id 管理员ID
     * @apiSuccessExample  {json} Response-Example
     * {"code":200,"message":"success","data":{"roles":[{"id":2,"display_name":"asasa"}],"perms":[{"id":108,"parent_id":0,"title":"\u81ea\u5b9a\u4e49\u7ec4\u4ef6","children":[]},{"id":2,"parent_id":0,"title":"\u7cfb\u7edf\u7ba1\u7406","children":[{"id":3,"parent_id":2,"title":"\u7528\u6237\u7ba1\u7406","children":[{"id":6,"parent_id":3,"title":"\u7528\u6237\u6570\u636e","children":[]},{"id":7,"parent_id":3,"title":"\u7528\u6237\u65b0\u589e","children":[]},{"id":122,"parent_id":3,"title":"\u7528\u6237\u7f16\u8f91","children":[]},{"id":8,"parent_id":3,"title":"\u4fee\u6539\u72b6\u6001","children":[]},{"id":96,"parent_id":3,"title":"\u7528\u6237\u5220\u9664","children":[]},{"id":36,"parent_id":3,"title":"\u4fee\u6539\u5bc6\u7801","children":[]},{"id":121,"parent_id":3,"title":"\u5206\u914d\u89d2\u8272","children":[]}]},{"id":4,"parent_id":2,"title":"\u89d2\u8272\u7ba1\u7406","children":[{"id":9,"parent_id":4,"title":"\u89d2\u8272\u6570\u636e","children":[]},{"id":37,"parent_id":4,"title":"\u89d2\u8272\u65b0\u589e","children":[]},{"id":38,"parent_id":4,"title":"\u89d2\u8272\u7f16\u8f91","children":[]},{"id":39,"parent_id":4,"title":"\u89d2\u8272\u5220\u9664","children":[]},{"id":93,"parent_id":4,"title":"\u89d2\u8272\u5206\u914d\u6743\u9650","children":[]}]},{"id":5,"parent_id":2,"title":"\u83dc\u5355\u7ba1\u7406","children":[{"id":104,"parent_id":5,"title":"\u83dc\u5355\u6570\u636e","children":[]},{"id":40,"parent_id":5,"title":"\u83dc\u5355\u65b0\u589e","children":[]},{"id":102,"parent_id":5,"title":"\u83dc\u5355\u7f16\u8f91","children":[]},{"id":103,"parent_id":5,"title":"\u83dc\u5355\u5220\u9664","children":[]}]}]}],"admin_perms":[],"role_id":0}}
     */
    public function getAdminPerms()
    {
        $this->validate(request(), [
            'admin_id' => 'required|integer:min:1'
        ]);

        $admin_id = request()->input('admin_id');
        // 权限 Tree
        $perms = services()->rbacService->getPermsTree();
        // 角色列表
        $roles = Role::get(['id', 'display_name'])->toarray();
        // 管理员已赋予的权限
        $adminPerms = AdminPermission::where('admin_id', $admin_id)->pluck('permission_id')->toArray();

        return $this->success([
            'roles' => $roles,
            'perms' => getPermsTree($perms),
            'admin_perms' => $adminPerms,
            'role_id' => RoleAdmin::where('admin_id', $admin_id)->value('role_id') ?? 0
        ], 'success');
    }
}
