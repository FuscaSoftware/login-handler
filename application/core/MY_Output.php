<?php
/**
 * User: sbraun
 * Date: 14.09.17
 * Time: 19:47
 */

/**
 * Class MY_Output
 * sb: is used by CI without any configurations :D
 */
class MY_Output extends CI_Output
{

    public function _display($output = '') {
//        if (!ci()->is_ajax_request() && !headers_sent()) {
        if (!headers_sent()) {
            return parent::_display($output); // TODO: Change the autogenerated stub
        }
//        } else {
//            echo ":(";
//        }
    }
}