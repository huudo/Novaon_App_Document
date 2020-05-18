<?php

class ModelExtensionAppstoreAppDemo extends Model
{
    function getProductSales()
    {
        $sql = "SELECT product_id FROM " . DB_PREFIX . "app_demo";
        $query = $this->db->query($sql);

        return $query->rows;
    }
}