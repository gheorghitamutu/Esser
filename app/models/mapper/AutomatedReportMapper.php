<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 08-Jun-18
 * Time: 18:34
 */

namespace ModelMapper;
use DatabaseConnectivity, AppModel, OCI_Collection;

class AutomatedReportMapper extends AbstractMapper
{
    protected $_entityTable = 'AUTOMATEDREPORT';
    protected $_entityClass = 'AutomatedReport';

    /**
     * Create an useracc entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $automatedreport = new $this->_entityClass(array(
            'reportId'      => $data['reportId'],
            'reportPath'    => $data['reportPath'],
            'reportType'    => $data['reportType'],
            'reportFormat'  => $data['reportFormat'],
            'rCreatedAt'    => $data['rCreatedAt']
        ));
        return $automatedreport;
    }
}