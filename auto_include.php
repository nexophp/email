<?php

/**
 * 发送邮件
 * @param string $template_code 模板代码
 * @param string $address 接收地址
 * @param array $replace 替换内容
 * @return bool
 */
function send_mail($template_code, $address, $replace = [])
{
    return \modules\email\lib\Mail::sendByTemplate($template_code, $address, $replace);
}

/**
 * 添加模板
 * @param string $code 模板代码
 * @param string $name 模板名称
 * @param string $title 模板标题
 * @param string $content 模板内容
 */
function add_mail_template($code, $name, $title, $content)
{
    $template = db_get_one('email_template', "*", [
        'code' => $code,
    ]);
    if ($template) {
        db_update('email_template', [
            'name' => $name,
            'code' => $code,
            'subject' => $title,
            'content' => $content,
        ], [
            'id' => $template['id'],
        ]);
    } else {
        db_insert('email_template', [
            'name' => $name,
            'code' => $code,
            'subject' => $title,
            'content' => $content,
        ]);
    }
}


add_action('admin.setting.form', function () {
?>
    <div class="mb-4">
        <h6 class="fw-bold mb-3 border-bottom pb-2">
            <i class="bi bi-envelope me-2"></i><?= lang('邮件服务') ?>
            <button type="button" class="btn btn-sm btn-primary ms-2" @click="showTestDrawer = true">
                <i class="bi bi-check-circle me-1"></i> <?= lang('测试服务') ?>
            </button>
            <button type="button" class="btn btn-sm btn-secondary ms-2" @click="showTemplateDrawer = true">
                <i class="bi bi-file-earmark-text me-1"></i> <?= lang('邮件模板') ?>
            </button>
        </h6>

        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">
                    <?= lang('邮件主机地址') ?>
                </label>
                <input v-model="form.mail_smtp" class="form-control" placeholder="smtp.qq.com">
            </div>

            <div class="col-md-3">
                <label class="form-label">
                    <?= lang('邮件端口') ?>
                </label>
                <input v-model="form.mail_port" class="form-control" placeholder="465">
            </div>

            <div class="col-md-3">
                <label class="form-label">
                    <?= lang('邮件用户名') ?>
                </label>
                <input v-model="form.mail_from" class="form-control" placeholder="">
            </div>

            <div class="col-md-3">
                <label class="form-label">
                    <?= lang('邮件密码') ?>
                </label>
                <input v-model="form.mail_pwd" type="password" class="form-control" placeholder="">
            </div>
        </div>
    </div>

    <!-- 测试服务抽屉 -->
    <el-drawer
        title="<?= lang('测试邮件服务') ?>"
        :visible.sync="showTestDrawer"
        direction="rtl"
        size="30%">
        <div class="p-3">
            <el-form label-width="120px">
                <el-form-item label="<?= lang('测试邮件地址') ?>" required>
                    <el-input v-model="testForm.email" placeholder="<?= lang('请输入测试邮件地址') ?>"></el-input>
                </el-form-item>
                <el-form-item label="<?= lang('邮件标题') ?>" required>
                    <el-input v-model="testForm.subject" placeholder="<?= lang('请输入邮件标题') ?>"></el-input>
                </el-form-item>
                <el-form-item label="<?= lang('邮件内容') ?>" required>
                    <el-input type="textarea" v-model="testForm.content" :rows="4" placeholder="<?= lang('请输入邮件内容') ?>"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="sendTestEmail()"> <?= lang('发送测试') ?> </el-button>
                </el-form-item>
            </el-form>
        </div>
    </el-drawer>

    <!-- 邮件模板抽屉 -->
    <el-drawer
        title="<?= lang('邮件模板管理') ?>"
        :visible.sync="showTemplateDrawer"
        direction="rtl"
        size="50%">
        <div class="p-3">
            <el-button type="primary" size="small" @click="showAddTemplate = true"> <?= lang('添加模板') ?> </el-button>
            <el-table :data="templateList" style="width: 100%" class="mt-3">
                <el-table-column prop="code" label="<?= lang('模板代码') ?>"></el-table-column>
                <el-table-column prop="name" label="<?= lang('模板名称') ?>"></el-table-column>
                <el-table-column prop="subject" label="<?= lang('邮件标题') ?>"></el-table-column>
                <el-table-column label="<?= lang('操作') ?>" width="180">
                    <template slot-scope="scope">
                        <el-button size="mini" @click="editTemplate(scope.row)"> <?= lang('编辑') ?> </el-button>
                        <el-button size="mini" type="danger" @click="deleteTemplate(scope.row)"> <?= lang('删除') ?> </el-button>
                    </template>
                </el-table-column>
            </el-table>

            <!-- 添加/编辑模板对话框 -->
            <el-dialog :title="templateDialogTitle" :visible.sync="showAddTemplate" width="50%" :append-to-body="true" :modal-append-to-body="true">
                <el-form label-width="120px">
                    <el-form-item label="<?= lang('模板代码') ?>" required>
                        <el-input v-model="templateForm.code" placeholder="<?= lang('一般为英文') ?>"></el-input>
                    </el-form-item>
                    <el-form-item label="<?= lang('模板名称') ?>" required>
                        <el-input v-model="templateForm.name"></el-input>
                    </el-form-item>
                    <el-form-item label="<?= lang('邮件标题') ?>" required>
                        <el-input v-model="templateForm.subject"></el-input>
                    </el-form-item>
                    <el-form-item label="<?= lang('邮件内容') ?>" required>
                        <el-input type="textarea" :rows="6" v-model="templateForm.content" placeholder="<?= lang('请输入邮件内容, 变量{name} {code}') ?>"></el-input>
                    </el-form-item>
                </el-form>
                <span slot="footer">
                    <el-button @click="showAddTemplate = false"> <?= lang('取消') ?> </el-button>
                    <el-button type="primary" @click="saveTemplate()"> <?= lang('保存') ?> </el-button>
                </span>
            </el-dialog>
        </div>
    </el-drawer>
<?php

    global $vue;

    $vue->data('showTestDrawer', false);
    $vue->data('showTemplateDrawer', false);
    $vue->data('testForm', ['email' => '', 'subject' => '', 'content' => '']);
    $vue->data('templateList', []);
    $vue->data('showAddTemplate', false);
    $vue->data('templateDialogTitle', lang('添加模板'));
    // 在 Vue 数据中添加 code
    $vue->data('templateForm', ['id' => '', 'code' => '', 'name' => '', 'subject' => '', 'content' => '']);

    // 修改 editTemplate 方法
    $vue->method('editTemplate(row)', "
    this.templateForm = Object.assign({}, row);
    this.templateDialogTitle = '" . lang('编辑模板') . "';
    this.isEditTemplate = true;
    this.showAddTemplate = true;
");

    $vue->method('deleteTemplate(row)', "
    this.\$confirm('" . lang('确认删除该模板？') . "', '" . lang('提示') . "', {type: 'warning'}).then(() => {
        ajax('/email/template/delete', {id: row.id}, function(res) {
            if (res.code === 0) {
                _this.\$message.success('" . lang('删除成功') . "');
                _this.loadTemplates();
            }
        });
    });
");

    $vue->method('saveTemplate()', "
    var url = this.isEditTemplate ? '/email/template/update' : '/email/template/add';
    ajax(url, this.templateForm, function(res) {
        " . vue_message() . "
        if (res.code === 0) { 
            _this.showAddTemplate = false;
            _this.loadTemplates();
        }
    });
");

    $vue->method('loadTemplates()', "
    ajax('/email/template/list', {}, function(res) {
        if (res.code === 0) {
            _this.templateList = res.data;
        }
    });
");

    $vue->method('editTemplate(row)', "
    this.templateForm = Object.assign({}, row);
    this.templateDialogTitle = '" . lang('编辑模板') . "';
    this.isEditTemplate = true;
    this.showAddTemplate = true;
");

    $vue->watch('showTemplateDrawer', "
    handler(new_val,old_val){
        if (this.showTemplateDrawer) {
            this.loadTemplates();
        }
    }
");

    $vue->method('sendTestEmail()', "
    if (!this.testForm.email || !this.testForm.subject || !this.testForm.content) {
        this.\$message.error('" . lang('请填写完整信息') . "');
        return;
    }
    ajax('/email/site/test', this.testForm, function(res) {
        " . vue_message() . "
        if (res.code === 0) { 
            _this.showTestDrawer = false;
        } else { 
        }
    });
");
},500);
