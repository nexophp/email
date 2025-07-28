<?php
/**
 * 邮件模板
 * @author sunkangchina <68103403@qq.com>
 * @license MIT <https://mit-license.org/>
 * @date 2025
 */
namespace modules\email\controller;

class TemplateController extends \core\AdminController
{
    /**
     * 模板列表
     * @permission 邮件模板.管理 邮件模板.查看
     */
    public function actionList()
    {
        $list = db_get_all('email_template', '*', [], 'id DESC');
        json_success(['data' => $list]);
    }
    /**
     * 添加模板
     * @permission 邮件模板.管理 邮件模板.添加
     */
    public function actionAdd()
    {
        $data = $this->post_data;
        // 检查 code 是否唯一
        if (db_get_one('email_template', 'id', ['code' => $data['code']])) {
            json_error(['msg' => lang('模板代码已存在')]);
        }
        $data['created_at'] = time();
        $id = db_insert('email_template', $data);
        if ($id) {
            json_success(['msg' => lang('添加成功')]);
        }
        json_error(['msg' => lang('添加失败')]);
    }
    /**
     * 更新模板
     * @permission 邮件模板.管理 邮件模板.更新
     */
    public function actionUpdate()
    {
        $data = $this->post_data;
        $id = $data['id'];
        // 检查 code 是否唯一（排除自身）
        $existing = db_get_one('email_template', 'id', ['code' => $data['code'], 'id[!]' => $id]);
        if ($existing) {
            json_error(['msg' => lang('模板代码已存在')]);
        }
        unset($data['id']);
        $res = db_update('email_template', $data, ['id' => $id]);
        if ($res) {
            json_success(['msg' => lang('更新成功')]);
        }
        json_error(['msg' => lang('更新失败')]);
    }
    /**
     * 删除模板
     * @permission 邮件模板.管理 邮件模板.删除
     */
    public function actionDelete()
    {
        $id = $this->post_data['id'];
        $res = db_delete('email_template', ['id' => $id]);
        if ($res) {
            json_success(['msg' => lang('删除成功')]);
        }
        json_error(['msg' => lang('删除失败')]);
    }
}