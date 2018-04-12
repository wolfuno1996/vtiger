<?php
/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

/**
 * Class QuotingTool_PDFContent_Model
 */
class QuotingTool_PDFContent_Model extends Vtiger_Module_Model
{

    /**
     * @param int $recordId
     * @return array
     */
    public function getLineItemsAndTotal($recordId)
    {
        global $adb;
        $sql = "SELECT
                vtiger_products.product_no,
                vtiger_products.productname,
                vtiger_products.productcode,
                vtiger_products.productcategory,
                vtiger_products.manufacturer,
                vtiger_products.weight,
                vtiger_products.pack_size,
                vtiger_products.cost_factor,
                vtiger_products.commissionmethod,
                vtiger_products.reorderlevel,
                vtiger_products.mfr_part_no,
                vtiger_products.vendor_part_no,
                vtiger_products.serialno,
                vtiger_products.qtyinstock,
                vtiger_products.productsheet,
                vtiger_products.qtyindemand,
                vtiger_products.glacct,
                vtiger_products.vendor_id,
                vtiger_products.imagename,

                vtiger_service.serviceid,
                vtiger_service.service_no,
                vtiger_service.servicename,
                vtiger_service.servicecategory,
                vtiger_service.service_usageunit,"

            . " CASE WHEN vtiger_products.productid != ''
                    THEN vtiger_products.qty_per_unit
                    ELSE vtiger_service.qty_per_unit
                END AS qty_per_unit,"
            . " CASE WHEN vtiger_products.productid != ''
                    THEN vtiger_products.unit_price
                    ELSE vtiger_service.unit_price
                END AS unit_price,"
            . " CASE WHEN vtiger_products.productid != ''
                    THEN vtiger_products.sales_start_date
                    ELSE vtiger_service.sales_start_date
                END AS sales_start_date,"
            . " CASE WHEN vtiger_products.productid != ''
                    THEN vtiger_products.sales_end_date
                    ELSE vtiger_service.sales_end_date
                END AS sales_end_date,"
            . " CASE WHEN vtiger_products.productid != ''
                    THEN vtiger_products.start_date
                    ELSE vtiger_service.start_date
                END AS start_date,"
            . " CASE WHEN vtiger_products.productid != ''
                    THEN vtiger_products.expiry_date
                    ELSE vtiger_service.expiry_date
                END AS expiry_date,"
            . " CASE WHEN vtiger_products.productid != ''
                    THEN vtiger_products.discontinued
                    ELSE vtiger_service.discontinued
                END AS discontinued,"
            . " CASE WHEN vtiger_products.productid != ''
                    THEN vtiger_products.website
                    ELSE vtiger_service.website
                END AS website,"
            . " CASE WHEN vtiger_products.productid != ''
                    THEN vtiger_products.taxclass
                    ELSE vtiger_service.taxclass
                END AS taxclass,"
            . " CASE WHEN vtiger_products.productid != ''
                    THEN vtiger_products.currency_id
                    ELSE vtiger_service.currency_id
                END AS currency_id,"
            . " CASE WHEN vtiger_products.productid != ''
                    THEN vtiger_products.commissionrate
                    ELSE vtiger_service.commissionrate
                END AS commissionrate,"
            . " CASE WHEN vtiger_products.productid != ''
                    THEN vtiger_products.productname
                    ELSE vtiger_service.servicename
                END AS productname,"
            . " CASE WHEN vtiger_products.productid != ''
                    THEN vtiger_products.productid
                    ELSE vtiger_service.serviceid
                END AS psid,"
            . " CASE WHEN vtiger_products.productid != ''
                    THEN vtiger_products.product_no
                    ELSE vtiger_service.service_no
                END AS psno,"
//            . " CASE WHEN vtiger_products.productid != ''
//                    THEN vtiger_products.productcode
//                    ELSE vtiger_service.service_no
//                END AS productcode,"
            . " CASE WHEN vtiger_products.productid != ''
                    THEN 'Products'
                    ELSE 'Services'
                END AS entitytype,"
            . " CASE WHEN vtiger_products.productid != ''
                    THEN vtiger_products.unit_price
                    ELSE vtiger_service.unit_price
                END AS unit_price,"
            . " CASE WHEN vtiger_products.productid != ''
                    THEN vtiger_products.usageunit
                    ELSE vtiger_service.service_usageunit
                END AS usageunit,"
            . " CASE WHEN vtiger_products.productid != ''
                    THEN vtiger_products.qty_per_unit
                    ELSE vtiger_service.qty_per_unit
                END AS qty_per_unit,"
            . " CASE WHEN vtiger_products.productid != ''
                    THEN vtiger_products.qtyinstock
                    ELSE 'NA'
                END AS qtyinstock,"
            . " CASE WHEN vtiger_products.productid != ''
                    THEN c1.description
                    ELSE c2.description
                END AS psdescription,"

            . " vtiger_inventoryproductrel.* "
            . " FROM vtiger_inventoryproductrel "
            . " LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_inventoryproductrel.productid "
            . " LEFT JOIN vtiger_crmentity AS c1 ON c1.crmid = vtiger_products.productid "
            . " LEFT JOIN vtiger_service ON vtiger_service.serviceid = vtiger_inventoryproductrel.productid "
            . " LEFT JOIN vtiger_crmentity AS c2 ON c2.crmid = vtiger_service.serviceid "
            . " WHERE vtiger_inventoryproductrel.id = ? ORDER BY sequence_no";
        $result = $adb->pquery($sql, array($recordId));
        $count = $adb->num_rows($result);
        $data = array();

        if ($count) {
            $i = 0;
            while ($row = $adb->fetch_array($result)) {
                $data[$i] = array();
                foreach ($row as $k => $d) {
                    $data[$i][$k] = $d;
                }

                $i++;
            }
        }

        return $data;
    }
}