<?php
/**
 * Created by PhpStorm.
 * User: sbraun
 * Date: 29.12.17
 * Time: 17:09
 */

class Uuid_lib
{

    public function q_create_function_binary_ordered_uuid() {
        $q = "CREATE DEFINER=`root`@`localhost` FUNCTION `ordered_uuid`(uuid BINARY(36)) RETURNS binary(16)
    DETERMINISTIC
RETURN UNHEX(CONCAT(SUBSTR(uuid, 15, 4),SUBSTR(uuid, 10, 4),SUBSTR(uuid, 1, 8),SUBSTR(uuid, 20, 4),SUBSTR(uuid, 25)));";
        return $q;
    }

    public function q_create_function_revert_binary_ordered_uuid() {
        $q = "CREATE DEFINER=`root`@`localhost` FUNCTION `readable_uuid`(ordered_uuid BINARY(36)) RETURNS char(36)
    DETERMINISTIC
RETURN concat(SUBSTR(hex(ordered_uuid), 9, 8), '-', SUBSTR(hex(ordered_uuid), 5, 4), '-', SUBSTR(hex(ordered_uuid), 1, 4), '-', SUBSTR(hex(ordered_uuid), 17, 4), '-', SUBSTR(hex(ordered_uuid), 21, 12));";
        return $q;
    }

    /**
     * converts std uuid() to a binary ordered uuid
     * @return string
     */
    public function q_ordered_uuid() {
        $q = "UNHEX(CONCAT(SUBSTR(uuid, 15, 4),SUBSTR(uuid, 10, 4),SUBSTR(uuid, 1, 8),SUBSTR(uuid, 20, 4),SUBSTR(uuid, 25)))";
        return $q;
    }

    /**
     * converts a binary ordered uuid back to std uuid() in original order
     * @return string
     */
    public function q_std_uuid() {
        $q = "concat(SUBSTR(hex(ordered_uuid), 9, 8), '-', SUBSTR(hex(ordered_uuid), 5, 4), '-', SUBSTR(hex(ordered_uuid), 1, 4), '-', SUBSTR(hex(ordered_uuid), 17, 4), '-', SUBSTR(hex(ordered_uuid), 21, 12))";
        return $q;
    }

    public function q1() {
        $q1 = "
select `ordered_uuid`(uuid());
        ";
        return $q1;
    }

    public function q2() {
        $q2 = "
select hex(uuid());
set @uuid = uuid();
select @uuid;
select replace(@uuid, '-','');
set @uuidbin = unhex(replace(@uuid, '-',''));
select @uuidbin;
select hex(@uuidbin);
";
    }

    public function q3() {
        $q3 = "
select
UNHEX(CONCAT(SUBSTR(@uuid, 15, 4),SUBSTR(@uuid, 10, 4),SUBSTR(@uuid, 1, 8),SUBSTR(@uuid, 20, 4),SUBSTR(@uuid, 25)));
";
    }

    public function q4() {
        $q4 = "
select
(CONCAT(SUBSTR(@uuid, 15, 4),'-',SUBSTR(@uuid, 10, 4),'-',SUBSTR(@uuid, 1, 8),'-',SUBSTR(@uuid, 20, 4),'-',SUBSTR(@uuid, 25))) as ordered_uuid
union
select
@uuid
;
";
    }

    public function q5() {
        $q5 = "
select `ordered_uuid`(@uuid);
";
    }

}