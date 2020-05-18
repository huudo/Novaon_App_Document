<?php
class ModelExtensionAppstoreAppDemo extends Model
{
    function getProductSale()
    {

    }
    function addAppDemo($product_id){
        // Kiểm tra xem sản phẩm đã thêm chưa
        // Nếu chưa thì thêm mới
        $sqlCheck = "SELECT COUNT(demo.id) AS total FROM " . DB_PREFIX . "app_demo as demo
                WHERE demo.product_id = " . (int)$product_id . " ";
        $queryCheck = $this->db->query($sqlCheck);
        // Update db
        if($queryCheck->row['total'] > 0){

        }else{
            $sql = "INSERT INTO `" . DB_PREFIX . "app_demo`
                SET `product_id` = '" . (int)$product_id . "',
                    `date_added` =  NOW(),
                    `date_published` =  NOW() ,
                    `date_modified` = NOW()   ";

            $this->db->query($sql);
        }
    }
    function createTables()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "app_demo (
                id INT(11) NOT NULL AUTO_INCREMENT,
                product_id INT(11) NOT NULL,
                date_added DATETIME NOT NULL,
                date_published DATETIME NOT NULL,
                date_modified DATETIME NOT NULL,
                PRIMARY KEY (id)
            )
            COLLATE='utf8_general_ci'
            ENGINE=MyISAM;");
    }
    function dropTables(){
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "app_demo`");
    }
}
