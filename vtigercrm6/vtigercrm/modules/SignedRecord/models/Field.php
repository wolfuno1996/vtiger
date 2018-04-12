<?php
/* ********************************************************************************
 * The content of this file is subject to the Signed Record ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

require_once 'modules/SignedRecord/resources/signature-to-image/signature-to-image.php';

/**
 * Class SignedRecord_Field_Model
 */
class SignedRecord_Field_Model extends Vtiger_Field_Model
{
    /**
     * Function to check whether field is ajax editable'
     * @return <Boolean>
     */
    public function isAjaxEditable()
    {
        return false;
    }

    /**
     * Function to retieve display value for a value
     * @param <String> $value - value which need to be converted to display value
     * @return <String> - converted display value
     */
    public function getDisplayValue($value, $record = false, $recordInstance = false)
    {
        if ($this->name == 'signature') {
            $img = $this->sigJsonToImage($value);
            return '<img src="' . $img . '" style="height: 100px;"/>';
        } else if ($this->name == 'filename') {
            return '<a href="index.php?module=SignedRecord&action=DownloadFile&record=' . $record . '">' . basename($value) . '</a>';
        } else {
            return parent::getDisplayValue($value, $record, $recordInstance);
        }
    }

    /**
     * @link https://github.com/thomasjbradley/signature-to-image/
     * @link http://stackoverflow.com/questions/22266402/how-to-encode-an-image-resource-to-base64
     *
     * @param string $json
     * @return object
     */
    public function sigJsonToImage($json)
    {
        $json = json_decode(htmlspecialchars_decode($json));
        $img = sigJsonToImage($json, array(
            'imageSize' => array(500, 180)
        ));

        ob_start(); // Let's start output buffering.
        imagepng($img); //This wxill normally output the image, but because of ob_start(), it won't.
        $contents = ob_get_contents(); //Instead, output above is saved to $contents
        ob_end_clean(); //End the output buffer.

        // close stream
        imagedestroy($img);
        $dataUri = "data:image/png;base64," . base64_encode($contents);

        return $dataUri;

    }

}