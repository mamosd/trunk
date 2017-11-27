<?php
/**
 * Description of CompareBehavior
 *
 * @author ramon
 */
class CompareBehavior extends CActiveRecordBehavior
{
  public function compare($other) {
    if(!is_object($other))
      return false;

    // does the objects have the same type?
    if(get_class($this->owner) !== get_class($other))
      return false;

    $differences = array();

    $ignoreKeys = array();
    if(isset($this->owner->ignoreKeyChanges))
        $ignoreKeys = $this->owner->ignoreKeyChanges;
    
    foreach($this->owner->attributes as $key => $value) {
      if ((!in_array($key, $ignoreKeys)) && ($this->owner->$key != $other->$key))
        $differences[$key] = array(
            'old' => $this->owner->$key,
            'new' => $other->$key);
    }

    return $differences;
  }
}
