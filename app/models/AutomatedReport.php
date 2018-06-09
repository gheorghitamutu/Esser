<?php

namespace AppModel;

use http\Exception\InvalidArgumentException;

class AutomatedReport extends AbstractEntity
{
    protected $_allowedFields = array('reportId','reportPath','reportType','reportFormat','rCreatedAt');

    /**
     * Set the entry ID
     * @param bool $id = false Method implemented but should not be used. Id's are generated automatically and should remain the value generated!
     */
    public function setId($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 999999999)))) {
            throw new InvalidArgumentException('Report id can only be between 1 and 999999999!');
        }
        $this->_values['reportId'] = $id;
    }

    public function setPath($path) {
        if (!is_string($path) ) {
            throw new InvalidArgumentException('Report path input should only be in string format!');
        }
        $this->_values['reportPath'] = $path;
    }

    public function setType($type) {
        if (!filter_var($type, FILTER_VALIDATE_INT, array('options' => array('min_range' => 0, 'max_range' => 3)))) {
            throw new InvalidArgumentException('Report type can only be between 0 and 3!');
        }
        $this->_values['reportType'] = $type;
    }

    public function setFormat($format) {
        if (!is_string($format)) {
            throw new InvalidArgumentException('Report format needs to be a string always!');
        }
        if(strlen($format) < 4) {
            throw new InvalidArgumentException('Report format length cannot be less than 4! Yours has only ' . strlen($format) . ' !');
        }
        if(strlen($format) > 5) {
            throw new InvalidArgumentException('Report format length cannot be greater than 5! Yours has only ' . strlen($format) . ' !');
        }
        if (!preg_match('/(.xml)|(.html)(.json)(.csv)/', $format)) {
            throw new InvalidArgumentException('Only \'.csv\', \'.xml\', \'.html\' and \'.json\' formats are allowed!');
        }
        $this->_values['reportFormat'] = $format;
    }

    public function setCreatedAt($createdAt = false) {
        $this->_values['rCreatedAt'] = date("Y-m-d H:i:s");
    }
}