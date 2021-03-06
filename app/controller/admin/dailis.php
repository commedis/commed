<?php
namespace BL\app\controller\admin;

use BL\app\libs\Controller;

class dailis extends CheckAdmin
{
    public function index()
    {
        $data = array('title' => '代理列表');
        $lists = $this->model()->select()->from('daili')->fetchAll();
        $data += array('lists' => $lists);
        $this->put('dllist.php', $data);
    }
    public function save()
    {
        $data = array();
        if (isset($_POST)) {
            foreach ($_POST as $key => $val) {
                if ($key != 'adminname' && $key != 'adminpass' && $key != 'cirpwd' && $key != 'is_state') {
                    $data[$key] = $this->req->post($key);
                }
            }
        }
        $uname = $this->req->post('adminname');
        $upass = $this->req->post('adminpass');
        $cirpwd = $this->req->post('cirpwd');
        $is_state = $this->req->post('is_state');
        if ($uname == '' || $upass == '' || $cirpwd == '') {
            echo json_encode(array('status' => 0, 'msg' => '选项填写不完整'));
            exit;
        }
        if ($this->model()->select()->from('daili')->where(array('fields' => 'adminname=?', 'values' => array($uname)))->count()) {
            echo json_encode(array('status' => 0, 'msg' => $uname . ' 账号已存在'));
            exit;
        }
        if (strlen($upass) < 6 || strlen($upass) > 20) {
            echo json_encode(array('status' => 0, 'msg' => '登录密码长度在6-20位之间'));
            exit;
        }
        if ($upass != $cirpwd) {
            echo json_encode(array('status' => 0, 'msg' => '两次输入的密码匹配'));
            exit;
        }
        $data = array('adminname' => $uname, 'adminpass' => sha1($upass), 'is_state' => $is_state, 'limits' => json_encode($data), 'token' => sha1($this->res->getRandomString(40)));
        if ($this->model()->from('daili')->insertData($data)->insert()) {
            echo json_encode(array('status' => 1, 'msg' => '设置保存成功', 'url' => $this->dir . 'dailis'));
            exit;
        }
        echo json_encode(array('status' => 0, 'msg' => '设置保存失败'));
        exit;
    }
    public function edit()
    {
        $data = array('title' => '编辑账号信息');
        $id = isset($this->action[3]) ? intval($this->action[3]) : 0;
        $admin = $this->model()->select()->from('daili')->where(array('fields' => 'id=?', 'values' => array($id)))->fetchRow();
        $admin['limits'] = json_decode($admin['limits'], true);
        $this->put('dledit.php', $data += array('data' => $admin));
    }
    public function editsave()
    {
        $id = isset($this->action[3]) ? intval($this->action[3]) : 0;
        $data = array();
        if (isset($_POST)) {
            foreach ($_POST as $key => $val) {
                if ($key != 'adminname' && $key != 'adminpass' && $key != 'cirpwd' && $key != 'is_state') {
                    $data[$key] = $this->req->post($key);
                }
            }
        }
        $upass = $this->req->post('adminpass');
        $cirpwd = $this->req->post('cirpwd');
        $is_state = $this->req->post('is_state');
        $data = array('is_state' => $is_state, 'limits' => json_encode($data));
        if ($upass) {
            if (strlen($upass) < 6 || strlen($upass) > 20) {
                echo json_encode(array('status' => 0, 'msg' => '登录密码长度在6-20位之间'));
                exit;
            }
            if ($upass != $cirpwd) {
                echo json_encode(array('status' => 0, 'msg' => '两次输入的密码匹配'));
                exit;
            }
            $data += array('adminpass' => sha1($upass));
        }
        if ($this->model()->from('daili')->updateSet($data)->where(array('fields' => 'id=?', 'values' => array($id)))->update()) {
            echo json_encode(array('status' => 1, 'msg' => '设置保存成功', 'url' => $this->dir . 'dailis'));
            exit;
        }
        echo json_encode(array('status' => 0, 'msg' => '设置保存失败'));
        exit;
    }
    public function del()
    {
        $id = $this->req->get('id');
        if ($id) {
            if ($this->model()->from('daili')->where(array('fields' => 'id=?', 'values' => array($id)))->delete()) {
                echo json_encode(array('status' => 1));
                exit;
            }
        }
        echo json_encode(array('status' => 0));
        exit;
    }
}