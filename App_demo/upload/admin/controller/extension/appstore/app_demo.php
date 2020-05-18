<?php

use App_Api\App_Config;
use App_Api\App_Setting;
use App_Api\Product_Field;
use App_Api\Query_App_Builder;
use App_Api\Table_Name;

class ControllerExtensionAppstoreAppDemo extends Controller
{
    private $error = array();

    function index()
    {
        $this->load->language('extension/appstore/app_demo');
        $this->document->setTitle($this->language->get('heading_title'));
        /* Hiển thị danh sách các module( Chương trình ) của app */
        $module_data = array();
        $setting = new App_Setting($this->registry);
        $modules = $setting->getModulesByCode('app_demo');

        foreach ($modules as $module) {
            if ($module['setting']) {
                $setting_info = json_decode($module['setting'], true);
            } else {
                $setting_info = array();
            }

            $module_data[] = array(
                'module_id' => $module['module_id'],
                'name'      => $module['name'],
                'status'    => (isset($setting_info['status']) && $setting_info['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'edit'      => $this->url->link('extension/appstore/app_demo/module', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module['module_id'], true),
                'delete'    => $this->url->link('extension/extension/appstore/delete', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module['module_id'], true)
            );
        }
        $data['modules'] = $module_data;
        $data['action_add'] = $this->url->link('extension/appstore/app_demo/module','user_token=' . $this->session->data['user_token'],true);
        /* Lấy danh sách sản phẩm */
        $data['products'] = $this->setup_product();

        $data['custom_header'] = $this->load->controller('common/custom_header');
        $data['custom_column_left'] = $this->load->controller('common/custom_column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/appstore/app_demo', $data));
    }
    function module(){
        $this->load->language('extension/appstore/app_demo');
        $setting = new App_Setting($this->registry);
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if (!isset($this->request->get['module_id'])) {
                $setting->addModule('app_demo', $this->request->post);
                $this->session->data['success'] = "Add Module Success !";
                $this->response->redirect($this->url->link('extension/appstore/app_demo', 'user_token=' . $this->session->data['user_token'], true));
            } else {
                $setting->editModule($this->request->get['module_id'], $this->request->post);
            }
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/appstore/app_demo', 'user_token=' . $this->session->data['user_token'], true));
        }
        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $module_info = $setting->getModule($this->request->get['module_id']);
            $data['name'] = $module_info['name'];
            $data['status'] = $module_info['status'];

            $data['action'] = $this->url->link('extension/appstore/app_demo/module','user_token=' . $this->session->data['user_token'].
                '&module_id=' . $this->request->get['module_id'],true);
            $config = [
                'app' => 'app_demo',
                'module_id' => $this->request->get['module_id']
            ];
            $data['app_config'] = $this->load->controller('common/app_config/setTheme',$config);
            $data['custom_header'] = $this->load->controller('common/custom_header');
            $data['custom_column_left'] = $this->load->controller('common/custom_column_left');
            $data['footer'] = $this->load->controller('common/footer');
            $this->response->setOutput($this->load->view('extension/appstore/app_demo/edit_module',$data));
        }
    }
    function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/appstore/app_demo')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        return !$this->error;
    }

    function setup_product(){
        $this->load->language('extension/appstore/app_demo');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') ) {
            if(isset($this->request->post['products'])){
                $products = $this->request->post['products'];
                $this->load->model('extension/appstore/app_demo');
                foreach($products as $product) {
                    $this->model_extension_appstore_app_demo->addAppDemo($product);
                }
            }
            $redirect = $this->url->link('extension/appstore/app_demo','user_token='.$this->session->data['user_token'],true);
            $this->response->redirect($redirect);
        }
        /*Get Products to set App Demo*/

        $table = Table_Name::PRODUCT;
        $fields = [
            Product_Field::PRODUCT_ID,
            Product_Field::PRODUCT_NAME,
            Product_Field::PRODUCT_PRICE,
            Product_Field::PRODUCT_IMAGE,
            Product_Field::MANUFACTURER_ID,
            Product_Field::MANUFACTURER_NAME,
        ];
        $conditions = [
            //Product_Field::PRODUCT_ID => 95,
        ];

        $builder = new Query_App_Builder($this->registry);
        $products = $builder->getData($table, $fields, $conditions);
        return $products;
    }
    function registerApp($data){
        $config = new App_Config($this->registry);
        $config->registerApp($data);
    }
    function install()
    {
        $this->load->model('extension/appstore/app_demo');
        $this->model_extension_appstore_app_demo->createTables();

        // Update app_name & Logo
        $data = [
            "app_name" => "App Demo",
            "path_logo" => "view/image/appstore/app_demo.png"
        ];

        $setting = new App_Setting($this->registry);
        $setting->updateInfo("app_demo",$data);
    }

    function uninstall()
    {
        /* Gỡ ứng dụng */
        $config = new App_Config($this->registry);
        $config->unRegisterApp('app_demo');

        /* DROP các bảng đã thêm */
        $this->load->model('extension/appstore/app_demo');
        $this->model_extension_appstore_app_demo->dropTables();
    }
}
