<?php
namespace Utilities;
/**
 * Includes app wide data validators.
 *
 */
class Data
{
  /**
   * Loops through an array to check empty non-zero values. If condition is met, a string message with the value's key underscores and "id" replaced with spaces is returned. If no empty value exists a false boolean is returned.
   *
   * @param  array $array
   * @return mixed        description of empty value.
   */
  public static function arrayHasEmptyValue($array)
  {
    foreach($array as $key => $value){
      if(is_int($value) && $value === 0){
        continue;
      }
      if(empty($array[$key])){
        return str_replace(["partner_order_id", "_", "id"], ["Partner order #", " ", ""], $key).' cannot be empty';
      }
    }
    return false;
  }

  /**
   * This function converts an stdClass object into a php array. This may be
   * used for accepting post values from an angular app.
   *
   * @param  object $data serialized post array
   * @return array        php post array
   */
  public static function convertObjToArr($data)
  {
    if(is_object($data)){
      $data = get_object_vars($data);
    }
    if(is_array($data)){
      return array_map("Prism\Data::convertObjToArr", $data);
    } else {
      return $data;
    }
  }

  /**
   * Phone number validator, checks if the length is not 10 and that the string has no non numeric characters.
   *
   * @param  int $phone_number
   * @return mixed              null or message array
   */
  public static function phoneNumberIsValid($phone_number)
  {
    if(!ctype_digit($phone_number) || strlen($phone_number) !== 10){
      return [
        'status' => 'error',
        'message' => 'Phone number must be a 10 digit number without the international code'
      ];
    }
  }
}
