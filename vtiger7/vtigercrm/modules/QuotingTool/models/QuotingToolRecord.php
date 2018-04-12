<?php
/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

/**
 * Class QuotingTool_QuotingToolRecord_Model
 */
class QuotingTool_QuotingToolRecord_Model extends Vtiger_Record_Model
{
    protected $table_name = 'vtiger_quotingtool';
    protected $table_index = 'id';

    /**
     * Function to get the Detail View url for the record
     * @return string - Record Detail View Url
     */
    public function getDetailViewUrl()
    {
        return '';
    }

    /**
     * @param array $conditions
     * @return string
     */
    protected function parseConditions($conditions = array())
    {
        if (!$conditions || !is_array($conditions) || empty($conditions)) {
            return "";
        }

        $strCondition = "";
//        $operators = array('AND', 'OR', 'NOT IN', 'IN');
        $exampleConditions1 = array(
            "id = 1",
            array(
                'id' => 1,
                'template_id' => 2,
            ),
            'OR' => array(
                'id' => 1,
                'template_id' => 2,
            )
        );
        $exampleConditions2 = array(
            'AND' => array(
                'id' => 1,
                'template_id' => 2,
            ),
            'OR' => array(
                'id' => 1,
                'template_id' => 2,
                'AND' => array(
                    'id' => 1,
                    'template_id' => 2,
                )
            )
        );

        foreach ($conditions as $key => $condition) {
            if (!$condition) {
                // invalid condition
                continue;
            }

            // Parse with operator
            $key = uppercase($key);

            switch ($key) {
                case 'AND':
                case 'OR':
                    if (is_string($condition)) {
                        // condition string
                        $condition = array($condition);
                    }

                    if (count($condition) <= 1) {
                        // one condition
                        $strCondition .= " ({$condition})";
                    } else {
                        $tmpCondition = "(";

                        foreach ($condition as $c) {
                            $tmpCondition .= " {$c} {$key}";
                        }

                        $tmpCondition = rtrim($tmpCondition, $key);
                        $tmpCondition .= ")";
                        $strCondition .= " {$tmpCondition}";
                    }

                    break;

                case 'NOT':
                case 'NOT IN':
                    if (is_string($condition)) {
                        $condition = rtrim($condition, '()');
                        /** @link http://stackoverflow.com/questions/19347005/how-can-i-explode-and-trim-whitespace */
                        $condition = array_map('trim', explode(',', $condition));
                    }

                    $strCondition = " {$key} " . implode(',', $condition) . ")";
                    break;

                default:
                    if (is_array($condition)) {
                        $strCondition .= $this->parseConditions($condition);
                    } else {
                        $strCondition .= ' AND ' . $condition;
                    }
                    break;
            }
        }

        return $strCondition;
    }

    /**
     * @param array $fields
     * @return string
     */
    protected function parseFields($fields = array())
    {
        $strField = "";

        if ($fields && is_array($fields) && !empty($fields)) {
            foreach ($fields as $field) {
                $strField .= " {$field},";
            }

            $strField = rtrim($strField, ',');
        }

        return $strField;
    }

    /**
     * @param array $options
     * @return array
     */
    public function findAll($options = array())
    {
        $db = PearDatabase::getInstance();
        $instances = array();

        $fields = $options['fields'] ? $options['fields'] : array();
        $conditions = $options['conditions'] ? $options['conditions'] : array();

        $sql = "SELECT";

        // fields
        $strField = $this->parseFields($fields);
        $sql .= ($strField == '') ? " *" : $strField;

        // from tables
        $sql .= " FROM `{$this->table_name}` WHERE `deleted` != 1";

        // conditions
        if (!$conditions || empty($conditions)) {
            $sql .= (!$conditions) ? "" : $this->parseConditions();
        }

        $rs = $db->pquery($sql);

        if ($db->num_rows($rs)) {
            while ($data = $db->fetch_array($rs)) {
                $instances[] = new self($data);
            }
        }
        return $instances;
    }

}