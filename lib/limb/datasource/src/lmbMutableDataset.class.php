<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMutableDataset.class.php 4992 2007-02-08 15:35:40Z pachanga $
 * @package    datasource
 */
lmb_require('limb/datasource/src/lmbArrayDataset.class.php');

class lmbMutableDataset extends lmbArrayDataset {

    function unshiftRow($row) {
        return array_unshift($this->dataset, $row);
    }

    function shiftRow() {
        return array_shift($this->dataset);
    }

    function pushRow($row) {
        return array_push($this->dataset, $row);
    }

    function popRow() {
        return array_pop($this->dataset);
    }

    function insertRow($row) {
        $newkey = key($this->dataset) + 1;
        $after = array_splice($this->dataset, $newkey);
        array_push($this->dataset, $row);
        $this->dataset = array_merge($this->dataset, $after);
        $this->seekRow($newkey);
        return count ( $this->dataset );
    }

    function deleteRow() {
        ($key = key($this->dataset) ) > 0 ? $lastkey = ($key - 1) : $lastkey = 0;
        if ( !$key ) $key = 0;
        $deleted = $this->dataset[$key];
        unset($this->dataset[$key]);
        $this->dataset = array_values($this->dataset);
        $this->seekRow($lastkey);
        return $deleted;
    }

    function seekRow($index) {
        $last = (count($this->dataset)-1);
        if ( $index > 0 && $index < $last ) {
            if ( $index < ($last/2) ) {
                for ($i = 0; $i <= $index; $i++ ) {
                    if ( $i != 0 ) {
                        next($this->dataset);
                    } else {
                        reset($this->dataset);
                    }
                }
            } else {
                for ($i = $last; $i >= $index; $i-- ) {
                    if ( $i != $last ) {
                        prev($this->dataset);
                    } else {
                        end ($this->dataset);
                    }
                }
            }
            $row = current($this->dataset);
        } else if ( $index >= $last ) {
            $row = end($this->dataset);
        } else {
            $this->rewind();
            $row = reset($this->dataset);
        }
        return $row;
    }
}
?>