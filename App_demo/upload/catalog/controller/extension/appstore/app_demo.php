<?php

use App_Api\Product_Field;
use App_Api\Query_App_Builder;
use App_Api\Table_Name;
use App_Api\App_Setting;

class ControllerExtensionAppstoreAppDemo extends Controller
{
    public function index()
    {
        if (isset($this->request->get['module_id'])) {
            $module_id = $this->request->get['module_id'];
            $setting = new App_Setting($this->registry);
            $data = $setting->getModule($module_id);

            $data['link_app_demo'] = array(
                'text' => 'Trang App Demo',
                'href' => $this->url->link('extension/appstore/app_demo/detail','',true)
            );
            $this->response->setOutput($this->load->view('extension/appstore/app_demo', $data));
        }
    }
    public  function detail(){
        $this->load->model('extension/appstore/app_demo');
        $this->document->setTitle("App Demo");
        $flashProduct = $this->model_extension_appstore_app_demo->getProductSales();
        $products = array();

        /*Get Products to set App Demo*/

        $table = Table_Name::PRODUCT;
        $fields = [
            Product_Field::PRODUCT_ID,
            Product_Field::PRODUCT_NAME,
            Product_Field::PRODUCT_PRICE,
            Product_Field::PRODUCT_IMAGE,
        ];
        $builder = new Query_App_Builder($this->registry);
        foreach($flashProduct as $flash){
            $product_id = $flash['product_id'];

            $conditions = [
                Product_Field::PRODUCT_ID => $product_id,
            ];
            $product = $builder->getData($table, $fields, $conditions);
            $product['href'] = $this->url->link('product/product', 'product_id=' . $product_id);
            $products[] = $product;
        }
        $data['products'] = $products;
        /* base layout */
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('extension/appstore/app_demo_detail', $data));
    }
}